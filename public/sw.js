const cacheName = 'finance-pro-v1';
const staticAssets = [
    '/manifest.json',
    '/icon-192x192.png',
    '/icon-512x512.png'
];


self.addEventListener('message', event => {
    if (event.data?.type === 'SKIP_WAITING') self.skipWaiting();
});


self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(cacheName).then(cache => cache.addAll(staticAssets))
    );
    // Força o novo SW a ativar imediatamente sem esperar
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(key => key !== cacheName).map(key => caches.delete(key)))
        )
    );
    // Toma controlo de todas as páginas imediatamente
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // NUNCA fazer cache de:
    // - Pedidos POST/PUT/DELETE/PATCH
    // - Endpoints do Livewire
    // - Páginas HTML (navegação)
    // - Qualquer coisa com X-Livewire header
    if (
        event.request.method !== 'GET' ||
        url.pathname.startsWith('/livewire') ||
        url.pathname.startsWith('/api') ||
        event.request.mode === 'navigate' ||
        event.request.headers.get('X-Livewire') ||
        event.request.headers.get('Accept')?.includes('text/html')
    ) {
        event.respondWith(fetch(event.request));
        return;
    }

    // Só faz cache de assets estáticos (imagens, manifest)
    if (staticAssets.some(asset => url.pathname === asset)) {
        event.respondWith(
            caches.match(event.request).then(cached => cached || fetch(event.request))
        );
        return;
    }

    // Tudo o resto vai direto à rede
    event.respondWith(fetch(event.request));
});

self.addEventListener('push', function(event) {
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

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});
