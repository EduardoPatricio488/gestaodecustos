import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const source = readFileSync(new URL('../../resources/views/partials/theme.blade.php', import.meta.url), 'utf8')
    .replace(/^<script>\s*/, '').replace(/\s*<\/script>\s*$/, '');

function openPage(storage, hour = 12) {
    const listeners = {};
    const classes = new Set();
    const root = {
        classList: {
            toggle(name, enabled) { enabled ? classes.add(name) : classes.delete(name); },
            contains(name) { return classes.has(name); },
        },
        dataset: {},
        style: {},
    };
    const localStorage = {
        getItem(key) { return storage.get(key) ?? null; },
        setItem(key, value) { storage.set(key, value); },
        removeItem(key) { storage.delete(key); },
    };
    const window = {
        addEventListener(name, callback) { (listeners[name] ??= []).push(callback); },
        dispatchEvent(event) { (listeners[event.type] ?? []).forEach((callback) => callback(event)); },
    };
    const document = { documentElement: root, addEventListener() {} };
    class Clock extends Date {
        getHours() { return hour; }
        getMinutes() { return 0; }
    }
    class CustomEvent {
        constructor(type, options) { this.type = type; this.detail = options.detail; }
    }
    vm.runInNewContext(source, { window, document, localStorage, Date: Clock, CustomEvent, setInterval() {}, console });
    return { window, root };
}

test('a escolha da entrada mantém-se nas páginas seguintes e pode ser alterada', () => {
    const storage = new Map([['finance-pro-theme', 'light']]);
    const launch = openPage(storage);
    assert.equal(launch.root.classList.contains('dark'), false);
    launch.window.FinanceProTheme.toggleTheme();
    const site = openPage(storage);
    assert.equal(site.root.classList.contains('dark'), true);
    assert.equal(storage.get('flux.appearance'), 'dark');
    assert.equal(storage.has('finance-pro-theme'), false);
    site.window.FinanceProTheme.setTheme('light');
    assert.equal(openPage(storage).root.classList.contains('dark'), false);
});

test('a preferência atual prevalece sobre chaves antigas', () => {
    const storage = new Map([['flux.appearance', 'light'], ['finance-pro-theme', 'dark']]);
    const page = openPage(storage);
    assert.equal(page.root.classList.contains('dark'), false);
    assert.equal(storage.get('flux.appearance'), 'light');
});

test('o modo automático e o horário persistem entre páginas', () => {
    const storage = new Map([
        ['flux.appearance', 'system'],
        ['finance-pro-theme', 'light'],
        ['finance-pro-theme-schedule', JSON.stringify({ light: '08:00', dark: '18:00' })],
    ]);
    assert.equal(openPage(storage, 12).root.classList.contains('dark'), false);
    const night = openPage(storage, 20);
    assert.equal(night.root.classList.contains('dark'), true);
    assert.equal(storage.get('flux.appearance'), 'system');
    night.window.FinanceProTheme.toggleTheme();
    assert.equal(storage.get('flux.appearance'), 'light');
});

test('o Flux respeita o modo automático por horário', () => {
    const storage = new Map([
        ['flux.appearance', 'system'],
        ['finance-pro-theme-schedule', JSON.stringify({ light: '22:00', dark: '06:00' })],
    ]);
    const page = openPage(storage, 12);
    page.window.Flux.applyAppearance('system');
    assert.equal(storage.get('flux.appearance'), 'system');
    assert.equal(page.root.classList.contains('dark'), true);
    assert.equal(openPage(storage, 23).root.classList.contains('dark'), false);
});

test('alterações noutra página atualizam o estado reativo do Flux', () => {
    const storage = new Map([['flux.appearance', 'dark']]);
    const page = openPage(storage);
    page.window.Flux = { appearance: 'dark' };
    storage.set('flux.appearance', 'light');
    page.window.dispatchEvent({ type: 'storage', key: 'flux.appearance' });
    assert.equal(page.root.classList.contains('dark'), false);
    assert.equal(page.window.Flux.appearance, 'light');
});
