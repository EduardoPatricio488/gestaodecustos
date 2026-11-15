<script>
    (() => {
        if (window.FinanceProTheme) {
            window.FinanceProTheme.applyTheme(window.FinanceProTheme.getTheme());
            return;
        }

        const storageKey = 'flux.appearance';
        const legacyKeys = ['finance-pro-theme', 'finance-pro-mobile-theme', 'theme'];
        const scheduleKey = 'finance-pro-theme-schedule';
        const defaultSchedule = { light: '07:00', dark: '19:00' };

        function getTheme() {
            const saved = localStorage.getItem(storageKey);
            if (['light', 'dark', 'system'].includes(saved)) return saved;
            for (const key of legacyKeys) {
                const legacy = localStorage.getItem(key);
                if (legacy === 'light' || legacy === 'dark') return legacy;
            }
            return 'system';
        }

        function getSchedule() {
            try {
                const saved = JSON.parse(localStorage.getItem(scheduleKey) || 'null');
                if (saved?.light && saved?.dark) return saved;
            } catch (error) {
                console.warn('Não foi possível ler o horário do tema.', error);
            }
            return { ...defaultSchedule };
        }

        function timeToMinutes(value) {
            const [hours, minutes] = String(value || '').split(':').map(Number);
            return Number.isFinite(hours) && Number.isFinite(minutes) ? (hours * 60) + minutes : 0;
        }

        function isDark(theme = getTheme()) {
            if (theme === 'dark') return true;
            if (theme === 'light') return false;
            const schedule = getSchedule();
            const now = new Date();
            const minutes = (now.getHours() * 60) + now.getMinutes();
            const light = timeToMinutes(schedule.light);
            const dark = timeToMinutes(schedule.dark);
            return light < dark ? !(minutes >= light && minutes < dark) : minutes >= dark && minutes < light;
        }

        function applyTheme(theme) {
            if (!['light', 'dark', 'system'].includes(theme)) theme = 'system';
            localStorage.setItem(storageKey, theme);
            legacyKeys.forEach((key) => localStorage.removeItem(key));
            const dark = isDark(theme);
            document.documentElement.classList.toggle('dark', dark);
            document.documentElement.dataset.theme = dark ? 'dark' : 'light';
            document.documentElement.dataset.lockinTheme = dark ? 'dark' : 'light';
            document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
            if (window.Flux && 'appearance' in window.Flux && window.Flux.appearance !== theme) {
                window.Flux.appearance = theme;
            }
            window.dispatchEvent(new CustomEvent('finance-pro-theme-changed', { detail: { value: theme, dark } }));
        }

        function setSchedule(light, dark) {
            const schedule = {
                light: /^([01]\d|2[0-3]):[0-5]\d$/.test(light) ? light : defaultSchedule.light,
                dark: /^([01]\d|2[0-3]):[0-5]\d$/.test(dark) ? dark : defaultSchedule.dark,
            };
            localStorage.setItem(scheduleKey, JSON.stringify(schedule));
            applyTheme(getTheme());
            window.dispatchEvent(new CustomEvent('finance-pro-theme-schedule-changed', { detail: schedule }));
        }

        function toggleTheme() { applyTheme(isDark() ? 'light' : 'dark'); }

        window.FinanceProTheme = { getTheme, applyTheme, setTheme: applyTheme, toggleTheme, getSchedule, setSchedule };
        window.Flux = window.Flux || {};
        // Flux's default "system" mode uses the OS preference and removes the saved value.
        if (!('appearance' in window.Flux)) window.Flux.applyAppearance = applyTheme;
        applyTheme(getTheme());
        document.addEventListener('livewire:navigated', () => applyTheme(getTheme()));
        window.addEventListener('pageshow', () => applyTheme(getTheme()));
        window.addEventListener('storage', (event) => {
            if (event.key === storageKey || event.key === scheduleKey || event.key === null) applyTheme(getTheme());
        });
        setInterval(() => { if (getTheme() === 'system') applyTheme('system'); }, 30000);
    })();
</script>
