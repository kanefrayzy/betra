import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug']
            }
        },
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    // Разделяем vendor библиотеки для лучшего кеширования
                    if (id.includes('node_modules')) {
                        if (id.includes('noty')) return 'vendor-noty';
                        if (id.includes('@hotwired/turbo')) return 'vendor-turbo';
                        if (id.includes('alpinejs')) return 'vendor-alpine';
                        return 'vendor';
                    }
                    // Разделяем чат в отдельный чанк
                    if (id.includes('/chat/')) return 'chat';
                    // Telegram в отдельный чанк
                    if (id.includes('telegram')) return 'telegram';
                },
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]'
            }
        },
        chunkSizeWarningLimit: 700,
        cssCodeSplit: true,
        assetsInlineLimit: 4096,
        // Увеличиваем производительность сборки
        reportCompressedSize: false,
        // Оптимизация для продакшена
        target: 'es2018'
    },
    optimizeDeps: {
        include: ['noty'],
        exclude: ['socket.io-client']
    },
    server: {
        hmr: { overlay: false }
    }
});
