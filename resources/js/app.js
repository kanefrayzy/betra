import './bootstrap.js';
import * as Turbo from '@hotwired/turbo';

// ============================================
// ЛЕНИВАЯ ЗАГРУЗКА МОДУЛЕЙ
// ============================================

let NotyLoaded = false;
const loadNoty = async () => {
    if (NotyLoaded) return window.Noty;
    const [Noty] = await Promise.all([
        import('noty'),
        import('noty/lib/noty.css')
    ]);
    window.Noty = Noty.default;
    NotyLoaded = true;
    return Noty.default;
};

let notyThemeInitialized = false;
const initNotyThemeOnce = async () => {
    if (notyThemeInitialized) return;
    await loadNoty();
    const { initNotyTailwindTheme } = await import('./noty-tailwind-theme');
    initNotyTailwindTheme();
    notyThemeInitialized = true;
};

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

// ============================================
// TURBO КОНФИГУРАЦИЯ
// ============================================

Turbo.session.drive = true;
Turbo.setProgressBarDelay(999999);

document.addEventListener('turbo:before-fetch-request', (event) => {
    event.detail.fetchOptions.headers['X-Turbo-Request'] = 'true';
});

window.livewireScriptConfig = window.livewireScriptConfig || (() => ({
    ...window.livewireScriptConfig?.() || {},
    navigate: false
}));

// ============================================
// CORE MODULES INITIALIZATION
// ============================================

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
    setupChatGlobals,
    setupGamePlayer
} from './core/livewire-hooks.js';

initializeAppConfig();
setupCsrfHandler();
setupUnhandledRejectionHandler();
setupChatPreservation();
setupNotificationEvents();
setupSidebarController();
setupChatStore();
setupUIStore();
setupChatGlobals();
setupGamePlayer();
loadNonCriticalModules();

const modalManager = new ModalManager();
const dropdownManager = new DropdownManager();

window.modalManager = modalManager;
window.openModal = (id) => modalManager.open(id);
window.closeModal = (id) => modalManager.close(id);
window.requireAuth = (callback, event) => modalManager.requireAuth(callback, event);
window.dropdownStates = dropdownManager.states;
window.toggleDropdown = (id, event) => dropdownManager.toggle(id, event);
window.openChat = openChat;
window.closeChat = closeChat;
window.toggleChat = toggleChat;

// ============================================
// SIDEBAR ACTIVE LINKS
// ============================================

function updateSidebarActiveLinks() {
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar-item').forEach(link => {
        const linkPath = new URL(link.href, window.location.origin).pathname;
        link.classList.toggle('active', linkPath === currentPath);
    });
}

// ============================================
// ЗАЩИТА ОПАСНЫХ ССЫЛОК
// ============================================

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

const AUTH_ONLY_ROUTES = [
    '/slots/history',
    '/slots/favorites',
    '/account',
    '/transaction',
    '/account/referrals',
];

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

function isUserAuthenticated() {
    return window.appConfig?.user !== null && window.appConfig?.user !== undefined;
}

function protectDangerousLinks() {
    document.querySelectorAll('a[href*="/logout"], a[href*="/auth/logout"]').forEach(link => {
        link.setAttribute('data-turbo', 'false');
        link.setAttribute('data-no-prefetch', '');
    });
    
    document.querySelectorAll('a[onclick]').forEach(link => {
        const onclick = link.getAttribute('onclick');
        if (onclick && ONCLICK_BLOCKLIST.some(action => onclick.includes(action))) {
            link.setAttribute('data-no-prefetch', '');
        }
    });
}

// ============================================
// DOM CONTENT LOADED
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    protectDangerousLinks();
    modalManager.init();
    
    const hasNotifications = document.querySelector('meta[name="success-message"]') ||
                            document.querySelector('meta[name="error-message"]') ||
                            document.querySelector('meta[name="errors"]');
    
    if (hasNotifications) initNotyThemeOnce();
});

// ============================================
// PAGE LOADER
// ============================================

document.addEventListener('turbo:click', (event) => {
    const link = event.target.closest('a[href]');
    if (!link || link.getAttribute('data-turbo') === 'false') return;
    
    const oldLoader = document.querySelector('.page-loader');
    if (oldLoader) oldLoader.remove();
    
    const loader = document.createElement('div');
    loader.className = 'page-loader';
    loader.innerHTML = `
        <div class="loader-spinner">
            <div class="spinner"></div>
            <div class="loader-text">Загрузка...</div>
        </div>
    `;
    document.body.appendChild(loader);
    requestAnimationFrame(() => loader.classList.add('active'));
});

document.addEventListener('turbo:render', () => {
    const loader = document.querySelector('.page-loader');
    if (loader) {
        setTimeout(() => {
            loader.classList.remove('active');
            setTimeout(() => loader.remove(), 200);
        }, 100);
    }
    
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

document.addEventListener('turbo:load', () => {
    const loader = document.querySelector('.page-loader');
    if (loader) loader.remove();
});

// ============================================
// TURBO CACHE & NAVIGATION
// ============================================

document.addEventListener('turbo:before-cache', () => {
    document.body.dataset.turboCached = 'true';
    
    document.querySelectorAll('[x-data*="gamePlayer"]').forEach(el => {
        if (el.__x?.$data) {
            el.__x.$data.loading = true;
            el.__x.$data.error = false;
            el.__x.$data.gameUrl = null;
            if (el.__x.$data.loadTimeout) {
                clearTimeout(el.__x.$data.loadTimeout);
                el.__x.$data.loadTimeout = null;
            }
        }
    });
    
    document.querySelectorAll('[x-data]').forEach(el => {
        if (el.__x && !el.hasAttribute('data-turbo-permanent')) {
            if (el.__x.$data?.open !== undefined) el.__x.$data.open = false;
            if (el.__x.$data?.isOpen !== undefined) el.__x.$data.isOpen = false;
        }
    });
    
    document.querySelectorAll('[wire\\:poll]').forEach(el => {
        el.removeAttribute('wire:poll.3s.visible');
    });
});

document.addEventListener('turbo:before-visit', (event) => {
    const url = new URL(event.detail.url);
    
    if ((url.pathname.includes('/logout') || url.pathname.includes('/auth/logout')) &&
        event.detail.fetchOptions?.headers?.['X-Prefetch']) {
        event.preventDefault();
    }
});

document.addEventListener('turbo:load', () => {
    if (sessionStorage.getItem('clearSWCache')) {
        sessionStorage.removeItem('clearSWCache');
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({ type: 'CLEAR_CACHE' });
        }
    }
});

document.addEventListener('turbo:before-render', (event) => {
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');
    const chatOpen = localStorage.getItem('chatOpen');
    
    if (sidebarCollapsed === 'true' && window.innerWidth >= 1280) {
        event.detail.newBody.querySelector('.sidebar-wrapper')?.classList.add('collapsed');
        event.detail.newBody.querySelector('.main-content')?.classList.add('sidebar-collapsed');
    }
    
    if (chatOpen === 'true' && window.innerWidth >= 768) {
        event.detail.newBody.classList.add('chat-open');
    }
});

document.addEventListener('turbo:render', () => {
    requestAnimationFrame(() => {
        document.querySelector('.sidebar-wrapper')?.classList.add('alpine-initialized');
        document.querySelector('.main-content')?.classList.add('alpine-initialized');
    });
});

document.addEventListener('turbo:load', () => {
    protectDangerousLinks();
    modalManager.init();
    
    const hasNotifications = document.querySelector('meta[name="success-message"]') ||
                            document.querySelector('meta[name="error-message"]') ||
                            document.querySelector('meta[name="errors"]');
    
    if (hasNotifications) initNotyThemeOnce();
    
    updateSidebarActiveLinks();
    window.scrollTo({ top: 0, behavior: 'instant' });
});

// ============================================
// TELEGRAM MODULES
// ============================================

import './chat/main.js';
import './telegram-auth.js';

if (document.querySelector('[data-telegram-auth]') || navigator.userAgent.includes('Telegram')) {
    import('./telegram-auth-global.js');
}

const isTelegramEnv = window.location.search.includes('tgWebAppPlatform') || 
                      window.location.hash.includes('tgWebAppData') ||
                      document.referrer.includes('telegram.org') ||
                      navigator.userAgent.includes('Telegram');

if (isTelegramEnv) {
    import('./telegram-webapp-full.js');
}

import('./service-worker-register.js');

// ============================================
// LIVEWIRE COMPATIBILITY
// ============================================

document.addEventListener('livewire:navigated', () => {
    initNotyThemeOnce();
    window.scrollTo({ top: 0, behavior: 'instant' });
});

// ============================================
// NOTIFICATIONS
// ============================================

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

// ============================================
// ERROR SUPPRESSION
// ============================================

window.addEventListener('error', function(event) {
    const ignoreMessages = [
        "Cannot read properties of null",
        "window.Echo.socketId is not a function",
        "Cannot redefine property: $persist"
    ];

    const shouldSuppress = ignoreMessages.some(msg => event.message.includes(msg)) &&
        (event.filename.includes("livewire") || event.filename.includes("alpine"));

    if (shouldSuppress) {
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.warn('[Suppressed]:', event.message);
        }
        event.preventDefault();
    }
});