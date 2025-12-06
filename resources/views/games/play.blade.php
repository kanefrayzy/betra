<x-layouts.app>
    <div class="min-h-screen" x-data="gamePlayer" @turbo:before-cache.window="cleanup()">
        <div class="relative flex justify-center items-start p-4 play-game-wrapper">
            <div class="bg-gray-800 overflow-hidden rounded-lg w-full max-w-5xl shadow-2xl">
                <div class="relative w-full play-game-container">
                    <!-- Loading Overlay -->
                    <div x-show="loading" 
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="play-game-preloader">
                        <div class="text-center">
                            <div class="w-12 h-12 border-4 border-gray-600 border-t-blue-500 rounded-full animate-spin mb-4"></div>
                        </div>
                    </div>

                    <!-- Game Iframe -->
                    <iframe x-ref="iframe"
                            class="border-0 rounded-lg"
                            allow="fullscreen; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            loading="eager"
                            fetchpriority="high"
                            x-bind:style="loading ? 'opacity: 0; display: none;' : 'opacity: 1; display: block;'"
                            @load="hideLoader()">
                    </iframe>

                    <!-- Error State -->
                    <div x-show="error" 
                         x-cloak
                         class="play-game-error">
                        <div class="text-center">
                            <i class="fas fa-exclamation-circle text-3xl text-red-500 mb-3"></i>
                            <h3 class="text-white font-medium mb-2">Ошибка загрузки</h3>
                            <p class="text-gray-400 text-sm mb-4">Не удалось загрузить игру</p>
                            <button @click="retry()"
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
                            <button @click="toggleFullscreen()"
                                    class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded transition-all duration-200"
                                    title="На весь экран">
                                <i class="fas" :class="fullscreen ? 'fa-compress' : 'fa-expand'" class="text-sm"></i>
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
        document.addEventListener('alpine:init', () => {
            Alpine.data('gamePlayer', () => ({
                loading: true,
                error: false,
                fullscreen: false,
                gameUrl: '{!! addslashes($gameUrl) !!}',
                loadTimeout: null,
                
                init() {
                    // Загружаем игру сразу при инициализации
                    this.$nextTick(() => {
                        this.loadGame();
                    });
                    
                    // Слушаем сообщения от игры
                    window.addEventListener('message', (event) => {
                        if (event.data === 'closeGame' || event.data === 'close' || event.data === 'GAME_MODE:LOBBY') {
                            window.location.href = '{{ route("slots.lobby") }}';
                        }
                    });
                    
                    // Отслеживаем fullscreen
                    document.addEventListener('fullscreenchange', () => {
                        this.fullscreen = !!document.fullscreenElement;
                    });
                },
                
                loadGame() {
                    if (!this.gameUrl || !this.$refs.iframe) {
                        this.loading = false;
                        this.error = true;
                        return;
                    }
                    
                    // Устанавливаем таймаут
                    this.loadTimeout = setTimeout(() => {
                        this.loading = false;
                    }, 8000);
                    
                    // Загружаем URL
                    this.$refs.iframe.src = this.gameUrl;
                },
                
                hideLoader() {
                    if (this.loadTimeout) {
                        clearTimeout(this.loadTimeout);
                    }
                    this.loading = false;
                },
                
                retry() {
                    this.error = false;
                    this.loading = true;
                    this.$refs.iframe.src = 'about:blank';
                    setTimeout(() => {
                        this.loadGame();
                    }, 100);
                },
                
                toggleFullscreen() {
                    const iframe = this.$refs.iframe;
                    
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
                },
                
                cleanup() {
                    // Очистка перед кешированием Turbo
                    if (this.loadTimeout) {
                        clearTimeout(this.loadTimeout);
                    }
                }
            }));
        });
    </script>
</x-layouts.app>
