@php $baseUrl = 'qwdkox1i20'; @endphp
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>{{$settings->sitename}} - Панель управления</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/images/favicon.png') }}" rel="shortcut icon" type="image/png">

    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('admin-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                    }
                }
            }
        }
        var cpBaseUrl = '{{$baseUrl}}';
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass-effect {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-item:hover {
            transform: translateX(4px);
            transition: all 0.2s ease;
        }

        /* Стили для сворачиваемого сайдбара */
        .sidebar-collapsed {
            width: 4.5rem !important; /* 72px */
        }

        .sidebar-collapsed .nav-text {
            opacity: 0;
            visibility: hidden;
            width: 0;
            transition: all 0.3s ease;
        }

        .sidebar-collapsed .nav-badge {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-collapsed .user-info {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-collapsed .site-title {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-collapsed .nav-item:hover {
            transform: none;
        }

        .nav-text {
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .nav-badge {
            transition: all 0.3s ease;
        }

        .user-info {
            transition: all 0.3s ease;
        }

        .site-title {
            transition: all 0.3s ease;
        }

        /* Tooltip для свернутого режима */
        .tooltip {
            position: absolute;
            left: 4.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 1000;
            pointer-events: none;
        }

        .dark .tooltip {
            background: rgba(255, 255, 255, 0.9);
            color: black;
        }

        .sidebar-collapsed .nav-item:hover .tooltip {
            opacity: 1;
            visibility: visible;
        }

        /* Аккордеон стили */
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            opacity: 0;
        }

        .accordion-content.active {
            max-height: 2000px;
            opacity: 1;
            transition: max-height 0.5s ease-in, opacity 0.3s ease-in;
        }

        .accordion-header {
            cursor: pointer;
            position: relative;
            background: rgba(243, 244, 246, 0.5);
            border: 1px solid transparent;
            transition: background 0.2s ease;
            height: 40px;
        }

        .dark .accordion-header {
            background: rgba(31, 41, 55, 0.5);
        }

        .accordion-header:hover {
            background: rgba(229, 231, 235, 0.8);
        }

        .dark .accordion-header:hover {
            background: rgba(55, 65, 81, 0.6);
        }

        .accordion-header.active {
            background: rgba(219, 234, 254, 1);
            border-color: rgba(59, 130, 246, 0.2);
        }

        .dark .accordion-header.active {
            background: rgba(30, 58, 138, 0.3);
            border-color: rgba(59, 130, 246, 0.3);
        }

        .accordion-header svg.chevron {
            transition: transform 0.2s ease;
            color: #9ca3af;
        }

        .dark .accordion-header svg.chevron {
            color: #6b7280;
        }

        .accordion-header.active svg.chevron {
            transform: rotate(180deg);
            color: rgb(59, 130, 246);
        }

        .dark .accordion-header.active svg.chevron {
            color: rgb(96, 165, 250);
        }

        /* Скрыть стрелочки аккордеона при свернутом сайдбаре */
        .sidebar-collapsed .accordion-header svg.chevron {
            opacity: 0;
            visibility: hidden;
        }

        /* Скрыть контент аккордеона при свернутом сайдбаре */
        .sidebar-collapsed .accordion-content {
            display: none;
        }

        .sidebar-collapsed .accordion-header {
            background: transparent;
            border-color: transparent;
        }

        /* Стили для текста заголовка */
        .accordion-header span {
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .accordion-header.active span {
            color: rgb(59, 130, 246);
        }

        .dark .accordion-header.active span {
            color: rgb(96, 165, 250);
        }

        /* Анимация для main content */
        .main-content {
            margin-left: 20rem; /* 320px */
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 4.5rem; /* 72px */
        }

        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
            }
            .main-content.expanded {
                margin-left: 0;
            }
        }
        /* ========== UNIFIED SCROLLBAR STYLING ========== */
        
        /* Общая ширина scrollbar - тонкий */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        /* Трек scrollbar */
        ::-webkit-scrollbar-track {
            background: transparent;
        }

        /* Ползунок scrollbar - светлая тема */
        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 3px;
            transition: background 0.2s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.7);
        }

        /* Ползунок scrollbar - тёмная тема */
        .dark ::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.5);
            border-radius: 3px;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.8);
        }

        /* Угол scrollbar */
        ::-webkit-scrollbar-corner {
            background: transparent;
        }
    </style>
    @if($u->is_admin)
        <script type="text/javascript">
            const admin = '{{ $u->is_admin }}';
            const moder = 'null';
        </script>
    @endif
    @if($u->is_moder)
        <script type="text/javascript">
            const moder = '{{ $u->is_moder }}';
            const admin = 'null';
        </script>
    @endif
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 font-inter">
    <div class="min-h-screen flex">
        <!-- Mobile Menu Overlay -->
        <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-80 bg-white dark:bg-gray-800 shadow-2xl transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out border-r border-gray-200 dark:border-gray-700">
            <div class="flex flex-col h-full">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-600 to-purple-600">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                </svg>
                            </div>
                            <div class="site-title">
                                <h1 class="text-xl font-bold text-white">{{$settings->sitename}}</h1>
                                <p class="text-blue-100 text-sm">Панель управления</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-4">
                        <img src="{{$u->avatar}}" alt="{{$u->username}}" class="w-12 h-12 rounded-full border-2 border-blue-200 dark:border-blue-800"/>
                        <div class="user-info">
                            <p class="font-semibold text-gray-900 dark:text-white">{{$u->username}}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($u->is_admin) Администратор
                                @elseif($u->is_moder) Модератор
                                @elseif($u->is_withdraw_moder) Модератор выводов
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

                    @if($u->is_withdraw_moder)
                    <!-- МОДЕРАЦИЯ ВЫВОДОВ -->
                    <div class="nav-item relative">
                        <a href="/{{$baseUrl}}/withdraw" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-400 transition-all duration-200 group">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="ml-3 font-medium nav-text">Выводы</span>
                            <div class="tooltip">Выводы</div>
                        </a>
                    </div>
                    @endif

                    @if($u->is_admin || $u->is_moder)
                    <!-- РАЗДЕЛ: АНАЛИТИКА И СТАТИСТИКА -->
                    <div class="mb-2">
                        <button class="accordion-header w-full px-3 py-2 flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-colors" data-section="analytics">
                            <span class="flex items-center nav-text">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                </svg>
                                Аналитика
                            </span>
                            <svg class="w-4 h-4 chevron nav-text" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div class="accordion-content space-y-1 mt-1">
                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/stats" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-700 dark:hover:text-blue-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Общая статистика</span>
                                    <div class="tooltip">Общая статистика</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/detailed-statistics" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-700 dark:hover:text-emerald-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">По дням</span>
                                    <div class="ml-auto w-2 h-2 bg-emerald-500 rounded-full animate-pulse nav-badge"></div>
                                    <div class="tooltip">Статистика по дням</div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($u->is_admin)
                    <!-- РАЗДЕЛ: ФИНАНСЫ -->
                    <div class="mb-2">
                        <button class="accordion-header w-full px-3 py-2 flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-colors" data-section="finance">
                            <span class="flex items-center nav-text">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                </svg>
                                Финансы
                            </span>
                            <svg class="w-4 h-4 chevron nav-text" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div class="accordion-content space-y-1 mt-1">
                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/withdraw" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Выводы</span>
                                    <div class="tooltip">Выводы</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/inserts" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-teal-50 dark:hover:bg-teal-900/20 hover:text-teal-700 dark:hover:text-teal-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center group-hover:bg-teal-200 dark:group-hover:bg-teal-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Автопополнения</span>
                                    <div class="tooltip">Автоматические пополнения</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <!-- <a href="/{{$baseUrl}}/manual-deposits" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-700 dark:hover:text-orange-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center group-hover:bg-orange-200 dark:group-hover:bg-orange-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Ручные пополнения</span>
                                    <div class="tooltip">Ручные пополнения</div>
                                </a> -->
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/expenses" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center group-hover:bg-red-200 dark:group-hover:bg-red-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Расходы</span>
                                    <div class="tooltip">Расходы</div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- РАЗДЕЛ: ПОЛЬЗОВАТЕЛИ -->
                    <div class="mb-2">
                        <button class="accordion-header w-full px-3 py-2 flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-colors" data-section="users">
                            <span class="flex items-center nav-text">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                Пользователи
                            </span>
                            <svg class="w-4 h-4 chevron nav-text" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div class="accordion-content space-y-1 mt-1">
                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/users" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 hover:text-cyan-700 dark:hover:text-cyan-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center group-hover:bg-cyan-200 dark:group-hover:bg-cyan-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Управление</span>
                                    <div class="tooltip">Управление пользователями</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/verifications" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center group-hover:bg-amber-200 dark:group-hover:bg-amber-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Верификация</span>
                                    <div class="tooltip">Верификация</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/banned-users" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center group-hover:bg-red-200 dark:group-hover:bg-red-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Баны в чате</span>
                                    <div class="tooltip">Баны в чате</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/ranks" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 hover:text-yellow-700 dark:hover:text-yellow-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 dark:group-hover:bg-yellow-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Ранки</span>
                                    <div class="tooltip">Ранки</div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- РАЗДЕЛ: ИГРЫ И КОНТЕНТ -->
                    <div class="mb-2">
                        <button class="accordion-header w-full px-3 py-2 flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-colors" data-section="games">
                            <span class="flex items-center nav-text">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H8a1 1 0 000 2h4a1 1 0 100-2H8.771z" clip-rule="evenodd"/>
                                </svg>
                                Игры и контент
                            </span>
                            <svg class="w-4 h-4 chevron nav-text" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div class="accordion-content space-y-1 mt-1">
                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/slots" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-violet-50 dark:hover:bg-violet-900/20 hover:text-violet-700 dark:hover:text-violet-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-violet-100 dark:bg-violet-900/30 rounded-lg flex items-center justify-center group-hover:bg-violet-200 dark:group-hover:bg-violet-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-violet-600 dark:text-violet-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Слоты</span>
                                    <div class="tooltip">Управление слотами</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/categories" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center group-hover:bg-amber-200 dark:group-hover:bg-amber-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Категории игр</span>
                                    <div class="tooltip">Конструктор категорий</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/providers" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Провайдеры игр</span>
                                    <div class="tooltip">Управление провайдерами игр</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/words" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:text-rose-700 dark:hover:text-rose-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900/30 rounded-lg flex items-center justify-center group-hover:bg-rose-200 dark:group-hover:bg-rose-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Запрещенные слова</span>
                                    <div class="tooltip">Модерация чата</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/banners" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 dark:hover:text-amber-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center group-hover:bg-amber-200 dark:group-hover:bg-amber-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Баннеры</span>
                                    <div class="tooltip">Управление баннерами</div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- РАЗДЕЛ: ПЛАТЕЖИ И СИСТЕМЫ -->
                    <div class="mb-2">
                        <button class="accordion-header w-full px-3 py-2 flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-colors" data-section="payments">
                            <span class="flex items-center nav-text">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                </svg>
                                Платежные системы
                            </span>
                            <svg class="w-4 h-4 chevron nav-text" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div class="accordion-content space-y-1 mt-1">
                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/payment_systems" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-700 dark:hover:text-blue-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Системы</span>
                                    <div class="tooltip">Платежные системы</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/payment_handlers" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-700 dark:hover:text-emerald-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Обработчики</span>
                                    <div class="tooltip">Обработчики платежей</div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- РАЗДЕЛ: МАРКЕТИНГ -->
                    <div class="mb-2">
                        <button class="accordion-header w-full px-3 py-2 flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-colors" data-section="marketing">
                            <span class="flex items-center nav-text">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                                Маркетинг
                            </span>
                            <svg class="w-4 h-4 chevron nav-text" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div class="accordion-content space-y-1 mt-1">
                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/promocodes" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-pink-900/20 hover:text-pink-700 dark:hover:text-pink-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center group-hover:bg-pink-200 dark:group-hover:bg-pink-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-pink-600 dark:text-pink-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Промокоды</span>
                                    <div class="tooltip">Промокоды</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/notify" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-700 dark:hover:text-indigo-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Уведомления</span>
                                    <div class="tooltip">Массовые уведомления</div>
                                </a>
                            </div>

                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/telegram-broadcast" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-700 dark:hover:text-blue-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Telegram Рассылка</span>
                                    <div class="tooltip">Telegram Рассылка</div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- РАЗДЕЛ: НАСТРОЙКИ -->
                    <div class="mb-2">
                        <button class="accordion-header w-full px-3 py-2 flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition-colors" data-section="settings">
                            <span class="flex items-center nav-text">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                </svg>
                                Настройки
                            </span>
                            <svg class="w-4 h-4 chevron nav-text" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <div class="accordion-content space-y-1 mt-1">
                            <div class="nav-item relative">
                                <a href="/{{$baseUrl}}/settings" class="flex items-center p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center group-hover:bg-gray-200 dark:group-hover:bg-gray-600 transition-colors">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="ml-3 font-medium nav-text">Основные</span>
                                    <div class="tooltip">Основные настройки</div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                </nav>

                <!-- Footer -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span class="nav-text">© 2025 {{$settings->domain}}</span>
                        <button id="themeToggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-4 h-4 sun-icon hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                            </svg>
                            <svg class="w-4 h-4 moon-icon block dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content flex-1 lg:ml-80">
            <!-- Top Navigation -->
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 lg:hidden">
                <div class="flex items-center justify-between p-4">
                    <button id="openSidebar" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div class="flex items-center space-x-3">
                        <img src="{{$u->avatar}}" alt="{{$u->username}}" class="w-8 h-8 rounded-full"/>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{$u->username}}</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="container mx-auto my-4 p-6 lg:p-8 animate-fade-in">
                @yield('content')
            </div>

        </main>
    </div>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Загружаем состояние сайдбара
        const sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (sidebarCollapsed && window.innerWidth >= 1024) {
            toggleSidebarState(true);
        }
    });
    </script>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('cp/js/jquery.dataTableOld.min.js') }}"></script>
    <script src="{{ asset('cp/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('cp/js/jquery.toast.min.js') }}"></script>
    <script src="{{ asset('cp/js/init.js') }}?v1.13"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>
    <script src="{{ asset('cp/js/adminPost.js') }}?v=1.7"></script>

    <script>
        // Accordion Menu with localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            const savedStates = JSON.parse(localStorage.getItem('accordion-states') || '{}');

            // Инициализация состояний из localStorage
            accordionHeaders.forEach(header => {
                const section = header.getAttribute('data-section');
                const content = header.nextElementSibling;
                
                // Если есть сохраненное состояние, применяем его
                if (savedStates[section]) {
                    header.classList.add('active');
                    content.classList.add('active');
                }

                // Обработчик клика
                header.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isActive = this.classList.contains('active');
                    
                    // Можно раскомментировать, если нужно закрывать остальные разделы при открытии нового
                    // accordionHeaders.forEach(h => {
                    //     if (h !== this) {
                    //         h.classList.remove('active');
                    //         h.nextElementSibling.classList.remove('active');
                    //     }
                    // });
                    
                    // Переключаем текущий
                    this.classList.toggle('active');
                    content.classList.toggle('active');
                    
                    // Сохраняем состояние
                    const currentStates = JSON.parse(localStorage.getItem('accordion-states') || '{}');
                    currentStates[section] = !isActive;
                    localStorage.setItem('accordion-states', JSON.stringify(currentStates));
                });
            });

            // Функция для сброса состояния аккордеонов (можно вызвать из консоли)
            window.resetAccordionStates = function() {
                localStorage.removeItem('accordion-states');
                location.reload();
            };
        });

        // Mobile Menu Toggle
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        const mainContent = document.querySelector('.main-content');

        function toggleMobileSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        if (openSidebar) openSidebar.addEventListener('click', toggleMobileSidebar);
        if (closeSidebar) closeSidebar.addEventListener('click', toggleMobileSidebar);
        if (overlay) overlay.addEventListener('click', toggleMobileSidebar);

        // Desktop Sidebar Toggle
        const toggleSidebar = document.getElementById('toggleSidebar');

        function toggleSidebarState(collapse = null) {
            const isCollapsed = collapse !== null ? collapse : sidebar.classList.contains('sidebar-collapsed');

            if (collapse === null) {
                sidebar.classList.toggle('sidebar-collapsed');
                mainContent.classList.toggle('expanded');
            } else if (collapse) {
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.add('expanded');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                mainContent.classList.remove('expanded');
            }

            // Меняем иконку
            const icon = toggleSidebar.querySelector('svg');
            if (sidebar.classList.contains('sidebar-collapsed')) {
                icon.innerHTML = '<path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>';
                localStorage.setItem('sidebar-collapsed', 'true');
            } else {
                icon.innerHTML = '<path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>';
                localStorage.setItem('sidebar-collapsed', 'false');
            }
        }

        if (toggleSidebar) {
            toggleSidebar.addEventListener('click', () => toggleSidebarState());
        }

        // Theme Toggle with localStorage
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;

        themeToggle?.addEventListener('click', () => {
            html.classList.toggle('dark');
            const isDark = html.classList.contains('dark');
            localStorage.setItem('admin-theme', isDark ? 'dark' : 'light');

            // Показываем уведомление о сохранении темы
            showNotification(isDark ? 'Темная тема включена' : 'Светлая тема включена');
        });

        // Сброс состояния сайдбара на мобильных устройствах
        window.addEventListener('resize', function() {
            if (window.innerWidth < 1024) {
                sidebar.classList.remove('sidebar-collapsed');
                mainContent.classList.remove('expanded');
            } else {
                // Восстанавливаем состояние на десктопе
                const sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                if (sidebarCollapsed) {
                    toggleSidebarState(true);
                }
            }
        });

        // Функция для показа уведомлений
        function showNotification(message) {
            // Создаем элемент уведомления
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
            notification.textContent = message;

            document.body.appendChild(notification);

            // Показываем уведомление
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Скрываем уведомление через 3 секунды
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Currency Tab functionality
        $(document).ready(function () {
            $('#currencyTab a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });

        // Toast notifications
        @if(session('error'))
            $.toast({
                position: 'top-right',
                text: "{{ session('error') }}",
                icon: 'error',
                bgColor: '#ef4444',
                textColor: 'white'
            });
        @elseif(session('success'))
            $.toast({
                position: 'top-right',
                text: "{{ session('success') }}",
                icon: 'success',
                bgColor: '#10b981',
                textColor: 'white'
            });
        @endif
    </script>
</body>
</html>
