<style>
    .portal-link-modal-fixed {
        width: min(560px, calc(100vw - 2rem)) !important;
        max-width: 560px !important;
        max-height: 90vh !important;
        padding: 0 !important;
        overflow: hidden !important;
    }
    .portal-link-modal-fixed > div {
        max-height: 90vh !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 1.75rem !important;
        box-sizing: border-box !important;
    }
    .portal-link-modal-fixed .font-mono { min-width: 0 !important; overflow-wrap: anywhere !important; word-break: break-word !important; }
    .portal-link-modal-fixed .text-5xl { font-size: clamp(2rem, 9vw, 3rem) !important; line-height: 1 !important; letter-spacing: .12em !important; }
    @media (max-width: 640px) {
        .portal-link-modal-fixed > div { padding: 1.25rem !important; }
        .portal-link-modal-fixed .p-8 { padding: 1.25rem !important; }
        .portal-link-modal-fixed .flex.gap-4:last-child { flex-direction: column !important; }
        .portal-link-modal-fixed .flex.gap-4:last-child > * { width: 100% !important; flex: 1 1 auto !important; }
    }
</style>

<script>
(() => {
    const componentFor = (el) => {
        if (!window.Livewire) return null;
        const root = el.closest('[wire\\:id]');
        const id = root?.getAttribute('wire:id');
        return id ? window.Livewire.find(id) : null;
    };

    const bind = () => {
        document.querySelectorAll('[wire\\:click]').forEach((el) => {
            const action = (el.getAttribute('wire:click') || '').trim();
            const match = action.match(/^(generatePortalLink|openHistory|edit|delete)\(\s*(\d+)\s*\)$/);
            if (!match || el.dataset.clientBridgeBound === '1') return;
            const component = componentFor(el);
            if (!component) return;

            el.dataset.clientBridgeBound = '1';
            el.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                if (match[1] === 'delete' && !window.confirm('Apagar cliente e todo o histórico associado?')) return;
                component.call(match[1], Number(match[2]));
            });
        });

        document.querySelectorAll('button').forEach((el) => {
            if (el.dataset.clientNewBridgeBound === '1') return;
            if (el.textContent.trim().replace(/\s+/g, ' ') !== 'Novo Cliente') return;
            const component = componentFor(el);
            if (!component) return;
            el.dataset.clientNewBridgeBound = '1';
            el.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                component.call('openClientModal');
            });
        });
    };

    const stylePortalModal = () => {
        document.querySelectorAll('[role="dialog"]').forEach((modal) => {
            const text = modal.textContent || '';
            if (text.includes('Chave de Acesso') && text.includes('Código Único de Entrada')) {
                modal.classList.add('portal-link-modal-fixed');
            }
        });
    };

    const refresh = () => { bind(); stylePortalModal(); };
    refresh();
    document.addEventListener('livewire:navigated', refresh);
    document.addEventListener('livewire:init', refresh, { once: true });
    window.addEventListener('flux:modal-opened', refresh);
})();
</script>

@include('livewire.business.client-hub')
