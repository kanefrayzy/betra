// Service Worker для кеширования страниц и ресурсов
const CACHE_VERSION = 'betra-v2';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const PAGE_CACHE = `${CACHE_VERSION}-pages`;
const PAGE_CACHE_AUTH = `${CACHE_VERSION}-pages-auth`;
const ASSET_CACHE = `${CACHE_VERSION}-assets`;

// 🔓 ПУБЛИЧНЫЕ МАРШРУТЫ - кешировать всегда (доступны без авторизации)
const PUBLIC_ROUTES = [
    '/',
    '/slots/lobby',
    '/slots/popular',
    '/slots/new',
    '/rules',
    '/setlocale/',
];

// 🔒 АВТОРИЗОВАННЫЕ МАРШРУТЫ - кешировать ТОЛЬКО если авторизован
const AUTH_ROUTES = [
    '/slots/history',
    '/slots/favorites',
    '/account',
    '/transaction',
    '/account/referrals',
];

// ❌ ОПАСНЫЕ ПУТИ - НЕ КЕШИРОВАТЬ НИКОГДА!
const DANGEROUS_PATHS = [
    '/logout',
    '/auth/logout',
    '/slots/play',
    '/slots/mobile',
    '/game/',
    '/play/',
    '/api/',
    '/livewire/',
];

// Установка Service Worker
self.addEventListener('install', (event) => {
    event.waitUntil(
        Promise.all([
            // Предварительно открываем все кеши
            caches.open(STATIC_CACHE),
            caches.open(PAGE_CACHE),
            caches.open(PAGE_CACHE_AUTH),
            caches.open(ASSET_CACHE)
        ])
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

// Вспомогательная функция - проверка авторизации через cookies
function isAuthenticated(request) {
    const cookies = request.headers.get('cookie') || '';
    // Проверяем наличие Laravel session cookie
    return cookies.includes('laravel_session=') || cookies.includes('XSRF-TOKEN=');
}

// Вспомогательная функция - определение типа маршрута
function getRouteType(pathname) {
    // Проверяем опасные пути
    if (DANGEROUS_PATHS.some(path => pathname.includes(path))) {
        return 'dangerous';
    }
    
    // Проверяем авторизованные маршруты
    if (AUTH_ROUTES.some(route => pathname.startsWith(route))) {
        return 'auth';
    }
    
    // Проверяем публичные маршруты
    if (PUBLIC_ROUTES.some(route => pathname === route || pathname.startsWith(route))) {
        return 'public';
    }
    
    // По умолчанию - публичный
    return 'public';
}

// Стратегия кеширования
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Игнорируем не-GET запросы
    if (request.method !== 'GET') return;

    // Игнорируем внешние запросы
    if (url.origin !== location.origin) return;

    // Определяем тип маршрута
    const routeType = getRouteType(url.pathname);

    // ❌ ОПАСНЫЕ ПУТИ - пропускаем без кеширования
    if (routeType === 'dangerous') {
        return; // Браузер сам обработает
    }

    // 📦 Стратегия для статических ресурсов (JS, CSS, изображения, шрифты)
    if (request.destination === 'script' || 
        request.destination === 'style' || 
        request.destination === 'image' ||
        request.destination === 'font') {
        event.respondWith(cacheFirstStrategy(request, ASSET_CACHE));
        return;
    }

    // 📄 Стратегия для HTML страниц
    const isHtmlRequest = request.destination === 'document' || 
                         request.destination === '' || 
                         request.headers.get('accept')?.includes('text/html');
    
    if (isHtmlRequest) {
        // Проверяем авторизацию для auth маршрутов
        if (routeType === 'auth') {
            if (isAuthenticated(request)) {
                // Авторизован - кешируем в отдельный кеш
                event.respondWith(staleWhileRevalidate(request, PAGE_CACHE_AUTH));
            } else {
                // Не авторизован - НЕ кешируем (вернёт redirect на login)
                event.respondWith(fetch(request));
            }
        } else {
            // Публичный маршрут - кешируем всегда
            event.respondWith(staleWhileRevalidate(request, PAGE_CACHE));
        }
        return;
    }
    
    // Для всех остальных запросов - просто fetch без кеширования
    // (например, AJAX запросы, которые не попали в категории выше)
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

// Stale-While-Revalidate - мгновенная загрузка из кеша с обновлением в фоне
async function staleWhileRevalidate(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    
    // Запускаем fetch в фоне для обновления кеша
    const fetchPromise = fetch(request).then(response => {
        if (response?.status === 200) {
            cache.put(request, response.clone());
        }
        return response;
    }).catch(() => cached); // Возвращаем кеш при ошибке
    
    // Возвращаем кеш немедленно или ждем fetch если кеша нет
    return cached || fetchPromise;
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
