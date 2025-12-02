// Проверяем, были ли уже инициализированы Livewire и Alpine
let livewireInit = false;
let alpineInit = false;

// Экспортируем функцию для безопасной инициализации Livewire
export function initLivewire() {
    if (livewireInit) {
        console.info('Livewire уже инициализирован');
        return;
    }

    try {
        // Динамический импорт Livewire
        import('../../vendor/livewire/livewire/dist/livewire.esm').then(({ Livewire, Alpine }) => {
            // Инициализируем Livewire только если еще не инициализирован
            if (!window.Livewire) {
                console.info('Инициализация Livewire');

                // Патчим Echo, если его нет
                if (typeof window.Echo === 'undefined') {
                    window.Echo = {
                        socketId: () => null,
                        private: () => ({
                            listen: () => {},
                            listenForWhisper: () => {}
                        }),
                        channel: () => ({
                            listen: () => {},
                            listenForWhisper: () => {}
                        }),
                        join: () => ({
                            listen: () => {},
                            listenForWhisper: () => {},
                            here: () => {},
                            joining: () => {},
                            leaving: () => {}
                        })
                    };
                }

                // Инициализируем Alpine только если еще не инициализирован
                if (!window.Alpine) {
                    // Патчим $persist для предотвращения ошибок переопределения
                    if (Alpine.store && typeof Alpine.store.$persist !== 'undefined') {
                        console.info('Alpine $persist уже зарегистрирован');
                    }

                    window.Alpine = Alpine;
                    alpineInit = true;
                }

                // Запускаем Livewire
                Livewire.start();
                livewireInit = true;

                // Патчим для предотвращения повторной инициализации компонентов
                const originalInitialize = Livewire.find;
                Livewire.find = function(componentId) {
                    try {
                        // Проверяем, существует ли уже компонент
                        if (Livewire.components &&
                            Livewire.components.componentsById &&
                            Livewire.components.componentsById[componentId]) {
                            // Возвращаем существующий компонент
                            return Livewire.components.componentsById[componentId];
                        }
                    } catch (e) {
                        console.warn('Ошибка при проверке компонента:', e);
                    }

                    // Вызываем оригинальный метод
                    return originalInitialize.apply(this, arguments);
                };
            }
        }).catch(err => {
            console.error('Ошибка при инициализации Livewire:', err);
        });
    } catch (e) {
        console.error('Критическая ошибка при инициализации Livewire:', e);
    }
}

// Инициализация при импорте файла
initLivewire();

// Экспортируем функцию для повторного вызова, если нужно
export default initLivewire;
