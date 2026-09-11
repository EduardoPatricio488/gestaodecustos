import './push-notifications';
import './offline-expenses';

(function () {
    const STORAGE_KEY = 'flux.appearance';
    const LEGACY_KEY = 'theme';
    const SCHEDULE_KEY = 'finance-pro-theme-schedule';
    const MEDIA_QUERY = '(prefers-color-scheme: dark)';
    const DEFAULT_SCHEDULE = { light: '07:00', dark: '19:00' };

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

    function getSchedule() {
        try {
            const saved = JSON.parse(localStorage.getItem(SCHEDULE_KEY) || 'null');
            if (saved?.light && saved?.dark) return saved;
        } catch (_) {}
        return { ...DEFAULT_SCHEDULE };
    }

    function timeToMinutes(value) {
        const [hours, minutes] = String(value || '').split(':').map(Number);
        return Number.isFinite(hours) && Number.isFinite(minutes) ? (hours * 60) + minutes : 0;
    }

    function scheduledTheme() {
        const schedule = getSchedule();
        const now = new Date();
        const minutes = (now.getHours() * 60) + now.getMinutes();
        const light = timeToMinutes(schedule.light);
        const dark = timeToMinutes(schedule.dark);
        if (light < dark) return minutes >= light && minutes < dark ? 'light' : 'dark';
        return minutes >= dark && minutes < light ? 'dark' : 'light';
    }

    function isDark(theme = getTheme()) {
        if (theme === 'dark') return true;
        if (theme === 'light') return false;
        return scheduledTheme() === 'dark';
    }

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
    function setSchedule(light, dark) {
        const schedule = {
            light: /^([01]\d|2[0-3]):[0-5]\d$/.test(light) ? light : DEFAULT_SCHEDULE.light,
            dark: /^([01]\d|2[0-3]):[0-5]\d$/.test(dark) ? dark : DEFAULT_SCHEDULE.dark,
        };
        localStorage.setItem(SCHEDULE_KEY, JSON.stringify(schedule));
        applyTheme(getTheme());
        window.dispatchEvent(new CustomEvent('finance-pro-theme-schedule-changed', { detail: schedule }));
    }
    function toggleTheme() { setTheme(isDark() ? 'light' : 'dark'); }

    window.FinanceProTheme = { getTheme, applyTheme, setTheme, toggleTheme, getSchedule, setSchedule };
    applyTheme(getTheme());
    document.addEventListener('livewire:navigated', () => applyTheme(getTheme()));
    const media = window.matchMedia(MEDIA_QUERY);
    media.addEventListener('change', () => { if (getTheme() === 'system') applyTheme('system'); });
    setInterval(() => { if (getTheme() === 'system') applyTheme('system'); }, 30000);
    document.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) return;
        const label = button.textContent?.replace(/\s+/g, ' ').trim() || '';
        if (label !== 'Modo Claro' && label !== 'Modo Escuro') return;
        event.preventDefault(); event.stopImmediatePropagation(); toggleTheme();
    }, true);
    window.addEventListener('storage', (event) => {
        if (event.key === STORAGE_KEY || event.key === LEGACY_KEY || event.key === SCHEDULE_KEY) applyTheme(getTheme());
    });
})();

(function () {
    const protectedPaths = [/^\/trocar-espaco\/\d+$/, /^\/trocar-contexto\/\d+$/, /^\/sair-empresa$/, /^\/fitness\/strava\/disconnect$/];
    const isProtectedPath = (pathname) => protectedPaths.some((pattern) => pattern.test(pathname));
    const submitPost = (url) => {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) return false;
        const form = document.createElement('form');
        form.method = 'POST'; form.action = url.toString(); form.style.display = 'none';
        const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = token;
        form.appendChild(csrf); document.body.appendChild(form); form.submit(); return true;
    };
    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0) return;
        const anchor = event.target.closest('a[href]');
        if (!anchor || anchor.target === '_blank' || anchor.hasAttribute('download')) return;
        let url; try { url = new URL(anchor.href, window.location.origin); } catch { return; }
        if (url.origin !== window.location.origin || !isProtectedPath(url.pathname)) return;
        event.preventDefault(); event.stopImmediatePropagation(); submitPost(url);
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
        fieldTypes.set(input, type); return type;
    };
    const apply = (input) => {
        if (!(input instanceof HTMLInputElement) || ['password','email','hidden','number'].includes(input.type)) return;
        const sizes = getFieldType(input); if (!sizes) return;
        const value = groups(input.value, sizes);
        if (value !== input.value) { input.value = value; input.dispatchEvent(new Event('input',{bubbles:true})); }
    };
    document.addEventListener('input', e => apply(e.target), true);
    document.addEventListener('focusin', e => apply(e.target), true);
    document.addEventListener('livewire:navigated', () => document.querySelectorAll('input').forEach(apply));
})();

(function () {
    const closeModal = (button) => {
        const dialog = button.closest('[role="dialog"], dialog'); if (!dialog) return false;
        const modalName = dialog.getAttribute('data-modal') || dialog.getAttribute('data-name') || dialog.getAttribute('data-modal-name') || dialog.id || '';
        if (modalName) window.dispatchEvent(new CustomEvent('modal-close', { detail: { name: modalName } }));
        if (typeof dialog.close === 'function' && !dialog.hasAttribute('open')) return true;
        if (typeof dialog.close === 'function' && dialog.open) dialog.close(); return true;
    };
    document.addEventListener('click', (event) => {
        const button = event.target.closest('button, [role="button"]');
        if (!button || button.type === 'submit' || button.disabled) return;
        const label = (button.getAttribute('aria-label') || button.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
        const isCloseIcon = !!button.querySelector('svg') && /^(fechar|close|close modal|cancelar|descartar|cancel|discard)/.test(label);
        const isCloseText = ['fechar', 'cancelar', 'descartar', 'close', 'close modal', 'cancel', 'discard'].includes(label);
        if ((!isCloseIcon && !isCloseText) || !button.closest('[role="dialog"], dialog')) return;
        event.preventDefault(); closeModal(button);
    }, true);
})();

(function () {
    const normalize = (value) => String(value || '').replace(/\s+/g, ' ').trim();
    const addPortalEmailAction = (root = document) => {
        const dialogs = root.querySelectorAll?.('[role="dialog"], dialog') || [];
        dialogs.forEach((dialog) => {
            if (dialog.dataset.portalEmailReady === '1') return;
            if (!normalize(dialog.textContent).includes('Copiar Link de Login')) return;
            const copyButton = Array.from(dialog.querySelectorAll('button')).find((button) => normalize(button.textContent).includes('Copiar Link de Login'));
            if (!copyButton) return;
            const component = dialog.closest('[wire\\:id], [wire\\:id]') || document.querySelector('[wire\\:id]');
            if (!component) return;
            const emailButton = document.createElement('button');
            emailButton.type = 'button';
            emailButton.className = 'flex-[2] h-14 bg-brand-600 text-white rounded-2xl font-black uppercase text-xs shadow-xl shadow-brand-500/20 hover:bg-brand-700 transition-all flex items-center justify-center gap-2';
            emailButton.innerHTML = '<span aria-hidden="true">✉</span><span>Enviar Código por Email</span>';
            emailButton.addEventListener('click', () => {
                const wireId = component.getAttribute('wire:id'); if (!wireId || !window.Livewire) return;
                const livewireComponent = window.Livewire.find(wireId); if (!livewireComponent) return;
                emailButton.disabled = true; emailButton.classList.add('opacity-60', 'cursor-wait');
                Promise.resolve(livewireComponent.call('sendPortalEmail')).finally(() => {
                    emailButton.disabled = false; emailButton.classList.remove('opacity-60', 'cursor-wait');
                });
            });
            copyButton.parentElement?.insertBefore(emailButton, copyButton); dialog.dataset.portalEmailReady = '1';
        });
    };
    const observer = new MutationObserver(() => addPortalEmailAction());
    const start = () => { addPortalEmailAction(); if (document.body) observer.observe(document.body, { childList: true, subtree: true }); };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, { once: true }); else start();
    document.addEventListener('livewire:navigated', () => setTimeout(addPortalEmailAction, 50));
})();

// Show and manage pending public client portal access requests inside /empresa/clientes.
(function () {
    const normalize = (value) => String(value || '').replace(/\s+/g, ' ').trim();
    const findComponent = () => {
        const heading = Array.from(document.querySelectorAll('h1')).find((el) => normalize(el.textContent).toLowerCase().includes('gestão de clientes'));
        if (!heading || !window.Livewire) return null;
        const root = heading.closest('[wire\\:id]');
        if (!root) return null;
        const wireId = root.getAttribute('wire:id');
        return wireId ? window.Livewire.find(wireId) : null;
    };
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
    const callAction = (component, method, id, button) => {
        if (!component || !id || !window.Livewire || button?.disabled) return;
        if (button) { button.disabled = true; button.classList.add('opacity-60', 'cursor-wait'); }
        Promise.resolve(component.call(method, id)).then(() => refresh()).catch(() => {}).finally(() => {
            if (button) { button.disabled = false; button.classList.remove('opacity-60', 'cursor-wait'); }
        });
    };
    const render = (component, requests) => {
        let panel = document.getElementById('finance-pro-client-access-requests');
        if (!requests.length) { panel?.remove(); return; }
        if (!panel) {
            panel = document.createElement('section');
            panel.id = 'finance-pro-client-access-requests';
            panel.className = 'mb-8 rounded-[2rem] border border-brand-200 dark:border-brand-900/50 bg-brand-50/70 dark:bg-brand-950/20 shadow-sm overflow-hidden';
            const heading = Array.from(document.querySelectorAll('h1')).find((el) => normalize(el.textContent).toLowerCase().includes('gestão de clientes'));
            const header = heading?.closest('.relative')?.parentElement || heading?.closest('.relative') || heading?.parentElement;
            if (header?.parentElement) header.parentElement.insertBefore(panel, header.nextSibling);
        }
        panel.innerHTML = `
            <div class="px-6 py-5 border-b border-brand-200/70 dark:border-brand-900/40">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full bg-brand-600 text-white text-[10px] font-black">${requests.length}</span>
                    <h2 class="text-sm font-black uppercase tracking-widest text-brand-700 dark:text-brand-300">Pedidos de acesso ao portal</h2>
                </div>
                <p class="text-xs text-brand-700/70 dark:text-brand-300/70 mt-1">Clientes que solicitaram acesso através do portal público.</p>
            </div>
            <div class="divide-y divide-brand-200/70 dark:divide-brand-900/40">
                ${requests.map((request) => `
                    <div class="px-6 py-4 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4" data-access-request-id="${escapeHtml(request.id)}">
                        <div class="min-w-0">
                            <p class="font-black text-sm text-zinc-900 dark:text-white">${escapeHtml(request.name)}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">${escapeHtml(request.email)}${request.tax_number ? ` · NIF ${escapeHtml(request.tax_number)}` : ''}</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mt-2">Pedido em ${escapeHtml(request.requested_at || '')}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" data-request-action="approve" data-request-id="${escapeHtml(request.id)}" class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-brand-600 text-white text-[10px] font-black uppercase tracking-wider hover:bg-brand-700 transition">Aprovar e enviar acesso</button>
                            <button type="button" data-request-action="reject" data-request-id="${escapeHtml(request.id)}" class="inline-flex items-center justify-center h-10 px-4 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 text-[10px] font-black uppercase tracking-wider hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">Rejeitar</button>
                        </div>
                    </div>
                `).join('')}
            </div>`;
        panel.querySelectorAll('[data-request-action]').forEach((button) => {
            button.addEventListener('click', () => {
                const method = button.dataset.requestAction === 'approve' ? 'approveAccessRequest' : 'rejectAccessRequest';
                callAction(component, method, button.dataset.requestId, button);
            });
        });
    };
    const refresh = () => {
        const component = findComponent();
        if (!component) return;
        Promise.resolve(component.call('getPendingAccessRequests'))
            .then((requests) => render(component, Array.isArray(requests) ? requests : []))
            .catch(() => {});
    };
    let timer = null;
    const schedule = () => { clearTimeout(timer); timer = setTimeout(refresh, 200); };
    document.addEventListener('livewire:navigated', schedule);
    document.addEventListener('livewire:initialized', schedule, { once: true });
    document.addEventListener('client-access-request-updated', schedule);
    schedule();
})();

// Show and manage pending public supplier portal access requests inside /empresa/fornecedores.
(function () {
    const normalize = (value) => String(value || '').replace(/\s+/g, ' ').trim();
    const findComponent = () => {
        const heading = Array.from(document.querySelectorAll('h1')).find((el) => normalize(el.textContent).toLowerCase().includes('fornecedores & parceiros'));
        if (!heading || !window.Livewire) return null;
        const root = heading.closest('[wire\\:id]');
        if (!root) return null;
        const wireId = root.getAttribute('wire:id');
        return wireId ? window.Livewire.find(wireId) : null;
    };
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
    const refresh = () => {
        const component = findComponent();
        if (!component) return;
        Promise.resolve(component.call('getPendingAccessRequests'))
            .then((requests) => render(component, Array.isArray(requests) ? requests : []))
            .catch(() => {});
    };
    const callAction = (component, method, id, button) => {
        if (!component || !id || !window.Livewire || button?.disabled) return;
        button.disabled = true;
        button.classList.add('opacity-60', 'cursor-wait');
        Promise.resolve(component.call(method, id))
            .then(() => refresh())
            .catch(() => {})
            .finally(() => {
                button.disabled = false;
                button.classList.remove('opacity-60', 'cursor-wait');
            });
    };
    const render = (component, requests) => {
        let panel = document.getElementById('finance-pro-supplier-access-requests');
        if (!requests.length) { panel?.remove(); return; }
        if (!panel) {
            panel = document.createElement('section');
            panel.id = 'finance-pro-supplier-access-requests';
            panel.className = 'mb-8 rounded-[2rem] border border-brand-200 dark:border-brand-900/50 bg-brand-50/70 dark:bg-brand-950/20 shadow-sm overflow-hidden';
            const heading = Array.from(document.querySelectorAll('h1')).find((el) => normalize(el.textContent).toLowerCase().includes('fornecedores & parceiros'));
            const header = heading?.closest('.relative')?.parentElement || heading?.closest('.relative') || heading?.parentElement;
            if (header?.parentElement) header.parentElement.insertBefore(panel, header.nextSibling);
        }
        panel.innerHTML = `
            <div class="px-6 py-5 border-b border-brand-200/70 dark:border-brand-900/40">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full bg-brand-600 text-white text-[10px] font-black">${requests.length}</span>
                    <h2 class="text-sm font-black uppercase tracking-widest text-brand-700 dark:text-brand-300">Pedidos de acesso ao portal</h2>
                </div>
                <p class="text-xs text-brand-700/70 dark:text-brand-300/70 mt-1">Fornecedores que solicitaram acesso através do portal público.</p>
            </div>
            <div class="divide-y divide-brand-200/70 dark:divide-brand-900/40">
                ${requests.map((request) => `
                    <div class="px-6 py-4 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-black text-sm text-zinc-900 dark:text-white">${escapeHtml(request.name)}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">${escapeHtml(request.email)}${request.tax_number ? ` · NIF ${escapeHtml(request.tax_number)}` : ''}</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 mt-2">Pedido em ${escapeHtml(request.requested_at || '')}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" data-supplier-request-action="approve" data-supplier-request-id="${escapeHtml(request.id)}" class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-brand-600 text-white text-[10px] font-black uppercase tracking-wider hover:bg-brand-700 transition">Aprovar e enviar acesso</button>
                            <button type="button" data-supplier-request-action="reject" data-supplier-request-id="${escapeHtml(request.id)}" class="inline-flex items-center justify-center h-10 px-4 rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 text-[10px] font-black uppercase tracking-wider hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">Rejeitar</button>
                        </div>
                    </div>
                `).join('')}
            </div>`;
        panel.querySelectorAll('[data-supplier-request-action]').forEach((button) => {
            button.addEventListener('click', () => {
                const method = button.dataset.supplierRequestAction === 'approve' ? 'approveAccessRequest' : 'rejectAccessRequest';
                callAction(component, method, button.dataset.supplierRequestId, button);
            });
        });
    };
    let timer = null;
    const schedule = () => { clearTimeout(timer); timer = setTimeout(refresh, 200); };
    document.addEventListener('livewire:navigated', schedule);
    document.addEventListener('livewire:initialized', schedule, { once: true });
    document.addEventListener('supplier-access-request-updated', schedule);
    schedule();
})();\n\n// Global modal audit: one working close X, preserve every functional action.\n(function () {\n    const MODAL_SELECTOR = '[role="dialog"], dialog';\n    const CLOSE_LABELS = /^(fechar|close|close modal|cancelar|cancel|descartar|discard|x|×)$/i;\n\n    const visible = (el) => {\n        if (!el) return false;\n        const style = window.getComputedStyle(el);\n        return style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0';\n    };\n\n    const isCloseButton = (button) => {\n        const label = (button.getAttribute('aria-label') || button.getAttribute('title') || button.textContent || '')\n            .replace(/\\s+/g, ' ').trim();\n        return CLOSE_LABELS.test(label);\n    };\n\n    const closeModal = (modal) => {\n        if (!modal) return;\n        const alpineRoot = modal.querySelector('[x-data]') || modal;\n        if (alpineRoot._x_dataStack?.length) {\n            const data = Alpine.$data(alpineRoot);\n            if (data && 'show' in data) data.show = false;\n        }\n        modal.dispatchEvent(new CustomEvent('close', { bubbles: true }));\n        const name = modal.getAttribute('data-modal') || modal.getAttribute('data-name') || modal.id;\n        if (name) {\n            window.dispatchEvent(new CustomEvent('close-modal', { detail: name }));\n            window.dispatchEvent(new CustomEvent('modal-close', { detail: { name } }));\n        }\n        if (modal instanceof HTMLDialogElement && modal.open) modal.close();\n    };\n\n    const normalizeModal = (modal) => {\n        if (!(modal instanceof HTMLElement) || !visible(modal)) return;\n        if (modal.dataset.financeModalReady !== '1') {\n            modal.dataset.financeModalReady = '1';\n\n            const buttons = Array.from(modal.querySelectorAll('button, [role="button"]'));\n            const closeButtons = buttons.filter(isCloseButton);\n            const canonical = closeButtons.find((button) => /^(fechar|close|close modal)$/i.test((button.getAttribute('aria-label') || '').trim())) || closeButtons[0];\n\n            closeButtons.forEach((button) => {\n                if (button !== canonical) {\n                    button.setAttribute('data-finance-modal-secondary-close', '1');\n                    button.style.display = 'none';\n                }\n            });\n\n            if (!canonical) {\n                const host = modal.querySelector(':scope > div, .relative') || modal;\n                const close = document.createElement('button');\n                close.type = 'button';\n                close.setAttribute('aria-label', 'Fechar modal');\n                close.setAttribute('title', 'Fechar');\n                close.className = 'finance-pro-modal-close absolute left-4 top-4 z-[999] inline-flex h-9 w-9 items-center justify-center rounded-full bg-zinc-100 text-zinc-700 shadow-sm transition hover:bg-zinc-200 hover:text-zinc-950 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700';\n                close.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/></svg>';\n                close.addEventListener('click', (event) => { event.preventDefault(); event.stopPropagation(); closeModal(modal); });\n                if (window.getComputedStyle(host).position === 'static') host.style.position = 'relative';\n                host.prepend(close);\n            }\n        }\n    };\n\n    document.addEventListener('click', (event) => {\n        const button = event.target.closest('button, [role="button"]');\n        if (!button || button.disabled) return;\n        const modal = button.closest(MODAL_SELECTOR);\n        if (!modal || !isCloseButton(button)) return;\n        event.preventDefault();\n        event.stopImmediatePropagation();\n        closeModal(modal);\n    }, true);\n\n    const scan = () => document.querySelectorAll(MODAL_SELECTOR).forEach(normalizeModal);\n    const observer = new MutationObserver(scan);\n    const start = () => {\n        scan();\n        if (document.body) observer.observe(document.body, { childList: true, subtree: true });\n    };\n    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, { once: true });\n    else start();\n    document.addEventListener('livewire:navigated', () => setTimeout(scan, 50));\n})();\n