const cacheName = 'finance-pro-v1';
const assets = ['/', '/manifest.json', '/icon-192x192.png', '/icon-512x512.png'];

self.addEventListener('install', event => {
    event.waitUntil(caches.open(cacheName).then(cache => cache.addAll(assets)));
});

self.addEventListener('activate', event => {
    event.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(key => key !== cacheName).map(key => caches.delete(key)))));
});

self.addEventListener('fetch', event => {
    event.respondWith(caches.match(event.request).then(cacheRes => cacheRes || fetch(event.request)));
});

// --- LÓGICA DE PUSH NOTIFICATIONS ---
self.addEventListener('push', function (event) {
    const data = event.data ? event.data.json() : { title: 'Finance Pro', body: 'Nova atualização!' };

    const options = {
        body: data.body,
        icon: '/icon-192x192.png',
        badge: '/icon-192x192.png',
        vibrate: [100, 50, 100],
        data: { url: data.action_url || '/' }
    };

    event.waitUntil(self.registration.showNotification(data.title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});
