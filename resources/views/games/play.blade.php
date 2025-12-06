<x-layouts.app>
    <div class="min-h-screen">
        <div class="relative flex justify-center items-start p-4 play-game-wrapper">
            <div class="bg-gray-800 overflow-hidden rounded-lg w-full max-w-5xl shadow-2xl">
                <div class="relative w-full play-game-container">
                    <!-- Loading Overlay -->
                    <div id="playGameLoader" class="play-game-preloader">
                        <div class="text-center">
                            <div class="w-12 h-12 border-4 border-gray-600 border-t-blue-500 rounded-full animate-spin mb-4"></div>
                        </div>
                    </div>

                    <!-- Game Iframe - immediate load -->
                    <iframe id="playGameFrame"
                            src="{!! addslashes($gameUrl) !!}"
                            class="border-0 rounded-lg"
                            allow="fullscreen; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            loading="eager"
                            importance="high"
                            style="opacity: 0; display: none;">
                    </iframe>

                    <!-- Error State -->
                    <div id="playGameError" class="play-game-error hidden">
                        <div class="text-center">
                            <i class="fas fa-exclamation-circle text-3xl text-red-500 mb-3"></i>
                            <h3 class="text-white font-medium mb-2">Ошибка загрузки</h3>
                            <p class="text-gray-400 text-sm mb-4">Не удалось загрузить игру</p>
                            <button onclick="retryPlayGame()"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition-colors">
                                Попробовать снова
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Game Footer Controls -->
                <div class="bg-gray-800 border-t border-gray-700 px-4 py-3 rounded-b-lg">
                    <div class="flex items-center justify-between">
                        <!-- Left Controls -->
                        <div class="flex items-center space-x-3">
                            <button onclick="togglePlayGameFullscreen()"
                                    class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200"
                                    title="На весь экран">
                                <i id="playGameFullscreenIcon" class="fas fa-expand text-sm"></i>
                            </button>
                        </div>

                        <!-- Right Info -->
                        <div class="flex items-center space-x-4 text-sm text-gray-400">
                            <span>{{ $game->name ?? 'Game' }}</span>
                            <span class="text-gray-600">•</span>
                            <span>{{ $game->provider ?? 'Provider' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Game wrapper */
        .play-game-wrapper {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }
        
        /* Game container with aspect ratio */
        .play-game-container {
            width: 100%;
            position: relative;
            overflow: hidden;
            background: #1a1f26;
        }
        
        /* Create 16:9 aspect ratio */
        .play-game-container::before {
            content: '';
            display: block;
            padding-top: 56.25%;
        }
        
        /* Position iframe and overlays absolutely */
        .play-game-container iframe,
        .play-game-container .play-game-preloader,
        .play-game-container .play-game-error {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        
        /* Preloader styles */
        .play-game-preloader {
            background: linear-gradient(135deg, #1a2332 0%, #0f1419 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        .play-game-preloader.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
        
        /* Error styles */
        .play-game-error {
            background: #131a28;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20;
        }
        
        .play-game-error.hidden {
            display: none;
        }
        
        /* Iframe transitions */
        .play-game-container iframe {
            transition: opacity 0.3s ease-in;
        }
        
        /* Mobile portrait */
        @media (max-width: 767px) and (orientation: portrait) {
            .play-game-wrapper {
                padding: 0.5rem;
            }
            
            .bg-gray-800.border-t {
                padding: 0.75rem 0.5rem;
            }
            
            .flex.items-center.justify-between {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
        
        /* Mobile landscape */
        @media (max-width: 767px) and (orientation: landscape) {
            .play-game-wrapper {
                padding: 0.5rem;
            }
        }
    </style>

    <script>
        // Game variables
        var playGameUrl = '{!! addslashes($gameUrl) !!}';
        var playGameLoaded = false;

        // Глобальная функция для скрытия прелоадера
        window.hidePlayGameLoader = function() {
            var preloader = document.getElementById('playGameLoader');
            var iframe = document.getElementById('playGameFrame');
            
            if (preloader && !playGameLoaded) {
                playGameLoaded = true;
                
                preloader.style.opacity = '0';
                preloader.style.visibility = 'hidden';
                
                setTimeout(function() {
                    preloader.classList.add('hidden');
                    if (iframe) {
                        iframe.style.display = 'block';
                        iframe.style.opacity = '1';
                    }
                }, 500);
            }
        };

        // Initialize game
        function initPlayGame() {
            var preloader = document.getElementById('playGameLoader');
            var iframe = document.getElementById('playGameFrame');
            
            // Сбрасываем состояние загрузки
            playGameLoaded = false;
            
            if (preloader) {
                preloader.classList.remove('hidden');
                preloader.style.opacity = '1';
                preloader.style.visibility = 'visible';
            }
            
            if (iframe) {
                iframe.style.display = 'none';
                iframe.style.opacity = '0';
                
                // Если iframe уже загружен, сбрасываем его
                if (iframe.src !== 'about:blank') {
                    iframe.src = 'about:blank';
                }
            }
            
            loadPlayGame();
        }

        // Load game
        function loadPlayGame() {
            var iframe = document.getElementById('playGameFrame');
            
            if (!playGameUrl || !iframe) {
                window.hidePlayGameLoader();
                return;
            }
            
            if (iframe.src !== 'about:blank') {
                window.hidePlayGameLoader();
                return;
            }
            
            
            // Таймаут на случай если iframe не загрузится
            var loadTimeout = setTimeout(function() {
                window.hidePlayGameLoader();
            }, 10000);
            
            // Обработчик загрузки
            iframe.onload = function() {
                clearTimeout(loadTimeout);
                window.hidePlayGameLoader();
            };
            
            // Обработчик ошибок
            iframe.onerror = function() {
                clearTimeout(loadTimeout);
                console.error('Ошибка загрузки игры');
                document.getElementById('playGameLoader').classList.add('hidden');
                document.getElementById('playGameError').classList.remove('hidden');
            };
            
            // Загружаем игру
            iframe.src = playGameUrl;
        }

        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', initPlayGame);
        
        // Initialize on Livewire navigation
        document.addEventListener('livewire:navigated', initPlayGame);

        // Fullscreen toggle
        function togglePlayGameFullscreen() {
            var iframe = document.getElementById('playGameFrame');
            var icon = document.getElementById('playGameFullscreenIcon');
            
            if (!document.fullscreenElement) {
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else if (iframe.webkitRequestFullscreen) {
                    iframe.webkitRequestFullscreen();
                } else if (iframe.msRequestFullscreen) {
                    iframe.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }

        // Update fullscreen icon
        document.addEventListener('fullscreenchange', function() {
            var icon = document.getElementById('playGameFullscreenIcon');
            if (icon) {
                icon.className = document.fullscreenElement ? 'fas fa-compress text-sm' : 'fas fa-expand text-sm';
            }
        });

        // Retry loading game
        function retryPlayGame() {
            var iframe = document.getElementById('playGameFrame');
            var errorState = document.getElementById('playGameError');
            var loader = document.getElementById('playGameLoader');
            
            errorState.classList.add('hidden');
            loader.classList.remove('hidden');
            loader.style.opacity = '1';
            loader.style.visibility = 'visible';
            playGameLoaded = false;
            
            iframe.src = 'about:blank';
            setTimeout(function() {
                loadPlayGame();
            }, 100);
        }

        // Handle game messages
        window.addEventListener('message', function(event) {
            if (event.data === 'closeGame' || event.data === 'close' || event.data === 'GAME_MODE:LOBBY') {
                window.location.href = '{{ route("slots.lobby") }}';
            }
        });
    </script>
</x-layouts.app>
