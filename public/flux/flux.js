document.addEventListener('alpine:init', () => {
    Alpine.data('fluxModal', (name) => ({
        init() {
            // Escuta o evento de abrir
            window.addEventListener('modal-show', (e) => {
                if (e.detail.name === name) {
                    this.$el.showModal();
                    document.body.style.overflow = 'hidden'; // Trava o scroll do fundo
                }
            });

            // Escuta o evento de fechar
            window.addEventListener('modal-close', (e) => {
                if (!e.detail.name || e.detail.name === name) {
                    this.$el.close();
                    document.body.style.overflow = 'auto'; // Liberta o scroll
                }
            });

            // Permite fechar ao clicar na tecla ESC
            this.$el.addEventListener('cancel', (e) => {
                document.body.style.overflow = 'auto';
            });
        },
        // Funções para os botões dentro do modal
        handleShow(e) { if (e.detail.name === name) this.$el.showModal(); },
        handleClose(e) { if (!e.detail.name || e.detail.name === name) this.$el.close(); }
    }));
});

// Helper global para os botões usarem
window.$flux = {
    modal: (name) => ({
        show: () => document.dispatchEvent(new CustomEvent('modal-show', { detail: { name } })),
        hide: () => document.dispatchEvent(new CustomEvent('modal-close', { detail: { name } }))
    })
};
