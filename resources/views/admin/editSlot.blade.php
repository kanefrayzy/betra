@extends('panel')
@php $baseUrl = 'dicex'; @endphp

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur">
                        @if($game->image)
                            <img src="{{ $game->image }}" alt="{{ $game->name }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">{{ $game->name }}</h1>
                        <p class="text-indigo-100 mt-1 flex items-center space-x-4">
                            <span>Редактирование слота</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-20 backdrop-blur">
                                🎮 {{ $game->provider }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-indigo-100 text-sm mb-1">ID игры</div>
                    <div class="text-2xl font-bold">#{{ $game->id }}</div>
                </div>
            </div>
        </div>
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white bg-opacity-10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white bg-opacity-5 rounded-full -ml-24 -mb-24"></div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <form method="POST" action="{{ route('slotegrator_games.update', $game->id) }}" enctype="multipart/form-data" class="space-y-0">
            @csrf

            <!-- Basic Info Section -->
            <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    Основная информация
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                                <span>Название игры</span>
                            </div>
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ $game->name }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                            readonly
                        >
                        <p class="text-xs text-gray-500 dark:text-gray-400">Название устанавливается провайдером автоматически</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-4l-3 3-3-3H5a2 2 0 01-2-2V5zm5.5 6a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm5-1.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Провайдер</span>
                            </div>
                        </label>
                        <input
                            type="text"
                            name="provider"
                            value="{{ $game->provider }}"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                            readonly
                        >
                        <p class="text-xs text-gray-500 dark:text-gray-400">Провайдер определяется системой автоматически</p>
                    </div>
                </div>
            </div>
            <!-- Image Upload Section -->
            <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    Изображение игры
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Current Image -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Текущее изображение</h3>
                        <div class="relative">
                            @if($game->image)
                                <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl overflow-hidden shadow-lg">
                                    <img
                                        src="{{ $game->image }}"
                                        alt="{{ $game->name }}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                    >
                                </div>
                            @else
                                <div class="aspect-square bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center">
                                    <div class="text-center text-gray-400">
                                        <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                        <p>Изображение не загружено</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Upload New Image -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Загрузить новое изображение</h3>
                        <div class="space-y-4">
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-8 text-center hover:border-emerald-500 dark:hover:border-emerald-400 transition-colors duration-200">
                                <input
                                    type="file"
                                    name="image"
                                    id="image-upload"
                                    class="hidden"
                                    accept="image/*"
                                >
                                <label for="image-upload" class="cursor-pointer">
                                    <div class="space-y-4">
                                        <div class="w-16 h-16 mx-auto bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm5 5a1 1 0 011-1h1a1 1 0 110 2v5a1 1 0 11-2 0V9a1 1 0 01-1-1zM9 3a1 1 0 000 2v1a1 1 0 002 0V5a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-lg font-medium text-gray-900 dark:text-white">Нажмите для загрузки</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">или перетащите файл сюда</p>
                                        </div>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">
                                            PNG, JPG, GIF до 10MB
                                        </p>
                                    </div>
                                </label>
                            </div>
                            <div id="image-preview" class="hidden">
                                <img class="w-full h-48 object-cover rounded-xl border border-gray-200 dark:border-gray-600">
                            </div>
                        </div>
                    </div>
                </div>
            <!-- Game Settings Section -->
            <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    Настройки игры
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Activity Status -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                        <label class="block text-sm font-semibold text-blue-800 dark:text-blue-300 mb-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Активность</span>
                            </div>
                        </label>
                        <select
                            name="is_active"
                            class="w-full px-4 py-3 border border-blue-300 dark:border-blue-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            required
                        >
                            <option value="1" {{ $game->is_active == 1 ? 'selected' : '' }}>✅ Включена</option>
                            <option value="0" {{ $game->is_active == 0 ? 'selected' : '' }}>❌ Выключена</option>
                        </select>
                    </div>

                    <!-- Live Status -->
                    <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-red-200 dark:border-red-800">
                        <label class="block text-sm font-semibold text-red-800 dark:text-red-300 mb-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                </svg>
                                <span>Live игра</span>
                            </div>
                        </label>
                        <select
                            name="is_live"
                            class="w-full px-4 py-3 border border-red-300 dark:border-red-600 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            required
                        >
                            <option value="1" {{ $game->is_live == 1 ? 'selected' : '' }}>🔴 Да</option>
                            <option value="0" {{ $game->is_live == 0 ? 'selected' : '' }}>⚫ Нет</option>
                        </select>
                    </div>

                    <!-- High RTP -->
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl p-4 border border-yellow-200 dark:border-yellow-800">
                        <label class="block text-sm font-semibold text-yellow-800 dark:text-yellow-300 mb-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span>Повышенная отдача</span>
                            </div>
                        </label>
                        <select
                            name="is_higher"
                            class="w-full px-4 py-3 border border-yellow-300 dark:border-yellow-600 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            required
                        >
                            <option value="1" {{ $game->is_higher == 1 ? 'selected' : '' }}>🔥 Да</option>
                            <option value="0" {{ $game->is_higher == 0 ? 'selected' : '' }}>❄️ Нет</option>
                        </select>
                    </div>

                    <!-- New Game -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                        <label class="block text-sm font-semibold text-green-800 dark:text-green-300 mb-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                                <span>Новая игра</span>
                            </div>
                        </label>
                        <select
                            name="is_new"
                            class="w-full px-4 py-3 border border-green-300 dark:border-green-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            required
                        >
                            <option value="1" {{ $game->is_new == 1 ? 'selected' : '' }}>✨ Да</option>
                            <option value="0" {{ $game->is_new == 0 ? 'selected' : '' }}>🌙 Нет</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Homepage Categories Section -->
            <div class="p-8 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                        </svg>
                    </div>
                    Категории для главной страницы
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Popular -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
                        <label class="block text-sm font-semibold text-purple-800 dark:text-purple-300 mb-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span>Популярные</span>
                            </div>
                        </label>
                        <select
                            name="is_popular"
                            class="w-full px-4 py-3 border border-purple-300 dark:border-purple-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            required
                        >
                            <option value="1" {{ $game->is_popular == 1 ? 'selected' : '' }}>⭐ Да</option>
                            <option value="0" {{ $game->is_popular == 0 ? 'selected' : '' }}>⚪ Нет</option>
                        </select>
                    </div>

                    <!-- Table Games -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-xl p-4 border border-indigo-200 dark:border-indigo-800">
                        <label class="block text-sm font-semibold text-indigo-800 dark:text-indigo-300 mb-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Настольные игры</span>
                            </div>
                        </label>
                        <select
                            name="is_table"
                            class="w-full px-4 py-3 border border-indigo-300 dark:border-indigo-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            required
                        >
                            <option value="1" {{ $game->is_table == 1 ? 'selected' : '' }}>🃏 Да</option>
                            <option value="0" {{ $game->is_table == 0 ? 'selected' : '' }}>🎰 Нет</option>
                        </select>
                    </div>

                    <!-- Roulette -->
                    <div class="bg-gradient-to-br from-rose-50 to-red-50 dark:from-rose-900/20 dark:to-red-900/20 rounded-xl p-4 border border-rose-200 dark:border-rose-800">
                        <label class="block text-sm font-semibold text-rose-800 dark:text-rose-300 mb-3">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                </svg>
                                <span>Рулетка</span>
                            </div>
                        </label>
                        <select
                            name="is_roulette"
                            class="w-full px-4 py-3 border border-rose-300 dark:border-rose-600 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            required
                        >
                            <option value="1" {{ $game->is_roulette == 1 ? 'selected' : '' }}>🎲 Да</option>
                            <option value="0" {{ $game->is_roulette == 0 ? 'selected' : '' }}>🎰 Нет</option>
                        </select>
                    </div>

                    <!-- Additional Category Placeholder -->
                    <div class="bg-gradient-to-br from-gray-50 to-slate-50 dark:from-gray-700 dark:to-slate-700 rounded-xl p-4 border border-gray-200 dark:border-gray-600 flex items-center justify-center">
                        <div class="text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm">Дополнительные категории</div>
                        </div>
                    </div>
                </div>
            </div>

            </div>

            <!-- Submit Button -->
            <div class="bg-gray-50 dark:bg-gray-700 px-8 py-6">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <p>Последнее изменение: <span class="font-medium">{{ $game->updated_at ? $game->updated_at->format('d.m.Y H:i') : 'Не обновлялось' }}</span></p>
                    </div>
                    <button
                        type="submit"
                        class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-2xl"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Сохранить изменения
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image upload preview
    const imageUpload = document.getElementById('image-upload');
    const imagePreview = document.getElementById('image-preview');

    if (imageUpload) {
        imageUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = imagePreview.querySelector('img');
                    img.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Drag and drop functionality
    const dropZone = document.querySelector('label[for="image-upload"]').parentElement;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-emerald-500', 'bg-emerald-50', 'dark:bg-emerald-900/20');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            imageUpload.files = files;
            imageUpload.dispatchEvent(new Event('change'));
        }
    }
});
</script>

@endsection
