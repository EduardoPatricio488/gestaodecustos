import './push-notifications';
import './offline-expenses';

/**
 * Finance Pro theme manager.
 * Keeps the sidebar toggle, profile selector and Flux in sync.
 */
(function () {
    const STORAGE_KEY = 'flux.appearance';
    const LEGACY_KEY = 'theme';

    function getTheme() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved === 'dark' || saved === 'light' || saved === 'system') {
            return saved;
        }

        const legacy = localStorage.getItem(LEGACY_KEY);
        if (legacy === 'dark' || legacy === 'light') {
            localStorage.setItem(STORAGE_KEY, legacy);
            localStorage.removeItem(LEGACY_KEY);
            return legacy;
        }

        localStorage.setItem(STORAGE_KEY, 'system');
        localStorage.removeItem(LEGACY_KEY);
        return 'system';
    }

    function isDark(theme) {
        return theme === 'dark' || (
            theme === 'system' &&
            window.matchMedia('(prefers-color-scheme: dark)').matches
        );
    }

    function applyTheme(theme) {
        const dark = isDark(theme);

        document.documentElement.classList.toggle('dark', dark);
        document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
        localStorage.setItem(STORAGE_KEY, theme);
        localStorage.removeItem(LEGACY_KEY);

        window.dispatchEvent(new CustomEvent('finance-pro-theme-changed', {
            detail: { value: theme, dark },
        }));
    }

    function setTheme(theme) {
        if (!['dark', 'light', 'system'].includes(theme)) {
            theme = 'system';
        }

        document.documentElement.classList.add('theme-switching');
        applyTheme(theme);

        if (window.Flux?.applyAppearance) {
            // Flux receives the same source of truth; the DOM is already updated.
            window.Flux.applyAppearance(theme);
        }

        window.setTimeout(() => {
            document.documentElement.classList.remove('theme-switching');
        }, 200);
    }

    function toggleTheme() {
        setTheme(isDark(getTheme()) ? 'light' : 'dark');
    }

    window.FinanceProTheme = {
        getTheme,
        applyTheme,
        setTheme,
        toggleTheme,
    };

    // Apply immediately on every full page load.
    applyTheme(getTheme());

    // Follow the OS only when the user selected Automatic.
    const media = window.matchMedia('(prefers-color-scheme: dark)');
    media.addEventListener('change', () => {
        if (getTheme() === 'system') {
            applyTheme('system');
        }
    });

    // Fix the existing sidebar toggle without requiring a layout rewrite.
    // Its Alpine state can become stale after Livewire navigation; this makes
    // the actual theme and the button behaviour use the same source of truth.
    document.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) return;

        const label = button.textContent?.replace(/\s+/g, ' ').trim() || '';
        if (label !== 'Modo Claro' && label !== 'Modo Escuro') return;

        event.preventDefault();
        event.stopPropagation();
        setTheme(isDark(getTheme()) ? 'light' : 'dark');
    }, true);
})();

window.addEventListener('copy-to-clipboard', (event) => {
    const text = event.detail.text;

    if (!text) {
        return;
    }

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text);
        return;
    }

    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    textArea.remove();
});
