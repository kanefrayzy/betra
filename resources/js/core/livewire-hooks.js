/**
 * Livewire Hooks и обработчики событий
 */

/**
 * CSRF 419 Handler - перезагрузка страницы при истечении сессии
 */
export function setupCsrfHandler() {
    document.addEventListener('livewire:init', () => {
        if (typeof Livewire === 'undefined') return;
        
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 419) {
                    preventDefault();
                    window.location.reload();
                }
            });
        });
    });
}

/**
 * Обработчик отмененных переходов (Alpine navigate)
 */
export function setupUnhandledRejectionHandler() {
    window.addEventListener('unhandledrejection', function(event) {
        if (event.reason && event.reason.isFromCancelledTransition) {
            event.preventDefault();
        }
    });
}

/**
 * Chat WebSocket Preservation при навигации
 */
export function setupChatPreservation() {
    document.addEventListener('livewire:navigating', () => {
        if (window.chatSystem && window.chatSystem.ws) {
            window.chatSystem.preserveConnection = true;
        }
    });

    document.addEventListener('livewire:navigated', () => {
        if (window.chatSystem && window.chatSystem.preserveConnection) {
            window.chatSystem.preserveConnection = false;
        }
    });
}

/**
 * Notification Events
 */
export function setupNotificationEvents() {
    document.addEventListener('livewire:init', function() {
        if (typeof Livewire === 'undefined') return;
        
        Livewire.on('notificationUpdated', () => {
            Livewire.dispatch('refresh-notifications');
        });

        Livewire.on('allNotificationsRead', () => {
            Livewire.dispatch('refresh-notifications');
        });
    });
}

/**
 * Sidebar Controller (Alpine component)
 */
export function setupSidebarController() {
    document.addEventListener('alpine:init', () => {
        if (typeof Alpine === 'undefined') return;
        
        Alpine.data('sidebarController', () => ({
            init() {
                this.sidebarOpen = window.innerWidth >= 1024;

                window.addEventListener('resize', () => {
                    this.sidebarOpen = window.innerWidth >= 1024;
                });
            }
        }));
    });
}

/**
 * Alpine Chat Store initialization
 */
export function setupChatStore() {
    document.addEventListener('alpine:init', () => {
        if (typeof Alpine === 'undefined' || !Alpine.store) return;
        
        const config = window.appConfig || {};
        const emojis = config.chatEmojis || [];
        
        Alpine.store('chat', {
            emojis: emojis
        });
    });
}

/**
 * Alpine UI Store для глобального состояния интерфейса
 */
export function setupUIStore() {
    document.addEventListener('alpine:init', () => {
        if (typeof Alpine === 'undefined' || !Alpine.store) return;
        
        // Инициализируем UI store только если его еще нет
        if (!Alpine.store('ui')) {
            Alpine.store('ui', {
                sidebarCollapsed: (window.innerWidth >= 1280 && localStorage.getItem('sidebarCollapsed') === 'true'),
                chatOpen: (window.innerWidth >= 768 && localStorage.getItem('chatOpen') === 'true'),
                
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    if (window.innerWidth >= 1280) {
                        localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed.toString());
                        
                        const mainContent = document.querySelector('.main-content');
                        const sidebarWrapper = document.querySelector('.sidebar-wrapper');
                        
                        if (mainContent) mainContent.classList.toggle('sidebar-collapsed', this.sidebarCollapsed);
                        if (sidebarWrapper) sidebarWrapper.classList.toggle('collapsed', this.sidebarCollapsed);
                    }
                },
                
                toggleChat() {
                    this.chatOpen = !this.chatOpen;
                    if (window.innerWidth >= 768) {
                        localStorage.setItem('chatOpen', this.chatOpen.toString());
                        
                        if (this.chatOpen) {
                            document.body.classList.add('chat-open');
                        } else {
                            document.body.classList.remove('chat-open');
                        }
                    }
                }
            });
            
            // Применяем начальное состояние
            const uiStore = Alpine.store('ui');
            if (uiStore.chatOpen) {
                document.body.classList.add('chat-open');
            }
            if (uiStore.sidebarCollapsed) {
                const mainContent = document.querySelector('.main-content');
                const sidebarWrapper = document.querySelector('.sidebar-wrapper');
                if (mainContent) mainContent.classList.add('sidebar-collapsed');
                if (sidebarWrapper) sidebarWrapper.classList.add('collapsed');
            }
        }
    });
}


/**
 * Обратная совместимость - глобальные переменные для чата
 */
export function setupChatGlobals() {
    const config = window.appConfig || {};
    
    if (typeof window.chatEmojis === 'undefined') {
        window.chatEmojis = config.chatEmojis || [];
        window.isAuthenticated = config.user !== null;
        
        if (config.user) {
            window.currentUserUsername = config.user.username || '';
            window.currentUserId = config.user.id || 0;
            window.currentUserCurrency = config.user.currency || '';
            window.isModerator = config.user.isModerator || false;
        }
    }
}

/**
 * Alpine компонент для игрового плеера
 */
export function setupGamePlayer() {
    document.addEventListener('alpine:init', () => {
        if (typeof Alpine === 'undefined') return;
        
        Alpine.data('gamePlayer', (gameSlug) => ({
            loading: true,
            error: false,
            fullscreen: false,
            gameSlug: gameSlug,
            gameUrl: null,
            loadTimeout: null,
            
            init() {
                this.fetchGameUrl();
                
                window.addEventListener('message', (event) => {
                    if (event.data === 'closeGame' || event.data === 'close' || event.data === 'GAME_MODE:LOBBY') {
                        window.location.href = window.appConfig?.routes?.slotsLobby || '/slots';
                    }
                });
                
                document.addEventListener('fullscreenchange', () => {
                    this.fullscreen = !!document.fullscreenElement;
                });
            },
            
            async fetchGameUrl() {
                try {
                    const response = await fetch(`/slots/api/game-url/${this.gameSlug}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error('Failed to load game');
                    }
                    
                    const data = await response.json();
                    
                    if (data.success && data.url) {
                        this.gameUrl = data.url;
                        this.$nextTick(() => {
                            this.loadGame();
                        });
                    } else {
                        this.error = true;
                        this.loading = false;
                    }
                } catch (err) {
                    console.error('Game URL fetch error:', err);
                    this.error = true;
                    this.loading = false;
                }
            },
            
            loadGame() {
                if (!this.gameUrl || !this.$refs.iframe) {
                    this.loading = false;
                    this.error = true;
                    return;
                }
                
                this.loadTimeout = setTimeout(() => {
                    this.loading = false;
                }, 8000);
                
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
                this.gameUrl = null;
                this.$refs.iframe.src = 'about:blank';
                setTimeout(() => {
                    this.fetchGameUrl();
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
            }
        }));
    });
}
