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
