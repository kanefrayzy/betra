/**
 * Глобальный скрипт для интеграции Telegram WebView авторизации
 * Подключается на всех страницах сайта
 */

// Функция для корректного определения Telegram WebApp (глобальная)
window.isTelegramWebApp = function() {
    if (!window.Telegram || !window.Telegram.WebApp) {
        return false;
    }
    
    const tg = window.Telegram.WebApp;
    
    // Проверяем реальные признаки Telegram WebApp
    // Если initData пустая И platform unknown - это не настоящий Telegram
    if (!tg.initData && tg.platform === 'unknown') {
        return false;
    }
    
    // Дополнительная проверка User Agent
    const userAgent = navigator.userAgent.toLowerCase();
    const isTelegramUA = userAgent.includes('telegram') || 
                        userAgent.includes('tgwebapp') ||
                        userAgent.includes('tgwebview');
    
    // Если в UA нет упоминания Telegram и нет данных - это не Telegram
    if (!isTelegramUA && !tg.initData) {
        return false;
    }
    
    return true;
};

// Функция для запроса полноэкранного режима с задержкой
function requestFullscreenMode(tg, attempt = 1, maxAttempts = 5) {
    if (typeof tg.requestFullscreen !== 'function') {
        console.log('Fullscreen API not available');
        return;
    }
    
    console.log(`Fullscreen attempt ${attempt}/${maxAttempts}`);
    
    try {
        tg.requestFullscreen();
        console.log('Fullscreen mode requested');
        
        // Проверяем через небольшую задержку, применился ли fullscreen
        setTimeout(() => {
            if (tg.isFullscreen === false && attempt < maxAttempts) {
                console.log('Fullscreen not activated, retrying...');
                requestFullscreenMode(tg, attempt + 1, maxAttempts);
            } else if (tg.isFullscreen) {
                console.log('Fullscreen activated successfully');
            } else {
                console.log('Fullscreen check:', tg.isFullscreen);
            }
        }, 300);
        
    } catch (e) {
        console.log('Fullscreen request failed:', e);
        if (attempt < maxAttempts) {
            setTimeout(() => {
                requestFullscreenMode(tg, attempt + 1, maxAttempts);
            }, 500);
        }
    }
}

// Глобальная функция для применения всех настроек Telegram WebApp
window.applyTelegramWebAppSettings = function() {
    if (!window.isTelegramWebApp()) {
        return;
    }
    
    const tg = window.Telegram.WebApp;
    const isGamePage = window.location.pathname.includes('/slots/play/') || window.location.pathname.includes('/slots/fun/');
    
    // Expand
    tg.expand();
    
    // Отключаем вертикальные свайпы
    if (typeof tg.disableVerticalSwipes === 'function') {
        tg.disableVerticalSwipes();
        console.log('Vertical swipes disabled');
    }
    
    // Включаем подтверждение закрытия
    if (typeof tg.enableClosingConfirmation === 'function') {
        tg.enableClosingConfirmation();
        console.log('Closing confirmation enabled');
    }
    
    // Запрашиваем fullscreen
    // Для игровых страниц - мгновенно, для остальных - с задержкой
    if (isGamePage) {
        requestFullscreenMode(tg);
    } else {
        setTimeout(() => {
            requestFullscreenMode(tg);
        }, 200);
    }
};

// Проверка авторизации при загрузке страницы
document.addEventListener('DOMContentLoaded', async function() {
    const isTelegram = window.isTelegramWebApp();
    
    if (!isTelegram) {
        return;
    }
    
    const tg = window.Telegram.WebApp;
    
    // Инициализируем WebApp
    tg.ready();
    
    // Применяем все настройки (expand, swipes, confirmation, fullscreen)
    window.applyTelegramWebAppSettings();
    
    // Устанавливаем обработчик на событие закрытия
    tg.onEvent('viewportChanged', function() {
        if (!tg.isExpanded) {
            tg.expand();
        }
    });
    
    // Предотвращаем закрытие через BackButton
    if (tg.BackButton) {
        tg.BackButton.onClick(function() {
            // Показываем свое подтверждение или игнорируем
            if (confirm('Вы уверены, что хотите выйти?')) {
                tg.close();
            }
        });
    }
    
    // Блокируем стандартное поведение свайпа назад
    let touchStartY = 0;
    document.addEventListener('touchstart', function(e) {
        touchStartY = e.touches[0].clientY;
    }, { passive: true });
    
    document.addEventListener('touchmove', function(e) {
        const touchY = e.touches[0].clientY;
        const touchDiff = touchY - touchStartY;
        
        // Если свайп вниз от верха страницы
        if (touchDiff > 0 && window.scrollY === 0) {
            e.preventDefault();
        }
    }, { passive: false });
    
    // Применяем класс telegram-webapp для активации специальных стилей
    document.documentElement.classList.add('telegram-webapp');
    document.body.classList.add('telegram-webapp');
    
    console.log('Telegram WebApp detected, classes applied');
    
    // Применяем тему Telegram
    if (tg.themeParams) {
        const root = document.documentElement;
        Object.entries(tg.themeParams).forEach(([key, value]) => {
            root.style.setProperty(`--tg-theme-${key.replace(/_/g, '-')}`, value);
        });
    }
    
    // Проверяем, есть ли уже авторизованный пользователь
    try {
        const response = await fetch('/auth/check', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const result = await response.json();
            
            if (result.authenticated) {
                // Загружаем информацию о пользователе
                try {
                    const userResponse = await fetch('/api/telegram/user', {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (userResponse.ok) {
                        const userData = await userResponse.json();
                        
                        // Вызываем глобальный обработчик если есть
                        if (window.onTelegramUserLoaded) {
                            window.onTelegramUserLoaded(userData);
                        }
                    }
                } catch (error) {
                    // Игнорируем ошибку
                }
            } else {
                // Если мы в Telegram WebApp и пользователь не авторизован
                if (window.Telegram && window.Telegram.WebApp && window.Telegram.WebApp.initData) {
                    initTelegramAuth();
                }
            }
        }
    } catch (error) {
        // Игнорируем ошибку
    }
});

/**
 * Инициализация авторизации через Telegram WebApp
 */
function initTelegramAuth() {
    const tg = window.Telegram.WebApp;
    
    if (!tg.initData) {
        showTelegramMessage('Нет данных от Telegram WebApp', 'error');
        return;
    }
    
    tg.ready();
    tg.expand();
    
    // Парсим данные пользователя
    const initDataParams = new URLSearchParams(tg.initData);
    const userParam = initDataParams.get('user');
    
    if (userParam) {
        try {
            const userData = JSON.parse(decodeURIComponent(userParam));
            
            // Получаем все параметры из initData
            const initDataParams = new URLSearchParams(tg.initData);
            
            // Формируем данные только с теми полями, которые реально есть
            const telegramUserData = {
                id: userData.id,
                first_name: userData.first_name
            };
            
            // Добавляем опциональные поля только если они есть и не пустые
            if (userData.last_name) telegramUserData.last_name = userData.last_name;
            if (userData.username) telegramUserData.username = userData.username;
            if (userData.language_code) telegramUserData.language_code = userData.language_code;
            if (userData.photo_url) telegramUserData.photo_url = userData.photo_url;
            if (userData.allows_write_to_pm !== undefined) telegramUserData.allows_write_to_pm = userData.allows_write_to_pm;
            
            // Добавляем параметры из initData
            const authDate = initDataParams.get('auth_date');
            const hash = initDataParams.get('hash');
            const queryId = initDataParams.get('query_id');
            const signature = initDataParams.get('signature');
            
            if (authDate) telegramUserData.auth_date = authDate;
            if (hash) telegramUserData.hash = hash;
            if (queryId) telegramUserData.query_id = queryId;
            if (signature) telegramUserData.signature = signature;
            
            // Сначала попробуем авторизоваться без валюты чтобы проверить существование пользователя
            authenticateWithTelegram(telegramUserData);
            
        } catch (error) {
            showTelegramMessage('Ошибка обработки данных пользователя', 'error');
        }
    } else {
        showTelegramMessage('Нет данных пользователя', 'error');
    }
}

/**
 * Отправка данных на сервер для авторизации
 */
async function authenticateWithTelegram(authData) {
    try {
        const response = await fetch('/auth/telegram-webview', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(authData),
            credentials: 'include'
        });
        
        let result;
        try {
            result = await response.json();
        } catch (parseError) {
            throw new Error('Ошибка формата ответа сервера');
        }
        
        if (response.ok && result.success) {
            
            // Закрываем все модалки
            const event = new CustomEvent('close-all-modals');
            window.dispatchEvent(event);
            
            // Перенаправляем на главную - withSuccess покажется автоматически
            setTimeout(() => {
                if (result.redirect) {
                    window.location.href = result.redirect;
                } else {
                    window.location.reload();
                }
            }, 500);
            
        } else if (result.show_currency_modal) {
            
            // Открываем модалку для выбора валюты для нового пользователя
            if (window.openTelegramAuthModal) {
                window.openTelegramAuthModal(result.user_data || authData);
            } else {
                showTelegramMessage('Ошибка интерфейса', 'error');
            }
            
        } else {
            showTelegramMessage(result.message || 'Ошибка авторизации', 'error');
        }
        
    } catch (error) {
        if (window.showTelegramNotification) {
            window.showTelegramNotification('Ошибка соединения с сервером', 'error');
        }
    }
}

/**
 * Показ уведомления
 */
function showTelegramMessage(message, type = 'info') {
    // Используем Telegram WebApp haptic feedback если доступен
    if (window.Telegram && window.Telegram.WebApp) {
        const tg = window.Telegram.WebApp;
        if (type === 'success') {
            tg.HapticFeedback?.notificationOccurred('success');
        } else if (type === 'error') {
            tg.HapticFeedback?.notificationOccurred('error');
        }
    }
    
    // Создаем простое уведомление
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: bold;
        z-index: 10000;
        max-width: 90%;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Убираем через 3 секунды
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Экспорт функций для глобального использования
window.initTelegramAuth = initTelegramAuth;
window.authenticateWithTelegram = authenticateWithTelegram;
window.showTelegramMessage = showTelegramMessage;