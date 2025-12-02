@extends('panel')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-indigo-900 dark:to-purple-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.payment_handlers.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Назад к списку
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header Card -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-8 text-white">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-edit text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Редактирование обработчика</h1>
                        <p class="text-lg opacity-90">{{$paymentHandler->name}}</p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('admin.payment_handlers.update', $paymentHandler) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf

                <!-- Basic Info Section -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle text-white text-sm"></i>
                        </div>
                        Основная информация
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                Платежная система
                            </label>
                            <select name="payment_system_id"
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300"
                                    required>
                                @foreach($paymentSystems as $system)
                                    <option value="{{ $system->id }}" {{ $paymentHandler->payment_system_id == $system->id ? 'selected' : '' }}>
                                        {{ $system->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                Валюта
                            </label>
                            <input type="text"
                                   name="currency"
                                   value="{{$paymentHandler->currency}}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300"
                                   placeholder="USD, EUR, RUB..."
                                   required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                URL API
                            </label>
                            <input type="text"
                                   name="url"
                                   value="{{$paymentHandler->url}}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300"
                                   placeholder="https://api.example.com">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                Статус
                            </label>
                            <select name="active"
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-purple-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300"
                                    required>
                                <option value="1" {{ $paymentHandler->active ? 'selected' : '' }}>Активен</option>
                                <option value="0" {{ !$paymentHandler->active ? 'selected' : '' }}>Неактивен</option>
                            </select>
                        </div>
                    </div>

                    <!-- Icon Upload -->
                    <div class="mt-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">
                            Иконка обработчика
                        </label>

                        @if($paymentHandler->icon)
                        <div class="mb-4 p-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900 dark:to-orange-900 rounded-xl border border-amber-200 dark:border-amber-800">
                            <div class="flex items-center space-x-4">
                                <img src="{{ asset('storage/' . $paymentHandler->icon) }}"
                                     class="w-16 h-16 rounded-xl object-cover border-2 border-amber-300"
                                     alt="Текущая иконка">
                                <div>
                                    <p class="font-semibold text-amber-800 dark:text-amber-200">Текущая иконка</p>
                                    <p class="text-sm text-amber-600 dark:text-amber-300">Выберите новый файл для замены</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-purple-400 transition-all duration-300 cursor-pointer"
                             onclick="document.getElementById('icon').click()">
                            <input type="file" name="icon" id="icon" accept="image/*" class="hidden">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">Нажмите для выбора новой иконки</p>
                            <p class="text-sm text-gray-500">PNG, JPG, SVG до 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- СЕКЦИЯ: Режим автовыплат -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-robot text-white text-sm"></i>
                        </div>
                        Автоматические выплаты
                    </h2>

                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900 dark:to-indigo-900 p-6 rounded-xl border border-purple-200 dark:border-purple-800">
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Левая колонка: Режим -->
                            <div class="space-y-4">
                                <label class="block text-sm font-semibold text-purple-700 dark:text-purple-300 uppercase tracking-wide mb-3">
                                    Режим обработки
                                </label>
                                
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all
                                    {{ $paymentHandler->withdrawal_mode === 'manual' ? 'border-gray-500 bg-white dark:bg-gray-800' : 'border-gray-200 hover:border-gray-400' }}">
                                    <input type="radio" name="withdrawal_mode" value="manual" 
                                           {{ $paymentHandler->withdrawal_mode === 'manual' ? 'checked' : '' }}
                                           class="w-4 h-4 text-gray-600">
                                    <span class="ml-3 font-medium text-gray-700 dark:text-gray-200">🖐️ Ручной</span>
                                </label>

                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all
                                    {{ $paymentHandler->withdrawal_mode === 'semi_auto' ? 'border-blue-500 bg-white dark:bg-gray-800' : 'border-gray-200 hover:border-blue-400' }}">
                                    <input type="radio" name="withdrawal_mode" value="semi_auto"
                                           {{ $paymentHandler->withdrawal_mode === 'semi_auto' ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600">
                                    <span class="ml-3 font-medium text-blue-700 dark:text-blue-200">⚡ Полуавтомат (с подтверждением)</span>
                                </label>

                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all
                                    {{ $paymentHandler->withdrawal_mode === 'instant' ? 'border-green-500 bg-white dark:bg-gray-800' : 'border-gray-200 hover:border-green-400' }}">
                                    <input type="radio" name="withdrawal_mode" value="instant"
                                           {{ $paymentHandler->withdrawal_mode === 'instant' ? 'checked' : '' }}
                                           class="w-4 h-4 text-green-600">
                                    <span class="ml-3 font-medium text-green-700 dark:text-green-200">🚀 Мгновенный (автоотправка)</span>
                                </label>
                            </div>

                            <!-- Правая колонка: Настройки -->
                            <div class="space-y-4">
                                <label class="block text-sm font-semibold text-purple-700 dark:text-purple-300 uppercase tracking-wide mb-3">
                                    Настройки
                                </label>

                                <div class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <input type="checkbox"
                                           name="auto_withdrawal_enabled"
                                           id="auto_withdrawal_enabled"
                                           value="1"
                                           {{ $paymentHandler->auto_withdrawal_enabled ? 'checked' : '' }}
                                           class="w-4 h-4 text-purple-600 rounded">
                                    <label for="auto_withdrawal_enabled" class="ml-3 font-medium text-gray-700 dark:text-gray-200">
                                        Включить автовыплаты
                                    </label>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Дневной лимит
                                    </label>
                                    <div class="relative">
                                        <input type="number"
                                               name="daily_auto_withdrawal_limit"
                                               value="{{ $paymentHandler->daily_auto_withdrawal_limit }}"
                                               step="0.01"
                                               min="0"
                                               placeholder="Без ограничений"
                                               class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-600 rounded-lg focus:border-purple-500 focus:ring-0 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                        <span class="absolute right-4 top-2.5 text-sm text-gray-500 dark:text-gray-400">{{ $paymentHandler->currency }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <input type="checkbox"
                                           name="require_admin_approval"
                                           id="require_admin_approval"
                                           value="1"
                                           {{ $paymentHandler->require_admin_approval ? 'checked' : '' }}
                                           class="w-4 h-4 text-purple-600 rounded">
                                    <label for="require_admin_approval" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Требовать одобрение админа
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fees Section -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-teal-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-percentage text-white text-sm"></i>
                        </div>
                        Комиссии
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900 dark:to-emerald-900 p-6 rounded-xl border border-green-200 dark:border-green-800">
                            <label class="block text-sm font-semibold text-green-700 dark:text-green-300 uppercase tracking-wide mb-2">
                                <i class="fas fa-arrow-down mr-2"></i>Комиссия за депозит (%)
                            </label>
                            <input type="number"
                                   name="deposit_fee"
                                   value="{{$paymentHandler->deposit_fee}}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-4 py-3 border-2 border-green-200 dark:border-green-600 rounded-xl focus:border-green-500 focus:ring-0 bg-white dark:bg-green-800 text-gray-900 dark:text-white transition-all duration-300">
                        </div>

                        <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900 dark:to-pink-900 p-6 rounded-xl border border-red-200 dark:border-red-800">
                            <label class="block text-sm font-semibold text-red-700 dark:text-red-300 uppercase tracking-wide mb-2">
                                <i class="fas fa-arrow-up mr-2"></i>Комиссия за вывод (%)
                            </label>
                            <input type="number"
                                   name="withdrawal_fee"
                                   value="{{$paymentHandler->withdrawal_fee}}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-4 py-3 border-2 border-red-200 dark:border-red-600 rounded-xl focus:border-red-500 focus:ring-0 bg-white dark:bg-red-800 text-gray-900 dark:text-white transition-all duration-300">
                        </div>
                    </div>
                </div>

                <!-- Limits Section -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-sliders-h text-white text-sm"></i>
                        </div>
                        Лимиты операций
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Deposit Limits -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900 p-6 rounded-xl border border-blue-200 dark:border-blue-800">
                            <h3 class="text-lg font-bold text-blue-800 dark:text-blue-200 mb-4 flex items-center">
                                <i class="fas fa-download mr-2"></i>Лимиты депозитов
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-blue-700 dark:text-blue-300 mb-2">
                                        Минимальная сумма
                                    </label>
                                    <input type="number"
                                           name="min_deposit_limit"
                                           value="{{$paymentHandler->min_deposit_limit}}"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-4 py-3 border-2 border-blue-200 dark:border-blue-600 rounded-xl focus:border-blue-500 focus:ring-0 bg-white dark:bg-blue-800 text-gray-900 dark:text-white transition-all duration-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-blue-700 dark:text-blue-300 mb-2">
                                        Максимальная сумма
                                    </label>
                                    <input type="number"
                                           name="max_deposit_limit"
                                           value="{{$paymentHandler->max_deposit_limit}}"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-4 py-3 border-2 border-blue-200 dark:border-blue-600 rounded-xl focus:border-blue-500 focus:ring-0 bg-white dark:bg-blue-800 text-gray-900 dark:text-white transition-all duration-300">
                                </div>
                            </div>
                        </div>

                        <!-- Withdrawal Limits -->
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900 dark:to-pink-900 p-6 rounded-xl border border-purple-200 dark:border-purple-800">
                            <h3 class="text-lg font-bold text-purple-800 dark:text-purple-200 mb-4 flex items-center">
                                <i class="fas fa-upload mr-2"></i>Лимиты выводов
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-purple-700 dark:text-purple-300 mb-2">
                                        Минимальная сумма
                                    </label>
                                    <input type="number"
                                           name="min_withdrawal_limit"
                                           value="{{$paymentHandler->min_withdrawal_limit}}"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-4 py-3 border-2 border-purple-200 dark:border-purple-600 rounded-xl focus:border-purple-500 focus:ring-0 bg-white dark:bg-purple-800 text-gray-900 dark:text-white transition-all duration-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-purple-700 dark:text-purple-300 mb-2">
                                        Максимальная сумма
                                    </label>
                                    <input type="number"
                                           name="max_withdrawal_limit"
                                           value="{{$paymentHandler->max_withdrawal_limit}}"
                                           step="0.01"
                                           min="0"
                                           class="w-full px-4 py-3 border-2 border-purple-200 dark:border-purple-600 rounded-xl focus:border-purple-500 focus:ring-0 bg-white dark:bg-purple-800 text-gray-900 dark:text-white transition-all duration-300">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
