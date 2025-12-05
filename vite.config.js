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
                manualChunks: {
                    'vendor': ['noty']
                },
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]'
            }
        },
        chunkSizeWarningLimit: 700,
        cssCodeSplit: true,
        assetsInlineLimit: 4096
    },
    optimizeDeps: {
        include: ['noty'],
        exclude: ['socket.io-client']
    },
    server: {
        hmr: { overlay: false }
    }
});
