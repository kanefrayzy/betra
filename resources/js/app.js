import './bootstrap.js';
import Noty from 'noty';
import 'noty/lib/noty.css';

// Turbo для мгновенных переходов между страницами
import * as Turbo from '@hotwired/turbo';

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

window.Noty = Noty;

import { initNotyTailwindTheme } from './noty-tailwind-theme';
initNotyTailwindTheme();

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
    setupChatGlobals
} from './core/livewire-hooks.js';

import './banner-sliders.js';

// Initialize app config
initializeAppConfig();

// Setup Livewire hooks
setupCsrfHandler();
setupUnhandledRejectionHandler();
setupChatPreservation();
setupNotificationEvents();
setupSidebarController();
setupChatStore();
setupChatGlobals();

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
    modalManager.init();
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
    
    // Закрываем все дропдауны и модалы перед кешированием
    document.querySelectorAll('[x-data]').forEach(el => {
        if (el.__x && !el.hasAttribute('data-turbo-permanent')) {
            // Сбрасываем состояние open в false только для НЕ постоянных элементов
            if (el.__x.$data.open !== undefined) {
                el.__x.$data.open = false;
            }
        }
    });
    
    // Отключаем wire:poll перед кешированием
    document.querySelectorAll('[wire\\:poll]').forEach(el => {
        el.removeAttribute('wire:poll.3s.visible');
    });
    
    // Уничтожаем Alpine компоненты, которые НЕ permanent
    document.querySelectorAll('[x-data]').forEach(el => {
        if (el.__x && !el.hasAttribute('data-turbo-permanent')) {
            if (typeof Alpine !== 'undefined' && Alpine.destroyTree) {
                Alpine.destroyTree(el);
            }
        }
    });
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
    // Реинициализируем Alpine для новых элементов
    if (typeof Alpine !== 'undefined') {
        // Инициализируем только новые компоненты (не permanent)
        document.querySelectorAll('[x-data]').forEach(el => {
            if (!el.hasAttribute('data-turbo-permanent') && !el.__x) {
                Alpine.initTree(el);
            }
        });
    }
    
    // Добавляем класс alpine-initialized для transitions
    requestAnimationFrame(() => {
        document.querySelector('.sidebar-wrapper')?.classList.add('alpine-initialized');
        document.querySelector('.main-content')?.classList.add('alpine-initialized');
    });
});

document.addEventListener('turbo:load', () => {
    // Реинициализация модалов
    modalManager.init();
    
    // Реинициализация нотификаций
    initNotyTailwindTheme();
    
    // Обновляем активные ссылки в сайдбаре
    updateSidebarActiveLinks();
    
    // Скролл вверх (мгновенно)
    window.scrollTo({ top: 0, behavior: 'instant' });
});

// Агрессивный prefetch для моментальной загрузки
const prefetchedUrls = new Set();

function shouldPrefetch(link) {
    return link && 
           link.href && 
           link.href.startsWith(window.location.origin) && 
           !link.hasAttribute('data-turbo="false"') &&
           !prefetchedUrls.has(link.href);
}

function prefetchLink(link) {
    if (shouldPrefetch(link)) {
        prefetchedUrls.add(link.href);
        // Используем fetch для предзагрузки в фоне
        fetch(link.href, {
            method: 'GET',
            headers: {
                'X-Turbo-Request': 'true',
                'Accept': 'text/html, application/xhtml+xml'
            },
            credentials: 'same-origin',
            priority: 'low' // Низкий приоритет чтобы не мешать основным запросам
        }).catch(() => {
            // Игнорируем ошибки prefetch
            prefetchedUrls.delete(link.href);
        });
    }
}

// Prefetch при наведении (быстрее - без задержки)
document.addEventListener('mouseover', (e) => {
    const link = e.target.closest('a[href]');
    if (shouldPrefetch(link)) {
        prefetchLink(link);
    }
}, { passive: true });

// Prefetch при mousedown/touchstart - предзагрузка ДО клика
document.addEventListener('mousedown', (e) => {
    const link = e.target.closest('a[href]');
    if (shouldPrefetch(link)) {
        prefetchLink(link);
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

// Import chat system - прямая загрузка для быстрого подключения
import './chat/main.js';

// Import game sliders management
import './game-sliders.js';

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
    initNotyTailwindTheme();
    window.scrollTo({ top: 0, behavior: 'instant' });
});

// Notifications from meta tags
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.showSuccessNotification === 'undefined') {
        initNotyTailwindTheme();
    }

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

    if (successMessage && typeof window.showSuccessNotification === 'function') {
        window.showSuccessNotification(successMessage);
    }

    if (errorMessage && typeof window.showErrorNotification === 'function') {
        window.showErrorNotification(errorMessage);
    }

    if (errors.length > 0 && typeof window.showErrorsNotification === 'function') {
        window.showErrorsNotification(errors);
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
