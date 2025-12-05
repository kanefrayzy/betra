/**
 * Инициализация глобальных переменных из PHP
 */

export function initializeAppConfig() {
    // Проверяем что window.appConfig существует (инжектится из blade)
    if (typeof window.appConfig === 'undefined') {
        console.warn('window.appConfig not found. Some features may not work.');
        window.appConfig = {
            routes: {},
            user: null,
            chatEmojis: [],
            i18n: {}
        };
    }

    return window.appConfig;
}

/**
 * Хелпер для получения роута из конфига
 */
export function route(name) {
    const config = window.appConfig || {};
    return config.routes?.[name] || '';
}

/**
 * Хелпер для получения переведенной строки
 */
export function __(key) {
    const config = window.appConfig || {};
    return config.i18n?.[key] || key;
}

/**
 * Хелпер для проверки авторизации
 */
export function isAuthenticated() {
    const config = window.appConfig || {};
    return config.user !== null;
}

/**
 * Хелпер для получения данных пользователя
 */
export function currentUser() {
    const config = window.appConfig || {};
    return config.user || null;
}
