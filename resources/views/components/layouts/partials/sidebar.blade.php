<div class="flex flex-col h-full bg-[#0f212e] transition-all duration-300" 
     x-data="sidebarState()" 
     :class="{ 'sidebar-collapsed': isCollapsed }">
    
    <div class="flex items-center gap-2 p-4">
        <button @click="sidebarOpen = false" 
                class="xl:hidden p-2 rounded-lg hover:bg-white/5 transition-colors text-gray-400 hover:text-white flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <button @click="toggleSidebar()" 
                class="hidden xl:block p-2 rounded-lg hover:bg-white/5 transition-colors text-gray-400 hover:text-white flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        
        <div class="flex items-center gap-2 flex-1">
            <a href="{{ route('slots.lobby') }}" 
               
               style="background: linear-gradient(135deg, #4dda31 0%, #3bb825 100%); box-shadow: 0 4px 14px rgba(77, 218, 49, 0.3);"
               class="flex items-center justify-center flex-1 px-4 py-2.5 hover:brightness-110 rounded-lg text-white font-semibold transition-all text-sm"
               x-show="!isCollapsed || window.innerWidth < 1280" 
               x-transition>
                {{ __('Казино') }}
            </a>
            <a href="#" 
               style="background: linear-gradient(135deg, #1e3a4a 0%, #16303d 100%);"
               class="flex items-center justify-center flex-1 px-4 py-2.5 hover:brightness-110 rounded-lg text-white font-semibold transition-all text-sm border border-white/5"
               x-show="!isCollapsed || window.innerWidth < 1280" 
               x-transition>
                {{ __('Live') }}
            </a>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto px-3 pb-6 custom-scrollbar" :class="isCollapsed ? 'mt-4' : ''">
        @auth
        <div class="mb-4 bg-[#1a2c38] rounded-lg p-2 space-y-1">
            <a href="{{ route('slots.history') }}"
               
               @click="sidebarOpen = false"
               class="sidebar-item {{ request()->routeIs('slots.history') ? 'active' : '' }}"
               x-tooltip="isCollapsed ? '{{ __('Недавние') }}' : ''">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Недавние') }}</span>
            </a>

            <a href="{{ route('slots.popular') }}"
               
               @click="sidebarOpen = false"
               class="sidebar-item {{ request()->routeIs('slots.popular') ? 'active' : '' }}"
               x-tooltip="isCollapsed ? '{{ __('Популярные') }}' : ''">
               <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M10.4 1c-.2122 0-.41566.08429-.56569.23431-.15002.15003-.23431.35352-.23431.56569 0 4.0456-5.6 7.2-5.6 12.8 0 4.2543 4.6108 7.0047 6.2344 7.1828.0544.0115.1656.0172.1656.0172.2122 0 .4157-.0843.5657-.2343s.2343-.3535.2343-.5657c-.0001-.1188-.0266-.2362-.0777-.3435s-.1254-.2018-.2176-.2768v-.0016c-.728-.5904-2.1047-2.3032-2.1047-3.8672 0-2.5672 2.4-3.5109 2.4-3.5109-1.3688 3.872 3.1163 4.3398 4.0188 8.1734h.0015c.0394.1775.1381.3363.2799.4502.1417.1139.318.1761.4998.1764.1685-.0004.3747-.0868.5109-.1859-.0137.0114.0145-.0105 0 0 .1302-.0846 3.4891-2.3061 3.4891-7.0141 0-1.9221-.9613-5.2236-1.6531-6.68594l-.0016-.00469-.0015-.00312c-.059-.14923-.1614-.27731-.294-.36759-.1326-.09027-.2894-.13859-.4498-.13866-.1868.00018-.3677.06574-.5113.18532-.1435.11958-.2407.28564-.2746.46937v.00312c-.002.00875-.3694 1.64061-1.6141 2.54219 0-3.76931-2.7213-7.66271-4.1562-9.27344-.0216-.02916-.0451-.05683-.0704-.08281-.0744-.07692-.1635-.13813-.262-.18s-.2044-.06355-.3114-.06375z"/>
               </svg>
                <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Популярные') }}</span>
            </a>

            <a href="{{ route('slots.new') }}"
               
               @click="sidebarOpen = false"
               class="sidebar-item {{ request()->routeIs('slots.new') ? 'active' : '' }}"
               x-tooltip="isCollapsed ? '{{ __('Новые') }}' : ''">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="16"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
                <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Новые') }}</span>
            </a>

            <a href="{{ route('slots.favorites') }}"
               
               @click="sidebarOpen = false"
               class="sidebar-item {{ request()->routeIs('slots.favorites') ? 'active' : '' }}"
               x-tooltip="isCollapsed ? '{{ __('Избранные') }}' : ''">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                </svg>
                <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Избранные') }}</span>
            </a>
        </div>
        @endauth

        <div class="mb-4 bg-[#1a2c38] rounded-lg p-2">
            <h3 class="mb-3 flex items-center px-3 py-2 text-xs font-bold uppercase tracking-wider text-[#4ed930] bg-[#0f212e] rounded-md"
                x-show="!isCollapsed" x-transition>
                <svg class="mr-2 h-4 w-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/>
                    <line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
                <span class="truncate">{{ __('ИГРЫ') }}</span>
            </h3>

            <div class="space-y-1">
                <a href="{{ route('slots.lobby') }}"
                   
                   @click="sidebarOpen = false"
                   class="sidebar-item {{ request()->routeIs('slots.lobby') ? 'active' : '' }}"
                   x-tooltip="isCollapsed ? '{{ __('Все слоты') }}' : ''">
                    <svg fill="currentColor" viewBox="0 0 96 96" class="h-5 w-5 flex-shrink-0">
                        <path fill-rule="evenodd" d="M56.8 47.08a49.76 49.76 0 0 0-5.6 22.8v5H32.32a55.6 55.6 0 0 1 5-22.76A87 87 0 0 1 50.8 31h-28V16.36H72v7.76a134 134 0 0 0-15.2 22.96m26.4 16.24a30.56 30.56 0 0 0-6 13.04l-.6 3L60 76.32a38.12 38.12 0 0 1 13.36-22.28l-12-2.36 5.04-10.64L96 46.88l-.92 4.64a85.5 85.5 0 0 0-11.88 11.8m-58.52 9.32a30.1 30.1 0 0 1 0-14.36 79.7 79.7 0 0 1 5.8-15.84l-1.12-4.6L0 44.88v11.68l12-2.84a37.88 37.88 0 0 0-2.88 25.92l16.28-4z" clip-rule="evenodd"/>
                    </svg>
                    <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Все слоты') }}</span>
                </a>

                @foreach($sidebarCategories as $cat)
                <a href="{{ route('slots.category', $cat->slug) }}"
                   
                   @click="sidebarOpen = false"
                   class="sidebar-item {{ request()->is('slots/category/'.$cat->slug) ? 'active' : '' }}"
                   x-tooltip="isCollapsed ? '{{ __($cat->name) }}' : ''">
                    @if($cat->icon)
                        <div class="h-5 w-5 flex-shrink-0">
                            {!! $cat->icon !!}
                        </div>
                    @else
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                        </svg>
                    @endif
                    <span x-show="!isCollapsed" x-transition class="truncate">{{ __($cat->name) }}</span>
                </a>
                @endforeach
            </div>
        </div>

        @auth
        <div class="mb-4 bg-[#1a2c38] rounded-lg p-2">
            <h3 class="mb-3 flex items-center px-3 py-2 text-xs font-bold uppercase tracking-wider text-[#4ed930] bg-[#0f212e] rounded-md"
                x-show="!isCollapsed" x-transition>
                <svg class="mr-2 h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM7.07 18.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78C15.57 19.36 13.86 20 12 20s-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33A7.95 7.95 0 014 12c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM12 6c-1.94 0-3.5 1.56-3.5 3.5S10.06 13 12 13s3.5-1.56 3.5-3.5S13.94 6 12 6zm0 5c-.83 0-1.5-.67-1.5-1.5S11.17 8 12 8s1.5.67 1.5 1.5S12.83 11 12 11z"/>
                </svg>
                <span class="truncate">{{ __('АККАУНТ') }}</span>
            </h3>

            <div class="space-y-1">
                <a href="javascript:void(0);" 
                   onclick="openRakebackModal(); document.body.classList.remove('sidebar-open')" 
                   class="sidebar-item"
                   x-tooltip="isCollapsed ? '{{ __('Бонус') }}' : ''">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                        <line x1="9" y1="9" x2="9.01" y2="9"/>
                        <line x1="15" y1="9" x2="15.01" y2="9"/>
                    </svg>
                    <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Бонус') }}</span>
                </a>

                <a href="javascript:void(0);" 
                   onclick="openRankModal(); document.body.classList.remove('sidebar-open')" 
                   class="sidebar-item"
                   x-tooltip="isCollapsed ? '{{ __('Уровень') }}' : ''">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Уровень') }}</span>
                </a>

                <a href="javascript:void(0);" 
                   onclick="openPromoModal(); document.body.classList.remove('sidebar-open')" 
                   class="sidebar-item"
                   x-tooltip="isCollapsed ? '{{ __('Промо') }}' : ''">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <polyline points="20 12 20 22 4 22 4 12"/>
                        <rect x="2" y="7" width="20" height="5"/>
                        <line x1="12" y1="22" x2="12" y2="7"/>
                        <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                        <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                    </svg>
                    <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Промо') }}</span>
                </a>

                <a href="{{ route('account.referrals') }}"
                   
                   @click="sidebarOpen = false"
                   class="sidebar-item {{ request()->routeIs('account.referrals') ? 'active' : '' }}"
                   x-tooltip="isCollapsed ? '{{ __('Партнерам') }}' : ''">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <polyline points="17 11 19 13 23 9"/>
                    </svg>
                    <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Партнерам') }}</span>
                </a>

                <a href="javascript:void(0);" 
                   onclick="window.openModalWithMyInfo(); document.body.classList.remove('sidebar-open')" 
                   class="sidebar-item"
                   x-tooltip="isCollapsed ? '{{ __('Статистика') }}' : ''">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                    <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Статистика') }}</span>
                </a>

                <a href="{{ route('transaction') }}"
                   
                   @click="sidebarOpen = false"
                   class="sidebar-item {{ request()->routeIs('transaction') ? 'active' : '' }}"
                   x-tooltip="isCollapsed ? '{{ __('Транзакции') }}' : ''">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <line x1="12" y1="1" x2="12" y2="23"/>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                    <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Транзакции') }}</span>
                </a>

                <a href="{{ route('account') }}"
                   
                   @click="sidebarOpen = false"
                   class="sidebar-item {{ request()->routeIs('account') ? 'active' : '' }}"
                   x-tooltip="isCollapsed ? '{{ __('Настройки') }}' : ''">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Настройки') }}</span>
                </a>

                <a href="{{ route('auth.logout') }}" 
                   class="sidebar-item text-red-400 hover:text-red-300"
                   x-tooltip="isCollapsed ? '{{ __('Выход') }}' : ''">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Выход') }}</span>
                </a>
            </div>
        </div>
        @endauth

        <div class="mb-6 border-t border-gray-800 pt-4">
            <a href="https://t.me/flashgame_support_bot" 
               target="_blank" 
               class="sidebar-item"
               x-tooltip="isCollapsed ? '{{ __('Поддержка') }}' : ''">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Поддержка') }}</span>
            </a>
            @if(isset($settings) && isset($settings->support_tg) && $settings->support_tg)
            <a href="https://t.me/{{ $settings->support_tg }}" 
               target="_blank" 
               rel="noopener" 
               class="sidebar-item"
               x-tooltip="isCollapsed ? '{{ __('Telegram канал') }}' : ''">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9.75 15.75l-2.25-.75c-.6-.2-.6-.6 0-.8l10.5-4.1c.6-.2 1 .2.8.8l-1.8 7.2c-.2.6-.6.7-1.1.5l-3-2.2-1.4 1.3c-.4.4-.7.3-.8-.2l-.5-2.1z" />
                </svg>
                <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Telegram канал') }}</span>
            </a>
            @endif

            <a href="{{ route('rules') }}"
               
               @click="sidebarOpen = false"
               class="sidebar-item"
               x-tooltip="isCollapsed ? '{{ __('Правила') }}' : ''">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                <span x-show="!isCollapsed" x-transition class="truncate">{{ __('Правила') }}</span>
            </a>
        </div>

        <div class="bg-[#0f212e] pt-2 pb-4 mb-8">
            <div x-data="{ open: false }" class="relative">
                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-2"
                     style="display: none;"
                     class="absolute bottom-full left-0 right-0 mb-2 bg-[#1a2c38] rounded-lg overflow-hidden shadow-xl border border-gray-700">
                    <div class="grid gap-1 p-2" :class="isCollapsed ? 'grid-cols-1' : 'grid-cols-3'">
                        <a href="/setlocale/ru"  class="flex flex-col items-center p-2 rounded hover:bg-gray-800 text-gray-400 hover:text-white transition">
                            <img src="/assets/images/lang/ru.png" class="h-6 w-6 rounded mb-1" alt="RU">
                            <span class="text-[10px] uppercase" x-show="!isCollapsed">RU</span>
                        </a>
                        <a href="/setlocale/en"  class="flex flex-col items-center p-2 rounded hover:bg-gray-800 text-gray-400 hover:text-white transition">
                            <img src="/assets/images/lang/en.png" class="h-6 w-6 rounded mb-1" alt="EN">
                            <span class="text-[10px] uppercase" x-show="!isCollapsed">EN</span>
                        </a>
                        <a href="/setlocale/tr"  class="flex flex-col items-center p-2 rounded hover:bg-gray-800 text-gray-400 hover:text-white transition">
                            <img src="/assets/images/lang/tr.png" class="h-6 w-6 rounded mb-1" alt="TR">
                            <span class="text-[10px] uppercase" x-show="!isCollapsed">TR</span>
                        </a>
                        <a href="/setlocale/az"  class="flex flex-col items-center p-2 rounded hover:bg-gray-800 text-gray-400 hover:text-white transition">
                            <img src="/assets/images/lang/az.png" class="h-6 w-6 rounded mb-1" alt="AZ">
                            <span class="text-[10px] uppercase" x-show="!isCollapsed">AZ</span>
                        </a>
                        <a href="/setlocale/kz"  class="flex flex-col items-center p-2 rounded hover:bg-gray-800 text-gray-400 hover:text-white transition">
                            <img src="/assets/images/lang/kz.png" class="h-6 w-6 rounded mb-1" alt="KZ">
                            <span class="text-[10px] uppercase" x-show="!isCollapsed">KZ</span>
                        </a>
                        <a href="/setlocale/uz"  class="flex flex-col items-center p-2 rounded hover:bg-gray-800 text-gray-400 hover:text-white transition">
                            <img src="/assets/images/lang/uz.png" class="h-6 w-6 rounded mb-1" alt="UZ">
                            <span class="text-[10px] uppercase" x-show="!isCollapsed">UZ</span>
                        </a>
                    </div>
                </div>

                <button @click.stop="open = !open" 
                        type="button" 
                        class="flex w-full items-center justify-between px-3 py-2 rounded-lg text-gray-400 hover:bg-[#1a2c38] hover:text-white transition"
                        :class="isCollapsed ? 'justify-center' : ''"
                        x-tooltip="isCollapsed ? 'Language' : ''">
                    <div class="flex items-center gap-2">
                        <img src="/assets/images/lang/{{ Config::get('app.locale') }}.png" 
                             class="h-5 w-5 rounded" 
                             alt="{{ Config::get('app.locale') }}">
                        <span class="uppercase text-sm" x-show="!isCollapsed" x-transition>
                            {{ Config::get('app.locale') }}
                        </span>
                    </div>
                    <svg :class="open && 'rotate-180'" 
                         class="h-4 w-4 transition-transform" 
                         fill="none" 
                         stroke="currentColor" 
                         viewBox="0 0 24 24"
                         x-show="!isCollapsed">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function sidebarState() {
    return {
        isCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        
        toggleSidebar() {
            this.isCollapsed = !this.isCollapsed;
            localStorage.setItem('sidebarCollapsed', this.isCollapsed);
            
            document.querySelector('.sidebar-wrapper')?.classList.toggle('collapsed', this.isCollapsed);
            document.querySelector('.main-content')?.classList.toggle('sidebar-collapsed', this.isCollapsed);
        }
    }
}
</script>