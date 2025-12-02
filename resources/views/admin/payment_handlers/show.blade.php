@extends('panel')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-indigo-900 dark:to-purple-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row gap-4">
            <a href="{{ route('admin.payment_handlers.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Назад к списку
            </a>
            <a href="{{ route('admin.payment_handlers.edit', $paymentHandler->id) }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                <i class="fas fa-edit mr-2"></i>
                Редактировать
            </a>
        </div>

        <!-- Main Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white">
                <div class="flex flex-col lg:flex-row items-start lg:items-center space-y-4 lg:space-y-0 lg:space-x-6">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        @if($paymentHandler->icon)
                            <img src="{{ asset('storage/' . $paymentHandler->icon) }}"
                                 class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-xl"
                                 alt="{{ $paymentHandler->name }}">
                        @else
                            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center border-4 border-white shadow-xl">
                                <i class="fas fa-credit-card text-3xl text-white"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="flex-grow">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h1 class="text-3xl font-bold mb-2">{{ $paymentHandler->paymentSystem->name }}</h1>
                                <div class="flex items-center space-x-4">
                                    <span class="text-xl font-semibold bg-white bg-opacity-20 px-4 py-2 rounded-xl">
                                        {{ $paymentHandler->currency }}
                                    </span>
                                    <span class="px-4 py-2 rounded-xl font-semibold {{ $paymentHandler->active ? 'bg-green-500' : 'bg-red-500' }}">
                                        {{ $paymentHandler->active ? 'Активен' : 'Неактивен' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Fees Section -->
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900 dark:to-orange-900 rounded-2xl p-6 border border-amber-200 dark:border-amber-800">
                        <h2 class="text-xl font-bold text-amber-800 dark:text-amber-200 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-percentage text-white text-sm"></i>
                            </div>
                            Комиссии
                        </h2>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-white dark:bg-amber-800 rounded-xl">
                                <span class="font-semibold text-amber-700 dark:text-amber-200">Депозит:</span>
                                <span class="text-xl font-bold text-amber-800 dark:text-amber-100">{{ $paymentHandler->deposit_fee }}%</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-white dark:bg-amber-800 rounded-xl">
                                <span class="font-semibold text-amber-700 dark:text-amber-200">Вывод:</span>
                                <span class="text-xl font-bold text-amber-800 dark:text-amber-100">{{ $paymentHandler->withdrawal_fee }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- API Info Section -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900 rounded-2xl p-6 border border-blue-200 dark:border-blue-800">
                        <h2 class="text-xl font-bold text-blue-800 dark:text-blue-200 mb-6 flex items-center">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-link text-white text-sm"></i>
                            </div>
                            API Информация
                        </h2>

                        <div class="space-y-4">
                            <div class="p-4 bg-white dark:bg-blue-800 rounded-xl">
                                <label class="block text-sm font-semibold text-blue-700 dark:text-blue-300 mb-2">URL:</label>
                                @if($paymentHandler->url)
                                    <code class="block p-2 bg-blue-100 dark:bg-blue-900 rounded text-sm font-mono text-blue-800 dark:text-blue-200 break-all">
                                        {{ $paymentHandler->url }}
                                    </code>
                                @else
                                    <span class="text-gray-500 italic">Не указан</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Limits Section -->
                <div class="mt-8 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900 dark:to-pink-900 rounded-2xl p-6 border border-purple-200 dark:border-purple-800">
                    <h2 class="text-xl font-bold text-purple-800 dark:text-purple-200 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-sliders-h text-white text-sm"></i>
                        </div>
                        Лимиты операций
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Deposit Limits -->
                        <div class="bg-white dark:bg-purple-800 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-purple-700 dark:text-purple-200 mb-4 flex items-center">
                                <i class="fas fa-download mr-2 text-green-500"></i>
                                Депозиты
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-purple-600 dark:text-purple-300 font-medium">Минимум:</span>
                                    <span class="font-bold text-purple-800 dark:text-purple-100">
                                        {{ $paymentHandler->min_deposit_limit ?: 'Не ограничено' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-purple-600 dark:text-purple-300 font-medium">Максимум:</span>
                                    <span class="font-bold text-purple-800 dark:text-purple-100">
                                        {{ $paymentHandler->max_deposit_limit ?: 'Не ограничено' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Withdrawal Limits -->
                        <div class="bg-white dark:bg-purple-800 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-purple-700 dark:text-purple-200 mb-4 flex items-center">
                                <i class="fas fa-upload mr-2 text-red-500"></i>
                                Выводы
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-purple-600 dark:text-purple-300 font-medium">Минимум:</span>
                                    <span class="font-bold text-purple-800 dark:text-purple-100">
                                        {{ $paymentHandler->min_withdrawal_limit ?: 'Не ограничено' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-purple-600 dark:text-purple-300 font-medium">Максимум:</span>
                                    <span class="font-bold text-purple-800 dark:text-purple-100">
                                        {{ $paymentHandler->max_withdrawal_limit ?: 'Не ограничено' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Section -->
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('admin.payment_handlers.edit', $paymentHandler->id) }}"
                       class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl text-center">
                        <i class="fas fa-edit mr-2"></i>
                        Редактировать обработчик
                    </a>

                    <form action="{{ route('admin.payment_handlers.destroy', $paymentHandler->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Вы уверены, что хотите удалить этот обработчик?')"
                                class="w-full px-8 py-4 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                            <i class="fas fa-trash mr-2"></i>
                            Удалить обработчик
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
