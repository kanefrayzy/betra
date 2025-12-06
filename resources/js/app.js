import './bootstrap.js';

// Turbo для мгновенных переходов между страницами
import * as Turbo from '@hotwired/turbo';

// Ленивая загрузка Noty - загружаем только когда нужно
let NotyLoaded = false;
const loadNoty = async () => {
    if (NotyLoaded) return window.Noty;
    
    const [Noty, css] = await Promise.all([
        import('noty'),
        import('noty/lib/noty.css')
    ]);
    
    window.Noty = Noty.default;
    NotyLoaded = true;
    return Noty.default;
};

// Конфигурация Turbo для максимальной скорости
Turbo.session.drive = true;

// ПОЛНОСТЬЮ отключаем прогресс-бар
Turbo.setProgressBarDelay(999999); // Никогда не показываем

// Включаем prefetch для мгновенных переходов
document.addEventListener('turbo:before-fetch-request', (event) => {
    // Добавляем заголовок для определения Turbo запросов
    event.detail.fetchOptions.headers['X-Turbo-Request'] = 'true';
});

// Предотвращаем множественную инициализацию Livewire/Alpine
window.livewireScriptConfig = window.livewireScriptConfig || (() => ({
    ...window.livewireScriptConfig?.() || {},
    navigate: false // Отключаем Livewire Navigate, используем Turbo
}));

// Ленивая инициализация Noty темы
let notyThemeInitialized = false;
const initNotyThemeOnce = async () => {
    if (notyThemeInitialized) return;
    
    await loadNoty();
    const { initNotyTailwindTheme } = await import('./noty-tailwind-theme');
    initNotyTailwindTheme();
    notyThemeInitialized = true;
};

// Core modules
import { initializeAppConfig } from './core/app-init.js';
import { 
    ModalManager, 
    DropdownManager,
    openChat,
    closeChat,
    toggleChat
} from './core/ui-components.js';
import {
    setupCsrfHandler,
    setupUnhandledRejectionHandler,
    setupChatPreservation,
    setupNotificationEvents,
    setupSidebarController,
    setupChatStore,
    setupUIStore,
    setupChatGlobals
} from './core/livewire-hooks.js';

// Отложенная загрузка некритичных модулей
const loadNonCriticalModules = () => {
    if ('requestIdleCallback' in window) {
        requestIdleCallback(() => {
            import('./banner-sliders.js');
            import('./game-sliders.js');
        }, { timeout: 3000 });
    } else {
        setTimeout(() => {
            import('./banner-sliders.js');
            import('./game-sliders.js');
        }, 2000);
    }
};

// Initialize app config
initializeAppConfig();

// Setup Livewire hooks
setupCsrfHandler();
setupUnhandledRejectionHandler();
setupChatPreservation();
setupNotificationEvents();
setupSidebarController();
setupChatStore();
setupUIStore();
setupChatGlobals();

// Загружаем некритичные модули после основной инициализации
loadNonCriticalModules();

// Initialize UI Managers
const modalManager = new ModalManager();
const dropdownManager = new DropdownManager();

// Expose global functions for backward compatibility
window.modalManager = modalManager;
window.openModal = (id) => modalManager.open(id);
window.closeModal = (id) => modalManager.close(id);
window.requireAuth = (callback, event) => modalManager.requireAuth(callback, event);

window.dropdownStates = dropdownManager.states;
window.toggleDropdown = (id, event) => dropdownManager.toggle(id, event);

window.openChat = openChat;
window.closeChat = closeChat;
window.toggleChat = toggleChat;

// Функция обновления активных ссылок в сайдбаре
function updateSidebarActiveLinks() {
    const currentPath = window.location.pathname;
    
    // Находим все ссылки в сайдбаре с классом sidebar-item
    document.querySelectorAll('.sidebar-item').forEach(link => {
        const linkPath = new URL(link.href, window.location.origin).pathname;
        
        // Проверяем совпадение путей
        if (linkPath === currentPath) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

// Initialize modal manager on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    // Защищаем опасные ссылки
    protectDangerousLinks();
    
    modalManager.init();
    
    // Лениво инициализируем Noty только если есть уведомления
    const hasNotifications = document.querySelector('meta[name="success-message"]') ||
                            document.querySelector('meta[name="error-message"]') ||
                            document.querySelector('meta[name="errors"]');
    
    if (hasNotifications) {
        initNotyThemeOnce();
    }
});

// Показываем прелоадер при начале навигации
document.addEventListener('turbo:click', (event) => {
    // Проверяем что это навигационная ссылка
    const link = event.target.closest('a[href]');
    if (!link || link.getAttribute('data-turbo') === 'false') return;
    
    // Удаляем старый прелоадер если есть
    const oldLoader = document.querySelector('.page-loader');
    if (oldLoader) {
        oldLoader.remove();
    }
    
    // Создаём новый прелоадер
    const loader = document.createElement('div');
    loader.className = 'page-loader';
    loader.innerHTML = `
        <div class="loader-spinner">
            <div class="spinner"></div>
            <div class="loader-text">Загрузка...</div>
        </div>
    `;
    document.body.appendChild(loader);
    
    // Показываем прелоадер сразу
    requestAnimationFrame(() => {
        loader.classList.add('active');
    });
});

// Скрываем прелоадер при завершении загрузки
document.addEventListener('turbo:render', () => {
    const loader = document.querySelector('.page-loader');
    if (loader) {
        // Небольшая задержка для плавности
        setTimeout(() => {
            loader.classList.remove('active');
            setTimeout(() => loader.remove(), 200);
        }, 100);
    }
    
    // Анимация появления контента
    const mainContent = document.querySelector('#main-content-wrapper');
    if (mainContent) {
        mainContent.style.opacity = '0';
        mainContent.style.transform = 'translateY(10px)';
        requestAnimationFrame(() => {
            mainContent.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            mainContent.style.opacity = '1';
            mainContent.style.transform = 'translateY(0)';
        });
    }
});

// Скрываем прелоадер если произошла ошибка
document.addEventListener('turbo:load', () => {
    const loader = document.querySelector('.page-loader');
    if (loader) {
        loader.remove();
    }
});

// Turbo Events - для работы с Alpine и Livewire
document.addEventListener('turbo:before-cache', () => {
    // Помечаем страницу как кешированную
    document.body.dataset.turboCached = 'true';
    
    // Закрываем все дропдауны и модалы перед кешированием (только сбрасываем состояние)
    document.querySelectorAll('[x-data]').forEach(el => {
        if (el.__x && !el.hasAttribute('data-turbo-permanent')) {
            // Сбрасываем состояние open в false только для НЕ постоянных элементов
            if (el.__x.$data?.open !== undefined) {
                el.__x.$data.open = false;
            }
            if (el.__x.$data?.isOpen !== undefined) {
                el.__x.$data.isOpen = false;
            }
        }
    });
    
    // Отключаем wire:poll перед кешированием
    document.querySelectorAll('[wire\\:poll]').forEach(el => {
        el.removeAttribute('wire:poll.3s.visible');
    });
    
    // ❌ НЕ уничтожаем Alpine компоненты - это ломает Livewire!
    // Alpine и Livewire сами управляют жизненным циклом при Turbo навигации
});

// 🔒 TURBO - Защита от случайного logout
document.addEventListener('turbo:before-visit', (event) => {
    const url = new URL(event.detail.url);
    
    // Блокируем prefetch logout - разрешаем только если это реальный клик
    if (url.pathname.includes('/logout') || url.pathname.includes('/auth/logout')) {
        // Если это не обычная навигация (т.е. prefetch), блокируем
        if (event.detail.fetchOptions?.headers?.['X-Prefetch']) {
            event.preventDefault();
            console.warn('🔒 Blocked prefetch of logout URL');
        }
    }
});

// 🔄 Очистка Service Worker кеша при logout/login
document.addEventListener('turbo:load', () => {
    // Проверяем флаг очистки кеша (устанавливается после logout)
    if (sessionStorage.getItem('clearSWCache')) {
        sessionStorage.removeItem('clearSWCache');
        
        // Очищаем кеш Service Worker
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({
                type: 'CLEAR_CACHE'
            });
            console.log('🗑️ Service Worker cache cleared after logout');
        }
    }
});

document.addEventListener('turbo:before-render', (event) => {
    // Проверяем загрузку из кеша
    const isFromCache = event.detail.newBody.dataset.turboCached === 'true';
    if (isFromCache) {
        console.log('⚡ Загружено из кеша (0ms)');
    }
    
    // Сохраняем состояние sidebar и chat
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');
    const chatOpen = localStorage.getItem('chatOpen');
    
    // Применяем классы к новой странице ДО рендера
    if (sidebarCollapsed === 'true' && window.innerWidth >= 1280) {
        event.detail.newBody.querySelector('.sidebar-wrapper')?.classList.add('collapsed');
        event.detail.newBody.querySelector('.main-content')?.classList.add('sidebar-collapsed');
    }
    
    if (chatOpen === 'true' && window.innerWidth >= 768) {
        event.detail.newBody.classList.add('chat-open');
    }
});

document.addEventListener('turbo:render', () => {
    // ❌ НЕ реинициализируем Alpine вручную - Livewire сам это делает!
    // Livewire автоматически управляет Alpine компонентами при Turbo навигации
    
    // Добавляем класс alpine-initialized для transitions
    requestAnimationFrame(() => {
        document.querySelector('.sidebar-wrapper')?.classList.add('alpine-initialized');
        document.querySelector('.main-content')?.classList.add('alpine-initialized');
    });
});

// ============================================
// 🔒 ЗАЩИТА LOGOUT ССЫЛОК
// ============================================

// Добавляем data-turbo="false" на все опасные ссылки
function protectDangerousLinks() {
    // Logout ссылки
    document.querySelectorAll('a[href*="/logout"], a[href*="/auth/logout"]').forEach(link => {
        link.setAttribute('data-turbo', 'false');
        link.setAttribute('data-no-prefetch', '');
    });
    
    // Игровые ссылки
    document.querySelectorAll('a[href*="/slots/play"], a[href*="/game/"], a[href*="/play/"]').forEach(link => {
        link.setAttribute('data-no-prefetch', '');
    });
    
    // Модальные окна
    document.querySelectorAll('a[onclick]').forEach(link => {
        const onclick = link.getAttribute('onclick');
        if (onclick) {
            const hasModal = ONCLICK_BLOCKLIST.some(action => onclick.includes(action));
            if (hasModal) {
                link.setAttribute('data-no-prefetch', '');
            }
        }
    });
}

document.addEventListener('turbo:load', () => {
    // Защищаем опасные ссылки
    protectDangerousLinks();
    
    // Реинициализация модалов
    modalManager.init();
    
    // Ленивая инициализация Noty при необходимости
    const hasNotifications = document.querySelector('meta[name="success-message"]') ||
                            document.querySelector('meta[name="error-message"]') ||
                            document.querySelector('meta[name="errors"]');
    
    if (hasNotifications) {
        initNotyThemeOnce();
    }
    
    // Обновляем активные ссылки в сайдбаре
    updateSidebarActiveLinks();
    
    // Скролл вверх (мгновенно)
    window.scrollTo({ top: 0, behavior: 'instant' });
});

// ============================================
// 🔒 БЕЗОПАСНЫЙ PREFETCH - ИСПРАВЛЕНИЕ
// ============================================

// Список запрещённых путей для prefetch
const PREFETCH_BLOCKLIST = [
    '/auth/logout',
    '/logout',
    '/slots/play',
    '/slots/mobile',
    '/game/',
    '/play/',
    'javascript:',
    '#',
    'mailto:',
    'tel:',
    'tg://',
    'https://t.me',
];

// Список страниц требующих авторизации - НЕ prefetch если не залогинен
const AUTH_ONLY_ROUTES = [
    '/slots/history',
    '/slots/favorites',
    '/account',
    '/transaction',
    '/account/referrals',
];

// Список запрещённых onclick действий
const ONCLICK_BLOCKLIST = [
    'openLoginModal',
    'openRegisterModal',
    'openCashModal',
    'openRankModal',
    'openPromoModal',
    'openRakebackModal',
    'toggleChat',
    'openChat',
];

const prefetchedUrls = new Set();

// Проверка авторизации пользователя
function isUserAuthenticated() {
    // Проверяем наличие глобального флага авторизации
    return window.appConfig?.user !== null && window.appConfig?.user !== undefined;
}

// ✅ БЕЗОПАСНАЯ проверка ссылки
function shouldPrefetch(link) {
    if (!link || !link.href) return false;
    
    // Проверяем что это внутренняя ссылка
    if (!link.href.startsWith(window.location.origin)) return false;
    
    // Проверяем data-turbo="false"
    if (link.getAttribute('data-turbo') === 'false') return false;
    
    // Проверяем data-no-prefetch
    if (link.hasAttribute('data-no-prefetch')) return false;
    
    // Проверяем onclick (модалы, действия)
    const onclick = link.getAttribute('onclick');
    if (onclick) {
        const hasBlockedAction = ONCLICK_BLOCKLIST.some(action => 
            onclick.includes(action)
        );
        if (hasBlockedAction) return false;
    }
    
    // Проверяем href против blocklist
    const href = link.getAttribute('href') || '';
    const hasBlockedPath = PREFETCH_BLOCKLIST.some(blocked => 
        href.includes(blocked)
    );
    if (hasBlockedPath) return false;
    
    // 🔒 КРИТИЧНО: НЕ prefetch auth-only страницы если не авторизован
    const url = new URL(link.href);
    const isAuthOnlyRoute = AUTH_ONLY_ROUTES.some(route => 
        url.pathname.startsWith(route)
    );
    if (isAuthOnlyRoute && !isUserAuthenticated()) {
        return false; // Блокируем prefetch - вызовет redirect на login
    }
    
    // Проверяем что уже не prefetch'или
    if (prefetchedUrls.has(link.href)) return false;
    
    return true;
}

// ✅ БЕЗОПАСНЫЙ prefetch
function prefetchLink(link, priority = 'low') {
    if (!shouldPrefetch(link)) return;
    
    prefetchedUrls.add(link.href);
    
    const prefetchLink = document.createElement('link');
    prefetchLink.rel = 'prefetch';
    prefetchLink.href = link.href;
    prefetchLink.as = 'document';
    
    // Добавляем fetchpriority для важных ссылок
    if (priority === 'high') {
        prefetchLink.fetchPriority = 'high';
        // Используем prerender только если поддерживается
        if ('HTMLLinkElement' in window && 'relList' in HTMLLinkElement.prototype) {
            const supportsPrerender = document.createElement('link').relList?.supports?.('prerender');
            if (supportsPrerender) {
                prefetchLink.rel = 'prerender';
            }
        }
    }
    
    prefetchLink.onerror = () => {
        prefetchedUrls.delete(link.href);
    };
    
    document.head.appendChild(prefetchLink);
}

// Prefetch при наведении (быстрее - без задержки)
document.addEventListener('mouseover', (e) => {
    const link = e.target.closest('a[href]');
    if (shouldPrefetch(link)) {
        prefetchLink(link);
    }
}, { passive: true });

// Prefetch при mousedown/touchstart - предзагрузка ДО клика с высоким приоритетом
document.addEventListener('mousedown', (e) => {
    const link = e.target.closest('a[href]');
    if (shouldPrefetch(link)) {
        prefetchLink(link, 'high');
    }
}, { passive: true });

document.addEventListener('touchstart', (e) => {
    const link = e.target.closest('a[href]');
    if (shouldPrefetch(link)) {
        prefetchLink(link);
    }
}, { passive: true });

// Prefetch видимых ссылок в сайдбаре при загрузке
if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const link = entry.target;
                if (shouldPrefetch(link)) {
                    prefetchLink(link);
                }
            }
        });
    }, { rootMargin: '50px' });
    
    // Наблюдаем за ссылками в сайдбаре
    document.addEventListener('turbo:load', () => {
        document.querySelectorAll('.sidebar-item, .sidebar-nav-buttons a').forEach(link => {
            observer.observe(link);
        });
    });
}

// ============================================
// 🔒 КРИТИЧНОЕ: Автоматический prefetch только БЕЗОПАСНЫХ страниц
// ============================================

// Автоматический prefetch критических страниц при загрузке
if ('requestIdleCallback' in window) {
    document.addEventListener('turbo:load', () => {
        requestIdleCallback(() => {
            const criticalLinks = [
                document.querySelector('a[href*="/slots/lobby"]'),
                document.querySelector('a[href*="/slots/popular"]'),
                // ❌ НЕ добавляем history, favorites - они могут редиректить
            ].filter(link => link !== null && shouldPrefetch(link));
            
            criticalLinks.forEach(link => prefetchLink(link, 'low'));
        }, { timeout: 2000 });
    });
} else {
    document.addEventListener('turbo:load', () => {
        setTimeout(() => {
            const criticalLinks = [
                document.querySelector('a[href*="/slots/lobby"]'),
                document.querySelector('a[href*="/slots/popular"]'),
            ].filter(link => link !== null && shouldPrefetch(link));
            
            criticalLinks.forEach(link => prefetchLink(link, 'low'));
        }, 1000);
    });
}

// Import chat system - прямая загрузка для быстрого подключения
import './chat/main.js';

// Import Telegram auth component - всегда нужен для Alpine x-data
import './telegram-auth.js';

// Import Telegram auth global - условная загрузка
if (document.querySelector('[data-telegram-auth]') || 
    navigator.userAgent.includes('Telegram')) {
    import('./telegram-auth-global.js');
}

// Import Telegram WebApp FULL - только если реально в Telegram
const isTelegramEnv = window.location.search.includes('tgWebAppPlatform') 
    || window.location.hash.includes('tgWebAppData')
    || document.referrer.includes('telegram.org')
    || navigator.userAgent.includes('Telegram');

if (isTelegramEnv) {
    import('./telegram-webapp-full.js').then(() => {
        console.log('✅ Telegram WebApp Full loaded');
    });
}

// Service Worker для кеширования страниц
import('./service-worker-register.js');

// Livewire SPA Navigation (оставляем для совместимости)
document.addEventListener('livewire:navigated', () => {
    initNotyThemeOnce();
    window.scrollTo({ top: 0, behavior: 'instant' });
});

// Notifications from meta tags
document.addEventListener('DOMContentLoaded', async function() {
    function getMetaContent(name, defaultValue = '') {
        const meta = document.querySelector(`meta[name="${name}"]`);
        return meta ? meta.getAttribute('content') : defaultValue;
    }

    const successMessage = getMetaContent('success-message');
    const errorMessage = getMetaContent('error-message');
    const errorsContent = getMetaContent('errors', '[]');

    let errors = [];
    try {
        errors = JSON.parse(errorsContent);
        if (!Array.isArray(errors)) errors = [];
    } catch (e) {}

    // Загружаем Noty только если есть уведомления
    if (successMessage || errorMessage || errors.length > 0) {
        await initNotyThemeOnce();
        
        if (successMessage && typeof window.showSuccessNotification === 'function') {
            window.showSuccessNotification(successMessage);
        }

        if (errorMessage && typeof window.showErrorNotification === 'function') {
            window.showErrorNotification(errorMessage);
        }

        if (errors.length > 0 && typeof window.showErrorsNotification === 'function') {
            window.showErrorsNotification(errors);
        }
    }
});

// Suppress known errors (with logging in development)
window.addEventListener('error', function(event) {
    const ignoreMessages = [
        "Cannot read properties of null",
        "window.Echo.socketId is not a function",
        "Cannot redefine property: $persist"
    ];

    const shouldSuppress = ignoreMessages.some(msg => event.message.includes(msg)) &&
        (event.filename.includes("livewire") || event.filename.includes("alpine"));

    if (shouldSuppress) {
        // Логируем в консоль для отладки, но не показываем пользователю
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.warn('[Suppressed Error]:', event.message, event.filename);
        }
        event.preventDefault();
    }
});
