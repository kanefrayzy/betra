@extends('panel')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center space-x-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Telegram Рассылка</span>
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Отправка сообщений пользователям в Telegram
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="openTemplateManager()" class="flex items-center space-x-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Шаблоны</span>
            </button>
            <button onclick="refreshStats()" class="flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Обновить</span>
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Users -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Всего пользователей</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totalUsers) }}</p>
                    <p class="text-blue-100 text-xs mt-2">С подключенным Telegram</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Активные (7 дней)</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($activeUsers) }}</p>
                    <p class="text-green-100 text-xs mt-2">{{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0 }}% от общего числа</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Inactive Users -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Неактивные (>7 дней)</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($inactiveUsers) }}</p>
                    <p class="text-orange-100 text-xs mt-2">{{ $totalUsers > 0 ? round(($inactiveUsers / $totalUsers) * 100, 1) : 0 }}% от общего числа</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Broadcast Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Message Composer -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Создать рассылку</span>
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <form id="broadcastForm">
                        @csrf

                        <!-- Template Selection -->
                        @if(count($templates) > 0)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Выбрать шаблон (опционально)
                            </label>
                            <select id="template_select" onchange="loadTemplate(this.value)" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all">
                                <option value="">-- Без шаблона --</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" data-message="{{ $template->message }}" data-buttons="{{ $template->has_buttons }}" data-button-data="{{ $template->buttons ? json_encode($template->buttons) : '' }}">
                                        {{ $template->name }} @if($template->category)({{ $template->category }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Target Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                Кому отправить
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <label class="relative flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-2 border-transparent has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/20">
                                    <input type="radio" name="target" value="all" class="sr-only peer" checked onchange="toggleTargetFields('all')">
                                    <div class="flex items-center space-x-3 w-full">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center peer-checked:bg-blue-600 flex-shrink-0">
                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-300 peer-checked:text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 dark:text-white truncate">Всем</p>
                                            <p class="text-xs text-gray-500 truncate">{{ number_format($totalUsers) }} чел.</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-2 border-transparent has-[:checked]:border-green-500 has-[:checked]:bg-green-50 dark:has-[:checked]:bg-green-900/20">
                                    <input type="radio" name="target" value="active" class="sr-only peer" onchange="toggleTargetFields('active')">
                                    <div class="flex items-center space-x-3 w-full">
                                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center peer-checked:bg-green-600 flex-shrink-0">
                                            <svg class="w-6 h-6 text-green-600 dark:text-green-300 peer-checked:text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 dark:text-white truncate">Активным</p>
                                            <p class="text-xs text-gray-500 truncate">{{ number_format($activeUsers) }} чел.</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-2 border-transparent has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50 dark:has-[:checked]:bg-orange-900/20">
                                    <input type="radio" name="target" value="inactive" class="sr-only peer" onchange="toggleTargetFields('inactive')">
                                    <div class="flex items-center space-x-3 w-full">
                                        <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900 flex items-center justify-center peer-checked:bg-orange-600 flex-shrink-0">
                                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-300 peer-checked:text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 dark:text-white truncate">Неактивным</p>
                                            <p class="text-xs text-gray-500 truncate">{{ number_format($inactiveUsers) }} чел.</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-2 border-transparent has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/20">
                                    <input type="radio" name="target" value="specific" class="sr-only peer" onchange="toggleTargetFields('specific')">
                                    <div class="flex items-center space-x-3 w-full">
                                        <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center peer-checked:bg-purple-600 flex-shrink-0">
                                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-300 peer-checked:text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 dark:text-white truncate">Выборочно</p>
                                            <p class="text-xs text-gray-500 truncate">По ID</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors border-2 border-transparent has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 dark:has-[:checked]:bg-pink-900/20">
                                    <input type="radio" name="target" value="single" class="sr-only peer" onchange="toggleTargetFields('single')">
                                    <div class="flex items-center space-x-3 w-full">
                                        <div class="w-10 h-10 rounded-full bg-pink-100 dark:bg-pink-900 flex items-center justify-center peer-checked:bg-pink-600 flex-shrink-0">
                                            <svg class="w-6 h-6 text-pink-600 dark:text-pink-300 peer-checked:text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 dark:text-white truncate">Один юзер</p>
                                            <p class="text-xs text-gray-500 truncate">Для теста</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- User IDs Input (hidden by default) -->
                        <div id="userIdsBlock" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                ID пользователей
                            </label>
                            <input type="text" name="user_ids" id="user_ids" 
                                   placeholder="Введите ID через запятую (например: 1, 2, 3)"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Разделяйте ID запятыми</p>
                        </div>

                        <!-- Single User Search (hidden by default) -->
                        <div id="singleUserBlock" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Поиск пользователя
                            </label>
                            <div class="flex space-x-2">
                                <input type="text" id="user_search" 
                                       placeholder="Введите ID или username пользователя"
                                       class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all">
                                <button type="button" onclick="searchUser()" class="px-6 py-3 bg-pink-600 hover:bg-pink-700 text-white rounded-lg transition-colors flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <span>Найти</span>
                                </button>
                            </div>
                            <input type="hidden" name="single_user_id" id="single_user_id">
                            
                            <!-- User Info Display -->
                            <div id="userInfo" class="hidden mt-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border-2 border-pink-200 dark:border-pink-800">
                                <div class="flex items-center space-x-4">
                                    <img id="user_avatar" src="" class="w-12 h-12 rounded-full border-2 border-pink-400">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900 dark:text-white" id="user_username"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            ID: <span id="user_id_display"></span> | 
                                            Telegram ID: <span id="user_telegram_id"></span>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Последний вход: <span id="user_last_login"></span>
                                        </p>
                                    </div>
                                    <button type="button" onclick="clearUserSearch()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Текст сообщения
                                </label>
                                <span id="charCount" class="text-xs text-gray-500">0 / 4096</span>
                            </div>
                            <textarea name="message" id="message" rows="8" 
                                      placeholder="Введите текст сообщения... &#10;&#10;Доступные переменные:&#10;{sitename} - название сайта&#10;{username} - имя пользователя&#10;{date} - текущая дата&#10;{time} - текущее время"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none transition-all"
                                      maxlength="4096" required></textarea>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <button type="button" onclick="insertVariable('{sitename}')" class="text-xs px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors">
                                    + {sitename}
                                </button>
                                <button type="button" onclick="insertVariable('{username}')" class="text-xs px-3 py-1 bg-pink-100 dark:bg-pink-900 text-pink-700 dark:text-pink-300 rounded-full hover:bg-pink-200 dark:hover:bg-pink-800 transition-colors">
                                    + {username}
                                </button>
                                <button type="button" onclick="insertVariable('{date}')" class="text-xs px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                    + {date}
                                </button>
                                <button type="button" onclick="insertVariable('{time}')" class="text-xs px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 rounded-full hover:bg-purple-200 dark:hover:bg-purple-800 transition-colors">
                                    + {time}
                                </button>
                            </div>
                        </div>

                        <!-- Button Settings -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Добавить кнопку для открытия WebApp
                                </label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="has_buttons" id="has_buttons" class="sr-only peer" onchange="toggleButtonSettings(this.checked)">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <div id="buttonSettings" class="hidden space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Текст кнопки
                                    </label>
                                    <input type="text" name="button_text" id="button_text" 
                                           placeholder="Например: Играть сейчас"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        URL WebApp
                                    </label>
                                    <input type="url" name="button_url" id="button_url" 
                                           placeholder="https://your-webapp-url.com"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all">
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    💡 URL должен быть зарегистрирован как WebApp в настройках вашего Telegram бота
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" onclick="previewMessage()" class="flex-1 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Предпросмотр</span>
                            </button>
                            <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <span>Отправить</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Tips -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <h3 class="font-bold text-lg mb-3 flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                    <span>Советы</span>
                </h3>
                <ul class="space-y-2 text-sm text-indigo-100">
                    <li class="flex items-start space-x-2">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Используйте {username} для персонализации</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Сохраняйте часто используемые сообщения как шаблоны</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Тестируйте на одном пользователе перед массовой рассылкой</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Кнопки WebApp открывают ваше приложение в Telegram</span>
                    </li>
                </ul>
            </div>

            <!-- Recent Broadcasts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                        </svg>
                        <span>История рассылок</span>
                    </h3>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    @if(count($broadcastHistory) > 0)
                        <div class="space-y-3">
                            @foreach($broadcastHistory as $item)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($item['date'])->format('d.m.Y H:i') }}
                                        </span>
                                        <span class="text-xs px-2 py-1 rounded-full 
                                            @if($item['target'] == 'all') bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300
                                            @elseif($item['target'] == 'active') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                                            @elseif($item['target'] == 'inactive') bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300
                                            @elseif($item['target'] == 'single') bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-300
                                            @else bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300
                                            @endif">
                                            {{ 
                                                $item['target'] == 'all' ? 'Всем' : 
                                                ($item['target'] == 'active' ? 'Активным' : 
                                                ($item['target'] == 'inactive' ? 'Неактивным' : 
                                                ($item['target'] == 'single' ? 'Тест' : 'Выборочно')))
                                            }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2 line-clamp-2">{{ $item['message_preview'] }}</p>
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center space-x-3">
                                            <span class="text-gray-600 dark:text-gray-400">
                                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                                </svg>
                                                {{ $item['total_users'] }}
                                            </span>
                                            @if(isset($item['has_buttons']) && $item['has_buttons'])
                                                <span class="text-blue-600 dark:text-blue-400" title="С кнопкой">
                                                    <svg class="w-3 h-3 inline" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM4 8v6h12V8H4z"/>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="text-green-600 dark:text-green-400">✓ {{ $item['success'] }}</span>
                                            @if($item['failed'] > 0)
                                                <span class="text-red-600 dark:text-red-400">✗ {{ $item['failed'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        От: {{ $item['sent_by'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">История рассылок пуста</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.telegram-broadcast-modals')
@include('admin.telegram-broadcast-scripts')

@endsection
