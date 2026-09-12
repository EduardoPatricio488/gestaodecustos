(function () {
    'use strict';

    const CLOSE_TEXT = /^(fechar|close|close modal|dismiss|descartar|discard)$/i;
    const CLOSE_LABEL = /^(fechar|close|close modal|dismiss)$/i;
    const MODAL_SELECTOR = '[role="dialog"], dialog, [data-flux-modal]';

    // app.js historically registered a generic capture listener that treated
    // Cancel/Discard buttons as modal close controls. Prevent that legacy
    // listener from being registered; modal actions remain available to their
    // own Livewire/Flux handlers.
    const originalAddEventListener = document.addEventListener.bind(document);
    document.addEventListener = function (type, listener, options) {
        if (type === 'click' && typeof listener === 'function') {
            const source = Function.prototype.toString.call(listener);
            if (source.includes('cancelar') && source.includes('closeModal') && source.includes('[role="dialog"], dialog')) {
                return;
            }
        }
        return originalAddEventListener(type, listener, options);
    };

    const isModal = (element) => {
        if (!(element instanceof HTMLElement)) return false;
        return element.matches(MODAL_SELECTOR) || !!element.querySelector?.('[data-flux-modal-close]');
    };

    const getModal = (button) => button.closest(MODAL_SELECTOR);

    const isCloseControl = (button) => {
        if (!(button instanceof HTMLButtonElement)) return false;
        if (button.classList.contains('finance-pro-modal-close')) return true;
        if (button.matches('[data-flux-modal-close], [aria-label="Fechar modal"], [aria-label="Close modal"], [title="Fechar"], [title="Close"]')) return true;
        const label = (button.getAttribute('aria-label') || '').trim();
        const text = (button.textContent || '').replace(/\s+/g, ' ').trim();
        return CLOSE_LABEL.test(label) || CLOSE_TEXT.test(text);
    };

    const positionCloseControl = (button) => {
        button.classList.add('finance-pro-modal-close');
        button.style.position = 'absolute';
        button.style.left = '1.25rem';
        button.style.right = 'auto';
        button.style.top = '1.25rem';
        button.style.zIndex = '100';
        button.style.display = 'inline-flex';
        button.style.alignItems = 'center';
        button.style.justifyContent = 'center';
        button.setAttribute('aria-label', 'Fechar modal');
        button.setAttribute('title', 'Fechar');
    };

    const hideExtraCloseControls = (modal, keep) => {
        modal.querySelectorAll('button').forEach((button) => {
            if (button === keep) return;
            if (!isCloseControl(button)) return;
            button.setAttribute('data-finance-pro-hidden-close', '1');
            button.style.display = 'none';
        });
    };

    const makeFallbackClose = (modal) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'finance-pro-modal-close inline-flex h-9 w-9 items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-500 shadow-sm transition hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800';
        button.setAttribute('aria-label', 'Fechar modal');
        button.setAttribute('title', 'Fechar');
        button.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/></svg>';
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const nativeDialog = modal instanceof HTMLDialogElement ? modal : null;
            if (nativeDialog?.open) nativeDialog.close();

            const fluxClose = modal.querySelector('[data-flux-modal-close]');
            if (fluxClose && fluxClose !== button) fluxClose.click();

            const modalName = modal.getAttribute('data-modal') || modal.getAttribute('data-name') || modal.getAttribute('data-modal-name') || modal.id || '';
            if (modalName) {
                window.dispatchEvent(new CustomEvent('close-modal', { detail: modalName }));
                window.dispatchEvent(new CustomEvent('modal-close', { detail: { name: modalName } }));
            }

            if (window.Alpine) {
                modal.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', code: 'Escape', bubbles: true }));
            }
        });
        return button;
    };

    const normalizeModal = (modal) => {
        if (!isModal(modal) || modal.getAttribute('data-finance-pro-modal-normalized') === '1') return;

        const existing = Array.from(modal.querySelectorAll('button')).find((button) => {
            if (button.matches('[data-flux-modal-close], [aria-label="Fechar modal"], [aria-label="Close modal"], [title="Fechar"], [title="Close"]')) return true;
            const label = (button.getAttribute('aria-label') || '').trim();
            const text = (button.textContent || '').replace(/\s+/g, ' ').trim();
            return CLOSE_LABEL.test(label) || CLOSE_TEXT.test(text);
        });

        const close = existing || makeFallbackClose(modal);

        if (!existing) {
            const panel = modal.querySelector('[data-flux-modal-content], .relative') || modal.firstElementChild || modal;
            if (panel instanceof HTMLElement) {
                if (getComputedStyle(panel).position === 'static') panel.style.position = 'relative';
                panel.prepend(close);
            } else {
                modal.prepend(close);
            }
        }

        positionCloseControl(close);
        hideExtraCloseControls(modal, close);
        modal.setAttribute('data-finance-pro-modal-normalized', '1');
    };

    const scan = (root = document) => {
        const modals = root.matches?.(MODAL_SELECTOR) ? [root] : [];
        root.querySelectorAll?.(MODAL_SELECTOR).forEach((modal) => modals.push(modal));
        modals.forEach(normalizeModal);
    };

    const start = () => {
        scan();
        if (document.body) {
            const observer = new MutationObserver(() => scan());
            observer.observe(document.body, { childList: true, subtree: true });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }

    document.addEventListener('livewire:navigated', () => setTimeout(scan, 50));
    document.addEventListener('livewire:initialized', () => setTimeout(scan, 50), { once: true });
})();
