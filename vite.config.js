import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    optimizeDeps: {
        include: ['livewire', 'alpinejs'],
    },
    build: {
        // Минификация с terser для лучшего результата
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug']
            }
        },
        // Разделение кода на оптимальные чанки
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Livewire и Alpine
                    if (id.includes('livewire') || id.includes('alpinejs')) {
                        return 'alpine-livewire';
                    }
                    // Vendor библиотеки
                    if (id.includes('noty') || id.includes('swiper')) {
                        return 'vendor';
                    }
                    // Socket.io только если реально используется
                    if (id.includes('socket.io')) {
                        return 'websocket';
                    }
                }
            }
        },
        // Увеличиваем лимит для предупреждений о размере
        chunkSizeWarningLimit: 1000,
        // CSS code splitting
        cssCodeSplit: true,
        // Оптимизация ассетов
        assetsInlineLimit: 4096,
        // Опция для совместимости с браузером
        commonjsOptions: {
            transformMixedEsModules: true,
        }
    },
    // Алиасы для более удобного импорта
    resolve: {
        alias: {
            '@': '/resources/js',
            '@css': '/resources/css',
            // Полифилы для Node.js модулей в браузере
            'util': 'util/',
            'events': 'events/',
        }
    },
    // Оптимизация dev-сервера
    server: {
        hmr: {
            overlay: false
        }
    }
});
