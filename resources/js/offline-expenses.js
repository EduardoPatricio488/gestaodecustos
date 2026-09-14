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

// Na gestão de clientes, todas as ações do registo ficam dentro do menu de três pontos.
(function setupClientRecordActionsMenu() {
    const normalize = (value) => String(value || '').replace(/\s+/g, ' ').trim().toLowerCase();

    const findAction = (card, text) => Array.from(card.querySelectorAll('button, [role="button"]'))
        .find((button) => normalize(button.textContent).includes(text));

    const moveActionsIntoMenu = (card) => {
        const menu = card.querySelector('[x-show="optionsOpen"]');
        if (!menu) return;

        const menuBody = menu.querySelector(':scope > div') || menu;
        if (!menuBody) return;

        const actions = [
            { text: 'gerar portal', label: 'Gerar Portal' },
            { text: 'reenviar código', label: 'Reenviar Código' },
            { text: 'ver histórico', label: 'Ver Histórico' },
        ];

        const found = actions
            .map((action) => ({ ...action, button: findAction(card, action.text) }))
            .filter(({ button }) => button && !menu.contains(button));

        if (!found.length) return;

        let divider = menuBody.querySelector('[data-client-actions-divider="1"]');
        if (!divider) {
            divider = document.createElement('div');
            divider.dataset.clientActionsDivider = '1';
            divider.className = 'border-t border-zinc-100 dark:border-zinc-800 my-1';
            menuBody.appendChild(divider);
        }

        found.forEach(({ button, label }) => {
            button.classList.remove('shadow-sm', 'bg-brand-600', 'bg-brand-500', 'text-white');
            button.classList.add(
                '!w-full', '!justify-start', '!px-4', '!py-2.5', '!rounded-xl',
                '!text-[11px]', '!font-black', '!uppercase', '!tracking-widest',
                'text-brand-600', 'dark:text-brand-400',
                'hover:!bg-brand-50', 'dark:hover:!bg-brand-950/30', '!border-0',
                '!bg-transparent'
            );
            button.setAttribute('title', label);
            menuBody.appendChild(button);
        });

        // Remove the original action row so the buttons cannot remain visible outside the menu.
        const footer = found[0]?.button?.parentElement;
        if (footer && footer !== menuBody && !menu.contains(footer)) {
            footer.remove();
        }
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
