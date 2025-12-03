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
    // Добавим дедупликацию для предотвращения дублирования модулей
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // Выделяем Livewire и Alpine в отдельные чанки
                    'livewire': ['livewire'],
                    'vendors': ['noty']
                }
            }
        }
    },
    // Добавляем алиасы для более удобного импорта
    resolve: {
        alias: {
            '@': '/resources/js'
        }
    }
});
