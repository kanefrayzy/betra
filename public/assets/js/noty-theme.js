document.addEventListener('DOMContentLoaded', function() {
    // Проверяем, загружена ли библиотека Noty
    if (typeof Noty === 'undefined') {
        console.error('Noty не загружен!');
        return;
    }

    // Создаем собственную тему для Noty на основе Tailwind
    Noty.overrideDefaults({
        layout: 'topRight',
        theme: 'custom', // Используем собственную тему
        timeout: 3000,
        progressBar: true,
        closeWith: ['click', 'button'],
        animation: {
            open: function(promise) {
                var n = this;
                var Timeline = new mojs.Timeline();
                var body = new mojs.Html({
                    el: n.barDom,
                    x: {0: 0, delay: 0.3, duration: 300, easing: 'ease-out'},
                    opacity: {0: 1, delay: 0.3, duration: 300},
                    isForce3d: true,
                    onComplete: function() {
                        promise(function(resolve) {
                            resolve();
                        });
                    }
                });

                var parent = new mojs.Shape({
                    parent: n.barDom,
                    width: 200,
                    height: n.barDom.getBoundingClientRect().height,
                    radius: 0,
                    x: {[150]: -150},
                    duration: 1.2 * 500,
                    isShowStart: true
                });

                n.barDom.style.overflow = 'hidden';

                Timeline.add(body, parent);
                Timeline.play();
            },
            close: function(promise) {
                var n = this;
                new mojs.Html({
                    el: n.barDom,
                    x: {0: 0, delay: 0.3, duration: 300, easing: 'ease-out'},
                    opacity: {1: 0, delay: 0.3, duration: 300},
                    isForce3d: true,
                    onComplete: function() {
                        promise(function(resolve) {
                            resolve();
                        });
                    }
                }).play();
            }
        }
    });

    // Добавляем стили Tailwind к Noty
    const style = document.createElement('style');
    style.textContent = `
        .noty_theme__custom.noty_bar {
            @apply bg-dark-800 border border-dark-700 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out;
            margin: 4px 0;
            overflow: hidden;
            max-width: 320px;
        }

        .noty_theme__custom.noty_bar .noty_body {
            @apply text-sm text-white font-medium px-4 py-3;
            padding-right: 40px;
        }

        .noty_theme__custom.noty_bar .noty_buttons {
            @apply px-4 py-3 border-t border-dark-700 flex justify-end space-x-2;
        }

        .noty_theme__custom.noty_bar .noty_close_button {
            @apply text-gray-400 hover:text-white transition-colors absolute top-2 right-2 w-5 h-5 flex items-center justify-center;
        }

        .noty_theme__custom.noty_type__success {
            @apply border-l-4 border-success;
        }

        .noty_theme__custom.noty_type__success .noty_body:before {
            @apply text-success;
            content: '✓ ';
            margin-right: 8px;
        }

        .noty_theme__custom.noty_type__error {
            @apply border-l-4 border-danger;
        }

        .noty_theme__custom.noty_type__error .noty_body:before {
            @apply text-danger;
            content: '✕ ';
            margin-right: 8px;
        }

        .noty_theme__custom.noty_type__warning {
            @apply border-l-4 border-warning;
        }

        .noty_theme__custom.noty_type__warning .noty_body:before {
            @apply text-warning;
            content: '⚠ ';
            margin-right: 8px;
        }

        .noty_theme__custom.noty_type__info {
            @apply border-l-4 border-primary;
        }

        .noty_theme__custom.noty_type__info .noty_body:before {
            @apply text-primary;
            content: 'ℹ ';
            margin-right: 8px;
        }

        .noty_theme__custom .noty_progressbar {
            @apply h-1 bg-white/20;
        }

        .noty_theme__custom .noty_progressbar_inner {
            @apply h-full;
            background-color: rgba(255, 255, 255, 0.4);
        }
    `;
    document.head.appendChild(style);

    // Создаем глобальные функции для показа уведомлений
    window.showNotification = function(message, type = 'info', timeout = 3000) {
        return new Noty({
            text: message,
            type: type,
            theme: 'custom',
            timeout: timeout,
            progressBar: true,
            closeWith: ['click', 'button']
        }).show();
    };

    window.showSuccessNotification = function(message, timeout = 3000) {
        return window.showNotification(message, 'success', timeout);
    };

    window.showErrorNotification = function(message, timeout = 3000) {
        return window.showNotification(message, 'error', timeout);
    };

    window.showWarningNotification = function(message, timeout = 3000) {
        return window.showNotification(message, 'warning', timeout);
    };

    window.showInfoNotification = function(message, timeout = 3000) {
        return window.showNotification(message, 'info', timeout);
    };

    // Автоматически показываем уведомления из мета-тегов при загрузке страницы
    const successMessage = document.querySelector('meta[name="success-message"]')?.content;
    const errorMessage = document.querySelector('meta[name="error-message"]')?.content;
    const errorsJson = document.querySelector('meta[name="errors"]')?.content;

    if (successMessage && successMessage.trim() !== '') {
        window.showSuccessNotification(successMessage);
    }

    if (errorMessage && errorMessage.trim() !== '') {
        window.showErrorNotification(errorMessage);
    }

    if (errorsJson && errorsJson !== '[]') {
        try {
            const errors = JSON.parse(errorsJson);
            if (Array.isArray(errors)) {
                errors.forEach(error => {
                    window.showErrorNotification(error);
                });
            }
        } catch (e) {
            console.error('Ошибка при разборе JSON с ошибками:', e);
        }
    }
});
