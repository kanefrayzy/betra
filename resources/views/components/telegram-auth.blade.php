{{-- Компонент для интеграции Telegram WebView авторизации --}}

{{-- Глобальная функция проверки Telegram WebApp --}}
<script>
    // КРИТИЧНО: Проверяем реальное окружение Telegram
    window.isTelegramWebApp = function() {
        // Проверяем наличие специфичных параметров Telegram
        const hasParams = window.location.search.includes('tgWebAppPlatform') 
            || window.location.hash.includes('tgWebAppData')
            || document.referrer.includes('telegram.org');
        
        // Проверяем наличие Telegram API
        const hasTelegramAPI = window.Telegram && 
                              window.Telegram.WebApp && 
                              window.Telegram.WebApp.initData && 
                              window.Telegram.WebApp.initData.length > 0;
        
        return hasParams && hasTelegramAPI;
    };
</script>

{{-- Подключаем скрипт Telegram WebApp ТОЛЬКО если мы в Telegram --}}
<script>
    // Загружаем скрипт только если есть признаки Telegram окружения
    const needsTelegramScript = window.location.search.includes('tgWebAppPlatform') 
        || window.location.hash.includes('tgWebAppData')
        || document.referrer.includes('telegram.org');
    
    if (needsTelegramScript) {
        const script = document.createElement('script');
        script.src = 'https://telegram.org/js/telegram-web-app.js';
        script.async = true;
        script.onload = function() {
            console.log('✅ Telegram WebApp script loaded');
        };
        document.head.appendChild(script);
    }
</script>

{{-- Инициализация Telegram WebApp ТОЛЬКО в Telegram окружении --}}
<script>
    // Функция для настройки Telegram WebApp
    window.initTelegramWebApp = function() {
        // КРИТИЧНО: Пропускаем если не в реальном Telegram окружении
        if (typeof window.isTelegramWebApp !== 'function' || !window.isTelegramWebApp()) {
            return false;
        }
        
        if (!window.Telegram || !window.Telegram.WebApp) {
            return false;
        }
        
        const tg = window.Telegram.WebApp;
        
        // Разворачиваем на весь экран
        if (typeof tg.expand === 'function') {
            tg.expand();
            console.log('✅ WebApp развернут');
        }
        
        // Включаем полноэкранный режим если доступно
        if (typeof tg.requestFullscreen === 'function') {
            tg.requestFullscreen();
            console.log('✅ Полноэкранный режим запрошен');
        }
        
        // Применяем ТОЛЬКО contain для предотвращения закрытия (не none!)
        document.documentElement.style.overscrollBehaviorY = 'contain';
        document.body.style.overscrollBehaviorY = 'contain';
        
        console.log('✅ Overscroll contain применен (скролл работает)');
        
        // Делаем WebApp готовым ПОСЛЕ применения стилей
        if (typeof tg.ready === 'function') {
            tg.ready();
            console.log('✅ Telegram WebApp готов');
        }
        
        console.log('✅ Telegram WebApp настроен, скролл работает полностью');
        
        return true;
    };
    
    // КРИТИЧНО: Инициализируем ДО события load ТОЛЬКО если в Telegram
    (function() {
        // Пропускаем инициализацию если не в Telegram
        const needsInit = window.location.search.includes('tgWebAppPlatform') 
            || window.location.hash.includes('tgWebAppData')
            || document.referrer.includes('telegram.org');
        
        if (!needsInit) {
            console.log('ℹ️ Не в Telegram окружении - пропускаем инициализацию');
            return;
        }
        
        // Проверяем каждые 50ms до загрузки API
        let initAttempts = 0;
        const maxInitAttempts = 40; // 2 секунды максимум
        
        const tryInit = function() {
            if (window.Telegram && window.Telegram.WebApp && window.Telegram.WebApp.initData) {
                window.initTelegramWebApp();
                console.log('✅ Telegram WebApp инициализирован при первом открытии');
            } else if (initAttempts < maxInitAttempts) {
                initAttempts++;
                setTimeout(tryInit, 50);
            } else {
                console.warn('⚠️ Timeout при инициализации Telegram WebApp');
            }
        };
        
        // Начинаем попытки сразу
        tryInit();
    })();
    
    // Слушаем событие загрузки скрипта Telegram
    window.addEventListener('load', function() {
        // Пропускаем если не в Telegram
        if (typeof window.isTelegramWebApp !== 'function' || !window.isTelegramWebApp()) {
            return;
        }
        
        if (!window.initTelegramWebApp()) {
            // Если не удалось, пробуем через небольшой интервал
            let attempts = 0;
            const maxAttempts = 10;
            const checkInterval = setInterval(function() {
                attempts++;
                if (window.initTelegramWebApp() || attempts >= maxAttempts) {
                    clearInterval(checkInterval);
                    if (attempts >= maxAttempts) {
                        console.warn('⚠️ Не удалось инициализировать Telegram WebApp');
                    }
                }
            }, 100);
        }
    });
    
    // При навигации Livewire - ТОЛЬКО если в Telegram
    document.addEventListener('livewire:navigated', function() {
        if (typeof window.isTelegramWebApp === 'function' && window.isTelegramWebApp()) {
            console.log('Livewire навигация - переинициализация Telegram WebApp');
            window.initTelegramWebApp();
        }
    });
    
    // При изменении истории (для SPA) - ТОЛЬКО если в Telegram
    window.addEventListener('popstate', function() {
        if (typeof window.isTelegramWebApp === 'function' && window.isTelegramWebApp()) {
            console.log('Popstate - переинициализация Telegram WebApp');
            setTimeout(function() {
                window.initTelegramWebApp();
            }, 100);
        }
    });
</script>

{{-- Стили для Telegram WebApp --}}
    <style>
        /* Базовые стили для Telegram WebApp */
        body.telegram-webapp {
            background: var(--tg-theme-bg-color, #0f1419) !important;
            color: var(--tg-theme-text-color, #ffffff) !important;
            margin: 0;
            padding: 0;
        }
        
        html.telegram-webapp {
            /* Только предотвращаем overscroll, не блокируем сам скролл */
            overscroll-behavior-y: contain;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Скрываем элементы которые не нужны в WebApp */
        .telegram-webapp .navbar-brand,
        .telegram-webapp .main-navigation,
        .telegram-webapp .header-nav,
        .telegram-webapp .footer,
        .telegram-webapp .download-app {
            display: none !important;
        }
        
        /* Индикатор загрузки */
        .telegram-auth-loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.9);
            color: white;
            padding: 24px;
            border-radius: 12px;
            z-index: 10000;
            text-align: center;
            display: none;
            backdrop-filter: blur(10px);
        }
        
        .telegram-auth-loading.show {
            display: block;
        }
        
        .telegram-loading-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid #ffffff20;
            border-top: 3px solid #ffffff;
            border-radius: 50%;
            animation: telegram-spin 1s linear infinite;
            margin: 0 auto 16px;
        }
        
        @keyframes telegram-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Уведомления */
        .telegram-notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 16px 24px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            z-index: 10001;
            max-width: 90%;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
            transition: all 0.3s ease;
        }
        
        .telegram-notification.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        
        .telegram-notification.success {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }
        
        .telegram-notification.error {
            background: linear-gradient(135deg, #f44336, #d32f2f);
        }
        
        .telegram-notification.info {
            background: linear-gradient(135deg, #2196F3, #1976d2);
        }
    </style>
    
    {{-- HTML элементы --}}
    <div id="telegramAuthLoading" class="telegram-auth-loading">
        <div class="telegram-loading-spinner"></div>
        <div>{{ __('Авторизация через Telegram...') }}</div>
    </div>
    
    {{-- Утилиты для работы с Telegram UI --}}
    <script data-navigate-once>
        // Показать/скрыть загрузку
        if (typeof window.showTelegramLoading === 'undefined') {
            window.showTelegramLoading = function(show = true) {
                const loading = document.getElementById('telegramAuthLoading');
                if (loading) {
                    loading.classList.toggle('show', show);
                }
            };
        }
        
        // Уведомления
        if (typeof window.showTelegramNotification === 'undefined') {
            window.showTelegramNotification = function(message, type = 'info', duration = 3000) {
                // Используем haptic feedback если в Telegram
                if (window.Telegram?.WebApp?.HapticFeedback) {
                    const tg = window.Telegram.WebApp;
                    if (type === 'success') {
                        tg.HapticFeedback.notificationOccurred('success');
                    } else if (type === 'error') {
                        tg.HapticFeedback.notificationOccurred('error');
                    }
                }
                
                // Удаляем предыдущие уведомления
                document.querySelectorAll('.telegram-notification').forEach(n => n.remove());
                
                // Создаем новое уведомление
                const notification = document.createElement('div');
                notification.className = `telegram-notification ${type}`;
                notification.textContent = message;
                document.body.appendChild(notification);
                
                setTimeout(() => notification.classList.add('show'), 100);
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 300);
                }, duration);
            };
        }
    </script>