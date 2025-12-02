<x-layouts.app>

    <!-- Game Container -->
    <div class="iframe-container">
        <!-- Back Button -->
        <a href="{{ route('slots.lobby') }}" class="game-back-button" title="Вернуться в лобби">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        
        <div class="game-preloader" id="gamePreloader">
            <div class="game-loading-content">
                <div class="game-spinner-container">
                    <div class="game-spinner"></div>
                </div>
                <p class="game-loading-text">Loading game...</p>
            </div>
        </div>
        <iframe id="gameIframe" 
                src="about:blank" 
                allow="autoplay; fullscreen; payment; microphone; camera"
                allowfullscreen
                loading="eager"
                importance="high"
                fetchpriority="high"></iframe>

        <!-- Error State -->
        <div id="errorState" class="game-error-state game-hidden">
            <div class="error-content">
                <i class="fas fa-exclamation-circle"></i>
                <h3>Loading Error</h3>
                <p>Failed to load game</p>
                <button onclick="retryGame()" class="retry-btn">Try Again</button>
            </div>
        </div>
    </div>

    <script>
        // Game variables (используем var для совместимости с Livewire навигацией)
        var gameName = '{{ $game->name }}';
        var gameUrl = '{!! addslashes($gameUrl) !!}';
        var realGameUrl = `/slots/play/${encodeURIComponent(gameName)}`;
        var demoGameUrl = `/slots/fun/${encodeURIComponent(gameName)}`;
        var isDemoMode = window.location.pathname.includes('/slots/fun/');

        // Проверка Telegram WebView для оптимизации
        var isTelegramWebView = window.Telegram && window.Telegram.WebApp && window.Telegram.WebApp.initData;

        // Отслеживание фактической отрисовки контента в iframe
        var gameContentDetected = false;
        
        window.checkIframeContent = function() {
            var iframe = document.getElementById('gameIframe');
            if (!iframe || gameContentDetected) return;
            
            try {
                var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                // Проверяем наличие canvas (игры обычно рендерятся в canvas)
                var canvas = iframeDoc.querySelector('canvas');
                
                // Или проверяем что body не пустой
                var hasContent = iframeDoc.body && 
                    (iframeDoc.body.children.length > 0 || iframeDoc.body.textContent.trim().length > 0);
                
                if (canvas || hasContent) {
                    gameContentDetected = true;
                    console.log('🎮 Контент игры обнаружен в iframe');
                    window.hideGamePreloader();
                    return true;
                }
            } catch (e) {
                // Cross-origin iframe - не можем проверить содержимое
                // В этом случае полагаемся на таймер
                console.log('⚠️ Невозможно проверить содержимое iframe (cross-origin)');
            }
            return false;
        };
        
        // Глобальная функция для скрытия прелоадера игры
        window.hideGamePreloader = function() {
            var preloader = document.getElementById('gamePreloader');
            
            if (preloader && !preloader.classList.contains('game-hidden')) {
                console.log('Скрываем прелоадер игры...');
                
                // Быстрое исчезновение
                preloader.style.opacity = '0';
                preloader.style.transform = 'scale(0.95)';
                preloader.style.visibility = 'hidden';
                
                setTimeout(function() {
                    preloader.style.display = 'none';
                    preloader.classList.add('game-hidden');
                    adjustIframeSize();
                    console.log('✅ Прелоадер игры скрыт, игра отображается');
                }, 200);
            }
        };

        // Initialize
        function initGame() {
            updateModeUI();
            setupEventListeners();
            adjustIframeSize();
            
            // Сбрасываем и показываем прелоадер игры
            var preloader = document.getElementById('gamePreloader');
            var iframe = document.getElementById('gameIframe');
            
            if (preloader) {
                preloader.classList.remove('game-hidden');
                preloader.style.display = 'flex';
                preloader.style.opacity = '1';
                preloader.style.visibility = 'visible';
                preloader.style.transform = 'scale(1)';
                console.log('🔄 Прелоадер игры показан');
            }
            
            if (iframe) {
                iframe.style.display = 'none';
                iframe.style.opacity = '0';
                iframe.src = 'about:blank'; // Сбрасываем src для новой загрузки
            }
            
            // В Telegram WebView загружаем игру немедленно
            if (isTelegramWebView) {
                loadGame();
            } else {
                // В обычном браузере даем время на инициализацию
                setTimeout(loadGame, 50);
            }
        }

        // Инициализация при загрузке DOM и при навигации Livewire
        document.addEventListener('DOMContentLoaded', initGame);
        document.addEventListener('livewire:navigated', initGame);

        // Сохраняем URL игры в localStorage
        localStorage.setItem('gameUrl', gameUrl);

        function loadGame() {
            var iframe = document.getElementById('gameIframe');
            console.log('loadGame вызвана. gameUrl:', gameUrl);
            console.log('iframe найден:', !!iframe);
            console.log('текущий src:', iframe ? iframe.src : 'iframe не найден');
            
            if (gameUrl && iframe && iframe.src === 'about:blank') {
                // Таймер на случай если iframe не загрузится
                var timeout = isTelegramWebView ? 3000 : 5000; // 3 сек для Telegram, 5 для браузера
                var loadTimeout = setTimeout(function() {
                    console.log('⏱️ Таймаут загрузки игры - скрываем прелоадер принудительно');
                    window.hideGamePreloader();
                }, timeout);
                
                // Добавляем обработчик onload к iframe
                iframe.onload = function() {
                    clearTimeout(loadTimeout);
                    console.log('📄 Iframe onload сработал');
                    
                    // Для Telegram: показываем iframe СРАЗУ, чтобы увидеть прелоадер провайдера
                    // Наш прелоадер остается поверх (z-index: 9999)
                    if (isTelegramWebView) {
                        iframe.style.display = 'block';
                        iframe.style.opacity = '1';
                        console.log('📺 Iframe показан в Telegram (прелоадер провайдера должен быть виден)');
                    }
                    
                    // Пытаемся проверить содержимое iframe
                    var contentFound = window.checkIframeContent();
                    
                    if (!contentFound) {
                        // Если не можем проверить содержимое (cross-origin),
                        // скрываем прелоадер быстро
                        var hideDelay = isTelegramWebView ? 1500 : 800; // 1.5 сек для Telegram, 0.8 сек для браузера
                        
                        setTimeout(function() {
                            console.log('⏱️ Скрываем прелоадер (прошло время)');
                            window.hideGamePreloader();
                        }, hideDelay);
                    }
                    
                    // Для обычного браузера: показываем iframe с небольшой задержкой
                    if (!isTelegramWebView) {
                        setTimeout(function() {
                            iframe.style.display = 'block';
                            iframe.style.opacity = '1';
                        }, 200);
                    }
                };
                
                // Добавляем обработчик ошибок
                iframe.onerror = function() {
                    clearTimeout(loadTimeout);
                    console.error('Ошибка загрузки игры');
                    window.hideGamePreloader();
                };
                
                // Настройки iframe для оптимизации
                if (isTelegramWebView) {
                    iframe.setAttribute('loading', 'eager');
                    iframe.setAttribute('importance', 'high');
                }
                
                iframe.src = gameUrl;
                console.log('Загружается игра:', gameUrl);
            } else if (!gameUrl) {
                console.error('gameUrl не определен');
                window.hideGamePreloader();
            } else if (!iframe) {
                console.error('iframe не найден');
            } else {
                console.log('iframe уже имеет src:', iframe.src);
                // Если iframe уже загружен, скрываем прелоадер
                window.hideGamePreloader();
            }
        }

        function adjustIframeSize() {
            var container = document.querySelector('.iframe-container');
            var iframe = document.getElementById('gameIframe');
            var windowHeight = window.innerHeight;
            var headerHeight = 80; // Новая высота шапки 80px

            var containerHeight = windowHeight - headerHeight;
            container.style.top = headerHeight + 'px';
            container.style.height = containerHeight + 'px';
            iframe.style.height = containerHeight + 'px';
        }

        // Update mode UI
        function updateModeUI() {
            const realBtn = document.querySelector('.real-mode-btn');
            const demoBtn = document.querySelector('.demo-mode-btn');

            // Проверяем наличие кнопок
            if (!realBtn || !demoBtn) {
                return;
            }

            // Reset classes
            realBtn.classList.remove('active');
            demoBtn.classList.remove('active');

            if (isDemoMode) {
                demoBtn.classList.add('active');
            } else {
                realBtn.classList.add('active');
            }
        }

        // Switch game mode
        function switchGameMode(mode) {
            if ((mode === 'demo' && isDemoMode) || (mode === 'real' && !isDemoMode)) {
                return; // Already in this mode
            }

            isDemoMode = mode === 'demo';
            const newUrl = isDemoMode ? demoGameUrl : realGameUrl;

            // Show loading
            showLoader();

            setTimeout(() => {
                window.location.href = newUrl;
            }, 200);
        }

        // Show loader
        function showLoader() {
            const preloader = document.getElementById('gamePreloader');
            const iframe = document.getElementById('gameIframe');
            
            if (preloader) {
                preloader.classList.remove('game-hidden');
                preloader.style.display = 'flex';
                preloader.style.opacity = '1';
                preloader.style.visibility = 'visible';
                preloader.style.transform = 'scale(1)';
                
                if (iframe) {
                    iframe.style.opacity = '0';
                }
            }
        }

        // Fullscreen toggle
        function toggleFullscreen() {
            const iframe = document.getElementById('gameIframe');

            if (!document.fullscreenElement) {
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else if (iframe.mozRequestFullScreen) {
                    iframe.mozRequestFullScreen();
                } else if (iframe.webkitRequestFullscreen) {
                    iframe.webkitRequestFullscreen();
                } else if (iframe.msRequestFullscreen) {
                    iframe.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }

        // Retry game on error
        function retryGame() {
            const errorState = document.getElementById('errorState');
            const iframe = document.getElementById('gameIframe');

            errorState.classList.add('game-hidden');
            showLoader();

            // Reload iframe
            iframe.src = iframe.src;
        }

        // Setup event listeners
        function setupEventListeners() {
            const iframe = document.getElementById('gameIframe');
            const realBtn = document.querySelector('.real-mode-btn');
            const demoBtn = document.querySelector('.demo-mode-btn');

            // Mode button handlers (если кнопки существуют)
            if (realBtn) realBtn.onclick = () => switchGameMode('real');
            if (demoBtn) demoBtn.onclick = () => switchGameMode('demo');

            // Handle iframe load errors
            if (iframe) {
                iframe.addEventListener('error', function() {
                    const preloader = document.getElementById('gamePreloader');
                    const errorState = document.getElementById('errorState');
                    if (preloader) preloader.style.display = 'none';
                    if (errorState) errorState.classList.remove('game-hidden');
                });

                // Добавляем таймаут для загрузки
                iframe.addEventListener('load', function() {
                    console.log('Iframe загружен успешно');
                });
            }

            // Fullscreen change handler
            document.addEventListener('fullscreenchange', function() {
                const fullscreenBtn = document.querySelector('[onclick="toggleFullscreen()"] i');
                if (fullscreenBtn) {
                    fullscreenBtn.className = document.fullscreenElement ? 'fas fa-compress' : 'fas fa-expand';
                }
            });

            // Handle postMessage from game
            window.addEventListener('message', function(event) {
                if (event.data === 'closeGame' || event.data === 'close' || event.data === 'GAME_MODE:LOBBY') {
                    window.history.back();
                }
            });
        }

        // Обработчик события popstate (нажатие кнопки "назад")
        window.addEventListener('popstate', function(event) {
            loadGame();
        });

        // Предотвращаем стандартное поведение браузера при нажатии кнопки "назад"
        history.pushState(null, null, location.href);
        window.onpopstate = function(event) {
            history.go(1);
        };

        window.addEventListener('load', adjustIframeSize);
        window.addEventListener('resize', adjustIframeSize);
        window.addEventListener('orientationchange', adjustIframeSize);
    </script>

    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            /* Отключаем overscroll для Telegram WebView */
            overscroll-behavior: none;
            overscroll-behavior-y: none;
            -webkit-overscroll-behavior: none;
            -webkit-overscroll-behavior-y: none;
        }
        
        /* Telegram WebView специфичные стили */
        html.telegram-webapp,
        html.telegram-webapp body {
            overscroll-behavior: none !important;
            overscroll-behavior-y: none !important;
            -webkit-overscroll-behavior: none !important;
            touch-action: pan-y !important;
        }
        
        /* Предотвращаем закрытие по свайпу */
        html.telegram-webapp .iframe-container,
        html.telegram-webapp iframe {
            touch-action: pan-y !important;
            overscroll-behavior: none !important;
            overscroll-behavior-y: none !important;
        }
        /* Back Button */
        .game-back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 48px;
            height: 48px;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            z-index: 10000;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            text-decoration: none;
        }
        
        .game-back-button svg {
            width: 24px;
            height: 24px;
        }
        
        .game-back-button:hover {
            background: rgba(59, 130, 246, 0.9);
            border-color: rgba(59, 130, 246, 0.5);
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
        
        .game-back-button:active {
            transform: scale(0.95);
        }

        .provider-tag {
            font-size: 0.75rem;
            color: #6B7280;
            background: rgba(75, 85, 99, 0.5);
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            margin-left: 0.75rem;
        }

        .game-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .mode-toggle {
            display: flex;
            background: #374151;
            border-radius: 0.5rem;
            padding: 0.25rem;
            gap: 0.25rem;
        }

        .mode-toggle button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            color: #9CA3AF;
            background: transparent;
        }

        .mode-toggle button.active {
            color: #000000;
            background: #F59E0B;
        }

        .mode-toggle .demo-mode-btn.active {
            color: #ffffff;
            background: #3B82F6;
        }

        .control-btn {
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 0.5rem;
            color: #9CA3AF;
            cursor: pointer;
            transition: all 0.2s;
        }

        .control-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.2);
        }

        /* Game Container */
        .iframe-container {
            position: fixed;
            top: 80px;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: calc(100vh - 80px);
            background-color: #131a28;
            overflow: hidden;
            z-index: 1;
            /* Оптимизация для Telegram WebView */
            -webkit-overflow-scrolling: touch;
            transform: translateZ(0);
            will-change: transform;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
            opacity: 0;
            transition: opacity 0.3s ease-in;
            /* Оптимизация рендеринга */
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }
        
        /* Мгновенный переход для Telegram WebView */
        html.telegram-webapp iframe {
            transition: none;
        }

        /* Game Loading */
        .game-preloader {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 20, 25, 0.95); /* Полупрозрачный фон */
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s ease, transform 0.5s ease, visibility 0.5s ease;
            z-index: 9999; /* Поверх iframe */
            transform: scale(1);
            pointer-events: none; /* Не блокирует клики на iframe */
        }
        
        .game-preloader > * {
            pointer-events: auto; /* Возвращаем клики для содержимого */
        }
        
        /* Мгновенное скрытие для Telegram WebView */
        html.telegram-webapp .game-preloader {
            transition: none;
        }
        
        .game-preloader.game-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .game-loading-content {
            text-align: center;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .game-spinner-container {
            position: relative;
            margin-bottom: 2rem;
        }

        .game-spinner {
            width: 4rem;
            height: 4rem;
            border: 4px solid rgba(59, 130, 246, 0.1);
            border-top: 4px solid #3B82F6;
            border-radius: 50%;
            animation: game-spin 1.2s linear infinite;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }

        .game-spinner-container::before {
            content: '';
            position: absolute;
            top: -8px;
            left: -8px;
            right: -8px;
            bottom: -8px;
            border: 2px solid transparent;
            border-top: 2px solid rgba(255, 179, 0, 0.6);
            border-radius: 50%;
            animation: game-spin-reverse 2s linear infinite;
        }

        .game-loading-text {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 500;
            color: #ffffff;
            opacity: 0.9;
            letter-spacing: 0.5px;
            animation: game-pulse 2s ease-in-out infinite;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        @keyframes game-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes game-spin-reverse {
            0% { transform: rotate(360deg); }
            100% { transform: rotate(0deg); }
        }

        @keyframes game-pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        /* Error State */
        .game-error-state {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #131a28;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-content {
            text-align: center;
            color: #9CA3AF;
        }

        .error-content i {
            font-size: 3rem;
            color: #EF4444;
            margin-bottom: 1rem;
        }

        .error-content h3 {
            color: #ffffff;
            font-weight: 500;
            margin: 0 0 0.5rem 0;
        }

        .error-content p {
            margin: 0 0 1.5rem 0;
            font-size: 0.875rem;
        }

        .retry-btn {
            padding: 0.5rem 1rem;
            background: #3B82F6;
            border: none;
            border-radius: 0.5rem;
            color: #ffffff;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .retry-btn:hover {
            background: #2563EB;
        }

        .game-hidden {
            display: none;
        }

        /* Mobile Styles */
            .game-back-button {
                top: 15px;
                left: 15px;
                width: 44px;
                height: 44px;
            }
            
            .game-back-button svg {
                width: 22px;
                height: 22px;
            }
        
            .iframe-container {
                top: 80px;
                height: calc(100vh - 80px);
            }

            .game-preloader {
                width: 100vw;
                height: 100vh;
            }

            .game-spinner {
                width: 3.5rem;
                height: 3.5rem;
                border-width: 3px;
            }

            .game-spinner-container::before {
                top: -6px;
                left: -6px;
                right: -6px;
                bottom: -6px;
                border-width: 2px;
            }

            .game-loading-text {
                font-size: 1rem;
            }

            .footer {
                display: none;
            }

            .game-header,
            .provider-tag,
            .mode-toggle,
            .control-btn {
                display: none;
            }
        

            .game-back-button {
                top: 12px;
                left: 12px;
                width: 40px;
                height: 40px;
            }
            
            .game-back-button svg {
                width: 20px;
                height: 20px;
            }
        
            .iframe-container {
                top: 80px;
                height: calc(100vh - 80px);
            }

            .game-spinner {
                width: 3rem;
                height: 3rem;
                border-width: 3px;
            }

            .game-spinner-container {
                margin-bottom: 1.5rem;
            }

            .game-spinner-container::before {
                top: -5px;
                left: -5px;
                right: -5px;
                bottom: -5px;
            }

            .game-loading-text {
                font-size: 0.875rem;
                letter-spacing: 0.25px;
            }
        

        #mb-menu {
            display: none!important;
        }
    </style>
</x-layouts.app>
