// Service Worker for Mechanic Portal

const CACHE_NAME = 'mechfinder-mechanic-v1';
const ASSETS_TO_CACHE = [
    '/mechanic/dashboard',
    '/mechanic/profile',
    '/manifest-mechanic.json',
    '/css/app.css',
    '/js/app.js',
    '/icons/mobile-logo1.png'
];

self.addEventListener('install', event => {
    console.log('[Mechanic SW] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(ASSETS_TO_CACHE).catch(() => {
                console.log('[Mechanic SW] Some assets could not be cached');
            });
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    console.log('[Mechanic SW] Activating...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[Mechanic SW] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip cross-origin requests
    if (url.origin !== location.origin) return;

    // API calls - network first, then cache
    if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/mechanic/request/')) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    if (response.ok) {
                        caches.open(CACHE_NAME).then(cache => cache.put(request, response.clone()));
                    }
                    return response;
                })
                .catch(() => caches.match(request))
        );
        return;
    }

    // Assets - cache first, then network
    event.respondWith(
        caches.match(request).then(cachedResponse => {
            return cachedResponse || fetch(request).then(response => {
                if (response.ok && url.pathname.match(/\.(js|css|png|jpg|jpeg|svg|webp)$/)) {
                    caches.open(CACHE_NAME).then(cache => cache.put(request, response.clone()));
                }
                return response;
            });
        }).catch(() => {
            if (request.destination === 'document') {
                return caches.match('/mechanic/dashboard');
            }
        })
    );
});

self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
