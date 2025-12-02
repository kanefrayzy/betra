import './bootstrap.js';
import Noty from 'noty';
import 'noty/lib/noty.css';

window.Noty = Noty;

import { initNotyTailwindTheme } from './noty-tailwind-theme';
initNotyTailwindTheme();

// Import chat system
import './chat/main.js';

// Import Telegram auth
import './telegram-auth-global.js';

// Livewire SPA Navigation with lightning preloader
let navigationStartTime = 0;

document.addEventListener('livewire:navigating', () => {
    navigationStartTime = Date.now();
    const mainContent = document.getElementById('main-content-wrapper');
    const preloader = document.getElementById('preloader');
    
    // Очищаем консоль при навигации
    console.clear();
    
    // Полностью скрываем контент
    if (mainContent) {
        mainContent.style.opacity = '0';
        mainContent.style.visibility = 'hidden';
    }
    
    // Показываем прелоадер
    if (preloader) {
        preloader.classList.remove('fade-out');
        preloader.style.opacity = '1';
        preloader.style.pointerEvents = 'auto';
        preloader.style.display = 'flex';
    }
});

document.addEventListener('livewire:navigated', () => {
    const mainContent = document.getElementById('main-content-wrapper');
    const preloader = document.getElementById('preloader');
    
    const minLoaderTime = 400; // Minimum 400ms display time
    const elapsed = Date.now() - navigationStartTime;
    const remainingTime = Math.max(0, minLoaderTime - elapsed);
    
    setTimeout(() => {
        // Скрываем прелоадер
        if (preloader) {
            preloader.classList.add('fade-out');
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 300); // Wait for fade-out transition
        }
        
        // Показываем новый контент
        if (mainContent) {
            mainContent.style.visibility = 'visible';
            mainContent.style.opacity = '1';
        }
        
        initNotyTailwindTheme();
        window.scrollTo({ top: 0, behavior: 'instant' });
    }, remainingTime);
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
