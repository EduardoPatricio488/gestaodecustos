/**
 * Service Worker: Finance Pro v5
 * Navegação sempre atualizada e instalação resiliente em iOS/PWA.
 */

const cacheName = 'finance-pro-v6';
const OFFLINE_URL = '/offline.html';

const staticAssets = [
    OFFLINE_URL,
    '/manifest.json',
    '/icon-192x192.png',
    '/icon-512x512.png',
];

self.addEventListener('install', event => {
    event.waitUntil((async () => {
        const cache = await caches.open(cacheName);

        // Um recurso opcional em falta não pode impedir a instalação da PWA.
        await Promise.allSettled(
            staticAssets.map(async asset => {
                const response = await fetch(asset, { cache: 'reload' });
                if (response.ok) await cache.put(asset, response);
            })
        );

        await self.skipWaiting();
    })());
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(
                keys.filter(key => key.startsWith('finance-pro-') && key !== cacheName)
                    .map(key => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    // Nunca servir HTML antigo na abertura da aplicação instalada.
    if (request.mode === 'navigate') {
        event.respondWith((async () => {
            try {
                return await fetch(new Request(request, { cache: 'no-store' }));
            } catch (error) {
                const offlinePage = await caches.match(OFFLINE_URL);
                if (offlinePage) return offlinePage;
                return new Response(
                    '<!doctype html><html lang="pt"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Finance Pro IA</title><body style="font-family:system-ui;padding:32px;background:#09090b;color:white"><h1>Sem ligação à Internet</h1><p>Verifica a ligação e volta a abrir o Finance Pro IA.</p></body></html>',
                    { headers: { 'Content-Type': 'text/html; charset=utf-8' }, status: 503 }
                );
            }
        })());
        return;
    }

    if (
        url.pathname.startsWith('/livewire') ||
        url.pathname.startsWith('/api') ||
        request.headers.get('X-Livewire')
    ) {
        event.respondWith(fetch(request));
        return;
    }

    if (staticAssets.includes(url.pathname)) {
        event.respondWith(
            caches.match(request).then(cached => cached || fetch(request))
        );
        return;
    }

    event.respondWith(fetch(request));
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
    event.waitUntil(self.clients.openWindow(event.notification.data.url));
});

self.addEventListener('message', event => {
    if (event.data?.type === 'SKIP_WAITING') self.skipWaiting();
});
