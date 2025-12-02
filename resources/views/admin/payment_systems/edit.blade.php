@extends('panel')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-violet-50 via-purple-50 to-fuchsia-50 dark:from-gray-900 dark:via-purple-900 dark:to-fuchsia-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.payment_systems.index') }}"
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-lg mb-6">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Назад к списку
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-violet-600 to-purple-600 p-8 text-white">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="{{ isset($paymentSystem) ? 'M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793z' : 'M10 18a8 8 0 100-16 8 8 0 000 16z' }}M{{ isset($paymentSystem) ? '11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z' : '9 5a1 1 0 012 0v2h2a1 1 0 110 2h-2v2a1 1 0 11-2 0v-2H7a1 1 0 110-2h2V7z' }}"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ isset($paymentSystem) ? 'Редактирование системы' : 'Новая платежная система' }}</h1>
                        <p class="text-lg opacity-90">{{ isset($paymentSystem) ? $paymentSystem->name : 'Создание интеграции с платежным сервисом' }}</p>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="p-6 bg-red-50 dark:bg-red-900/20 border-b border-red-200 dark:border-red-800">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-red-800 dark:text-red-200 mb-2">Ошибки валидации:</h3>
                            <ul class="list-disc list-inside space-y-1 text-red-700 dark:text-red-300">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ isset($paymentSystem) ? route('admin.payment_systems.update', $paymentSystem->id) : route('admin.payment_systems.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="p-8">
                @csrf
                @if(isset($paymentSystem))
                    @method('PUT')
                @endif

                <!-- Basic Information -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        Основная информация
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                Название системы
                            </label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $paymentSystem->name ?? '') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-violet-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300"
                                   placeholder="PayPal, Stripe, QIWI..."
                                   required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                Статус
                            </label>
                            <select name="active"
                                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-violet-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300">
                                <option value="1" {{ (old('active', $paymentSystem->active ?? 0) == 1) ? 'selected' : '' }}>Активна</option>
                                <option value="0" {{ (old('active', $paymentSystem->active ?? 0) == 0) ? 'selected' : '' }}>Неактивна</option>
                            </select>
                        </div>

                        <div class="space-y-2 lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                                API URL
                            </label>
                            <input type="url"
                                   name="url"
                                   value="{{ old('url', $paymentSystem->url ?? '') }}"
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:border-violet-500 focus:ring-0 bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all duration-300"
                                   placeholder="https://api.paymentservice.com"
                                   required>
                        </div>
                    </div>

                    <!-- Logo Upload -->
                    <div class="mt-8">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">
                            Логотип системы
                        </label>

                        @if(isset($paymentSystem) && $paymentSystem->logo)
                        <div class="mb-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900 dark:to-orange-900 rounded-xl border border-amber-200 dark:border-amber-800">
                            <div class="flex items-center space-x-4">
                                <img src="{{ asset('storage/' . $paymentSystem->logo) }}"
                                     class="w-20 h-20 rounded-xl object-cover border-2 border-amber-300"
                                     alt="Текущий логотип">
                                <div>
                                    <p class="font-semibold text-amber-800 dark:text-amber-200">Текущий логотип</p>
                                    <p class="text-sm text-amber-600 dark:text-amber-300">Выберите новый файл для замены</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-violet-400 transition-all duration-300 cursor-pointer"
                             onclick="document.getElementById('logo').click()">
                            <input type="file" name="logo" id="logo" accept="image/*" class="hidden">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">Нажмите для выбора логотипа</p>
                            <p class="text-sm text-gray-500">PNG, JPG, SVG до 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- Merchant Configuration -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        Конфигурация мерчанта
                    </h2>

                    <div class="space-y-6">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900 p-6 rounded-xl border border-blue-200 dark:border-blue-800">
                            <label class="block text-sm font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wide mb-3">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Merchant ID
                            </label>
                            <input type="text"
                                   name="merchant_id"
                                   value="{{ old('merchant_id', $paymentSystem->merchant_id ?? '') }}"
                                   class="w-full px-4 py-3 border-2 border-blue-200 dark:border-blue-600 rounded-xl focus:border-blue-500 focus:ring-0 bg-white dark:bg-blue-800 text-gray-900 dark:text-white transition-all duration-300 font-mono"
                                   placeholder="merchant_123456789"
                                   required>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900 dark:to-pink-900 p-6 rounded-xl border border-red-200 dark:border-red-800">
                                <label class="block text-sm font-semibold text-red-700 dark:text-red-300 uppercase tracking-wide mb-3">
                                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Merchant Secret 1 (обязательный)
                                </label>
                                <input type="password"
                                       name="merchant_secret_1"
                                       value="{{ old('merchant_secret_1', $paymentSystem->merchant_secret_1 ?? '') }}"
                                       class="w-full px-4 py-3 border-2 border-red-200 dark:border-red-600 rounded-xl focus:border-red-500 focus:ring-0 bg-white dark:bg-red-800 text-gray-900 dark:text-white transition-all duration-300 font-mono"
                                       placeholder="Секретный ключ API"
                                       required>
                            </div>

                            <div class="bg-gradient-to-br from-orange-50 to-yellow-50 dark:from-orange-900 dark:to-yellow-900 p-6 rounded-xl border border-orange-200 dark:border-orange-800">
                                <label class="block text-sm font-semibold text-orange-700 dark:text-orange-300 uppercase tracking-wide mb-3">
                                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Merchant Secret 2 (опциональный)
                                </label>
                                <input type="password"
                                       name="merchant_secret_2"
                                       value="{{ old('merchant_secret_2', $paymentSystem->merchant_secret_2 ?? '') }}"
                                       class="w-full px-4 py-3 border-2 border-orange-200 dark:border-orange-600 rounded-xl focus:border-orange-500 focus:ring-0 bg-white dark:bg-orange-800 text-gray-900 dark:text-white transition-all duration-300 font-mono"
                                       placeholder="Дополнительный ключ (если нужен)">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-8 py-4 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ isset($paymentSystem) ? 'Сохранить изменения' : 'Создать систему' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Logo upload preview
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const uploadZone = this.parentElement;

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            uploadZone.innerHTML = `
                <img src="${e.target.result}" class="w-32 h-32 object-cover rounded-xl mx-auto mb-4">
                <p class="text-lg font-semibold text-gray-600 dark:text-gray-400">${file.name}</p>
                <p class="text-sm text-gray-500">Нажмите для изменения</p>
            `;
        };
        reader.readAsDataURL(file);
    }
});

// Show/hide password
document.querySelectorAll('input[type="password"]').forEach(input => {
    const container = input.parentElement;
    const toggleBtn = document.createElement('button');
    toggleBtn.type = 'button';
    toggleBtn.className = 'absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600';
    toggleBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/></svg>';

    container.style.position = 'relative';
    input.parentElement.appendChild(toggleBtn);

    toggleBtn.addEventListener('click', () => {
        if (input.type === 'password') {
            input.type = 'text';
            toggleBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>';
        } else {
            input.type = 'password';
            toggleBtn.innerHTML = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/></svg>';
        }
    });
});
</script>

@endsection
