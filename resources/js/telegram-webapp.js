/**
 * Telegram WebApp Integration
 * Загружает стили только если приложение открыто в Telegram
 */

// Функция проверки Telegram WebApp
function checkAndLoadTelegramStyles() {
    const isTelegram = (typeof window.isTelegramWebApp === 'function') 
        ? window.isTelegramWebApp() 
        : false;
    
    if (isTelegram) {
        if (!document.getElementById('telegram-webapp-styles')) {
            const link = document.createElement('link');
            link.id = 'telegram-webapp-styles';
            link.rel = 'stylesheet';
            // URL инжектится из blade шаблона через window.telegramWebAppConfig
            const config = window.telegramWebAppConfig || {};
            link.href = config.stylesUrl || '/css/telegram-webapp.css?v=1.0';
            document.head.appendChild(link);
        }
    }
}

const isGamePage = window.location.pathname.includes('/slots/play/') || window.location.pathname.includes('/slots/fun/');

if (isGamePage) {
    document.addEventListener('DOMContentLoaded', checkAndLoadTelegramStyles);
} else {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(checkAndLoadTelegramStyles, 50);
    });
}

document.addEventListener('livewire:navigated', function() {
    const isGamePage = window.location.pathname.includes('/slots/play/') || window.location.pathname.includes('/slots/fun/');
    
    if (isGamePage) {
        checkAndLoadTelegramStyles();
        if (typeof window.applyTelegramWebAppSettings === 'function') {
            window.applyTelegramWebAppSettings();
        }
    } else {
        setTimeout(checkAndLoadTelegramStyles, 50);
        if (typeof window.applyTelegramWebAppSettings === 'function') {
            setTimeout(() => {
                window.applyTelegramWebAppSettings();
            }, 150);
        }
    }
});
