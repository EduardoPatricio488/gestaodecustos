<style>
    /* Modal "Gerar Portal": layout dedicado para evitar sobreposições. */
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

    .portal-link-modal-fixed > div > div:first-child {
        position: relative !important;
        min-width: 0 !important;
        padding-right: 3rem !important;
    }

    .portal-link-modal-fixed button[aria-label="Fechar"] {
        position: absolute !important;
        top: 0 !important;
        right: 0 !important;
        z-index: 20 !important;
        flex-shrink: 0 !important;
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
        .portal-link-modal-fixed > div {
            padding: 1.25rem !important;
        }

        .portal-link-modal-fixed .space-y-6 {
            row-gap: 1rem !important;
        }

        .portal-link-modal-fixed .p-8 {
            padding: 1.25rem !important;
        }

        .portal-link-modal-fixed .relative.z-10.flex.items-center.justify-center {
            flex-wrap: wrap !important;
            gap: .875rem !important;
        }

        .portal-link-modal-fixed .text-xs {
            line-height: 1.45 !important;
        }

        .portal-link-modal-fixed .flex.gap-4:last-child {
            flex-direction: column !important;
        }

        .portal-link-modal-fixed .flex.gap-4:last-child > * {
            width: 100% !important;
            flex: 1 1 auto !important;
        }
    }
</style>

<script>
    (() => {
        const markPortalModal = () => {
            document.querySelectorAll('[role="dialog"], dialog, [data-flux-modal]').forEach((modal) => {
                const text = modal.textContent || '';

                if (text.includes('Chave de Acesso') && text.includes('Código Único de Entrada')) {
                    modal.classList.add('portal-link-modal-fixed');
                }
            });
        };

        const bindNewClientButton = () => {
            document.querySelectorAll('button').forEach((button) => {
                if (button.dataset.financeProClientModalBound === '1') {
                    return;
                }

                if ((button.textContent || '').trim().replace(/\s+/g, ' ') !== 'Novo Cliente') {
                    return;
                }

                const component = button.closest('[wire\\:id]');
                if (!component || !window.Livewire) {
                    return;
                }

                const componentId = component.getAttribute('wire:id');
                if (!componentId) {
                    return;
                }

                button.dataset.financeProClientModalBound = '1';
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    event.stopImmediatePropagation();

                    const livewireComponent = window.Livewire.find(componentId);
                    if (livewireComponent) {
                        livewireComponent.call('openClientModal');
                    }
                }, true);
            });
        };

        const refresh = () => {
            markPortalModal();
            bindNewClientButton();
        };

        refresh();

        if (!window.__financeProClientHubObserver) {
            window.__financeProClientHubObserver = new MutationObserver(refresh);
            window.__financeProClientHubObserver.observe(document.body, {
                childList: true,
                subtree: true,
            });
        }

        document.addEventListener('livewire:navigated', refresh);
    })();
</script>

@include('livewire.business.client-hub')
