// Service Worker для кеширования страниц и ресурсов
const CACHE_VERSION = 'betra-v1';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const PAGE_CACHE = `${CACHE_VERSION}-pages`;
const ASSET_CACHE = `${CACHE_VERSION}-assets`;

// Критические ресурсы для кеширования при установке
const CRITICAL_ASSETS = [
    '/',
    '/slots/lobby',
    '/slots/history',
    '/slots/popular',
];

// Установка Service Worker
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                // Кешируем критические страницы
                return cache.addAll(CRITICAL_ASSETS.map(url => new Request(url, { credentials: 'same-origin' })));
            })
            .catch(err => console.log('Cache install error:', err))
    );
    self.skipWaiting();
});

// Активация Service Worker
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => !cacheName.startsWith(CACHE_VERSION))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
    self.clients.claim();
});

// Стратегия кеширования
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Игнорируем не-GET запросы
    if (request.method !== 'GET') return;

    // Игнорируем внешние запросы
    if (url.origin !== location.origin) return;

    // Игнорируем Livewire запросы
    if (url.pathname.includes('/livewire/')) return;

    // Игнорируем API запросы
    if (url.pathname.startsWith('/api/')) return;

    // Стратегия для статических ресурсов (JS, CSS, изображения)
    if (request.destination === 'script' || 
        request.destination === 'style' || 
        request.destination === 'image' ||
        request.destination === 'font') {
        event.respondWith(cacheFirstStrategy(request, ASSET_CACHE));
        return;
    }

    // Стратегия для HTML страниц
    if (request.destination === 'document' || request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(networkFirstStrategy(request, PAGE_CACHE));
        return;
    }
});

// Cache First - приоритет кешу (для статики)
async function cacheFirstStrategy(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    
    if (cached) {
        // Обновляем кеш в фоне
        fetch(request).then(response => {
            if (response && response.status === 200) {
                cache.put(request, response.clone());
            }
        }).catch(() => {});
        
        return cached;
    }

    try {
        const response = await fetch(request);
        if (response && response.status === 200) {
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        return new Response('Offline - resource not available', { status: 503 });
    }
}

// Network First - приоритет сети (для HTML страниц)
async function networkFirstStrategy(request, cacheName) {
    const cache = await caches.open(cacheName);
    
    try {
        const response = await fetch(request);
        
        // Кешируем только успешные ответы
        if (response && response.status === 200) {
            // Клонируем ответ для кеша
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        // Если сеть недоступна, возвращаем из кеша
        const cached = await cache.match(request);
        
        if (cached) {
            return cached;
        }
        
        // Если в кеше тоже нет, возвращаем офлайн страницу
        return new Response('Offline - please check your connection', {
            status: 503,
            statusText: 'Service Unavailable',
            headers: new Headers({
                'Content-Type': 'text/html'
            })
        });
    }
}

// Очистка старого кеша при достижении лимита
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            caches.keys().then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => caches.delete(cacheName))
                );
            })
        );
    }
});
