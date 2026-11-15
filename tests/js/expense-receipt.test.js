import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const view = readFileSync(new URL('../../resources/views/livewire/expenses.blade.php', import.meta.url), 'utf8');
const start = view.lastIndexOf('x-data="{', view.indexOf('previewUrl: window.__financeProInvoicePreviewUrl'));
const end = view.indexOf('}"\n                        class=', start);
const expression = view.slice(start + 'x-data="'.length, end + 1);

function receiptComponent({ bitmap, toBlob } = {}) {
    let callbacks;
    let nextTimer = 0;
    const timers = new Map();
    const wire = {
        scannerReceipt: null,
        upload(name, file, success, failure, progress) {
            callbacks = { name, file, success, failure, progress };
        },
    };
    const parent = {
        receiptPreparing: false,
        receiptUploading: false,
        receiptReady: false,
        get receiptBusy() { return this.receiptPreparing || this.receiptUploading; },
        $wire: wire,
    };
    const window = {};
    const context = {
        window,
        URL: { createObjectURL: () => 'blob:preview', revokeObjectURL() {} },
        createImageBitmap: bitmap ?? (() => { throw new Error('unexpected compression'); }),
        document: {
            createElement: () => ({
                getContext: () => ({ fillRect() {}, drawImage() {} }),
                toBlob: toBlob ?? ((callback) => callback({ size: 200 })),
            }),
        },
        setTimeout(callback, delay) {
            const id = ++nextTimer;
            timers.set(id, { callback, delay });
            return id;
        },
        clearTimeout(id) { timers.delete(id); },
        File: class { constructor(parts, name, options) { this.name = name; this.type = options.type; } },
        console: { warn() {}, error() {} },
    };
    const component = vm.runInNewContext(`(${expression})`, context);
    Object.setPrototypeOf(component, parent);
    return {
        component,
        wire,
        callbacks: () => callbacks,
        fireTimer(delay) {
            const [id, timer] = [...timers].find(([, value]) => value.delay === delay);
            timers.delete(id);
            timer.callback();
        },
    };
}

test('small photos are previewed and uploaded without compression', async () => {
    const { component, wire, callbacks } = receiptComponent();
    const file = { name: 'fatura.jpg', type: 'image/jpeg', size: 600 * 1024 };
    await component.prepareAndUpload({ target: { files: [file], value: 'file' } });
    assert.equal(component.previewUrl, 'blob:preview');
    assert.equal(component.receiptPreparing, false);
    assert.equal(component.receiptUploading, true);
    assert.equal(callbacks().file, file);
    callbacks().progress({ detail: { progress: 55 } });
    assert.equal(component.uploadProgress, 55);
    wire.scannerReceipt = 'uploaded';
    callbacks().success();
    assert.equal(component.receiptBusy, false);
    assert.equal(component.receiptReady, true);
});

test('a large photo shows preparation immediately and then upload progress', async () => {
    let finishBitmap;
    const bitmap = new Promise((resolve) => { finishBitmap = resolve; });
    const { component, callbacks } = receiptComponent({ bitmap: () => bitmap });
    const processing = component.prepareAndUpload({
        target: { files: [{ name: 'foto.png', type: 'image/png', size: 4 * 1024 * 1024 }], value: 'file' },
    });
    assert.equal(component.previewUrl, 'blob:preview');
    assert.equal(component.receiptPreparing, true);
    finishBitmap({ width: 4000, height: 3000, close() {} });
    await processing;
    assert.equal(component.receiptPreparing, false);
    assert.equal(component.receiptUploading, true);
    assert.equal(callbacks().file.type, 'image/jpeg');
});

test('a photo that cannot be compressed is uploaded in its original form', async () => {
    const { component, callbacks } = receiptComponent({ bitmap: async () => { throw new Error('unsupported'); } });
    const file = { name: 'fatura.png', type: 'image/png', size: 2 * 1024 * 1024 };
    await component.prepareAndUpload({ target: { files: [file], value: 'file' } });
    assert.equal(callbacks().file, file);
    assert.equal(component.receiptUploading, true);
});

test('a stalled image decoder falls back to the original and releases a late bitmap', async () => {
    let finishBitmap;
    let closed = false;
    const bitmap = new Promise((resolve) => { finishBitmap = resolve; });
    const { component, callbacks, fireTimer } = receiptComponent({ bitmap: () => bitmap });
    const file = { name: 'fatura.png', type: 'image/png', size: 2 * 1024 * 1024 };
    const processing = component.prepareAndUpload({ target: { files: [file], value: 'file' } });
    fireTimer(5000);
    await processing;
    assert.equal(callbacks().file, file);
    assert.equal(component.receiptUploading, true);
    finishBitmap({ close() { closed = true; } });
    await Promise.resolve();
    assert.equal(closed, true);
});

test('a stalled image conversion falls back to the original', async () => {
    let closed = false;
    let converting = false;
    const { component, callbacks, fireTimer } = receiptComponent({
        bitmap: async () => ({ width: 100, height: 100, close() { closed = true; } }),
        toBlob() { converting = true; },
    });
    const file = { name: 'fatura.png', type: 'image/png', size: 2 * 1024 * 1024 };
    const processing = component.prepareAndUpload({ target: { files: [file], value: 'file' } });
    for (let i = 0; i < 10 && !converting; i++) await Promise.resolve();
    assert.equal(converting, true);
    fireTimer(5000);
    await processing;
    assert.equal(callbacks().file, file);
    assert.equal(closed, true);
});

test('an oversized original that cannot be compressed is rejected before upload', async () => {
    const { component, callbacks } = receiptComponent({ bitmap: async () => { throw new Error('unsupported'); } });
    const input = {
        files: [{ name: 'fatura.png', type: 'image/png', size: 21 * 1024 * 1024 }],
        value: 'file',
    };
    await component.prepareAndUpload({ target: input });
    assert.equal(callbacks(), undefined);
    assert.equal(component.receiptBusy, false);
    assert.match(component.uploadError, /até 20 MB/);
    assert.equal(input.value, '');
});

test('cancel and category navigation cannot close or change the modal while uploading', () => {
    const cancel = view.match(/x-on:click="(if \(!receiptBusy\) open = false)"/)[1];
    const category = view.match(/x-on:click="(if \(!receiptBusy\) activeTab = 'category')"/)[1];
    const state = { receiptBusy: true, open: true, activeTab: 'scanner' };
    vm.runInNewContext(cancel, state);
    vm.runInNewContext(category, state);
    assert.equal(state.open, true);
    assert.equal(state.activeTab, 'scanner');
    state.receiptBusy = false;
    vm.runInNewContext(category, state);
    vm.runInNewContext(cancel, state);
    assert.equal(state.activeTab, 'category');
    assert.equal(state.open, false);
});

test('navigating away while an upload is busy requests browser confirmation', () => {
    const handler = view.match(/x-on:beforeunload\.window="([^"]+)"/)[1];
    let prevented = false;
    const event = { preventDefault() { prevented = true; }, returnValue: true };
    vm.runInNewContext(handler, { receiptBusy: true, $event: event });
    assert.equal(prevented, true);
    assert.equal(event.returnValue, '');
    prevented = false;
    vm.runInNewContext(handler, { receiptBusy: false, $event: event });
    assert.equal(prevented, false);
});

test('slow uploads show guidance and clear it when the upload finishes', async () => {
    const { component, wire, callbacks, fireTimer } = receiptComponent();
    await component.prepareAndUpload({
        target: { files: [{ name: 'fatura.pdf', type: 'application/pdf', size: 100 }], value: 'file' },
    });
    fireTimer(30000);
    assert.equal(component.uploadSlow, true);
    wire.scannerReceipt = 'uploaded';
    callbacks().success();
    assert.equal(component.uploadSlow, false);
    assert.equal(component.receiptReady, true);
});

test('a completed upload without a validated receipt reports an error', async () => {
    const { component, callbacks } = receiptComponent();
    await component.prepareAndUpload({
        target: { files: [{ name: 'fatura.pdf', type: 'application/pdf', size: 100 }], value: 'file' },
    });
    callbacks().success();
    assert.equal(component.receiptReady, false);
    assert.match(component.uploadError, /Não foi possível confirmar/);
});

test('upload failures do not mark a previous file as ready', async () => {
    const { component, wire, callbacks } = receiptComponent();
    wire.scannerReceipt = 'old file';
    component.receiptReady = true;
    await component.prepareAndUpload({
        target: { files: [{ name: 'fatura.pdf', type: 'application/pdf', size: 100 }], value: 'file' },
    });
    assert.equal(component.receiptReady, false);
    callbacks().failure();
    assert.equal(component.receiptBusy, false);
    assert.equal(component.receiptReady, false);
    assert.match(component.uploadError, /Não foi possível enviar/);
});

test('pull-to-refresh does not reload the mobile page while the expense modal is open', () => {
    const mobile = readFileSync(new URL('../../resources/views/mobile.blade.php', import.meta.url), 'utf8');
    const start = mobile.lastIndexOf('(() => {', mobile.indexOf('let startY = 0'));
    const end = mobile.indexOf('</script>', start);
    const handlers = {};
    let reloads = 0;
    const window = {
        __financeProExpenseModalOpen: false,
        scrollY: 0,
        addEventListener(name, callback) { handlers[name] = callback; },
        location: { reload() { reloads++; } },
    };
    vm.runInNewContext(mobile.slice(start, end), {
        window,
        document: { scrollingElement: { scrollTop: 0 } },
    });
    const swipe = () => {
        handlers.touchstart({ touches: [{ clientX: 10, clientY: 10 }] });
        handlers.touchmove({ touches: [{ clientX: 10, clientY: 160 }] });
        handlers.touchend();
    };
    window.__financeProExpenseModalOpen = true;
    swipe();
    assert.equal(reloads, 0);
    window.__financeProExpenseModalOpen = false;
    handlers.touchstart({ touches: [{ clientX: 10, clientY: 10 }] });
    handlers.touchmove({ touches: [{ clientX: 10, clientY: 160 }] });
    window.__financeProExpenseModalOpen = true;
    handlers.touchend();
    assert.equal(reloads, 0);
    window.__financeProExpenseModalOpen = false;
    swipe();
    assert.equal(reloads, 1);
});

test('opening the expense scanner starts the quota request without waiting for it', () => {
    const handler = view.match(/x-on:open-expense-category-picker\.window="([^"]+)"/)[1];
    const window = {};
    const calls = [];
    const $wire = {
        openRouterQuota: null,
        refreshOpenRouterQuota(force) {
            assert.equal(context.open, true);
            calls.push(force);
            return new Promise(() => {});
        },
    };
    const context = { window, $wire, open: false, activeTab: 'category' };
    vm.runInNewContext(handler, context);
    assert.equal(context.open, true);
    assert.equal(context.activeTab, 'scanner');
    assert.deepEqual(calls, [false]);

    $wire.openRouterQuota = { available: true };
    vm.runInNewContext(handler, context);
    assert.deepEqual(calls, [false, false]);
});
