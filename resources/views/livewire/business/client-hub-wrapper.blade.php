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

    .portal-link-modal-fixed .font-mono {
        min-width: 0 !important;
        overflow-wrap: anywhere !important;
        word-break: break-word !important;
    }

    .portal-link-modal-fixed .text-5xl {
        font-size: clamp(2rem, 9vw, 3rem) !important;
        line-height: 1 !important;
        letter-spacing: .12em !important;
    }

    @media (max-width: 640px) {
        .portal-link-modal-fixed > div { padding: 1.25rem !important; }
        .portal-link-modal-fixed .p-8 { padding: 1.25rem !important; }
        .portal-link-modal-fixed .flex.gap-4:last-child { flex-direction: column !important; }
        .portal-link-modal-fixed .flex.gap-4:last-child > * { width: 100% !important; flex: 1 1 auto !important; }
    }
</style>

<script>
(() => {
    const openModal = (name) => {
        try {
            if (window.Flux?.modal) {
                window.Flux.modal(name).show();
                return true;
            }
        } catch (error) {
            console.error(`[ClientHub] Não foi possível abrir o modal ${name}.`, error);
        }

        return false;
    };

    const closeModal = (name) => {
        try {
            window.Flux?.modal?.(name)?.close();
        } catch (error) {
            console.error(`[ClientHub] Não foi possível fechar o modal ${name}.`, error);
        }
    };

    const stylePortalModal = () => {
        document.querySelectorAll('[role="dialog"]').forEach((modal) => {
            const text = modal.textContent || '';
            if (text.includes('Chave de Acesso') && text.includes('Código Único de Entrada')) {
                modal.classList.add('portal-link-modal-fixed');
            }
        });
    };

    const bind = () => {
        stylePortalModal();

        window.addEventListener('client-modal-open', () => {
            requestAnimationFrame(() => openModal('client-modal'));
        });

        window.addEventListener('client-modal-close', () => {
            requestAnimationFrame(() => closeModal('client-modal'));
        });

        window.addEventListener('history-modal-open', () => {
            requestAnimationFrame(() => openModal('history-modal'));
        });

        window.addEventListener('portal-link-modal-open', () => {
            requestAnimationFrame(() => {
                openModal('portal-link-modal');
                setTimeout(stylePortalModal, 50);
            });
        });

        window.addEventListener('flux:modal-opened', stylePortalModal);
    };

    if (window.Livewire) {
        bind();
    } else {
        document.addEventListener('livewire:init', bind, { once: true });
    }
})();
</script>

@include('livewire.business.client-hub')
