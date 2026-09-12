(function () {
    'use strict';

    const CLOSE_TEXT = /^(fechar|close|descartar|discard)$/i;
    const CLOSE_LABEL = /^(fechar|close|close modal|dismiss)$/i;

    const isModal = (element) => {
        if (!(element instanceof HTMLElement)) return false;
        return element.matches('[role="dialog"], dialog, [data-flux-modal]') || !!element.querySelector?.('[data-flux-modal-close]');
    };

    const getModal = (button) => button.closest('[role="dialog"], dialog, [data-flux-modal]');

    const isCloseControl = (button) => {
        if (!(button instanceof HTMLButtonElement)) return false;
        if (button.matches('[data-flux-modal-close], [aria-label="Fechar modal"], [aria-label="Close modal"], [title="Fechar"], [title="Close"]')) return true;
        const label = (button.getAttribute('aria-label') || '').trim();
        const text = (button.textContent || '').replace(/\s+/g, ' ').trim();
        return CLOSE_LABEL.test(label) || CLOSE_TEXT.test(text);
    };

    const hideExtraCloseControls = (modal, keep) => {
        modal.querySelectorAll('button').forEach((button) => {
            if (button === keep || !isCloseControl(button)) return;
            button.setAttribute('data-finance-pro-hidden-close', '1');
            button.style.display = 'none';
        });
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
            modal.querySelector('[data-flux-modal-close]')?.click();
            if (modal.getAttribute('data-modal')) {
                window.dispatchEvent(new CustomEvent('close-modal', { detail: modal.getAttribute('data-modal') }));
            }
            if (window.Alpine) {
                modal.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', code: 'Escape', bubbles: true }));
            }
        });
        return button;
    };

    const normalizeModal = (modal) => {
        if (!isModal(modal)) return;
        const existing = Array.from(modal.querySelectorAll('button')).find(isCloseControl);
        const close = existing || makeFallbackClose(modal);

        if (!existing) {
            const panel = modal.querySelector(':scope > div, .relative, [data-flux-modal-content]') || modal.firstElementChild || modal;
            if (panel instanceof HTMLElement) {
                if (getComputedStyle(panel).position === 'static') panel.style.position = 'relative';
                panel.prepend(close);
            } else {
                modal.prepend(close);
            }
        } else {
            positionCloseControl(close);
            const parent = close.parentElement;
            if (parent && parent !== modal && parent.children.length === 1 && !parent.textContent?.trim()) {
                parent.style.position = 'absolute';
                parent.style.left = '1.25rem';
                parent.style.right = 'auto';
                parent.style.top = '1.25rem';
                parent.style.zIndex = '100';
            }
        }

        positionCloseControl(close);
        hideExtraCloseControls(modal, close);
        modal.setAttribute('data-finance-pro-modal-normalized', '1');
    };

    const scan = (root = document) => {
        root.querySelectorAll?.('[role="dialog"], dialog, [data-flux-modal]').forEach((modal) => {
            if (modal.getAttribute('data-finance-pro-modal-normalized') !== '1') normalizeModal(modal);
        });
    };

    const start = () => {
        scan();
        const observer = new MutationObserver(() => scan());
        if (document.body) observer.observe(document.body, { childList: true, subtree: true });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }

    document.addEventListener('livewire:navigated', () => setTimeout(scan, 50));
    document.addEventListener('livewire:initialized', () => setTimeout(scan, 50), { once: true });
})();
