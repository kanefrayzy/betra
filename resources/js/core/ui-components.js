/**
 * UI компоненты и утилиты для управления интерфейсом
 */

/**
 * Modal Manager
 */
export class ModalManager {
    constructor() {
        this.modals = {};
        this.initialized = false;
    }

    init() {
        if (this.initialized) return;
        
        document.querySelectorAll('[data-modal]').forEach(modal => {
            const modalId = modal.getAttribute('data-modal');
            this.modals[modalId] = false;
        });

        // Close modal on overlay click
        const overlay = document.getElementById('overlay');
        if (overlay) {
            overlay.addEventListener('click', () => {
                Object.keys(this.modals).forEach(modalId => {
                    this.close(modalId);
                });
            });
        }

        this.initialized = true;
    }

    open(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById('overlay');
        
        if (modal && overlay) {
            modal.classList.remove('hidden');
            overlay.classList.remove('hidden');
            this.modals[modalId] = true;
        }
    }

    close(modalId) {
        const modal = document.getElementById(modalId);
        
        if (modal) {
            modal.classList.add('hidden');
            this.modals[modalId] = false;

            const hasOpenModal = Object.values(this.modals).some(isOpen => isOpen);
            if (!hasOpenModal) {
                const overlay = document.getElementById('overlay');
                overlay?.classList.add('hidden');
            }
        }
    }

    requireAuth(callback, event) {
        const config = window.appConfig || {};
        const isAuthenticated = config.user !== null;
        
        if (!isAuthenticated) {
            if (event) event.preventDefault();
            window.dispatchEvent(new CustomEvent('open-register-modal'));
            return false;
        }
        
        if (callback && typeof callback === 'function') {
            return callback();
        }
        return true;
    }
}

/**
 * Dropdown Manager
 */
export class DropdownManager {
    constructor() {
        this.states = {};
        this.setupClickAwayListener();
    }

    toggle(dropdownId, event) {
        event?.stopPropagation();

        const dropdown = document.getElementById(dropdownId);
        if (!dropdown) return;

        const isOpen = !dropdown.classList.contains('hidden');

        // Close all other dropdowns
        if (!isOpen) {
            Object.keys(this.states).forEach(id => {
                if (id !== dropdownId && this.states[id]) {
                    const other = document.getElementById(id);
                    other?.classList.add('hidden');
                    this.states[id] = false;
                }
            });
        }

        dropdown.classList.toggle('hidden');
        this.states[dropdownId] = !isOpen;

        const arrow = document.getElementById(dropdownId + '-arrow');
        arrow?.classList.toggle('rotate-180');

        return !isOpen;
    }

    setupClickAwayListener() {
        document.addEventListener('click', (event) => {
            // Close currency dropdown
            const currencyDropdown = document.getElementById('balance-currency-dropdown');
            const currencyButton = document.getElementById('balance-currency-button');
            if (currencyDropdown && currencyButton &&
                !currencyDropdown.contains(event.target) &&
                !currencyButton.contains(event.target)) {
                currencyDropdown.classList.add('hidden');
                document.getElementById('balance-currency-arrow')?.classList.remove('rotate-180');
            }

            // Close notifications
            if (window.notificationsState) {
                const notifDropdown = document.getElementById('notifications-dropdown');
                const notifButton = document.getElementById('notifications-button');
                if (notifDropdown && notifButton &&
                    !notifDropdown.contains(event.target) &&
                    !notifButton.contains(event.target)) {
                    notifDropdown.classList.add('hidden');
                    window.notificationsState = false;
                }
            }

            // Close other dropdowns
            Object.keys(this.states).forEach(dropdownId => {
                if (this.states[dropdownId]) {
                    const dropdown = document.getElementById(dropdownId);
                    const button = document.getElementById(dropdownId.replace('-dropdown', '-button'));

                    if (dropdown && button &&
                        !dropdown.contains(event.target) &&
                        !button.contains(event.target)) {
                        dropdown.classList.add('hidden');
                        this.states[dropdownId] = false;
                        document.getElementById(dropdownId + '-arrow')?.classList.remove('rotate-180');
                    }
                }
            });
        });
    }
}



/**
 * Chat Functions
 */
export function openChat() {
    document.body.classList.add('chat-open');
    if (window.innerWidth >= 768) {
        localStorage.setItem('chatOpen', 'true');
    }
}

export function closeChat() {
    document.body.classList.remove('chat-open');
    if (window.innerWidth >= 768) {
        localStorage.setItem('chatOpen', 'false');
    }
}

export function toggleChat() {
    document.body.classList.contains('chat-open') ? closeChat() : openChat();
}
