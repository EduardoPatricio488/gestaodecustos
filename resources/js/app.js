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
    document.addEventListener('livewire:navigated', () => applyTheme(getTheme()));
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

// State-changing navigation must use POST + CSRF, even when the UI is rendered as a link.
(function () {
    const protectedPaths = [
        /^\/trocar-espaco\/\d+$/,
        /^\/trocar-contexto\/\d+$/,
        /^\/sair-empresa$/,
        /^\/fitness\/strava\/disconnect$/,
    ];

    const isProtectedPath = (pathname) => protectedPaths.some((pattern) => pattern.test(pathname));

    const submitPost = (url) => {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            console.error('Finance Pro: token CSRF não encontrado.');
            return false;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url.toString();
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = token;
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
        return true;
    };

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0) return;

        const anchor = event.target.closest('a[href]');
        if (!anchor || anchor.target === '_blank' || anchor.hasAttribute('download')) return;

        let url;
        try {
            url = new URL(anchor.href, window.location.origin);
        } catch {
            return;
        }

        if (url.origin !== window.location.origin || !isProtectedPath(url.pathname)) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        submitPost(url);
    }, true);
})();

window.addEventListener('copy-to-clipboard', (event) => {
    const text = event.detail.text;
    if (!text) return;
    if (navigator.clipboard && window.isSecureContext) { navigator.clipboard.writeText(text); return; }
    const textArea = document.createElement('textarea');
    textArea.value = text; textArea.style.position = 'fixed'; textArea.style.left = '-9999px';
    document.body.appendChild(textArea); textArea.select(); document.execCommand('copy'); textArea.remove();
});

// Global business field formatting. Field classification is cached per input so
// typing does not repeatedly traverse the DOM and read innerText on every keystroke.
(function () {
    const digits = (v) => (v || '').replace(/\D/g, '');
    const groups = (v, sizes) => {
        const raw = digits(v).slice(0, sizes.reduce((a,b) => a+b, 0));
        const parts = []; let i = 0;
        for (const size of sizes) { if (i >= raw.length) break; parts.push(raw.slice(i, i + size)); i += size; }
        return parts.join(' ');
    };
    const fieldTypes = new WeakMap();
    const getFieldType = (input) => {
        if (fieldTypes.has(input)) return fieldTypes.get(input);
        const text = `${input.getAttribute('aria-label') || ''} ${input.getAttribute('placeholder') || ''} ${input.name || ''}`.toLowerCase();
        let type = null;
        if (/\b(nif|vat|tax id|tax number)\b/.test(text)) type = [3,3,3];
        else if (/\b(iban)\b/.test(text)) type = [4,4,4,4,4,3];
        else if (/\b(telem[oó]vel|telefone|phone|contacto telef[oó]nico|mobile)\b/.test(text)) type = [3,3,3];
        else if (/\b(c[oó]digo postal|postal code|zip)\b/.test(text)) type = [4,3];
        fieldTypes.set(input, type);
        return type;
    };
    const apply = (input) => {
        if (!(input instanceof HTMLInputElement) || ['password','email','hidden','number'].includes(input.type)) return;
        const sizes = getFieldType(input);
        if (!sizes) return;
        const value = groups(input.value, sizes);
        if (value !== input.value) { input.value = value; input.dispatchEvent(new Event('input',{bubbles:true})); }
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
        const modalName = dialog.getAttribute('data-modal') || dialog.getAttribute('data-name') || dialog.getAttribute('data-modal-name') || dialog.id || '';
        if (modalName) window.dispatchEvent(new CustomEvent('modal-close', { detail: { name: modalName } }));
        if (typeof dialog.close === 'function' && !dialog.hasAttribute('open')) return true;
        if (typeof dialog.close === 'function' && dialog.open) dialog.close();
        return true;
    };
    document.addEventListener('click', (event) => {
        const button = event.target.closest('button, [role="button"]');
        if (!button || button.type === 'submit' || button.disabled) return;
        const label = (button.getAttribute('aria-label') || button.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
        const isCloseIcon = !!button.querySelector('svg') && /^(fechar|close|close modal|cancelar|descartar|cancel|discard)/.test(label);
        const isCloseText = ['fechar', 'cancelar', 'descartar', 'close', 'close modal', 'cancel', 'discard'].includes(label);
        if ((!isCloseIcon && !isCloseText) || !button.closest('[role="dialog"], dialog')) return;
        event.preventDefault();
        closeModal(button);
    }, true);
})();
