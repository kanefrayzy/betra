<header class="bg-customHeader sticky top-0 z-20 relative shadow-[0px_3px_7px_0px_rgba(0,0,0,0.25)]">

    <div class="container mx-auto flex h-16 items-center justify-between px-4">
        <div class="flex items-center">
            <div class="hidden lg:flex">
                <a href="/"  class="mr-8">
                    <img src="/assets/images/logo.png?v1" alt="Flash" class="h-10">
                </a>
            </div>
            <div class="lg:hidden">
                <a href="/" >
                    <img src="/assets/images/logo-mobile.png?v1" alt="Flash" class="h-8">
                </a>
            </div>
        </div>
        @auth
            <!-- Баланс на мобильных -->
            <div class="md:hidden">
                <livewire:balance wire:init />
            </div>
      
            <div class="hidden md:block">
                <livewire:balance wire:init />
            </div>
        @endauth

        <!-- Правая часть -->
        <div class="flex items-center space-x-3">
            @auth
                <div class="hidden md:block">
                    <livewire:notifications wire:init/>
                </div>


        <div class="relative hidden md:block" x-data="{ isOpen: false }">
            <button @click.stop.prevent="isOpen = !isOpen"
                    type="button"
                    class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-[#1a2c38] transition-all duration-200 group">
                <div class="relative">
                    <img src="{{ auth()->user()->avatar ?? '/assets/images/avatar-placeholder.png' }}"
                        alt="{{ auth()->user()->username }}"
                        class="h-9 w-9 rounded-full object-cover border-2 border-gray-700 group-hover:border-[#4bd52d] transition-colors">
                    @if($u->rank && $u->rank->picture)
                        <img src="{{ asset('storage/' . $u->rank->picture) }}"
                            alt="Rank"
                            class="absolute -bottom-1 -right-1 w-5 h-5 object-cover">
                    @endif
                </div>

                <span class="ml-1 font-medium text-white hidden lg:block group-hover:text-[#4bd52d] transition-colors">
                    {{ Illuminate\Support\Str::limit($u->username, 12) }}
                </span>

                <svg :class="{'rotate-180': isOpen}"
                    class="h-4 w-4 text-gray-400 transition-transform duration-200 group-hover:text-[#4bd52d]"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="isOpen"
                @click.away="isOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                style="display: none;"
                class="absolute right-0 mt-3 w-full bg-white rounded-xl shadow-2xl border border-gray-200 overflow-visible z-50 profile-dropdown-arrow">

                <!-- Menu Items -->
                <div class="py-2">
                    <a href="javascript:void(0);" 
                    onclick="openCashModal()"
                    @click="isOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 6h-4c0 .71-.16 1.39-.43 2H20c.55 0 1 .45 1 1s-.45 1-1 1H4c-.55 0-1-.45-1-1s.45-1 1-1h3.43C7.16 7.39 7 6.71 7 6H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2m-2 11c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2"></path>
                            <path d="M9.38 9h5.24C15.46 8.27 16 7.2 16 6c0-2.21-1.79-4-4-4S8 3.79 8 6c0 1.2.54 2.27 1.38 3"></path>
                        </svg>
                        <span class="text-sm font-bold">{{ __('Кошелек') }}</span>
                    </a>

                    <a href="{{ route('slots.favorites') }}"
                    
                    @click="isOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span class="text-sm font-bold">{{ __('Хранилище') }}</span>
                    </a>

                    <a href="javascript:void(0);" 
                    onclick="openRankModal()"
                    @click="isOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        <span class="text-sm font-bold">{{ __('ВИП') }}</span>
                    </a>

                    <a href="{{ route('account.referrals') }}"
                    
                    @click="isOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-sm font-bold">{{ __('Партнерам') }}</span>
                    </a>

                    <a href="javascript:void(0);" 
                    onclick="window.openModalWithMyInfo()"
                    @click="isOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-sm font-bold">{{ __('Статистика') }}</span>
                    </a>

                    <a href="{{ route('transaction') }}"
                    
                    @click="isOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <span class="text-sm font-bold">{{ __('Транзакции') }}</span>
                    </a>

                    <a href="{{ route('slots.history') }}"
                    
                    @click="isOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span class="text-sm font-bold">{{ __('Мои ставки') }}</span>
                    </a>

                    <a href="{{ route('account') }}"
                    
                    @click="isOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm font-bold">{{ __('Настройки') }}</span>
                    </a>
                </div>

                    <a href="{{ route('auth.logout') }}" 
                    data-turbo="false"
                    onclick="sessionStorage.setItem('clearSWCache', 'true')"
                    @click="isOpen = false"
                    class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="text-sm">{{ __('Выход') }}</span>
                    </a>
            </div>
        </div>

            @else
                <!-- Кнопки входа/регистрации - только на десктопе -->
                <div class="hidden md:flex items-center gap-3">
                    <!-- Кнопка Вход -->
                    <button onclick="openLoginModal()"
                            class="group relative px-5 py-2.5 bg-white/[0.03] hover:bg-white/[0.08] border border-white/10 hover:border-[#4bd52d]/50 rounded-xl text-white font-medium transition-all duration-300 overflow-hidden">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            {{ __('Вход') }}
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                    </button>

                    <!-- Кнопка Регистрация -->
                    <button onclick="openRegisterModal();"
                            class="group relative px-5 py-2.5 bg-[#4bd52d] rounded-xl text-black font-semibold transition-all duration-300 hover:scale-105 active:scale-95 overflow-hidden">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            {{ __('Регистрация') }}
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                    </button>
                </div>
            @endauth

            <!-- Мобильные кнопки входа/регистрации - только для неавторизованных -->
            @guest
                <div class="md:hidden flex items-center gap-2">
                    <!-- Кнопка Вход -->
                    <button onclick="openLoginModal()"
                            class="group relative px-3 py-2 bg-white/[0.03] hover:bg-white/[0.08] border border-white/10 hover:border-[#4bd52d]/50 rounded-lg text-white text-sm font-medium transition-all duration-300 overflow-hidden">
                        <span class="relative z-10 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            {{ __('Вход') }}
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                    </button>

                    <!-- Кнопка Регистрация -->
                    <button onclick="openRegisterModal();"
                            class="group relative px-3 py-2 bg-[#4bd52d] rounded-lg text-black text-sm font-semibold transition-all duration-300 hover:scale-105 active:scale-95 overflow-hidden">
                        <span class="relative z-10 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            {{ __('Регистрация') }}
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                    </button>
                </div>
            @endguest

            <!-- Кнопка чата -->
            <button @click="$store.ui.toggleChat()"
                    class="relative group chat-toggle-button">
                <div class="relative p-2 rounded-full hover:bg-gray-800 transition-all duration-200">
                    <svg class="h-6 w-6 transition-colors duration-200"
                         :class="$store.ui.chatOpen ? 'text-[##44ce26]' : 'text-gray-400 group-hover:text-white'"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </button>
        </div>
    </div>
</header>

<!-- Нижнее мобильное меню -->
<div id="mb-menu" class="fixed bottom-0 left-0 right-0 z-50">
    <div class="absolute inset-0 bg-[#0f212e] backdrop-blur-xl border-t border-gray-800/50"></div>

    <div class="relative flex items-center justify-around h-16 px-2">
        <div class="flex flex-col items-center justify-center flex-1" x-data="{ isMenuOpen: false }" x-init="$watch('sidebarOpen', value => isMenuOpen = value)">
            <button @click="$store.ui.sidebarOpen = !$store.ui.sidebarOpen"
                    class="group text-gray-400 hover:text-[#4bd52d] transition-all duration-200">
                <div class="relative">
                    <div class="w-10 h-10 flex items-center justify-center rounded-xl group-hover:bg-gray-800 transition-colors">
                        <svg data-ds-icon="Browse" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" class="inline-block shrink-0">
                            <path fill="currentColor" d="M20.646 12.638a6.595 6.595 0 0 0 1.43-8.393C20.228 1.11 16.18.065 13.034 1.913c-3.135 1.848-4.18 5.896-2.332 9.042 1.661 2.816 5.082 3.938 8.041 2.805l1.727 2.926c.308.528.979.693 1.507.385s.693-.979.385-1.507l-1.727-2.926zm-8.03-2.805a4.407 4.407 0 0 1 1.551-6.028 4.407 4.407 0 0 1 6.028 1.551 4.407 4.407 0 0 1-1.551 6.028 4.407 4.407 0 0 1-6.028-1.55M3.2 23h17.6a2.2 2.2 0 0 0 0-4.4H3.2a2.2 2.2 0 0 0 0 4.4M7.886 9.8H3.2C1.99 9.8 1 10.79 1 12s.99 2.2 2.2 2.2h7.392a8.85 8.85 0 0 1-2.706-4.4M10.592 1H3.2C1.99 1 1 1.99 1 3.2s.99 2.2 2.2 2.2h4.686A8.85 8.85 0 0 1 10.592 1"/>
                        </svg>
                    </div>
                </div>
            </button>
            <span class="text-[10px] font-medium mt-1 text-gray-400" 
                  x-text="isMenuOpen ? '{{ __('Закрыть') }}' : '{{ __('Меню') }}'"></span>
        </div>

        <!-- Казино -->
        <a href="{{ route('slots.lobby') }}"
           
           class="flex flex-col items-center justify-center flex-1 group {{ request()->routeIs('slots.lobby') ? 'text-[#4bd52d]' : 'text-gray-400' }} hover:text-[#4bd52d] transition-all duration-200">
            <div class="relative">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl {{ request()->routeIs('slots.lobby') ? 'bg-[#4bd52d]/10' : 'group-hover:bg-gray-800' }} transition-colors">
                    <svg data-ds-icon="Casino" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" class="inline-block shrink-0">
                        <path fill="currentColor" d="m2.14 4.63 7.25-3.38c.63-.3 1.34-.23 1.89.11-.09.14-.18.28-.26.43L4.81 15.1 1.17 7.29c-.47-1-.03-2.19.97-2.66"/>
                        <path fill="currentColor" fill-rule="evenodd" d="m21.86 4.63-7.25-3.38c-1-.47-2.19-.03-2.66.97l-6.76 14.5c-.47 1-.03 2.19.97 2.66l7.25 3.38c1 .47 2.19.03 2.66-.97l6.76-14.5c.47-1 .03-2.19-.97-2.66m-9.54 11-.85-4.81 4.23-2.44.85 4.81z" clip-rule="evenodd"/>
                    </svg>
                </div>
                @if(request()->routeIs('slots.lobby'))
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-[#4bd52d] rounded-full"></div>
                @endif
            </div>
            <span class="text-[10px] font-medium mt-1">{{ __('Казино') }}</span>
        </a>

        <!-- Центральная кнопка -->
        @auth
            <div class="flex flex-col items-center justify-center flex-1 -mt-6">
                <button onclick="openCashModal();"
                        class="relative group">
                    <div class="absolute inset-0 bg-[#4fda2f] rounded-2xl blur-xl opacity-40 group-hover:opacity-60 transition-opacity"></div>
                    <div class="relative w-14 h-14 bg-gradient-to-br from-[#4fda2f] to-[#3bc91f] rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-200">
                        <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19" stroke-width="3"></line>
                            <line x1="5" y1="12" x2="19" y2="12" stroke-width="3"></line>
                        </svg>
                    </div>
                </button>
                <span class="text-[10px] font-medium mt-2 text-[#4fda2f]">{{ __('Пополнить') }}</span>
            </div>
        @else
            <div class="flex flex-col items-center justify-center flex-1 -mt-6">
                <button onclick="openLoginModal()"
                        class="relative group">
                    <!-- Glow Effect -->
                    <div class="absolute inset-0 bg-[#4bd52d] rounded-2xl blur-xl opacity-40 group-hover:opacity-60 transition-opacity"></div>

                    <!-- Button -->
                    <div class="relative w-14 h-14 bg-gradient-to-br from-[#4bd52d] to-[#e6a000] rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-200">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                </button>
                <span class="text-[10px] font-medium mt-2 text-[#4bd52d]">{{ __('Войти') }}</span>
            </div>
        @endauth

        <!-- Бонусы -->
        <a href="#"
           @auth onclick="openRakebackModal()" @else onclick="openLoginModal()" @endauth
           class="flex flex-col items-center justify-center flex-1 group text-gray-400 hover:text-[#4bd52d] transition-all duration-200">
            <div class="relative">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl group-hover:bg-gray-800 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
                @auth
                    <div class="absolute -top-1 -right-1 w-2 h-2 bg-green-500 rounded-full border-2 border-[#0f1419] animate-pulse"></div>
                @endauth
            </div>
            <span class="text-[10px] font-medium mt-1">{{ __('Бонусы') }}</span>
        </a>

        <!-- Профиль -->
        @auth
            <div class="relative flex flex-col items-center justify-center flex-1" x-data="{ isOpen: false }">
                <button @click.stop="isOpen = !isOpen"
                       class="flex flex-col items-center justify-center group text-gray-400 hover:text-[#4bd52d] transition-all duration-200">
                    <div class="relative">
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl group-hover:bg-gray-800 transition-colors p-1">
                            <img src="{{ auth()->user()->avatar ?? '/assets/images/avatar-placeholder.png' }}"
                                 alt="{{ auth()->user()->username }}"
                                 class="w-full h-full rounded-lg object-cover">
                        </div>
                    </div>
                    <span class="text-[10px] font-medium mt-1">{{ __('Профиль') }}</span>
                </button>

                <div x-show="isOpen"
                     @click.away="isOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute bottom-full mb-2 right-0 w-64 bg-white rounded-xl border border-gray-200 overflow-visible z-50"
                     style="display: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(0, 0, 0, 0.05);">

                    <div class="absolute -bottom-1 right-8 w-2 h-2 bg-white border-r border-b border-gray-200 transform rotate-45"></div>

                    <!-- Menu Items -->
                    <div class="py-2 relative">
                        <a href="javascript:void(0);" 
                        onclick="openCashModal()"
                        @click="isOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 6h-4c0 .71-.16 1.39-.43 2H20c.55 0 1 .45 1 1s-.45 1-1 1H4c-.55 0-1-.45-1-1s.45-1 1-1h3.43C7.16 7.39 7 6.71 7 6H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2m-2 11c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2"></path>
                                <path d="M9.38 9h5.24C15.46 8.27 16 7.2 16 6c0-2.21-1.79-4-4-4S8 3.79 8 6c0 1.2.54 2.27 1.38 3"></path>
                            </svg>
                            <span class="text-sm font-bold">{{ __('Кошелек') }}</span>
                        </a>


                        <a href="javascript:void(0);" 
                        onclick="openRankModal()"
                        @click="isOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                            <span class="text-sm font-bold">{{ __('ВИП') }}</span>
                        </a>

                        <a href="{{ route('account.referrals') }}"
                        
                        @click="isOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-sm font-bold">{{ __('Партнерам') }}</span>
                        </a>

                        <a href="javascript:void(0);" 
                        onclick="window.openModalWithMyInfo()"
                        @click="isOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="text-sm font-bold">{{ __('Статистика') }}</span>
                        </a>

                        <a href="{{ route('transaction') }}"
                        
                        @click="isOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <span class="text-sm font-bold">{{ __('Транзакции') }}</span>
                        </a>

                        <a href="{{ route('slots.history') }}"
                        
                        @click="isOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span class="text-sm font-bold">{{ __('Мои ставки') }}</span>
                        </a>

                        <a href="{{ route('account') }}"
                        
                        @click="isOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-sm font-bold">{{ __('Настройки') }}</span>
                        </a>
                    </div>

                        <a href="{{ route('auth.logout') }}" 
                        data-turbo="false"
                        onclick="sessionStorage.setItem('clearSWCache', 'true')"
                        @click="isOpen = false"
                        class="flex items-center gap-3 px-4 py-3 text-[#2f4553] hover:bg-gray-100 transition-colors font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="text-sm">{{ __('Выход') }}</span>
                        </a>
                </div>
            </div>
        @else
            <a href="#"
               onclick="openRegisterModal()"
               class="flex flex-col items-center justify-center flex-1 group text-gray-400 hover:text-[#4bd52d] transition-all duration-200">
                <div class="w-10 h-10 flex items-center justify-center rounded-xl group-hover:bg-gray-800 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <span class="text-[10px] font-medium mt-1">{{ __('Регистрация') }}</span>
            </a>
        @endauth
    </div>
</div>
