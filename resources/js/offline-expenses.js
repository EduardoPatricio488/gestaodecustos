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

// As ações do cliente pertencem exclusivamente ao menu dos três pontos.
// Não movemos os componentes Flux originais: criamos ações independentes no menu
// e ocultamos estruturalmente a antiga linha de ações do card. Assim, um refresh
// ou qualquer re-render do Livewire não volta a mostrar os botões no card.
(function setupClientRecordActionsMenu() {
    const CARD_SELECTOR = '[wire\\:key^="client-card-"]';
    const MENU_SELECTOR = '[x-show="optionsOpen"]';
    const FOOTER_ACTION_SELECTOR = `${CARD_SELECTOR} > div:last-child > div:last-child`;

    const normalize = (value) => String(value || '').replace(/\s+/g, ' ').trim().toLowerCase();

    const ensurePermanentFooterRule = () => {
        if (document.getElementById('finance-pro-client-actions-style')) return;

        const style = document.createElement('style');
        style.id = 'finance-pro-client-actions-style';
        style.textContent = `
            ${FOOTER_ACTION_SELECTOR} {
                display: none !important;
            }
        `;
        document.head.appendChild(style);
    };

    const getComponent = (card) => {
        const root = card.closest('[wire\\:id]');
        const componentId = root?.getAttribute('wire:id');

        if (!componentId || !window.Livewire?.find) return null;

        return window.Livewire.find(componentId);
    };

    const findOriginalAction = (card, text) => Array.from(card.querySelectorAll('button, [role="button"]'))
        .find((button) => !button.closest(MENU_SELECTOR) && normalize(button.textContent).includes(text));

    const closeMenu = (button) => {
        const alpineRoot = button.closest('[x-data]');
        const data = alpineRoot?._x_dataStack?.[0];

        if (data) data.optionsOpen = false;
    };

    const createMenuAction = ({ key, label, icon, wireClick, component }) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.clientMenuAction = key;
        button.className = 'w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-zinc-800 transition-all text-left';

        const iconElement = document.createElement('span');
        iconElement.className = 'w-4 shrink-0 text-center text-brand-500 font-black';
        iconElement.textContent = icon;

        const textElement = document.createElement('span');
        textElement.textContent = label;

        button.append(iconElement, textElement);

        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const match = String(wireClick || '').match(/^([A-Za-z0-9_]+)\((\d+)\)$/);
            if (!component || typeof component.call !== 'function' || !match) return;

            const [, method, id] = match;
            closeMenu(button);
            component.call(method, Number(id));
        });

        return button;
    };

    const setupCard = (card) => {
        const menu = card.querySelector(MENU_SELECTOR);
        if (!menu) return;

        const menuBody = menu.querySelector(':scope > div') || menu;
        if (!menuBody) return;

        const component = getComponent(card);
        if (!component) return;

        const actions = [
            { text: 'gerar portal', key: 'gerar-portal', label: 'Gerar Portal', icon: '↗' },
            { text: 'reenviar código', key: 'reenviar-codigo', label: 'Reenviar Código', icon: '✉' },
            { text: 'ver histórico', key: 'ver-historico', label: 'Ver Histórico', icon: '→' },
        ];

        let divider = menuBody.querySelector('[data-client-actions-divider="1"]');
        if (!divider) {
            divider = document.createElement('div');
            divider.dataset.clientActionsDivider = '1';
            divider.className = 'border-t border-zinc-100 dark:border-zinc-800 my-1';
        }

        const generated = [];

        actions.forEach((action) => {
            const existing = menuBody.querySelector(`[data-client-menu-action="${action.key}"]`);
            if (existing) {
                generated.push(existing);
                return;
            }

            const original = findOriginalAction(card, action.text);
            if (!original) return;

            const wireClick = original.getAttribute('wire:click');
            if (!wireClick) return;

            generated.push(createMenuAction({
                key: action.key,
                label: action.label,
                icon: action.icon,
                wireClick,
                component,
            }));
        });

        if (!generated.length) return;

        if (!menuBody.contains(divider)) {
            menuBody.appendChild(divider);
        }

        generated.forEach((button) => {
            if (!menuBody.contains(button)) {
                menuBody.appendChild(button);
            }
        });
    };

    let scanQueued = false;

    const scan = () => {
        scanQueued = false;
        ensurePermanentFooterRule();
        document.querySelectorAll(CARD_SELECTOR).forEach(setupCard);
    };

    const queueScan = () => {
        if (scanQueued) return;
        scanQueued = true;
        queueMicrotask(scan);
    };

    const scanAfterNavigation = () => {
        ensurePermanentFooterRule();
        scan();
        [50, 150, 350, 750].forEach((delay) => window.setTimeout(scan, delay));
    };

    const registerLivewireHooks = () => {
        if (!window.Livewire?.hook) return;

        try {
            window.Livewire.hook('morph.updated', queueScan);
        } catch {}

        try {
            window.Livewire.hook('message.processed', queueScan);
        } catch {}
    };

    const start = () => {
        ensurePermanentFooterRule();
        scanAfterNavigation();
        registerLivewireHooks();

        if (document.body) {
            new MutationObserver(queueScan).observe(document.body, {
                childList: true,
                subtree: true,
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }

    document.addEventListener('livewire:navigated', scanAfterNavigation);
    document.addEventListener('livewire:initialized', () => {
        registerLivewireHooks();
        scanAfterNavigation();
    });
    window.addEventListener('load', scanAfterNavigation);
})();
