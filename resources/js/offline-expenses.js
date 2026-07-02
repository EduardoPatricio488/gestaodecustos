const QUEUE_KEY = 'finance-pro-offline-expenses';

export function getOfflineQueue() {
    try {
        return JSON.parse(localStorage.getItem(QUEUE_KEY) || '[]');
    } catch {
        return [];
    }
}

export function saveOfflineExpense(expense) {
    const queue = getOfflineQueue();
    queue.push({
        ...expense,
        client_id: crypto.randomUUID(),
        created_at: new Date().toISOString(),
    });
    localStorage.setItem(QUEUE_KEY, JSON.stringify(queue));
    return queue.length;
}

export function clearSynced(clientIds) {
    const ids = new Set(clientIds);
    const remaining = getOfflineQueue().filter((e) => !ids.has(e.client_id));
    localStorage.setItem(QUEUE_KEY, JSON.stringify(remaining));
    return remaining.length;
}

export async function syncOfflineExpenses() {
    const queue = getOfflineQueue();
    if (queue.length === 0) return { synced: 0 };

    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) return { synced: 0 };

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

    if (!response.ok) {
        throw new Error('Sync failed');
    }

    const data = await response.json();
    const syncedIds = (data.synced || []).map((s) => s.client_id).filter(Boolean);
    clearSynced(syncedIds);

    return data;
}

window.addEventListener('online', () => {
    syncOfflineExpenses()
        .then((data) => {
            if (data.count > 0) {
                window.dispatchEvent(new CustomEvent('offline-synced', { detail: data }));
            }
        })
        .catch(() => {});
});

if (navigator.onLine && getOfflineQueue().length > 0) {
    syncOfflineExpenses().catch(() => {});
}

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.addEventListener('message', (event) => {
        if (event.data?.type === 'SYNC_OFFLINE_EXPENSES') {
            syncOfflineExpenses().catch(() => {});
        }
    });
}

window.financeProOffline = { saveOfflineExpense, getOfflineQueue, syncOfflineExpenses };
