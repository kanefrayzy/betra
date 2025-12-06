<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="turbo-cache-control" content="no-preview">
    
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

    <!-- Preconnect  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://telegram.org">

    <!-- Шрифты  -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" 
          rel="stylesheet" 
          media="print" 
          onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    </noscript>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
</head>
<body x-data="{ 
    sidebarOpen: false,
    sidebarCollapsed: (window.innerWidth >= 1280 && localStorage.getItem('sidebarCollapsed') === 'true'),
    chatOpen: (window.innerWidth >= 768 && localStorage.getItem('chatOpen') === 'true'),
    init() {
        // Синхронизируем начальное состояние с body классами
        if (this.chatOpen) {
            document.body.classList.add('chat-open');
        }
        if (this.sidebarCollapsed) {
            const mainContent = document.querySelector('.main-content');
            const sidebarWrapper = document.querySelector('.sidebar-wrapper');
            if (mainContent) mainContent.classList.add('sidebar-collapsed');
            if (sidebarWrapper) sidebarWrapper.classList.add('collapsed');
        }
        
        // Синхронизируем изменения чата с localStorage и body классом
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

        // Синхронизируем состояние sidebar
        this.$watch('sidebarCollapsed', (value) => {
            if (window.innerWidth >= 1280) {
                localStorage.setItem('sidebarCollapsed', value.toString());
                const mainContent = document.querySelector('.main-content');
                const sidebarWrapper = document.querySelector('.sidebar-wrapper');
                
                if (mainContent) {
                    if (value) {
                        mainContent.classList.add('sidebar-collapsed');
                    } else {
                        mainContent.classList.remove('sidebar-collapsed');
                    }
                }
                
                if (sidebarWrapper) {
                    if (value) {
                        sidebarWrapper.classList.add('collapsed');
                    } else {
                        sidebarWrapper.classList.remove('collapsed');
                    }
                }
            }
        });

        // Блокируем/разблокируем скролл при открытии/закрытии мобильного меню
        this.$watch('sidebarOpen', (value) => {
            if (window.innerWidth < 1280) {
                if (value) {
                    document.body.style.overflow = 'hidden';
                    document.documentElement.style.overflow = 'hidden';
                    document.body.classList.add('sidebar-scroll-locked');
                } else {
                    document.body.style.overflow = '';
                    document.documentElement.style.overflow = '';
                    document.body.classList.remove('sidebar-scroll-locked');
                }
            }
        });

        // Сбрасываем collapsed при переходе на мобильные размеры
        window.addEventListener('resize', () => {
            if (window.innerWidth < 1280) {
                this.sidebarCollapsed = false;
                document.body.classList.remove('sidebar-collapsed-initial');
                const mainContent = document.querySelector('.main-content');
                const sidebarWrapper = document.querySelector('.sidebar-wrapper');
                if (mainContent) {
                    mainContent.classList.remove('sidebar-collapsed');
                }
                if (sidebarWrapper) {
                    sidebarWrapper.classList.remove('collapsed');
                }
            }
        });
    },
    toggleSidebar() {
        this.sidebarCollapsed = !this.sidebarCollapsed;
    }
}" :class="{ 'chat-open': chatOpen, 'sidebar-open': sidebarOpen }" 
   class="app-layout"
   x-init="$nextTick(() => { 
       document.querySelector('.sidebar-wrapper')?.classList.add('alpine-initialized'); 
       document.querySelector('.main-content')?.classList.add('alpine-initialized'); 
   })">
    

    <script>
        // Синхронная инициализация состояния чата ДО Alpine и рендеринга
        (function() {
            const isTablet = window.innerWidth >= 768;
            
            // Применяем состояние чата
            if (isTablet && localStorage.getItem('chatOpen') === 'true') {
                document.body.classList.add('chat-open');
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
        <div class="sidebar-wrapper pb-14 xl:pb-0 overflow-y-auto bg-[#0f1419] sidebar-mobile-hidden custom-scrollbar"
             data-turbo-permanent
             id="sidebar-permanent"
             :class="{ 'translate-x-0': sidebarOpen }"
             style="-webkit-overflow-scrolling: touch; overscroll-behavior: contain;"
             @touchmove.stop
             @wheel.stop>
            <script>
                (function() {
                    var wrapper = document.currentScript.parentElement;
                    // Применяем класс collapsed МГНОВЕННО
                    if (window.innerWidth >= 1280 && localStorage.getItem('sidebarCollapsed') === 'true') {
                        wrapper.classList.add('collapsed');
                    }
                })();
            </script>
            <x-layouts.partials.sidebar />
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden main-content relative" id="main-content-permanent">
            <script>
                (function() {
                    var mainContent = document.currentScript.parentElement;
                    // Применяем класс sidebar-collapsed МГНОВЕННО
                    if (window.innerWidth >= 1280 && localStorage.getItem('sidebarCollapsed') === 'true') {
                        mainContent.classList.add('sidebar-collapsed');
                    }
                })();
            </script>
            <x-layouts.partials.header />

            <main id="main-content-wrapper" class="container mx-auto flex-1 overflow-x-hidden overflow-y-auto bg-[#0f212e] px-1 transition-opacity duration-300">
                {{ $slot }}
            </main>
            @if(!in_array(Route::currentRouteName(), ['slots.play', 'slots.mobile']))
            <footer class="bg-[#0f212e] border-t border-[#1a2c38] mt-auto">
                <div class="container mx-auto px-4 py-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-8">
                        <div class="lg:col-span-1">
                            <a href="/" class="inline-block mb-4">
                                <img src="/assets/images/logo.png" alt="Logo" class="h-10">
                            </a>
                            <p class="text-gray-400 text-sm">
                                {{ __('Лучшее онлайн казино с быстрыми выплатами') }}
                            </p>
                        </div>

                        <div class="hidden lg:block">
                            <h3 class="text-white font-bold text-sm mb-4 uppercase tracking-wider">{{ __('Аккаунт') }}</h3>
                            <ul class="space-y-2">
                                @auth
                                    <li><a href="javascript:void(0);" onclick="openRakebackModal()" class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Бонус') }}</a></li>
                                    <li><a href="javascript:void(0);" onclick="openRankModal()" class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Уровень') }}</a></li>
                                    <li><a href="javascript:void(0);" onclick="openPromoModal()" class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Промокод') }}</a></li>
                                    <li><a href="{{ route('account.referrals') }}"  class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Партнерам') }}</a></li>
                                    <li><a href="javascript:void(0);" onclick="window.openModalWithMyInfo()" class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Статистика') }}</a></li>
                                    <li><a href="{{ route('transaction') }}"  class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Транзакции') }}</a></li>
                                    <li><a href="{{ route('account') }}"  class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Настройки') }}</a></li>
                                @else
                                    <li><a href="#" onclick="openLoginModal()" class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Вход') }}</a></li>
                                    <li><a href="#" onclick="openRegisterModal()" class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Регистрация') }}</a></li>
                                @endauth
                                    <li><a href="{{ route('rules') }}"  class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Правила') }}</a></li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-white font-bold text-sm mb-4 uppercase tracking-wider">{{ __('Казино') }}</h3>
                            <ul class="space-y-2">
                                <li><a href="{{ route('slots.lobby') }}"  class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __('Все слоты') }}</a></li>
                                @foreach($sidebarCategories as $cat)
                                <li><a href="{{ route('slots.category', $cat->slug) }}"  class="text-gray-400 hover:text-[#3b82f6] transition text-sm">{{ __($cat->name) }}</a></li>
                                @endforeach
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-white font-bold text-sm mb-4 uppercase tracking-wider">{{ __('Связь') }}</h3>
                            <ul class="space-y-2 mb-6">
                                @if(isset($settings) && isset($settings->support_tg) && $settings->support_tg)
                                <li>
                                    <a href="https://t.me/{{ $settings->support_tg }}" target="_blank" class="text-gray-400 hover:text-[#3b82f6] transition text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248-1.64 8.173c-.125.55-.462.68-.937.424l-2.59-1.916-1.250 1.204c-.135.135-.25.25-.515.25-.265 0-.372-.118-.487-.487L9.374 12.5l-2.68-1c-.575-.176-.588-.575.125-.852l10.52-4.06c.485-.184.924.112.785.66z"/>
                                        </svg>
                                        Telegram
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a href="#" target="_blank" class="text-gray-400 hover:text-[#3b82f6] transition text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                        </svg>
                                        Instagram
                                    </a>
                                </li>
                            </ul>

                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button" class="flex items-center gap-2 px-3 py-2 bg-[#1a2c38] hover:bg-[#2d3748] border border-[#2d3748] rounded-lg transition text-sm text-gray-300">
                                    <img src="/assets/images/lang/{{ Config::get('app.locale') }}.png" alt="{{ Config::get('app.locale') }}" class="w-5 h-5 rounded">
                                    <span class="uppercase">{{ Config::get('app.locale') }}</span>
                                    <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="open"
                                    @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    x-cloak
                                    class="absolute bottom-full mb-2 left-0 w-full bg-[#1a2c38] rounded-lg shadow-xl border border-[#2d3748] overflow-hidden">
                                    <a href="/setlocale/ru"  class="flex items-center gap-2 px-3 py-2 hover:bg-[#2d3748] transition text-sm text-gray-300">
                                        <img src="/assets/images/lang/ru.png" alt="RU" class="w-5 h-5 rounded">
                                        <span>RU</span>
                                    </a>
                                    <a href="/setlocale/en"  class="flex items-center gap-2 px-3 py-2 hover:bg-[#2d3748] transition text-sm text-gray-300">
                                        <img src="/assets/images/lang/en.png" alt="EN" class="w-5 h-5 rounded">
                                        <span>EN</span>
                                    </a>
                                    <a href="/setlocale/tr"  class="flex items-center gap-2 px-3 py-2 hover:bg-[#2d3748] transition text-sm text-gray-300">
                                        <img src="/assets/images/lang/tr.png" alt="TR" class="w-5 h-5 rounded">
                                        <span>TR</span>
                                    </a>
                                    <a href="/setlocale/az"  class="flex items-center gap-2 px-3 py-2 hover:bg-[#2d3748] transition text-sm text-gray-300">
                                        <img src="/assets/images/lang/az.png" alt="AZ" class="w-5 h-5 rounded">
                                        <span>AZ</span>
                                    </a>
                                    <a href="/setlocale/kz"  class="flex items-center gap-2 px-3 py-2 hover:bg-[#2d3748] transition text-sm text-gray-300">
                                        <img src="/assets/images/lang/kz.png" alt="KZ" class="w-5 h-5 rounded">
                                        <span>KZ</span>
                                    </a>
                                    <a href="/setlocale/uz"  class="flex items-center gap-2 px-3 py-2 hover:bg-[#2d3748] transition text-sm text-gray-300">
                                        <img src="/assets/images/lang/uz.png" alt="UZ" class="w-5 h-5 rounded">
                                        <span>UZ</span>
                                    </a>                        
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-[#1a2c38] pt-8">
                        <p class="text-gray-500 text-xs text-center mb-4 max-w-4xl mx-auto">
                            {{ __('18+ Азартные игры могут вызывать зависимость. Играйте ответственно.') }}
                        </p>

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
    <div id="overlay" class="fixed inset-0 z-40 hidden bg-black/75 backdrop-blur-sm" data-turbo-permanent></div>

    <!-- Modals -->
    <div id="modals-container" data-turbo-permanent>
        @auth   
        <x-modals.cash/>
        <x-modals.rank/>
        <x-modals.promocode/>       
        <x-modals.rakeback/>
        @endauth
        <x-modals.chat-rules/>
        <x-modals.u-info/>
        @guest
        <x-modals.register/>
        <x-modals.login/>
        <x-modals.forgot-password/>
        <x-modals.telegram-auth/>
        <x-modals.currency-select/>
        @endguest
    </div>

    <!-- PHP Config Injection for JavaScript -->
    <script>
        window.appConfig = {
            routes: {
                telegramGenerate: '{{ route("telegram.generate-token") }}',
                telegramCheck: '{{ route("telegram.check-status") }}'
            },
            @auth
            user: {
                id: {{ auth()->id() }},
                username: "{{ auth()->user()->username }}",
                currency: "{{ $u->currency->symbol ?? '' }}",
                isModerator: {{ (auth()->user()->is_moder || auth()->user()->is_admin || auth()->user()->is_chat_moder) ? 'true' : 'false' }}
            },
            @else
            user: null,
            @endauth
            chatEmojis: {!! $chat_emoj !!},
            i18n: {
                telegramError: '{{ __("Ошибка при генерации ссылки Telegram") }}'
            }
        };
    </script>

    @livewireScripts

    @guest
        <script src="//ulogin.ru/js/ulogin.js" defer></script>
    @endguest
 
</body>
</html>
