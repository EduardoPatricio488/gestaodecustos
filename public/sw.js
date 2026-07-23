/**
 * Service Worker: Finance Pro v3
 * Gestão de Cache, Modo Offline e Push Notifications
 */

const cacheName = 'finance-pro-v3'; // Incrementar para v3 para forçar atualização
const OFFLINE_URL = '/offline.html';
const OFFLINE_QUEUE_KEY = 'finance-pro-offline-expenses';

const staticAssets = [
    OFFLINE_URL,
    '/manifest.json',
    '/icon-192x192.png',
    '/icon-512x512.png',
    'https://cdn.tailwindcss.com' // Opcional: faz cache do Tailwind para a página offline ficar bonita
];

// --- 1. INSTALAÇÃO ---
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(cacheName).then(cache => {
            console.log('📦 PWA: Cache de emergência ativado');
            return cache.addAll(staticAssets);
        })
    );
    self.skipWaiting();
});

// --- 2. ATIVAÇÃO ---
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== cacheName)
                    .map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// --- 3. GESTÃO DE REDE (FETCH) ---
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // ESTRATÉGIA DE FALLBACK PARA NAVEGAÇÃO
    // Se o utilizador tentar mudar de página e não houver internet...
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => {
                // ... mostramos o "Bunker Offline" que está na cache
                return caches.match(OFFLINE_URL);
            })
        );
        return;
    }

    // EXCEPÇÕES: Nunca guardar em cache
    if (
        event.request.method !== 'GET' ||
        url.pathname.startsWith('/livewire') ||
        url.pathname.startsWith('/api') ||
        event.request.headers.get('X-Livewire')
    ) {
        event.respondWith(fetch(event.request));
        return;
    }

    // GESTÃO DE ASSETS ESTÁTICOS (Imagens e Manifest)
    if (staticAssets.some(asset => url.pathname === asset)) {
        event.respondWith(
            caches.match(event.request).then(cached => cached || fetch(event.request))
        );
        return;
    }

    // PADRÃO: Rede primeiro
    event.respondWith(fetch(event.request));
});

// --- 4. SINCRONIZAÇÃO EM SEGUNDO PLANO ---
self.addEventListener('sync', event => {
    if (event.tag === 'sync-expenses') {
        event.waitUntil(syncOfflineExpenses());
    }
});

async function syncOfflineExpenses() {
    const clients = await self.clients.matchAll();
    for (const client of clients) {
        // Envia mensagem para o Layout da App (Blade) tratar da sincronização
        client.postMessage({ type: 'SYNC_OFFLINE_EXPENSES' });
    }
}

// --- 5. NOTIFICAÇÕES PUSH ---
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

// Força a atualização do Service Worker através de mensagem se necessário
self.addEventListener('message', event => {
    if (event.data?.type === 'SKIP_WAITING') self.skipWaiting();
});
