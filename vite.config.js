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
        include: ['livewire'],
    },
    build: {
        // Минификация с terser для лучшего результата
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Удаляем console.log в production
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug']
            }
        },
        // Разделение кода на оптимальные чанки
        rollupOptions: {
            output: {
                manualChunks: {
                    // Livewire и Alpine в отдельный чанк
                    'alpine-livewire': ['livewire', 'alpinejs'],
                    // Chat система отдельно
                    'chat': ['socket.io-client'],
                    // Vendor библиотеки
                    'vendor': ['noty', 'swiper']
                }
            }
        },
        // Увеличиваем лимит для предупреждений о размере
        chunkSizeWarningLimit: 1000,
        // CSS code splitting
        cssCodeSplit: true,
        // Оптимизация ассетов
        assetsInlineLimit: 4096, // 4kb
    },
    // Алиасы для более удобного импорта
    resolve: {
        alias: {
            '@': '/resources/js',
            '@css': '/resources/css'
        }
    },
    // Оптимизация dev-сервера
    server: {
        hmr: {
            overlay: false // Отключаем оверлей ошибок для лучшей производительности
        }
    }
});
