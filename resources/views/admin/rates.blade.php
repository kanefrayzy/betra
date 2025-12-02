@extends('panel')
@php $baseUrl = 'betrika'; @endphp
@section('content')

<style>
/* ... существующие стили ... */

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    animation: fadeIn 0.3s ease;
}

.modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 1.5rem;
    padding: 2rem;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease;
}

.dark .modal-content {
    background: #1f2937;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.currency-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.currency-badge.active {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.currency-badge.inactive {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.action-button {
    padding: 0.5rem;
    border-radius: 0.5rem;
    transition: all 0.2s;
}

.action-button:hover {
    transform: scale(1.1);
}
</style>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50 dark:from-gray-900 dark:via-blue-900 dark:to-purple-900 p-6">
    <!-- Header -->
    <div class="glass-card rounded-2xl p-6 mb-8">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                        Управление курсами валют
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">Настройка и мониторинг обменных курсов</p>
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="openModal()"
                        class="px-5 py-2.5 rounded-xl text-white font-medium bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>Добавить валюту</span>
                </button>

                <button onclick="autoUpdateRates()"
                        class="px-5 py-2.5 rounded-xl text-white font-medium bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                    </svg>
                    <span id="autoUpdateText">Автообновление</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card rounded-2xl p-6 rate-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-1">Всего валют</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total_currencies'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Активных: {{ $currencies->where('active', true)->count() }}
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 rate-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-1">Последнее обновление</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ $stats['last_update'] ? \Carbon\Carbon::parse($stats['last_update'])->diffForHumans() : 'Н/Д' }}
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-blue-500 to-cyan-600 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 rate-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium mb-1">Средн. изменение 24ч</p>
                    <p class="text-3xl font-bold {{ $stats['avg_change_24h'] >= 0 ? 'change-positive' : 'change-negative' }}">
                        {{ number_format($stats['avg_change_24h'], 2) }}%
                    </p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-gradient-to-r from-{{ $stats['avg_change_24h'] >= 0 ? 'green' : 'red' }}-500 to-{{ $stats['avg_change_24h'] >= 0 ? 'emerald' : 'orange' }}-600 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        @if($stats['avg_change_24h'] >= 0)
                        <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                        @else
                        <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd"/>
                        @endif
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Rates Form -->
    <form method="POST" action="{{ route('adminRatesUpdate') }}" id="ratesForm">
        @csrf
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Курсы валют</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Редактирование обменных курсов</p>
                        </div>
                    </div>

                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Сохранить изменения</span>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($currencies as $currency)
                    <div class="rate-card p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border-2 border-gray-200 dark:border-gray-700 {{ !$currency->active ? 'opacity-60' : '' }}">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                    @if($currency->icon)
                                        <span class="text-2xl">{{ $currency->icon }}</span>
                                    @else
                                        <span class="text-white font-bold text-lg">{{ substr($currency->symbol, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $currency->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $currency->symbol }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- Status Badge -->
                                <span class="currency-badge {{ $currency->active ? 'active' : 'inactive' }}">
                                    {{ $currency->active ? 'Активна' : 'Неактивна' }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Курс к USD
                                </label>
                                <div class="relative">
                                    <input type="hidden" name="rates[{{ $loop->index }}][currency_id]" value="{{ $currency->id }}">
                                    <input type="number"
                                           name="rates[{{ $loop->index }}][price]"
                                           value="{{ $currency->rate ? $currency->rate->price : 1 }}"
                                           step="0.00000001"
                                           class="rate-input w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-mono text-lg transition-all duration-300"
                                           {{ $currency->symbol === 'USD' || !$currency->active ? 'readonly' : '' }}>
                                    <div class="absolute right-3 top-3 text-gray-400 dark:text-gray-500 font-semibold">
                                        USD
                                    </div>
                                </div>
                            </div>

                            @if($currency->rate)
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Обновлено: {{ \Carbon\Carbon::parse($currency->rate->updated_at)->diffForHumans() }}
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                @if($currency->symbol !== 'USD')
                                    <button type="button"
                                            onclick="toggleCurrency({{ $currency->id }})"
                                            class="action-button bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-900/50"
                                            title="{{ $currency->active ? 'Деактивировать' : 'Активировать' }}">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            @if($currency->active)
                                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                                            @else
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                            @endif
                                        </svg>
                                    </button>

                                    <button type="button"
                                            onclick="deleteCurrency({{ $currency->id }}, '{{ $currency->name }}')"
                                            class="action-button bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50"
                                            title="Удалить валюту">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium px-3 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                        🔒 Базовая валюта
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal for Adding New Currency -->
<div id="addCurrencyModal" class="modal">
    <div class="modal-content">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Добавить валюту</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Создание новой валюты в системе</p>
                </div>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('adminCurrencyCreate') }}" id="addCurrencyForm">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Название валюты *
                    </label>
                    <input type="text"
                           name="name"
                           required
                           placeholder="Например: Азербайджанский манат"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Код валюты (символ) *
                    </label>
                    <input type="text"
                           name="symbol"
                           required
                           maxlength="10"
                           placeholder="Например: AZN"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-white uppercase">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Используйте стандартный код валюты (ISO 4217)
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Начальный курс к USD *
                    </label>
                    <input type="number"
                           name="initial_rate"
                           required
                           step="0.00000001"
                           min="0"
                           placeholder="1.00000000"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-mono">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Например: если 1 USD = 1.70 AZN, введите 1.70
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Иконка (эмодзи)
                    </label>
                    <input type="text"
                           name="icon"
                           maxlength="10"
                           placeholder="💵 ₼ ₽ € £"
                           class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-2xl text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">
                        Необязательно. Эмодзи или символ валюты
                    </p>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button"
                        onclick="closeModal()"
                        class="flex-1 px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300">
                    Отмена
                </button>
                <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
                    Создать валюту
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ... существующие функции autoUpdateRates() и showNotification() ...

function openModal() {
    document.getElementById('addCurrencyModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('addCurrencyModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    document.getElementById('addCurrencyForm').reset();
}

// Закрытие модального окна при клике вне его
document.getElementById('addCurrencyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Закрытие по Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

function toggleCurrency(id) {
    if (!confirm('Вы уверены, что хотите изменить статус этой валюты?')) {
        return;
    }

    fetch(`/{{$baseUrl}}/currency/toggle/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showNotification('success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Произошла ошибка при изменении статуса');
    });
}

function deleteCurrency(id, name) {
    if (!confirm(`Вы уверены, что хотите удалить валюту "${name}"?\n\nЭто действие необратимо!`)) {
        return;
    }

    fetch(`/{{$baseUrl}}/currency/delete/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showNotification('success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Произошла ошибка при удалении валюты');
    });
}

function autoUpdateRates() {
    const button = event.currentTarget;
    const textElement = document.getElementById('autoUpdateText');
    const originalText = textElement.textContent;

    button.disabled = true;
    button.classList.add('opacity-50', 'cursor-not-allowed');
    textElement.textContent = 'Обновление...';

    fetch('{{ route('adminRatesAutoUpdate') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showNotification('success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('error', data.message);
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            textElement.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Произошла ошибка при обновлении курсов');
        button.disabled = false;
        button.classList.remove('opacity-50', 'cursor-not-allowed');
        textElement.textContent = originalText;
    });
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-xl shadow-lg z-50 flex items-center gap-3 ${
        type === 'success'
            ? 'bg-green-500 text-white'
            : 'bg-red-500 text-white'
    }`;

    notification.innerHTML = `
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            ${type === 'success'
                ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
                : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
            }
        </svg>
        <span class="font-medium">${message}</span>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        notification.style.transition = 'all 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Анимация при загрузке
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.rate-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }, index * 50);
    });
});
</script>

@endsection
