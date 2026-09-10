import './push-notifications';
import './offline-expenses';

/**
 * Finance Pro theme manager.
 * Keeps the sidebar toggle, profile selector and page navigation in sync.
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

        localStorage.setItem(STORAGE_KEY, 'system');
        localStorage.removeItem(LEGACY_KEY);
        return 'system';
    }

    function isDark(theme) {
        return theme === 'dark' || (
            theme === 'system' &&
            window.matchMedia(MEDIA_QUERY).matches
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

        applyTheme(theme);
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

    // Livewire SPA navigation replaces page content without reloading app.js.
    // Re-apply the persisted theme after every navigation so the new page
    // cannot revert to light mode.
    document.addEventListener('livewire:navigated', () => {
        applyTheme(getTheme());
    });

    // Some Livewire/Flux initialisation can run just after navigation.
    // Run once more on the next frame as a final synchronisation point.
    document.addEventListener('livewire:navigated', () => {
        requestAnimationFrame(() => applyTheme(getTheme()));
    });

    // Follow the OS only when the user selected Automatic.
    const media = window.matchMedia(MEDIA_QUERY);
    media.addEventListener('change', () => {
        if (getTheme() === 'system') {
            applyTheme('system');
        }
    });

    // Keep the existing sidebar button working even if its Alpine handler
    // still exists in an older cached layout. Use the same persistent source.
    document.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) return;

        const label = button.textContent?.replace(/\s+/g, ' ').trim() || '';
        if (label !== 'Modo Claro' && label !== 'Modo Escuro') return;

        event.preventDefault();
        event.stopPropagation();
        toggleTheme();
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
