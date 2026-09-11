import './push-notifications';
import './offline-expenses';

/**
 * Finance Pro theme manager.
 * Single source of truth for the sidebar and profile theme selectors.
 */
(function () {
    const STORAGE_KEY = 'flux.appearance';
    const LEGACY_KEY = 'theme';
    const MEDIA_QUERY = '(prefers-color-scheme: dark)';

    function getTheme() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved === 'dark' || saved === 'light' || saved === 'system') return saved;
        const legacy = localStorage.getItem(LEGACY_KEY);
        if (legacy === 'dark' || legacy === 'light') {
            localStorage.setItem(STORAGE_KEY, legacy);
            localStorage.removeItem(LEGACY_KEY);
            return legacy;
        }
        return 'system';
    }
    function isDark(theme = getTheme()) { return theme === 'dark' || (theme === 'system' && window.matchMedia(MEDIA_QUERY).matches); }
    function applyTheme(theme) {
        if (!['dark', 'light', 'system'].includes(theme)) theme = 'system';
        localStorage.setItem(STORAGE_KEY, theme);
        localStorage.removeItem(LEGACY_KEY);
        const dark = isDark(theme);
        document.documentElement.classList.toggle('dark', dark);
        document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
        window.dispatchEvent(new CustomEvent('finance-pro-theme-changed', { detail: { value: theme, dark } }));
    }
    function setTheme(theme) { applyTheme(theme); }
    function toggleTheme() { setTheme(isDark() ? 'light' : 'dark'); }
    window.FinanceProTheme = { getTheme, applyTheme, setTheme, toggleTheme };
    applyTheme(getTheme());
    document.addEventListener('livewire:navigated', () => { applyTheme(getTheme()); requestAnimationFrame(() => applyTheme(getTheme())); });
    const observer = new MutationObserver(() => { const desiredDark = isDark(); if (document.documentElement.classList.contains('dark') !== desiredDark) document.documentElement.classList.toggle('dark', desiredDark); });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    const media = window.matchMedia(MEDIA_QUERY);
    media.addEventListener('change', () => { if (getTheme() === 'system') applyTheme('system'); });
    document.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) return;
        const label = button.textContent?.replace(/\s+/g, ' ').trim() || '';
        if (label !== 'Modo Claro' && label !== 'Modo Escuro') return;
        event.preventDefault(); event.stopImmediatePropagation(); toggleTheme();
    }, true);
    window.addEventListener('storage', (event) => { if (event.key === STORAGE_KEY || event.key === LEGACY_KEY) applyTheme(getTheme()); });
})();

window.addEventListener('copy-to-clipboard', (event) => {
    const text = event.detail.text;
    if (!text) return;
    if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(text); return; }
    const textArea = document.createElement('textarea');
    textArea.value = text; textArea.style.position = 'fixed'; textArea.style.left = '-9999px';
    document.body.appendChild(textArea); textArea.select(); document.execCommand('copy'); textArea.remove();
});

// Global business field formatting.
(function () {
    const digits = (v) => (v || '').replace(/\D/g, '');
    const groups = (v, sizes) => {
        const raw = digits(v).slice(0, sizes.reduce((a,b) => a+b, 0));
        const parts = []; let i = 0;
        for (const size of sizes) { if (i >= raw.length) break; parts.push(raw.slice(i, i + size)); i += size; }
        return parts.join(' ');
    };
    const apply = (input) => {
        if (!(input instanceof HTMLInputElement) || ['password','email','hidden','number'].includes(input.type)) return;
        const text = `${input.closest('div')?.innerText || ''} ${input.getAttribute('aria-label') || ''} ${input.getAttribute('placeholder') || ''}`.toLowerCase();
        let value = null;
        if (/\b(nif|vat|tax id|tax number)\b/.test(text)) value = groups(input.value,[3,3,3]);
        else if (/\b(iban)\b/.test(text)) value = groups(input.value,[4,4,4,4,4,3]);
        else if (/\b(telem[oó]vel|telefone|phone|contacto telef[oó]nico|mobile)\b/.test(text)) value = groups(input.value,[3,3,3]);
        else if (/\b(c[oó]digo postal|postal code|zip)\b/.test(text)) value = groups(input.value,[4,3]);
        if (value !== null && value !== input.value) { input.value = value; input.dispatchEvent(new Event('input',{bubbles:true})); }
    };
    document.addEventListener('input', e => apply(e.target), true);
    document.addEventListener('focusin', e => apply(e.target), true);
    document.addEventListener('livewire:navigated', () => document.querySelectorAll('input').forEach(apply));
})();

// Global fallback for business modal controls.
(function () {
    const closeModal = (button) => {
        const dialog = button.closest('[role="dialog"], dialog');
        if (!dialog) return false;
        const modalName = dialog.getAttribute('data-name') || dialog.getAttribute('data-modal-name') || dialog.id || '';
        if (modalName) window.dispatchEvent(new CustomEvent('modal-close', { detail: { name: modalName } }));
        dialog.dispatchEvent(new Event('close', { bubbles: true }));
        return true;
    };
    document.addEventListener('click', (event) => {
        const button = event.target.closest('button, [role="button"]');
        if (!button || button.type === 'submit' || button.disabled) return;
        const label = (button.getAttribute('aria-label') || button.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
        const isCloseIcon = label.includes('fechar') && !!button.querySelector('svg');
        const isCloseText = ['fechar', 'cancelar', 'descartar', 'close', 'cancel', 'discard'].includes(label);
        if ((!isCloseIcon && !isCloseText) || !button.closest('[role="dialog"], dialog')) return;
        event.preventDefault();
        closeModal(button);
    }, true);
})();
