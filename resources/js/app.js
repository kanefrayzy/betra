import './bootstrap.js';
import Noty from 'noty';
import 'noty/lib/noty.css';

window.Noty = Noty;

import { initNotyTailwindTheme } from './noty-tailwind-theme';
initNotyTailwindTheme();

// Core modules - app initialization
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

// Initialize modal manager on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    modalManager.init();
});

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


// Livewire SPA Navigation
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
