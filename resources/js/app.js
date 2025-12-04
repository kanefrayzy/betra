import './bootstrap.js';
import Noty from 'noty';
import 'noty/lib/noty.css';

window.Noty = Noty;

import { initNotyTailwindTheme } from './noty-tailwind-theme';
initNotyTailwindTheme();

// Import chat system - прямая загрузка для быстрого подключения
import './chat/main.js';

// Import Telegram auth - условная загрузка
if (document.querySelector('[data-telegram-auth]') || 
    navigator.userAgent.includes('Telegram')) {
    import('./telegram-auth-global.js');
}

// // Ленивая загрузка QRCode для модалки кошелька
// let qrcodeLoaded = false;
// window.loadQRCode = function() {
//     if (!qrcodeLoaded) {
//         qrcodeLoaded = true;
//         import('qrcodejs').then(module => {
//             window.QRCode = module.default || module;
//         }).catch(err => console.error('QRCode load error:', err));
//     }
// };

// Автозагрузка при открытии модалки кошелька
document.addEventListener('open-cash-modal', window.loadQRCode, { once: true });

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
