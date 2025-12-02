@extends('panel')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Создать категорию</h1>
                        <p class="text-green-100 mt-1">Добавить новую категорию игр</p>
                    </div>
                </div>
                <div class="text-right">
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur text-white rounded-xl font-semibold hover:bg-opacity-30 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                        </svg>
                        Назад к категориям
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            @csrf

            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                        </svg>
                    </div>
                    Информация о категории
                </h2>
            </div>

            <div class="p-8 space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Название категории <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           placeholder="Например: Слоты, Live игры, Рулетки..."
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" 
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Slug (URL)
                    </label>
                    <input type="text" 
                           name="slug" 
                           value="{{ old('slug') }}" 
                           placeholder="slots, live-games (оставьте пустым для автогенерации)"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Автоматически создастся из названия, если оставить пустым</p>
                    @error('slug')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Описание
                    </label>
                    <textarea name="description" 
                              rows="3" 
                              placeholder="Краткое описание категории..."
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Color -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Цвет категории
                    </label>
                    <div class="flex items-center space-x-4">
                        <input type="color" 
                               name="color" 
                               value="{{ old('color', '#ffb300') }}" 
                               class="h-12 w-20 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Выберите цвет для визуального оформления категории</p>
                    </div>
                    @error('color')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Icon SVG -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Иконка (SVG код)
                    </label>
                    <textarea name="icon" 
                              rows="6" 
                              placeholder="<svg>...</svg>"
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white font-mono text-sm">{{ old('icon') }}</textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Вставьте SVG код иконки. Можно получить на 
                        <a href="https://heroicons.com/" target="_blank" class="text-green-600 hover:underline">heroicons.com</a>
                    </p>
                    @error('icon')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Checkboxes -->
                <div class="space-y-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Активна</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">Категория будет доступна для пользователей</span>
                        </span>
                    </label>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="show_on_homepage" 
                               value="1" 
                               {{ old('show_on_homepage', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-3">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">Показывать на главной</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">Категория будет отображаться на главной странице</span>
                        </span>
                    </label>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-xl font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Отмена
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Создать категорию
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
