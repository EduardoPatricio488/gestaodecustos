// Finance Pro AI — global modal behaviour.
// Keeps Flux, Livewire and Alpine dialogs consistent without replacing their state management.
(() => {
    const CLOSE_LABELS = /^(fechar|close|close modal|cancelar|descartar|cancel|discard)$/i;
    const VISIBLE = (el) => {
        if (!el || !el.isConnected) return false;
        const style = window.getComputedStyle(el);
        const rect = el.getBoundingClientRect();
        return style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0' && rect.width > 0 && rect.height > 0;
    };
    const dialogs = () => Array.from(document.querySelectorAll('[role="dialog"], dialog[open]')).filter(VISIBLE);
    const topDialog = () => dialogs().at(-1) || null;
    const closeButtons = (dialog) => Array.from(dialog.querySelectorAll('button, [role="button"]')).filter((button) => {
        const label = (button.getAttribute('aria-label') || button.textContent || '').replace(/\\s+/g, ' ').trim();
        return CLOSE_LABELS.test(label) || button.dataset.modalClose === 'true' || button.classList.contains('finance-pro-modal-close');
    });
    const findExistingTopClose = (dialog) => {
        const rect = dialog.getBoundingClientRect();
        return Array.from(dialog.querySelectorAll('button')).find((button) => {
            if (button.dataset.financeModalInjected === '1') return false;
            const buttonRect = button.getBoundingClientRect();
            const text = (button.textContent || '').trim();
            const label = button.getAttribute('aria-label') || '';
            const iconOnly = !text && button.querySelector('svg');
            const nearTopRight = buttonRect.top - rect.top < 110 && rect.right - buttonRect.right < 110;
            return CLOSE_LABELS.test(label) || (iconOnly && nearTopRight);
        }) || null;
    };
    const close = (dialog) => {
        const existing = findExistingTopClose(dialog) || closeButtons(dialog)[0];
        if (existing) { existing.click(); return; }
        const name = dialog.getAttribute('data-modal') || dialog.getAttribute('data-name') || dialog.getAttribute('data-modal-name') || dialog.id || '';
        if (name) window.dispatchEvent(new CustomEvent('modal-close', { detail: { name } }));
        if (dialog instanceof HTMLDialogElement && dialog.open) dialog.close();
    };
    const ensureDialog = (dialog) => {
        if (!dialog.hasAttribute('role')) dialog.setAttribute('role', 'dialog');
        dialog.setAttribute('aria-modal', 'true');
        const heading = dialog.querySelector('h1,h2,h3,h4,h5,h6');
        if (heading && !heading.id) heading.id = `finance-pro-modal-title-${Math.random().toString(36).slice(2, 9)}`;
        if (heading && !dialog.hasAttribute('aria-labelledby')) dialog.setAttribute('aria-labelledby', heading.id);
        if (!heading && !dialog.hasAttribute('aria-label')) dialog.setAttribute('aria-label', 'Janela de diálogo');

        let button = findExistingTopClose(dialog);
        if (!button) {
            button = document.createElement('button');
            button.type = 'button';
            button.dataset.modalClose = 'true';
            button.dataset.financeModalInjected = '1';
            button.setAttribute('aria-label', 'Fechar');
            button.className = 'finance-pro-modal-close';
            button.innerHTML = '<span aria-hidden="true">&times;</span>';
            button.addEventListener('click', (event) => { event.preventDefault(); close(dialog); });
            if (getComputedStyle(dialog).position === 'static') dialog.style.position = 'relative';
            dialog.prepend(button);
        } else {
            button.classList.add('finance-pro-modal-close');
            if (!button.getAttribute('aria-label')) button.setAttribute('aria-label', 'Fechar');
        }
    };

    let lastActive = null;
    let hadDialog = false;
    const sync = () => {
        const activeDialogs = dialogs();
        activeDialogs.forEach(ensureDialog);
        if (activeDialogs.length && !hadDialog) {
            lastActive = document.activeElement instanceof HTMLElement ? document.activeElement : null;
            const closeButton = findExistingTopClose(activeDialogs.at(-1));
            (closeButton || activeDialogs.at(-1).querySelector('input,select,textarea,button,[tabindex]:not([tabindex="-1"])'))?.focus({ preventScroll: true });
        }
        hadDialog = activeDialogs.length > 0;
        document.documentElement.classList.toggle('finance-pro-modal-open', activeDialogs.length > 0);
        if (!activeDialogs.length && lastActive?.isConnected) { lastActive.focus({ preventScroll: true }); lastActive = null; }
    };

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const dialog = topDialog();
        if (!dialog) return;
        event.preventDefault();
        event.stopPropagation();
        close(dialog);
    }, true);

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Tab') return;
        const dialog = topDialog();
        if (!dialog) return;
        const focusable = Array.from(dialog.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])')).filter(VISIBLE);
        if (!focusable.length) return;
        const first = focusable[0], last = focusable.at(-1);
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    }, true);

    const observer = new MutationObserver(() => requestAnimationFrame(sync));
    const start = () => {
        observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['style', 'class', 'open', 'aria-hidden'] });
        sync();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, { once: true }); else start();
    document.addEventListener('livewire:navigated', () => setTimeout(sync, 50));
    window.addEventListener('modal-show', () => setTimeout(sync, 50));
    window.addEventListener('modal-close', () => setTimeout(sync, 50));
})();
