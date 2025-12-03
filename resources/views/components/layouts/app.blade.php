<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Notification messages for JavaScript -->
    @if(session('success'))
        <meta name="success-message" content="{{ session('success') }}">
    @endif
    @if(session('error'))
        <meta name="error-message" content="{{ session('error') }}">
    @endif
    @if(isset($errors) && $errors->any())
        <meta name="errors" content="{{ json_encode($errors->all()) }}">
    @endif
    
    <title>{{ $settings->sitename ?? '404 - Not Found' }}</title>

    <!-- SEO -->
    <meta name="description" content="{{ __('FlashGame - популярное онлайн казино с широким выбором игр, привлекательными бонусами и быстрыми выплатами') }}">
    <meta name="keywords" content="{{ __('онлайн казино, игровые автоматы, слоты, рулетка, FlashGame, бонусы казино, быстрые выплаты') }}">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="{{ $settings->sitename ?? __('FlashGame - Онлайн Казино') }}">
    <meta property="og:description" content="{{ __('Откройте для себя мир онлайн-казино FlashGame с уникальными бонусами и быстрыми выплатами') }}">
    <meta property="og:image" content="{{ asset('/assets/images/og-image.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicons/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/favicons/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicons/favicon.ico') }}">

    <!-- Preconnect для внешних ресурсов -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://kit.fontawesome.com">
    <link rel="dns-prefetch" href="https://telegram.org">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Manrope:wght@300;400;500;600;700;800&family=REM:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://kit.fontawesome.com/5bc1c1da4e.js" crossorigin="anonymous" async></script>

    <!-- reCAPTCHA - Отложенная загрузка -->
    <script>
        // Загружаем reCAPTCHA только когда это необходимо
        window.loadRecaptcha = function() {
            if (!document.querySelector('script[src*="recaptcha"]')) {
                const script = document.createElement('script');
                script.src = 'https://www.google.com/recaptcha/api.js';
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            }
        };
        
        // Загружаем при взаимодействии пользователя
        ['mousedown', 'touchstart', 'keydown'].forEach(function(event) {
            document.addEventListener(event, window.loadRecaptcha, { once: true, passive: true });
        });
    </script>


    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Подключение стилей для Telegram WebApp --}}
    <script>
        // Функция проверки Telegram WebApp
        function checkAndLoadTelegramStyles() {
            // Используем функцию isTelegramWebApp если она уже определена
            const isTelegram = (typeof window.isTelegramWebApp === 'function') 
                ? window.isTelegramWebApp() 
                : false;
            
            if (isTelegram) {
                // Проверяем, не подключены ли уже стили
                if (!document.getElementById('telegram-webapp-styles')) {
                    const link = document.createElement('link');
                    link.id = 'telegram-webapp-styles';
                    link.rel = 'stylesheet';
                    link.href = '{{ asset('css/telegram-webapp.css') }}?v=1.0';
                    document.head.appendChild(link);
                    console.log('Telegram WebApp styles loaded');
                }
            }
        }
        
        // Загружаем немедленно без задержки для игровых страниц
        let isGamePage = window.location.pathname.includes('/slots/play/') || window.location.pathname.includes('/slots/fun/');
        
        if (isGamePage) {
            // На игровых страницах загружаем стили без задержки
            document.addEventListener('DOMContentLoaded', checkAndLoadTelegramStyles);
        } else {
            // На остальных страницах используем минимальную задержку
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(checkAndLoadTelegramStyles, 50);
            });
        }
        
        // И при каждой навигации Livewire
        document.addEventListener('livewire:navigated', function() {
            const isGamePage = window.location.pathname.includes('/slots/play/') || window.location.pathname.includes('/slots/fun/');
            
            if (isGamePage) {
                checkAndLoadTelegramStyles();
                
                // Применяем настройки WebApp без задержки
                if (typeof window.applyTelegramWebAppSettings === 'function') {
                    window.applyTelegramWebAppSettings();
                }
            } else {
                setTimeout(checkAndLoadTelegramStyles, 50);
                
                if (typeof window.applyTelegramWebAppSettings === 'function') {
                    setTimeout(() => {
                        window.applyTelegramWebAppSettings();
                    }, 150);
                }
            }
        });
    </script>

    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply bg-customDark text-white font-sans antialiased;
            }
        }

        @layer components {
            .btn-primary {
                @apply inline-flex items-center justify-center px-4 py-2 bg-primary hover:bg-primary-dark text-white font-medium rounded-lg transition-colors duration-200;
            }

            .btn-secondary {
                @apply inline-flex items-center justify-center px-4 py-2 bg-dark-700 hover:bg-dark-600 text-white font-medium rounded-lg transition-colors duration-200 border border-dark-600;
            }

            .balance-chip {
                @apply flex items-center gap-2 px-3 py-1.5 bg-dark-800 rounded-full border border-dark-700;
            }

            .sidebar-item {
                @apply flex items-center gap-3 px-3 py-2 rounded-lg text-white hover:bg-dark-800 hover:text-white transition-colors duration-200;
            }

            .sidebar-item.active {
                @apply text-white bg-[#0f1419];
            }

            .scrollbar-thin::-webkit-scrollbar {
                @apply w-1.5;
            }

            .scrollbar-track-dark::-webkit-scrollbar-track {
                @apply bg-[#0f1419];
            }

            .scrollbar-thin::-webkit-scrollbar-thumb:hover {
                @apply bg-[#ff6a20]/70 shadow-[0_0_5px_1px_rgba(255,106,32,0.3)];
            }

            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        }
        
    </style>
    <style>
        /* Отступ для Telegram Web App */
        /* body.telegram-webapp .main-content {
            margin-top: 100px;
        } */
    </style>
    
    {{-- Подключение Telegram WebView авторизации --}}
    @include('components.telegram-auth')
</head>
<body x-data="{ 
    sidebarOpen: false, 
    chatOpen: false,
    init() {
        if (window.innerWidth >= 768) {
            const savedChatState = localStorage.getItem('chatOpen');
            if (savedChatState === 'true') {
                this.chatOpen = true;
                document.body.classList.add('chat-open');
            }
        }
        
        // Синхронизируем изменения с localStorage и body классом
        this.$watch('chatOpen', (value) => {
            if (value) {
                document.body.classList.add('chat-open');
                if (window.innerWidth >= 768) {
                    localStorage.setItem('chatOpen', 'true');
                }
            } else {
                document.body.classList.remove('chat-open');
                if (window.innerWidth >= 768) {
                    localStorage.setItem('chatOpen', 'false');
                }
            }
        });

        // Блокируем/разблокируем скролл при открытии/закрытии мобильного меню
        this.$watch('sidebarOpen', (value) => {
            if (window.innerWidth < 1280) { // xl breakpoint
                if (value) {
                    // Блокируем скролл основной страницы
                    document.body.style.overflow = 'hidden';
                    document.documentElement.style.overflow = 'hidden';
                    document.body.classList.add('sidebar-scroll-locked');
                } else {
                    // Разблокируем скролл основной страницы
                    document.body.style.overflow = '';
                    document.documentElement.style.overflow = '';
                    document.body.classList.remove('sidebar-scroll-locked');
                }
            }
        });
    }
}" :class="{ 'chat-open': chatOpen, 'sidebar-open': sidebarOpen }" class="app-layout">
    

    <script>
        (function() {
            if (window.innerWidth >= 768) {
                const savedChatState = localStorage.getItem('chatOpen');
                if (savedChatState === 'true') {
                    document.body.classList.add('chat-open');
                }
            }
        })();
    </script>

    <div class="flex h-screen overflow-hidden">
      <div x-show="sidebarOpen"
           @click="sidebarOpen = false"
           x-transition:enter="transition-opacity ease-linear duration-300"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition-opacity ease-linear duration-300"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="fixed inset-0 bg-black/60 backdrop-blur-sm z-20 xl:hidden"
           style="display: none;">
      </div>
        <!-- Sidebar -->
        <div class="sidebar-wrapper fixed inset-y-0 left-0 z-30 w-64 pb-14 xl:pb-0 overflow-y-auto bg-[#0f1419] transition-all duration-300 xl:translate-x-0 xl:static xl:inset-0 sidebar-mobile-hidden"
             :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
             style="-webkit-overflow-scrolling: touch; overscroll-behavior: contain;"
             @touchmove.stop
             @wheel.stop>
            <x-layouts.partials.sidebar />
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden main-content relative">
            <x-layouts.partials.header />

            <main id="main-content-wrapper" class="container mx-auto flex-1 overflow-x-hidden overflow-y-auto bg-customDark px-1 transition-opacity duration-300" 
                  data-navigate-once>
                {{ $slot }}
            </main>
            @if(!in_array(Route::currentRouteName(), ['slots.play', 'slots.mobile']))
    <footer class="bg-[#0f1419] border-t border-gray-800 mt-auto">
        <div class="container mx-auto px-4 py-12">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-8">
                <!-- Logo Column -->
                <div class="lg:col-span-1">
                    <a href="/" class="inline-block mb-4">
                        <img src="/assets/images/logo.png" alt="Logo" class="h-10" loading="lazy">
                    </a>
                    <p class="text-gray-400 text-sm">
                        {{ __('Лучшее онлайн казино с быстрыми выплатами') }}
                    </p>
                </div>

                <!-- Account Column -->
                <div class="hidden lg:block">
                    <h3 class="text-white font-bold text-sm mb-4 uppercase tracking-wider">{{ __('Аккаунт') }}</h3>
                    <ul class="space-y-2">
                        @auth
                            <li><a href="javascript:void(0);" onclick="openRakebackModal()" class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Бонус') }}</a></li>
                            <li><a href="javascript:void(0);" onclick="openRankModal()" class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Уровень') }}</a></li>
                            <li><a href="javascript:void(0);" onclick="openPromoModal()" class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Промокод') }}</a></li>
                            <li><a href="{{ route('account.referrals') }}" wire:navigate class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Партнерам') }}</a></li>
                            <li><a href="javascript:void(0);" onclick="window.openModalWithMyInfo()" class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Статистика') }}</a></li>
                            <li><a href="{{ route('transaction') }}" wire:navigate class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Транзакции') }}</a></li>
                            <li><a href="{{ route('account') }}" wire:navigate class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Настройки') }}</a></li>
                        @else
                            <li><a href="#" onclick="openLoginModal()" class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Вход') }}</a></li>
                            <li><a href="#" onclick="openRegisterModal()" class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Регистрация') }}</a></li>
                        @endauth
                            <li><a href="{{ route('rules') }}" wire:navigate class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Правила') }}</a></li>
                    </ul>
                </div>

                <!-- Casino Column -->
                <div>
                    <h3 class="text-white font-bold text-sm mb-4 uppercase tracking-wider">{{ __('Казино') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('slots.lobby') }}" wire:navigate class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __('Все слоты') }}</a></li>
                        @foreach($sidebarCategories as $cat)
                        <li><a href="{{ route('slots.category', $cat->slug) }}" wire:navigate class="text-gray-400 hover:text-[#ffb300] transition text-sm">{{ __($cat->name) }}</a></li>
                        @endforeach
                    </ul>
                </div>


                <!-- Social & Language Column -->
                <div>
                    <h3 class="text-white font-bold text-sm mb-4 uppercase tracking-wider">{{ __('Связь') }}</h3>
                    <ul class="space-y-2 mb-6">
                        @if(isset($settings) && isset($settings->support_tg) && $settings->support_tg)
                        <li>
                            <a href="https://t.me/{{ $settings->support_tg }}" target="_blank" class="text-gray-400 hover:text-[#ffb300] transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248-1.64 8.173c-.125.55-.462.68-.937.424l-2.59-1.916-1.250 1.204c-.135.135-.25.25-.515.25-.265 0-.372-.118-.487-.487L9.374 12.5l-2.68-1c-.575-.176-.588-.575.125-.852l10.52-4.06c.485-.184.924.112.785.66z"></path>
                                </svg>
                                Telegram
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="#" target="_blank" class="text-gray-400 hover:text-[#ffb300] transition text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path>
                                </svg>
                                Instagram
                            </a>
                        </li>
                    </ul>

                    <!-- Language Switcher -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button" class="flex items-center gap-2 px-3 py-2 bg-gray-800 hover:bg-gray-700 rounded-lg transition text-sm text-gray-300">
                            <img src="/assets/images/lang/{{ Config::get('app.locale') }}.png" alt="{{ Config::get('app.locale') }}" class="w-5 h-5 rounded" loading="lazy">
                            <span class="uppercase">{{ Config::get('app.locale') }}</span>
                            <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-cloak
                             class="absolute bottom-full mb-2 left-0 w-full bg-gray-800 rounded-lg shadow-xl border border-gray-700 overflow-hidden">
                            <a href="/setlocale/ru" wire:navigate class="flex items-center gap-2 px-3 py-2 hover:bg-gray-700 transition text-sm text-gray-300">
                                <img src="/assets/images/lang/ru.png" alt="RU" class="w-5 h-5 rounded" loading="lazy">
                                <span>RU</span>
                            </a>
                            <a href="/setlocale/en" wire:navigate class="flex items-center gap-2 px-3 py-2 hover:bg-gray-700 transition text-sm text-gray-300">
                                <img src="/assets/images/lang/en.png" alt="EN" class="w-5 h-5 rounded" loading="lazy">
                                <span>EN</span>
                            </a>
                            <a href="/setlocale/tr" wire:navigate class="flex items-center gap-2 px-3 py-2 hover:bg-gray-700 transition text-sm text-gray-300">
                                <img src="/assets/images/lang/tr.png" alt="TR" class="w-5 h-5 rounded" loading="lazy">
                                <span>TR</span>
                            </a>
                            <a href="/setlocale/az" wire:navigate class="flex items-center gap-2 px-3 py-2 hover:bg-gray-700 transition text-sm text-gray-300">
                                <img src="/assets/images/lang/az.png" alt="AZ" class="w-5 h-5 rounded" loading="lazy">
                                <span>AZ</span>
                            </a>
                            <a href="/setlocale/kz" wire:navigate class="flex items-center gap-2 px-3 py-2 hover:bg-gray-700 transition text-sm text-gray-300">
                                <img src="/assets/images/lang/kz.png" alt="KZ" class="w-5 h-5 rounded" loading="lazy">
                                <span>KZ</span>
                            </a>
                            <a href="/setlocale/uz" wire:navigate class="flex items-center gap-2 px-3 py-2 hover:bg-gray-700 transition text-sm text-gray-300">
                                <img src="/assets/images/lang/uz.png" alt="UZ" class="w-5 h-5 rounded" loading="lazy">
                                <span>UZ</span>
                            </a>                        
                        </div>
                    </div>
                                    <!-- License -->
                <div class="mt-4">
                    <a class="inline-block opacity-80 hover:opacity-100 transition">
                        <img src="/assets/images/curacao.png" alt="Curacao License" class="h-16" loading="lazy">
                    </a>
                </div>

                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-800 pt-8">
                <!-- Payment Methods -->
                <div class="flex flex-wrap items-center justify-center gap-4 mb-6 opacity-80">
                    <img src="/assets/images/payments/visa.webp" alt="Visa" class="h-6 hover:grayscale-1 transition" loading="lazy">
                    <img src="/assets/images/payments/mastercard.png" alt="Mastercard" class="h-6 hover:grayscale-1 transition" loading="lazy">
                    <img src="/assets/images/payments/btc.png" alt="Bitcoin" class="h-6 hover:grayscale-1 transition" loading="lazy">
                    <img src="/assets/images/payments/usdt.png" alt="USDT" class="h-6 hover:grayscale-1 transition" loading="lazy">
                </div>

                <!-- Warning -->
                <p class="text-gray-500 text-xs text-center mb-4 max-w-4xl mx-auto">
                    {{ __('18+ Азартные игры могут вызывать зависимость. Играйте ответственно.') }}
                </p>

                <!-- Copyright -->
                <p class="text-gray-600 text-xs text-center">
                    © {{ date('Y') }} {{ $settings->sitename ?? 'Flash' }}. {{ __('Все права защищены.') }}
                </p>
            </div>
        </div>
    </footer>
@endif

        </div>
    </div>

    <div id="chat-persist-container" data-navigate-once>
        <x-real-time.chat />
    </div>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 z-40 hidden bg-black/75 backdrop-blur-sm"></div>

    <!-- Modals -->
    @auth
        <x-modals.u-info/>
        <x-modals.cash/>
        <x-modals.rank/>
        <x-modals.promocode/>
        <x-modals.withdrawal-success/>
        <x-modals.chat-rules/>
    @endauth
    <x-modals.rakeback/>
    <x-modals.register/>
    <x-modals.login/>
    <x-modals.forgot-password/>
    <x-modals.telegram-auth/>
    <x-modals.crypto-guide/>
    <x-modals.currency-select/>

    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 419) {
                    preventDefault();
                    window.location.reload();
                }
            })
        })
    });

    window.addEventListener('unhandledrejection', function(event) {
        if (event.reason && event.reason.isFromCancelledTransition) {
            event.preventDefault();
        }
    });
    </script>

    @livewireScripts

    <script>
        document.addEventListener('livewire:navigating', () => {
            if (window.chatSystem && window.chatSystem.ws) {
                window.chatSystem.preserveConnection = true;
            }
        });

        document.addEventListener('livewire:navigated', () => {
            if (window.chatSystem && window.chatSystem.preserveConnection) {
                window.chatSystem.preserveConnection = false;
            }
        });
    </script>

    @guest
        <script src="//ulogin.ru/js/ulogin.js"></script>
    @endguest

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" data-navigate-once async></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js" data-navigate-once async></script>




    <script data-navigate-once>
        if (typeof window.chatEmojis === 'undefined') {
            window.chatEmojis = {!! $chat_emoj !!};
            window.isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            @auth
            window.currentUserUsername = "{{ Auth::user()->username }}";
            window.currentUserId = {{ Auth::user()->id }};
            window.currentUserCurrency = "{{ $u->currency->symbol }}";
            window.isModerator = {{ (Auth::user()->is_moder || Auth::user()->is_admin || Auth::user()->is_chat_moder) ? 'true' : 'false' }};
            @endauth
        }
    </script>

    <script data-navigate-once>
        // Modal Manager
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.modalManager === 'undefined') {
                window.modalManager = {
                modals: {},
                init() {
                    document.querySelectorAll('[data-modal]').forEach(modal => {
                        const modalId = modal.getAttribute('data-modal');
                        this.modals[modalId] = false;
                    });
                }
            };

            window.openModal = function(modalId) {
                const modal = document.getElementById(modalId);
                const overlay = document.getElementById('overlay');
                if (modal && overlay) {
                    modal.classList.remove('hidden');
                    overlay.classList.remove('hidden');
                    window.modalManager.modals[modalId] = true;
                }
            };

            window.closeModal = function(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                    window.modalManager.modals[modalId] = false;

                    const hasOpenModal = Object.values(window.modalManager.modals).some(isOpen => isOpen);
                    if (!hasOpenModal) {
                        document.getElementById('overlay').classList.add('hidden');
                    }
                }
            };

            window.modalManager.init();

            // Close modal on overlay click
            document.getElementById('overlay').addEventListener('click', () => {
                Object.keys(window.modalManager.modals).forEach(modalId => {
                    window.closeModal(modalId);
                });
            });

            // Проверка авторизации и открытие модалки регистрации
            window.requireAuth = function(callback, event) {
                const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
                
                if (!isAuthenticated) {
                    if (event) event.preventDefault();
                    window.dispatchEvent(new CustomEvent('open-register-modal'));
                    return false;
                }
                
                if (callback && typeof callback === 'function') {
                    return callback();
                }
                return true;
            };
            } 
        });


        if (typeof window.toggleBalanceCurrencyDropdown === 'undefined') {
            window.toggleBalanceCurrencyDropdown = function() {
                const dropdown = document.getElementById('balance-currency-dropdown');
                const arrow = document.getElementById('balance-currency-arrow');

                dropdown?.classList.toggle('hidden');
                arrow?.classList.toggle('rotate-180');
            };
        }

        if (typeof window.changeCurrency === 'undefined') {
            window.changeCurrency = function(currency) {
                document.getElementById('balance-currency-dropdown')?.classList.add('hidden');
                document.getElementById('balance-currency-arrow')?.classList.remove('rotate-180');
                Livewire.dispatch('balance-change-currency', { currency });
            };
        }



        // Notifications Toggle
        window.notificationsState = false;

        function toggleNotifications(event) {
            event?.stopPropagation();
            const dropdown = document.getElementById('notifications-dropdown');

            if (dropdown) {
                dropdown.classList.toggle('hidden');
                window.notificationsState = !dropdown.classList.contains('hidden');
            }
        }

        // Dropdown Manager
        window.dropdownStates = {};

        window.toggleDropdown = function(dropdownId, event) {
            event?.stopPropagation();

            const dropdown = document.getElementById(dropdownId);
            if (!dropdown) return;

            const isOpen = !dropdown.classList.contains('hidden');

            // Close all other dropdowns
            if (!isOpen) {
                Object.keys(window.dropdownStates).forEach(id => {
                    if (id !== dropdownId && window.dropdownStates[id]) {
                        const other = document.getElementById(id);
                        other?.classList.add('hidden');
                        window.dropdownStates[id] = false;
                    }
                });
            }

            dropdown.classList.toggle('hidden');
            window.dropdownStates[dropdownId] = !isOpen;

            const arrow = document.getElementById(dropdownId + '-arrow');
            arrow?.classList.toggle('rotate-180');

            return !isOpen;
        };

        // Close dropdowns on outside click
        document.addEventListener('click', function(event) {
            // Close currency dropdown
            const currencyDropdown = document.getElementById('balance-currency-dropdown');
            const currencyButton = document.getElementById('balance-currency-button');
            if (currencyDropdown && currencyButton &&
                !currencyDropdown.contains(event.target) &&
                !currencyButton.contains(event.target)) {
                currencyDropdown.classList.add('hidden');
                document.getElementById('balance-currency-arrow')?.classList.remove('rotate-180');
            }

            // Close notifications
            if (window.notificationsState) {
                const notifDropdown = document.getElementById('notifications-dropdown');
                const notifButton = document.getElementById('notifications-button');
                if (notifDropdown && notifButton &&
                    !notifDropdown.contains(event.target) &&
                    !notifButton.contains(event.target)) {
                    notifDropdown.classList.add('hidden');
                    window.notificationsState = false;
                }
            }

            // Close other dropdowns
            Object.keys(window.dropdownStates).forEach(dropdownId => {
                if (window.dropdownStates[dropdownId]) {
                    const dropdown = document.getElementById(dropdownId);
                    const button = document.getElementById(dropdownId.replace('-dropdown', '-button'));

                    if (dropdown && button &&
                        !dropdown.contains(event.target) &&
                        !button.contains(event.target)) {
                        dropdown.classList.add('hidden');
                        window.dropdownStates[dropdownId] = false;
                        document.getElementById(dropdownId + '-arrow')?.classList.remove('rotate-180');
                    }
                }
            });
        });

        // Chat Functions
        window.openChat = function() {
            document.body.classList.add('chat-open');
            if (window.innerWidth >= 768) {
                localStorage.setItem('chatOpen', 'true');
            }
        };

        window.closeChat = function() {
            document.body.classList.remove('chat-open');
            if (window.innerWidth >= 768) {
                localStorage.setItem('chatOpen', 'false');
            }
        };

        window.toggleChat = function() {
            document.body.classList.contains('chat-open') ? window.closeChat() : window.openChat();
        };

        // Sidebar Controller
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebarController', () => ({
                init() {
                    this.sidebarOpen = window.innerWidth >= 1024;

                    window.addEventListener('resize', () => {
                        this.sidebarOpen = window.innerWidth >= 1024;
                    });
                }
            }));
        });

        // Функция для показа всех уведомлений
        function showAllNotifications() {
            // Закрываем мобильное меню профиля
            Alpine.store('profileMenuOpen', false);
            
            // Находим и кликаем на кнопку уведомлений в десктопной версии
            const desktopNotificationsBtn = document.querySelector('.notifications-toggle-button');
            if (desktopNotificationsBtn) {
                desktopNotificationsBtn.click();
            }
        }

        // Livewire Events
        document.addEventListener('livewire:init', function() {
            Livewire.on('notificationUpdated', () => {
                Livewire.dispatch('refresh-notifications');
            });

            Livewire.on('allNotificationsRead', () => {
                Livewire.dispatch('refresh-notifications');
            });
        });

        // Telegram Auth Component
        function telegramAuth(type) {
            return {
                loading: false,
                token: '',
                deepLink: '',
                checkInterval: null,
                
                init() {
                    // Генерируем ссылку сразу при инициализации
                    this.generateDeepLink();
                },
                
                async generateDeepLink() {
                    try {
                        const response = await fetch('{{ route("telegram.generate-token") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ type: type })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.token = data.token;
                            this.deepLink = data.deep_link;
                        }
                    } catch (error) {
                        console.error('Error generating token:', error);
                    }
                },
                
                async handleTelegramAuth(event) {
                    // Если ссылка ещё не готова, предотвращаем переход
                    if (!this.deepLink || this.deepLink === '#') {
                        event.preventDefault();
                        return;
                    }
                    
                    if (this.loading) {
                        event.preventDefault();
                        return;
                    }
                    
                    this.loading = true;
                    
                    // Начинаем проверять статус
                    this.startChecking();
                },
                
                startChecking() {
                    this.checkInterval = setInterval(async () => {
                        await this.checkStatus();
                    }, 2000);
                    
                    // Автоматически останавливаем проверку через 10 минут
                    setTimeout(() => {
                        this.stopChecking();
                    }, 600000);
                },
                
                async checkStatus() {
                    try {
                        const response = await fetch('{{ route("telegram.check-status") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ token: this.token })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success && data.status === 'completed') {
                            this.stopChecking();
                            
                            if (data.action === 'login' && data.redirect) {
                                // Прямой вход
                                window.location.href = data.redirect;
                            } else if (data.data && data.data.action === 'register') {
                                // Регистрация - открываем выбор валюты
                                window.dispatchEvent(new CustomEvent('open-currency-select', {
                                    detail: {
                                        authType: 'telegram-code',
                                        authData: data.data
                                    }
                                }));
                            }
                        }
                    } catch (error) {
                        console.error('Error checking status:', error);
                    }
                },
                
                stopChecking() {
                    if (this.checkInterval) {
                        clearInterval(this.checkInterval);
                        this.checkInterval = null;
                    }
                    this.loading = false;
                }
            }
        }
    </script>
 
</body>
</html>
