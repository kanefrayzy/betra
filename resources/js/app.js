import './bootstrap.js';
import Noty from 'noty';
import 'noty/lib/noty.css';

// Import Swiper
import 'swiper/swiper-bundle.css';

// Import QRCode
import QRCode from 'qrcodejs';
window.QRCode = QRCode;

window.Noty = Noty;

import { initNotyTailwindTheme } from './noty-tailwind-theme';
initNotyTailwindTheme();

// Import chat system
import './chat/main.js';

// Import Telegram auth
import './telegram-auth-global.js';

// Livewire SPA Navigation - Optimized
document.addEventListener('livewire:navigating', () => {
    const mainContent = document.getElementById('main-content-wrapper');
    
    // Плавное скрытие контента при навигации
    if (mainContent) {
        mainContent.style.opacity = '0';
    }
});

document.addEventListener('livewire:navigated', () => {
    const mainContent = document.getElementById('main-content-wrapper');
    
    // Показываем новый контент
    if (mainContent) {
        mainContent.style.opacity = '1';
    }
    
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

// Suppress known errors
window.addEventListener('error', function(event) {
    const ignoreMessages = [
        "Cannot read properties of null",
        "window.Echo.socketId is not a function",
        "Cannot redefine property: $persist"
    ];

    if (ignoreMessages.some(msg => event.message.includes(msg)) &&
        (event.filename.includes("livewire") || event.filename.includes("alpine"))) {
        event.preventDefault();
    }
});
