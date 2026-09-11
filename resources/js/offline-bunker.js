const QUEUE_KEY = 'finance-pro-offline-expenses';
const LEGACY_QUEUE_KEY = 'offline_vault';

const safeParse = (value, fallback = []) => {
    try {
        const parsed = JSON.parse(value ?? '');
        return Array.isArray(parsed) ? parsed : fallback;
    } catch {
        return fallback;
    }
};

function normalizeLegacyItem(item) {
    return {
        client_id: item.client_id || item.id || crypto.randomUUID(),
        amount: Number(item.amount || 0),
        title: item.title || item.description || 'Despesa offline',
        description: item.description || null,
        category_slug: item.category_slug || item.category || null,
        spent_at: item.spent_at || item.date || item.created_at || new Date().toISOString(),
        payment_method: item.payment_method || null,
        notes: item.notes || null,
        created_at: item.created_at || item.date || new Date().toISOString(),
        local_status: item.local_status || 'pending',
        attempts: Number(item.attempts || 0),
        last_error: item.last_error || null,
    };
}

export function migrateOfflineStorage() {
    const canonical = safeParse(localStorage.getItem(QUEUE_KEY), []);
    const legacy = safeParse(localStorage.getItem(LEGACY_QUEUE_KEY), []);

    if (legacy.length) {
        const known = new Set(canonical.map(item => item.client_id).filter(Boolean));
        for (const item of legacy.map(normalizeLegacyItem)) {
            if (!known.has(item.client_id)) {
                canonical.push(item);
                known.add(item.client_id);
            }
        }
        localStorage.setItem(QUEUE_KEY, JSON.stringify(canonical));
        localStorage.removeItem(LEGACY_QUEUE_KEY);
    }

    return canonical;
}

export function getOfflineBunkerQueue() {
    migrateOfflineStorage();
    return safeParse(localStorage.getItem(QUEUE_KEY), []).map(normalizeLegacyItem);
}

export function writeOfflineBunkerQueue(queue) {
    localStorage.setItem(QUEUE_KEY, JSON.stringify(queue));
    window.dispatchEvent(new CustomEvent('offline-queue-updated', { detail: { queue } }));
    return queue;
}

export function saveBunkerExpense(expense) {
    const queue = getOfflineBunkerQueue();
    const item = normalizeLegacyItem({
        ...expense,
        client_id: crypto.randomUUID(),
        created_at: new Date().toISOString(),
        local_status: 'pending',
        attempts: 0,
        last_error: null,
    });
    queue.push(item);
    writeOfflineBunkerQueue(queue);
    return item;
}

export function removeBunkerItems(clientIds) {
    const ids = new Set(clientIds);
    return writeOfflineBunkerQueue(getOfflineBunkerQueue().filter(item => !ids.has(item.client_id)));
}

export function clearBunkerQueue() {
    localStorage.removeItem(QUEUE_KEY);
    localStorage.removeItem(LEGACY_QUEUE_KEY);
    window.dispatchEvent(new CustomEvent('offline-queue-updated', { detail: { queue: [] } }));
}

export function exportBunkerData(format = 'json') {
    const queue = getOfflineBunkerQueue();
    const stamp = new Date().toISOString().replace(/[:.]/g, '-');

    if (format === 'csv') {
        const headers = ['client_id', 'amount', 'title', 'description', 'category_slug', 'spent_at', 'payment_method', 'notes', 'created_at'];
        const escape = value => `"${String(value ?? '').replaceAll('"', '""')}"`;
        const csv = [headers.join(','), ...queue.map(item => headers.map(key => escape(item[key])).join(','))].join('\n');
        downloadBlob(new Blob([csv], { type: 'text/csv;charset=utf-8' }), `finance-pro-bunker-${stamp}.csv`);
        return;
    }

    downloadBlob(new Blob([JSON.stringify({ exported_at: new Date().toISOString(), expenses: queue }, null, 2)], { type: 'application/json' }), `finance-pro-bunker-${stamp}.json`);
}

function downloadBlob(blob, filename) {
    const url = URL.createObjectURL(blob);
    const anchor = document.createElement('a');
    anchor.href = url;
    anchor.download = filename;
    anchor.click();
    setTimeout(() => URL.revokeObjectURL(url), 1000);
}

export function getBunkerStats(queue = getOfflineBunkerQueue()) {
    const valid = queue.filter(item => Number.isFinite(Number(item.amount)) && Number(item.amount) > 0);
    const total = valid.reduce((sum, item) => sum + Number(item.amount), 0);
    const byCategory = valid.reduce((acc, item) => {
        const key = item.category_slug || 'sem-categoria';
        acc[key] = (acc[key] || 0) + Number(item.amount);
        return acc;
    }, {});
    const topCategory = Object.entries(byCategory).sort((a, b) => b[1] - a[1])[0];

    return {
        count: valid.length,
        total,
        average: valid.length ? total / valid.length : 0,
        largest: valid.reduce((max, item) => Math.max(max, Number(item.amount)), 0),
        topCategory: topCategory?.[0] || null,
    };
}

export function getLocalStorageEstimate() {
    if (!navigator.storage?.estimate) return null;
    return navigator.storage.estimate().then(({ usage, quota }) => ({
        usage: Number(usage || 0),
        quota: Number(quota || 0),
        ratio: quota ? Math.min(1, usage / quota) : null,
    })).catch(() => null);
}

export async function syncBunkerExpenses() {
    const queue = getOfflineBunkerQueue();
    if (!queue.length) return { synced: [], failed: [], count: 0 };

    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) throw new Error('SESSION_UNAVAILABLE');

    writeOfflineBunkerQueue(queue.map(item => ({ ...item, local_status: 'syncing' })));

    try {
        const response = await fetch('/api/offline/expenses/sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                Accept: 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ expenses: queue }),
        });

        if (response.status === 419 || response.status === 401) {
            writeOfflineBunkerQueue(queue.map(item => ({ ...item, local_status: 'pending', last_error: 'Sessão expirada.' })));
            throw new Error('SESSION_EXPIRED');
        }

        if (!response.ok) {
            throw new Error(`HTTP_${response.status}`);
        }

        const data = await response.json();
        const syncedIds = new Set((data.synced || []).map(item => item.client_id).filter(Boolean));
        const failedIds = new Set((data.failed || []).map(item => item.client_id).filter(Boolean));
        const remaining = getOfflineBunkerQueue()
            .filter(item => !syncedIds.has(item.client_id))
            .map(item => ({
                ...item,
                local_status: failedIds.has(item.client_id) ? 'error' : 'pending',
                attempts: Number(item.attempts || 0) + (failedIds.has(item.client_id) ? 1 : 0),
                last_error: data.failed?.find(error => error.client_id === item.client_id)?.message || null,
            }));

        writeOfflineBunkerQueue(remaining);
        return { ...data, remaining: remaining.length };
    } catch (error) {
        const latest = getOfflineBunkerQueue().map(item => ({
            ...item,
            local_status: 'pending',
            attempts: Number(item.attempts || 0) + 1,
            last_error: error.message === 'SESSION_EXPIRED' ? 'Sessão expirada. Entra novamente para sincronizar.' : 'Servidor indisponível. Tentaremos novamente quando a ligação estiver estável.',
        }));
        writeOfflineBunkerQueue(latest);
        throw error;
    }
}

function notifySync() {
    if (!navigator.onLine || !getOfflineBunkerQueue().length) return;
    syncBunkerExpenses()
        .then(data => window.dispatchEvent(new CustomEvent('offline-bunker-synced', { detail: data })))
        .catch(error => window.dispatchEvent(new CustomEvent('offline-bunker-sync-error', { detail: { error } })));
}

migrateOfflineStorage();
window.addEventListener('online', () => setTimeout(notifySync, 250));
window.addEventListener('offline', () => window.dispatchEvent(new CustomEvent('offline-bunker-status', { detail: { online: false } })));
if (navigator.onLine) setTimeout(notifySync, 500);

window.financeProBunker = {
    getOfflineQueue: getOfflineBunkerQueue,
    saveOfflineExpense: saveBunkerExpense,
    syncOfflineExpenses: syncBunkerExpenses,
    clearOfflineQueue: clearBunkerQueue,
    exportOfflineData: exportBunkerData,
    getStats: getBunkerStats,
    getStorageEstimate: getLocalStorageEstimate,
};
