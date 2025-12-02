@extends('panel')
@php $baseUrl = 'panel8808'; @endphp

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-yellow-600 to-orange-700 rounded-2xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Редактировать баннер</h1>
                    <p class="text-yellow-100 mt-1">Изменение существующего баннера "{{ $banner->title }}"</p>
                </div>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.banners.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 text-white rounded-lg font-medium hover:bg-opacity-30 transition-colors backdrop-blur">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                    Назад к списку
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
        <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column: Basic Info -->
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Название баннера
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $banner->title) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                               placeholder="Введите название баннера..."
                               required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Описание (опционально)
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                  placeholder="Краткое описание баннера...">{{ old('description', $banner->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="link" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Ссылка (опционально)
                        </label>
                        <input type="url" 
                               id="link" 
                               name="link" 
                               value="{{ old('link', $banner->link) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                               placeholder="https://example.com">
                        @error('link')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Тип баннера
                            </label>
                            <select id="type" 
                                    name="type" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                    required>
                                <option value="main" {{ old('type', $banner->type) === 'main' ? 'selected' : '' }}>Главный</option>
                                <option value="small" {{ old('type', $banner->type) === 'small' ? 'selected' : '' }}>Маленький</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="locale" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Язык
                            </label>
                            <select id="locale" 
                                    name="locale" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                    required>
                                <option value="ru" {{ old('locale', $banner->locale) === 'ru' ? 'selected' : '' }}>Русский</option>
                                <option value="en" {{ old('locale', $banner->locale) === 'en' ? 'selected' : '' }}>English</option>
                                <option value="kz" {{ old('locale', $banner->locale) === 'kz' ? 'selected' : '' }}>Қазақша</option>
                                <option value="tr" {{ old('locale', $banner->locale) === 'tr' ? 'selected' : '' }}>Türkçe</option>
                                <option value="az" {{ old('locale', $banner->locale) === 'az' ? 'selected' : '' }}>Azərbaycan</option>
                                <option value="uz" {{ old('locale', $banner->locale) === 'uz' ? 'selected' : '' }}>Узбекский</option>
                            </select>
                            @error('locale')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Порядок сортировки
                            </label>
                            <input type="number" 
                                   id="order" 
                                   name="order" 
                                   value="{{ old('order', $banner->order) }}"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            @error('order')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-end">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $banner->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 dark:border-gray-600 text-yellow-600 focus:ring-yellow-500 dark:bg-gray-700">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Активен</span>
                            </label>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Дата начала
                            </label>
                            <input type="datetime-local" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date', $banner->start_date ? $banner->start_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Дата окончания
                            </label>
                            <input type="datetime-local" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date', $banner->end_date ? $banner->end_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            @error('end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Column: Images -->
                <div class="space-y-6">
                    <!-- Current Desktop Image -->
                    @if($banner->image)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Текущее изображение для ПК
                            </label>
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                                <img src="{{ asset($banner->image) }}" 
                                     alt="{{ $banner->title }}" 
                                     class="max-w-full h-48 object-cover rounded-lg">
                            </div>
                        </div>
                    @endif

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $banner->image ? 'Заменить изображение для ПК' : 'Изображение для ПК' }}
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*"
                                   class="hidden">
                            <label for="image" class="cursor-pointer">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="text-gray-600 dark:text-gray-400">Нажмите для выбора нового изображения</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">PNG, JPG, WEBP до 10MB</p>
                            </label>
                        </div>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        
                        <!-- New Image Preview -->
                        <div id="image-preview" class="mt-4 hidden">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Новое изображение:</p>
                            <img id="preview-img" src="" alt="Превью" class="max-w-full h-48 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                        </div>
                    </div>

                    <!-- Current Mobile Image -->
                    @if($banner->mobile_image)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Текущее изображение для мобильных
                            </label>
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                                <img src="{{ asset($banner->mobile_image) }}" 
                                     alt="{{ $banner->title }}" 
                                     class="max-w-full h-48 object-cover rounded-lg">
                            </div>
                        </div>
                    @endif

                    <div>
                        <label for="mobile_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $banner->mobile_image ? 'Заменить изображение для мобильных' : 'Изображение для мобильных' }} (опционально)
                        </label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                            <input type="file" 
                                   id="mobile_image" 
                                   name="mobile_image" 
                                   accept="image/*"
                                   class="hidden">
                            <label for="mobile_image" class="cursor-pointer">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="text-gray-600 dark:text-gray-400">Нажмите для выбора нового изображения</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">PNG, JPG, WEBP до 10MB</p>
                            </label>
                        </div>
                        @error('mobile_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        
                        <!-- New Mobile Image Preview -->
                        <div id="mobile-image-preview" class="mt-4 hidden">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Новое мобильное изображение:</p>
                            <img id="mobile-preview-img" src="" alt="Превью мобильное" class="max-w-full h-48 object-cover rounded-lg border border-gray-200 dark:border-gray-600">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.banners.index') }}" 
                   class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Отмена
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    Обновить баннер
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Image preview functionality
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('mobile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('mobile-preview-img').src = e.target.result;
            document.getElementById('mobile-image-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection

@section('title', 'Редактировать баннер')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Редактировать баннер</h1>
        <a href="{{ route('admin.banners.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            Назад к списку
        </a>
    </div>

    <div class="bg-gray-800 rounded-lg p-6">
        <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Основная информация -->
                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Название</label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $banner->title) }}"
                               class="w-full bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Описание</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="w-full bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $banner->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="link" class="block text-sm font-medium text-gray-300 mb-2">Ссылка</label>
                        <input type="url" 
                               id="link" 
                               name="link" 
                               value="{{ old('link', $banner->link) }}"
                               class="w-full bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="https://example.com">
                        @error('link')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-300 mb-2">Тип баннера</label>
                            <select id="type" 
                                    name="type" 
                                    class="w-full bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                                <option value="main" {{ old('type', $banner->type) === 'main' ? 'selected' : '' }}>Главный</option>
                                <option value="small" {{ old('type', $banner->type) === 'small' ? 'selected' : '' }}>Маленький</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="locale" class="block text-sm font-medium text-gray-300 mb-2">Язык</label>
                            <select id="locale" 
                                    name="locale" 
                                    class="w-full bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                                <option value="ru" {{ old('locale', $banner->locale) === 'ru' ? 'selected' : '' }}>Русский</option>
                                <option value="en" {{ old('locale', $banner->locale) === 'en' ? 'selected' : '' }}>English</option>
                                <option value="kz" {{ old('locale', $banner->locale) === 'kz' ? 'selected' : '' }}>Қазақша</option>
                                <option value="tr" {{ old('locale', $banner->locale) === 'tr' ? 'selected' : '' }}>Türkçe</option>
                                <option value="az" {{ old('locale', $banner->locale) === 'az' ? 'selected' : '' }}>Azərbaycan</option>
                                <option value="uz" {{ old('locale', $banner->locale) === 'uz' ? 'selected' : '' }}>Узбекский</option>
                            </select>
                            @error('locale')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-300 mb-2">Порядок сортировки</label>
                            <input type="number" 
                                   id="order" 
                                   name="order" 
                                   value="{{ old('order', $banner->order) }}"
                                   min="0"
                                   class="w-full bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('order')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-end">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $banner->is_active) ? 'checked' : '' }}
                                       class="mr-2 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                                <span class="text-gray-300">Активен</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Изображения -->
                <div class="space-y-4">
                    <!-- Текущее изображение для ПК -->
                    @if($banner->image)
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Текущее изображение для ПК</label>
                            <img src="{{ asset($banner->image) }}" 
                                 alt="{{ $banner->title }}" 
                                 class="max-w-full h-32 object-cover rounded mb-2">
                        </div>
                    @endif

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-300 mb-2">
                            {{ $banner->image ? 'Заменить изображение для ПК' : 'Изображение для ПК' }}
                        </label>
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               class="w-full bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-gray-400 text-sm mt-1">Рекомендуемый размер: 800x400px</p>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        
                        <!-- Превью нового изображения -->
                        <div id="image-preview" class="mt-3 hidden">
                            <img id="preview-img" src="" alt="Превью" class="max-w-full h-32 object-cover rounded">
                        </div>
                    </div>

                    <!-- Текущее мобильное изображение -->
                    @if($banner->mobile_image)
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Текущее изображение для мобильных</label>
                            <img src="{{ asset($banner->mobile_image) }}" 
                                 alt="{{ $banner->title }}" 
                                 class="max-w-full h-32 object-cover rounded mb-2">
                        </div>
                    @endif

                    <div>
                        <label for="mobile_image" class="block text-sm font-medium text-gray-300 mb-2">
                            {{ $banner->mobile_image ? 'Заменить изображение для мобильных' : 'Изображение для мобильных' }} (опционально)
                        </label>
                        <input type="file" 
                               id="mobile_image" 
                               name="mobile_image" 
                               accept="image/*"
                               class="w-full bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-gray-400 text-sm mt-1">Рекомендуемый размер: 400x300px</p>
                        @error('mobile_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        
                        <!-- Превью нового мобильного изображения -->
                        <div id="mobile-image-preview" class="mt-3 hidden">
                            <img id="mobile-preview-img" src="" alt="Превью мобильное" class="max-w-full h-32 object-cover rounded">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-300 mb-2">Дата начала</label>
                            <input type="datetime-local" 
                                   id="start_date" 
                                   name="start_date" 
                                   value="{{ old('start_date', $banner->start_date ? $banner->start_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-300 mb-2">Дата окончания</label>
                            <input type="datetime-local" 
                                   id="end_date" 
                                   name="end_date" 
                                   value="{{ old('end_date', $banner->end_date ? $banner->end_date->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <a href="{{ route('admin.banners.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded">
                    Отмена
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                    Обновить баннер
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Превью изображений
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('mobile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('mobile-preview-img').src = e.target.result;
            document.getElementById('mobile-image-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection