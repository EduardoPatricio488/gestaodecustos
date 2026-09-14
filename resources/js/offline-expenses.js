import './offline-bunker-boot';

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

// Na gestão de clientes, concentra as ações do registo no menu de três pontos.
(function setupClientRecordActionsMenu() {
    const normalize = (value) => String(value || '').replace(/\s+/g, ' ').trim();

    const moveActionsIntoMenu = (card) => {
        if (!card || card.dataset.clientActionsMenuReady === '1') return;

        const menu = card.querySelector('[x-show="optionsOpen"]');
        if (!menu) return;

        const buttons = Array.from(card.querySelectorAll('button, [role="button"]'));
        const findAction = (text) => buttons.find((button) => normalize(button.textContent).toLowerCase().includes(text));

        const portalButton = findAction('gerar portal');
        const resendButton = findAction('reenviar código');
        const historyButton = findAction('ver histórico');

        if (!portalButton && !resendButton && !historyButton) return;

        const menuBody = menu.querySelector('.p-1\\.5') || menu.firstElementChild || menu;
        if (!menuBody) return;

        const divider = document.createElement('div');
        divider.className = 'border-t border-zinc-100 dark:border-zinc-800 my-1';

        const actions = [
            { button: portalButton, label: 'Gerar Portal', icon: 'link', tone: 'text-brand-600 dark:text-brand-400' },
            { button: resendButton, label: 'Reenviar Código', icon: 'envelope', tone: 'text-brand-600 dark:text-brand-400' },
            { button: historyButton, label: 'Ver Histórico', icon: 'clock', tone: 'text-brand-600 dark:text-brand-400' },
        ].filter((action) => action.button);

        if (!actions.length) return;

        menuBody.appendChild(divider);

        actions.forEach(({ button, label, tone }) => {
            button.classList.remove('shadow-sm');
            button.classList.add('!w-full', '!justify-start', '!px-4', '!py-2.5', '!rounded-xl', '!text-[11px]', '!font-black', '!uppercase', '!tracking-widest', tone, 'hover:!bg-brand-50', 'dark:hover:!bg-brand-950/30', '!border-0');
            if (label === 'Ver Histórico') {
                button.classList.add('text-brand-600', 'dark:text-brand-400');
            }
            menuBody.appendChild(button);
        });

        const footer = portalButton?.closest('.flex.items-center.gap-2') || historyButton?.parentElement;
        if (footer && footer !== menuBody) {
            footer.querySelectorAll('button, [role="button"]').forEach((button) => {
                if (!menu.contains(button)) button.remove();
            });
        }

        card.dataset.clientActionsMenuReady = '1';
    };

    const scan = () => {
        document.querySelectorAll('[wire\\:key^="client-card-"]').forEach(moveActionsIntoMenu);
    };

    const start = () => {
        scan();
        if (document.body) {
            new MutationObserver(() => scan()).observe(document.body, { childList: true, subtree: true });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }

    document.addEventListener('livewire:navigated', () => setTimeout(scan, 50));
})();
