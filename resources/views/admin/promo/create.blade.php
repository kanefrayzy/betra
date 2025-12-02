@extends('panel')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-pink-500 to-rose-600 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Создание промокода</h1>
                    <p class="text-pink-100 mt-1">Настройка нового промокода для пользователей</p>
                </div>
            </div>
            <a
                href="{{ route('admin.promo.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 text-white font-medium rounded-xl hover:bg-opacity-30 transition-all duration-200 backdrop-blur"
            >
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Назад к списку
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('admin.promo.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Amount Type -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                            </svg>
                            <span>Тип суммы</span>
                        </div>
                    </label>
                    <select
                        name="amount_type"
                        id="amountType"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                    >
                        <option value="fixed">💰 Фиксированная сумма</option>
                        <option value="random">🎲 Случайная сумма</option>
                    </select>
                </div>

                <!-- Fixed Amount Block -->
                <div id="fixedAmountBlock" class="space-y-4 p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-green-800 dark:text-green-300">Фиксированная сумма</h3>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-green-700 dark:text-green-300">Сумма промокода</label>
                        <div class="relative">
                            <input
                                type="number"
                                name="amount"
                                class="w-full pl-8 pr-4 py-3 border border-green-300 dark:border-green-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200 @error('amount') border-red-500 ring-2 ring-red-500 @enderror"
                                step="0.01"
                                placeholder="100.00"
                                value="{{ old('amount') }}"
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-green-500 text-sm">$</span>
                            </div>
                        </div>
                        @error('amount')
                            <div class="flex items-center mt-2 text-sm text-red-600 dark:text-red-400">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Random Amount Block -->
                <div id="randomAmountBlock" class="space-y-4 p-6 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border border-purple-200 dark:border-purple-800 hidden">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-300">Случайная сумма</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-purple-700 dark:text-purple-300">Минимальная сумма</label>
                            <div class="relative">
                                <input
                                    type="number"
                                    name="min_amount"
                                    class="w-full pl-8 pr-4 py-3 border border-purple-300 dark:border-purple-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200 @error('min_amount') border-red-500 ring-2 ring-red-500 @enderror"
                                    step="0.01"
                                    placeholder="10.00"
                                    value="{{ old('min_amount') }}"
                                >
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-purple-500 text-sm">$</span>
                                </div>
                            </div>
                            @error('min_amount')
                                <div class="flex items-center mt-2 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-purple-700 dark:text-purple-300">Максимальная сумма</label>
                            <div class="relative">
                                <input
                                    type="number"
                                    name="max_amount"
                                    class="w-full pl-8 pr-4 py-3 border border-purple-300 dark:border-purple-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200 @error('max_amount') border-red-500 ring-2 ring-red-500 @enderror"
                                    step="0.01"
                                    placeholder="100.00"
                                    value="{{ old('max_amount') }}"
                                >
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-purple-500 text-sm">$</span>
                                </div>
                            </div>
                            @error('max_amount')
                                <div class="flex items-center mt-2 text-sm text-red-600 dark:text-red-400">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Usage Limit -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            <span>Лимит использований</span>
                        </div>
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="usage_limit"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200 @error('usage_limit') border-red-500 ring-2 ring-red-500 @enderror"
                            placeholder="Оставьте пустым для безлимитного использования"
                            value="{{ old('usage_limit') }}"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                    </div>
                    @error('usage_limit')
                        <div class="flex items-center mt-2 text-sm text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Если не указано, промокод будет доступен для неограниченного количества использований
                    </p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <p class="font-medium mb-1">Важная информация:</p>
                            <ul class="space-y-1 text-blue-700 dark:text-blue-300">
                                <li>• Код промокода будет сгенерирован автоматически</li>
                                <li>• Фиксированная сумма выдает одинаковую награду всем пользователям</li>
                                <li>• Случайная сумма выбирает значение в указанном диапазоне</li>
                                <li>• Промокод станет активным сразу после создания</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="submit"
                        class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 dark:focus:ring-offset-gray-800 transition-all duration-200 transform hover:scale-105 shadow-lg"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        Создать промокод
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountType = document.getElementById('amountType');
    const fixedBlock = document.getElementById('fixedAmountBlock');
    const randomBlock = document.getElementById('randomAmountBlock');

    // Функция для переключения блоков с анимацией
    function toggleAmountBlocks() {
        if (amountType.value === 'fixed') {
            // Показываем фиксированный блок
            fixedBlock.classList.remove('hidden');
            randomBlock.classList.add('hidden');

            // Очищаем поля random при переключении
            randomBlock.querySelectorAll('input').forEach(input => input.value = '');
        } else {
            // Показываем случайный блок
            fixedBlock.classList.add('hidden');
            randomBlock.classList.remove('hidden');

            // Очищаем поле fixed при переключении
            fixedBlock.querySelectorAll('input').forEach(input => input.value = '');
        }
    }

    // Вызываем функцию при загрузке страницы
    toggleAmountBlocks();

    // Добавляем обработчик события изменения select
    amountType.addEventListener('change', function() {
        // Добавляем анимацию перехода
        const activeBlock = amountType.value === 'fixed' ? fixedBlock : randomBlock;
        const inactiveBlock = amountType.value === 'fixed' ? randomBlock : fixedBlock;

        // Анимация исчезновения
        inactiveBlock.style.opacity = '0';
        inactiveBlock.style.transform = 'translateY(-10px)';

        setTimeout(() => {
            toggleAmountBlocks();

            // Анимация появления
            activeBlock.style.opacity = '0';
            activeBlock.style.transform = 'translateY(-10px)';

            setTimeout(() => {
                activeBlock.style.transition = 'all 0.3s ease';
                activeBlock.style.opacity = '1';
                activeBlock.style.transform = 'translateY(0)';
            }, 50);
        }, 150);
    });

    // Добавляем плавные переходы
    fixedBlock.style.transition = 'all 0.3s ease';
    randomBlock.style.transition = 'all 0.3s ease';
});
</script>

@endsection
