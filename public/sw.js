/**
 * Service Worker: Finance Pro v4
 * Cache offline, Bunker fallback e sincronização em segundo plano.
 */

const cacheName = 'finance-pro-v4';
const OFFLINE_URL = '/offline.html';

const staticAssets = [
    OFFLINE_URL,
    '/manifest.json',
    '/icon-192x192.png',
    '/icon-512x512.png',
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(cacheName)
            .then(cache => cache.addAll(staticAssets))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => Promise.all(
            keys.filter(key => key !== cacheName).map(key => caches.delete(key))
        )).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    if (
        event.request.method !== 'GET' ||
        url.pathname.startsWith('/livewire') ||
        url.pathname.startsWith('/api') ||
        event.request.headers.get('X-Livewire')
    ) {
        event.respondWith(fetch(event.request));
        return;
    }

    if (url.origin === self.location.origin && staticAssets.includes(url.pathname)) {
        event.respondWith(
            caches.match(event.request).then(cached => cached || fetch(event.request))
        );
        return;
    }

    event.respondWith(fetch(event.request));
});

self.addEventListener('sync', event => {
    if (event.tag === 'sync-expenses') {
        event.waitUntil(notifyClientsToSync());
    }
});

async function notifyClientsToSync() {
    const clients = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
    clients.forEach(client => client.postMessage({ type: 'SYNC_OFFLINE_EXPENSES' }));
}

self.addEventListener('push', event => {
    const data = event.data ? event.data.json() : { title: 'Finance Pro', body: 'Nova atualização!' };
    event.waitUntil(self.registration.showNotification(data.title, {
        body: data.body,
        icon: '/icon-192x192.png',
        badge: '/icon-192x192.png',
        vibrate: [100, 50, 100],
        data: { url: data.action_url || '/' },
    }));
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});

self.addEventListener('message', event => {
    if (event.data?.type === 'SKIP_WAITING') self.skipWaiting();
});
