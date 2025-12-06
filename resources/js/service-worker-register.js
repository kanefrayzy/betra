// Регистрация Service Worker для кеширования страниц
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js')
            .then(registration => {
                console.log('✅ Service Worker зарегистрирован:', registration.scope);
                
                // Проверяем обновления каждые 5 минут
                setInterval(() => {
                    registration.update();
                }, 5 * 60 * 1000);
                
                // Обновляем при активации
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'activated') {
                            console.log('🔄 Service Worker обновлён');
                        }
                    });
                });
            })
            .catch(error => {
                console.log('❌ Ошибка регистрации Service Worker:', error);
            });
    });
    
    // Слушаем сообщения от Service Worker
    navigator.serviceWorker.addEventListener('message', (event) => {
        if (event.data && event.data.type === 'CACHE_UPDATED') {
            console.log('📦 Кеш обновлён:', event.data.url);
        }
    });
}

// Функция для очистки кеша (можно вызвать из консоли)
window.clearServiceWorkerCache = function() {
    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage({ type: 'CLEAR_CACHE' });
        console.log('🗑️ Кеш очищен');
    }
};
