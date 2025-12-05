/**
 * Telegram Auth Component для Alpine.js
 * Используется в модалках авторизации через Telegram
 */

window.telegramAuth = function(type) {
    return {
        loading: false,
        token: '',
        deepLink: '',
        checkInterval: null,
        checkAttempts: 0,
        maxAttempts: 300,
        
        init() {
            const navigatingHandler = () => this.stopChecking();
            document.addEventListener('livewire:navigating', navigatingHandler);
            this._navigatingHandler = navigatingHandler;
        },
        
        destroy() {
            this.stopChecking();
            if (this._navigatingHandler) {
                document.removeEventListener('livewire:navigating', this._navigatingHandler);
            }
        },
        
        async generateDeepLink() {
            const config = window.appConfig || {};
            try {
                const response = await fetch(config.routes?.telegramGenerate || '', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ type: type })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.token = data.token;
                    this.deepLink = data.deep_link;
                    return true;
                }
                return false;
            } catch (error) {
                console.error('Error generating token:', error);
                return false;
            }
        },
        
        async handleTelegramAuth(event) {
            event.preventDefault();
            
            if (this.loading) return;
            
            this.loading = true;
            
            if (!this.deepLink) {
                const success = await this.generateDeepLink();
                
                if (!success || !this.deepLink) {
                    this.loading = false;
                    const config = window.appConfig || {};
                    alert(config.i18n?.telegramError || 'Error');
                    return;
                }
            }
            
            window.open(this.deepLink, '_blank');
            this.startChecking();
        },
        
        startChecking() {
            this.checkAttempts = 0;
            this.scheduleNextCheck();
        },
        
        scheduleNextCheck() {
            if (this.checkAttempts >= this.maxAttempts) {
                this.stopChecking();
                return;
            }
            
            const delay = this.checkAttempts < 5 ? 2000 : 
                          this.checkAttempts < 15 ? 4000 : 
                          this.checkAttempts < 30 ? 8000 : 15000;
            
            this.checkInterval = setTimeout(async () => {
                await this.checkStatus();
                this.checkAttempts++;
                this.scheduleNextCheck();
            }, delay);
        },
        
        async checkStatus() {
            const config = window.appConfig || {};
            try {
                const response = await fetch(config.routes?.telegramCheck || '', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ token: this.token })
                });
                
                const data = await response.json();
                
                if (data.success && data.status === 'completed') {
                    this.stopChecking();
                    
                    if (data.action === 'login' && data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.data?.action === 'register') {
                        window.dispatchEvent(new CustomEvent('open-currency-select', {
                            detail: {
                                authType: 'telegram-code',
                                authData: data.data
                            }
                        }));
                    }
                }
            } catch (error) {
                console.error('Error checking status:', error);
            }
        },
        
        stopChecking() {
            if (this.checkInterval) {
                clearTimeout(this.checkInterval);
                this.checkInterval = null;
            }
            this.loading = false;
            this.checkAttempts = 0;
        }
    }
};
