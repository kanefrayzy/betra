@extends('panel')
@php $baseUrl = 'providers'; @endphp

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Управление провайдерами</h1>
                    <p class="text-indigo-100 mt-1">Управление типами провайдеров и отдельными провайдерами игр</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold" id="totalProviders">{{ count($providersData) }}</div>
                <div class="text-indigo-100">Типов провайдеров</div>
            </div>
        </div>
    </div>

    <!-- Общая статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @php
            $totalGames = 0;
            $totalActive = 0;
            $totalInactive = 0;
            $totalProviders = 0;

            foreach($providersData as $typeData) {
                $totalGames += $typeData['type_stats']->total_games;
                $totalActive += $typeData['type_stats']->active_games;
                $totalInactive += $typeData['type_stats']->inactive_games;
                $totalProviders += count($typeData['providers']);
            }
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Всего игр</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalGames) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Активных игр</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($totalActive) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Неактивных игр</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($totalInactive) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Провайдеров</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $totalProviders }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Провайдеры по типам -->
    @foreach($providersData as $typeData)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <!-- Заголовок типа провайдера -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $typeData['type'] }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $typeData['type_stats']->total_games }} игр,
                            {{ $typeData['type_stats']->active_games }} активных,
                            {{ count($typeData['providers']) }} провайдеров
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Переключатель активности типа -->
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="type_{{ $typeData['type'] }}"
                            class="toggle-type sr-only"
                            data-type="{{ $typeData['type'] }}"
                            {{ $typeData['type_stats']->active_games > 0 ? 'checked' : '' }}
                        >
                        <label for="type_{{ $typeData['type'] }}" class="relative cursor-pointer">
                            <div class="w-14 h-8 bg-gray-300 dark:bg-gray-600 rounded-full transition-all duration-300 toggle-bg"></div>
                            <div class="absolute left-1 top-1 w-6 h-6 bg-white rounded-full transition-all duration-300 toggle-dot"></div>
                        </label>
                    </div>

                    <!-- Кнопка сворачивания -->
                    <button type="button" class="collapse-toggle p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200" data-target="providers_{{ $typeData['type'] }}">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Список провайдеров -->
        <div id="providers_{{ $typeData['type'] }}" class="providers-list">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                @foreach($typeData['providers'] as $provider)
                <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ substr($provider['name'], 0, 2) }}</span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $provider['name'] }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $provider['stats']->total_games }} игр</p>
                            </div>
                        </div>

                        <!-- Переключатель провайдера -->
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="provider_{{ str_replace(' ', '_', $provider['name']) }}"
                                class="toggle-provider sr-only"
                                data-provider="{{ $provider['name'] }}"
                                {{ $provider['stats']->active_games > 0 ? 'checked' : '' }}
                            >
                            <label for="provider_{{ str_replace(' ', '_', $provider['name']) }}" class="relative cursor-pointer">
                                <div class="w-12 h-6 bg-gray-300 dark:bg-gray-600 rounded-full transition-all duration-300 toggle-bg-small"></div>
                                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full transition-all duration-300 toggle-dot-small"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Статистика провайдера -->
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-gray-600 dark:text-gray-400">Активных:</span>
                            <span class="font-semibold text-green-600">{{ $provider['stats']->active_games }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-gray-600 dark:text-gray-400">Неактивных:</span>
                            <span class="font-semibold text-red-600">{{ $provider['stats']->inactive_games }}</span>
                        </div>
                        @if($provider['stats']->live_games > 0)
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-red-400 rounded-full animate-pulse"></div>
                            <span class="text-gray-600 dark:text-gray-400">Live:</span>
                            <span class="font-semibold text-red-500">{{ $provider['stats']->live_games }}</span>
                        </div>
                        @endif
                        @if($provider['stats']->popular_games > 0)
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span class="text-gray-600 dark:text-gray-400">Популярных:</span>
                            <span class="font-semibold text-yellow-600">{{ $provider['stats']->popular_games }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Toast уведомления -->
<div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50"></div>

<style>
/* Стили для переключателей */
.toggle-bg.checked {
    background-color: #10b981;
}

.toggle-dot.checked {
    transform: translateX(24px);
}

.toggle-bg-small.checked {
    background-color: #10b981;
}

.toggle-dot-small.checked {
    transform: translateX(24px);
}

/* Анимации */
.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функция для показа уведомлений
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `flex items-center p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;

        toast.innerHTML = `
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    ${type === 'success'
                        ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                        : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                    }
                </svg>
                <span>${message}</span>
            </div>
        `;

        document.getElementById('toast-container').appendChild(toast);

        // Анимация появления
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Удаление через 5 секунд
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Управление переключателями типов провайдеров
    document.querySelectorAll('.toggle-type').forEach(toggle => {
        toggle.addEventListener('change', async function() {
            const providerType = this.dataset.type;
            const isActive = this.checked;

            try {
                const response = await fetch('{{ route("admin.providers.toggleType") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        provider_type: providerType,
                        is_active: isActive
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');

                    // Обновляем переключатели провайдеров этого типа
                    const providerToggles = document.querySelectorAll(`[data-provider]`);
                    providerToggles.forEach(providerToggle => {
                        // Найти провайдеров этого типа и обновить их состояние
                        const providerCard = providerToggle.closest('.bg-gray-50');
                        if (providerCard && providerCard.closest(`#providers_${providerType.replace(' ', '_')}`)) {
                            providerToggle.checked = isActive;
                            updateToggleAppearance(providerToggle);
                        }
                    });

                    updateToggleAppearance(this);
                } else {
                    showToast(data.message, 'error');
                    this.checked = !isActive; // Возвращаем предыдущее состояние
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Произошла ошибка при обновлении', 'error');
                this.checked = !isActive;
            }
        });
    });

    // Управление переключателями отдельных провайдеров
    document.querySelectorAll('.toggle-provider').forEach(toggle => {
        toggle.addEventListener('change', async function() {
            const provider = this.dataset.provider;
            const isActive = this.checked;

            try {
                const response = await fetch('{{ route("admin.providers.toggleProvider") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        provider: provider,
                        is_active: isActive
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    updateToggleAppearance(this);
                } else {
                    showToast(data.message, 'error');
                    this.checked = !isActive;
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Произошла ошибка при обновлении', 'error');
                this.checked = !isActive;
            }
        });
    });

    // Функция обновления внешнего вида переключателей
    function updateToggleAppearance(toggle) {
        const label = toggle.nextElementSibling;
        const bg = label.querySelector('.toggle-bg, .toggle-bg-small');
        const dot = label.querySelector('.toggle-dot, .toggle-dot-small');

        if (toggle.checked) {
            bg.classList.add('checked');
            dot.classList.add('checked');
        } else {
            bg.classList.remove('checked');
            dot.classList.remove('checked');
        }
    }

    // Управление сворачиванием/разворачиванием секций
    document.querySelectorAll('.collapse-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            const icon = this.querySelector('svg');

            if (target.style.display === 'none') {
                target.style.display = 'block';
                icon.style.transform = 'rotate(0deg)';
            } else {
                target.style.display = 'none';
                icon.style.transform = 'rotate(-90deg)';
            }
        });
    });

    // Инициализация внешнего вида переключателей
    document.querySelectorAll('.toggle-type, .toggle-provider').forEach(toggle => {
        updateToggleAppearance(toggle);
    });
});
</script>

@endsection
