@extends('panel')
@php $baseUrl = 'panel8808'; @endphp

@section('content')

<div class="space-y-8 animate-fade-in">


    <!-- Profit Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-right">
                    <div class="text-sm text-green-100 mb-1">Сегодня</div>
                    <div class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></div>
                </div>
            </div>
            <h3 class="text-lg font-medium text-green-100 mb-2">Общий профит за сегодня</h3>
            <p class="text-3xl font-bold">${{ number_format($payTodayInUSD - $withTodayInUSD, 2) }}</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-right">
                    <div class="text-sm text-blue-100 mb-1">Вчера</div>
                    <div class="w-2 h-2 bg-blue-300 rounded-full"></div>
                </div>
            </div>
            <h3 class="text-lg font-medium text-blue-100 mb-2">Общий профит за вчера</h3>
            <p class="text-3xl font-bold">${{ number_format($payYesterdayInUSD - $withYesterdayInUSD, 2) }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl p-6 text-white shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-right">
                    <div class="text-sm text-purple-100 mb-1">Всего</div>
                    <div class="w-2 h-2 bg-purple-300 rounded-full"></div>
                </div>
            </div>
            <h3 class="text-lg font-medium text-purple-100 mb-2">Общий профит за все время</h3>
            <p class="text-3xl font-bold">${{ number_format($totalDepositsInUSD - $totalWithdrawalsInUSD, 2) }}</p>
        </div>
    </div>

    <!-- Main Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-auto">
                    <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
                </div>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Общая сумма пополнений</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalDepositsInUSD, 2) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-xl">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-auto">
                    <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                </div>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Общая сумма выводов</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalWithdrawalsInUSD, 2) }}</p>
        </div>
    </div>

    <!-- Daily Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-green-400 to-emerald-500 rounded-xl text-white group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded-full">Сегодня</span>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Пополнения сегодня</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($payTodayInUSD, 2) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-xl text-white group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded-full">Вчера</span>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Пополнения вчера</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($payYesterdayInUSD, 2) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-purple-400 to-pink-500 rounded-xl text-white group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <span class="text-xs bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 px-2 py-1 rounded-full">7 дней</span>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Пополнения за неделю</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($payWeekInUSD, 2) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-gradient-to-r from-orange-400 to-red-500 rounded-xl text-white group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-xs bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400 px-2 py-1 rounded-full">30 дней</span>
            </div>
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Пополнения за месяц</h3>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($payMonthInUSD, 2) }}</p>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
            </div>
            Другая Статистика
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 rounded-xl p-6 border border-cyan-200 dark:border-cyan-800">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-cyan-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Всего пользователей</h3>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $additionalStats['totalUsers'] }} <span class="text-sm font-normal text-gray-500">чел.</span></p>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-green-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-auto">
                        <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded-full">Сегодня</span>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Зарегались сегодня</h3>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $additionalStats['newUsersToday'] }} <span class="text-sm font-normal text-gray-500">чел.</span></p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6 border border-purple-200 dark:border-purple-800">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-purple-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 3a1 1 0 00-1.447-.894L8.763 6H5a3 3 0 000 6h.28l1.771 5.316A1 1 0 008 18h1a1 1 0 001-1v-4.382l6.553 3.276A1 1 0 0018 15V3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Подтвердили телеграм</h3>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $additionalStats['telegramConfirmed'] }} <span class="text-sm font-normal text-gray-500">чел.</span></p>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl p-6 border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-yellow-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732L14.146 12.8l-1.179 4.456a1 1 0 01-1.934 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732L9.854 7.2l1.179-4.456A1 1 0 0112 2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Сегодня бонус получили</h3>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $additionalStats['dailyBonusCount'] }} <span class="text-sm font-normal text-gray-500">чел.</span></p>
                <p class="text-sm text-gray-500 dark:text-gray-400">({{ $additionalStats['dailyBonusTotal'] }}$)</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-auto">
                        <span class="text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded-full">Вчера</span>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Зарегались вчера</h3>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $additionalStats['newUsersYesterday'] }} <span class="text-sm font-normal text-gray-500">чел.</span></p>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 rounded-xl p-6 border border-amber-200 dark:border-amber-800">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-amber-500 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732L14.146 12.8l-1.179 4.456a1 1 0 01-1.934 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732L9.854 7.2l1.179-4.456A1 1 0 0112 2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-auto">
                        <span class="text-xs bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 px-2 py-1 rounded-full">Вчера</span>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Вчера бонус получили</h3>
                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $additionalStats['dailyBonusCountYesterday'] }} <span class="text-sm font-normal text-gray-500">чел.</span></p>
                <p class="text-sm text-gray-500 dark:text-gray-400">({{ $additionalStats['dailyBonusTotalYesterday'] }}$)</p>
            </div>
        </div>
    </div>

    <!-- Currency Statistics -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-8 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                Статистика по валютам
            </h2>
        </div>

        <!-- Currency Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8 px-8" aria-label="Tabs">
                @foreach($statistics as $index => $stats)
                    <button
                        class="currency-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 @if($index == 0) border-blue-500 text-blue-600 dark:text-blue-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif"
                        data-tab="currency-{{ $index }}"
                        data-index="{{ $index }}"
                    >
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($stats['currency_code'], 0, 1) }}
                            </div>
                            <span>{{ $stats['currency_name'] }} ({{ $stats['currency_code'] }})</span>
                        </div>
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Currency Tab Content -->
        <div class="p-8">
            @foreach($statistics as $index => $stats)
                <div class="currency-content @if($index != 0) hidden @endif" id="currency-{{ $index }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                            <div class="flex items-center mb-4">
                                <div class="p-3 bg-green-500 rounded-xl">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-auto">
                                    <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded-full">Всего</span>
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Общая сумма пополнений</h3>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['total_deposits'], 2) }} {{ $stats['currency_code'] }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 rounded-xl p-6 border border-red-200 dark:border-red-800">
                            <div class="flex items-center mb-4">
                                <div class="p-3 bg-red-500 rounded-xl">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-auto">
                                    <span class="text-xs bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 px-2 py-1 rounded-full">Всего</span>
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Общая сумма выводов</h3>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['total_withdrawals'], 2) }} {{ $stats['currency_code'] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-blue-500 rounded-xl">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded-full">Сегодня</span>
                            </div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Пополнения сегодня</h3>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['pay_today'], 2) }} {{ $stats['currency_code'] }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-6 border border-orange-200 dark:border-orange-800">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-orange-500 rounded-xl">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xs bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400 px-2 py-1 rounded-full">Сегодня</span>
                            </div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Выводы сегодня</h3>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($stats['with_today'], 2) }} {{ $stats['currency_code'] }}</p>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6 border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xs bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 px-2 py-1 rounded-full">Сегодня</span>
                            </div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Профит сегодня</h3>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($stats['pay_today'] - $stats['with_today'], 2) }} {{ $stats['currency_code'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
// Currency tabs functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.currency-tab');
    const contents = document.querySelectorAll('.currency-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = this.dataset.tab;
            const index = this.dataset.index;

            // Remove active classes from all tabs
            tabs.forEach(t => {
                t.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                t.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            });

            // Add active classes to clicked tab
            this.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');

            // Hide all content
            contents.forEach(content => {
                content.classList.add('hidden');
            });

            // Show target content
            document.getElementById(targetId).classList.remove('hidden');
        });
    });
});
</script>

@endsection
