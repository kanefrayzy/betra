<!-- Компонент фильтра -->
<div x-data="filterComponent()" class="mt-6 relative">
    <!-- Панель быстрых фильтров -->
    <div class="flex items-center gap-2 mb-6 overflow-x-auto scrollbar-hide pb-2 h-14">
        <button
            @click="clearFilters()"
            :class="selectedProviders.length === 0 ? 'bg-[#ffb300] text-black' : 'bg-dark-800/60 text-gray-300 hover:bg-dark-700/80'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold whitespace-nowrap transition-colors text-sm"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
            </svg>
            {{ __('Все игры') }}
        </button>

        <button
            @click="toggleDropdown()"
            class="flex items-center gap-2 px-4 py-2.5 rounded-lg font-semibold whitespace-nowrap transition-colors text-sm relative"
            :class="selectedProviders.length > 0 ? 'bg-[#ffb300] text-black' : 'bg-dark-800/60 text-gray-300 hover:bg-dark-700/80'"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
            </svg>
            <span>{{ __('Провайдеры') }}</span>
            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': isOpen}" fill="currentColor" viewBox="0 0 20 20">
                <polyline points="6 9 12 15 18 9" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div
                x-show="selectedProviders.length > 0"
                class="absolute -top-1 -right-1 w-5 h-5 bg-[#f43f5e] rounded-full flex items-center justify-center text-xs text-white font-bold"
                x-text="selectedProviders.length"
            ></div>
        </button>
    </div>

    <!-- Выпадающее меню с провайдерами -->
    <div
        x-cloak
        x-show="isOpen"
        @click.away="isOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-3 w-full max-w-2xl bg-[#1e2329] rounded-xl shadow-2xl border border-gray-800 overflow-hidden"
    >
        <!-- Заголовок меню -->
        <div class="p-4 border-b border-gray-800 flex justify-between items-center bg-gradient-to-r from-[#ffb300]/10 to-transparent">
            <h3 class="text-lg font-bold text-white">{{ __('Выберите провайдеров') }}</h3>
            <button
                @click="isOpen = false"
                class="w-8 h-8 rounded-lg bg-dark-800/60 hover:bg-dark-700/80 flex items-center justify-center text-gray-400 hover:text-white transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Поиск провайдера -->
        <div class="p-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    x-model="search"
                    placeholder="{{ __('Поиск провайдера...') }}"
                    class="w-full py-3 pl-10 pr-4 bg-dark-800/60 text-white border border-gray-800 rounded-lg focus:outline-none focus:border-[#ffb300] focus:ring-1 focus:ring-[#ffb300] transition-all"
                />
            </div>
        </div>

        <!-- Сетка провайдеров -->
        <div class="max-h-96 overflow-y-auto p-4 grid grid-cols-2 sm:grid-cols-3 gap-3 scrollbar-thin scrollbar-thumb-gray-800 scrollbar-track-transparent">
            <template x-for="provider in filteredProviders" :key="provider.provider">
                <div
                    @click="toggleProvider(provider.provider)"
                    :class="selectedProviders.includes(provider.provider) ? 'bg-[#ffb300]/10 border-[#ffb300]' : 'bg-dark-800/40 hover:bg-dark-700/60 border-gray-800'"
                    class="p-3 rounded-lg border cursor-pointer transition-all duration-200 hover:scale-[1.02]"
                >
                    <div class="flex justify-between items-center mb-2">
                        <span x-text="provider.provider" class="text-white font-medium text-sm truncate"></span>
                        <div
                            :class="selectedProviders.includes(provider.provider) ? 'bg-[#ffb300]' : 'bg-gray-700'"
                            class="h-5 w-5 rounded flex items-center justify-center transition-all duration-200 flex-shrink-0"
                        >
                            <svg
                                x-show="selectedProviders.includes(provider.provider)"
                                class="w-3 h-3 text-black"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400">{{ __('Игр') }}:</span>
                        <span
                            x-text="provider.games_count"
                            class="text-xs px-2 py-0.5 bg-dark-900/50 text-gray-300 rounded font-medium"
                        ></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Нижняя панель действий -->
        <div class="p-4 border-t border-gray-800 flex justify-between items-center bg-dark-900/50">
            <span class="text-sm text-gray-400">
                {{ __('Выбрано') }}: <span class="text-white font-bold" x-text="selectedProviders.length"></span>
            </span>
            <div class="flex gap-3">
                <button
                    @click="clearFilters()"
                    class="px-4 py-2 bg-dark-800/60 hover:bg-dark-700/80 text-gray-300 hover:text-white rounded-lg transition-all text-sm font-medium"
                >
                    {{ __('Сбросить') }}
                </button>
                <button
                    @click="isOpen = false"
                    class="px-4 py-2 bg-[#ffb300] hover:bg-[#f5a300] text-black rounded-lg transition-all text-sm font-bold"
                >
                    {{ __('Применить') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Панель выбранных фильтров -->
    <div x-cloak x-show="selectedProviders.length > 0"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="mt-4"
    >
        <div class="p-4 bg-dark-900/50 border border-gray-800 rounded-xl">
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm text-gray-400 font-medium">{{ __('Активные фильтры') }}</span>
                <button
                    @click="clearFilters()"
                    class="text-sm text-[#f43f5e] hover:text-red-400 transition-colors flex items-center gap-1 font-medium"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('Очистить все') }}
                </button>
            </div>

            <div class="flex flex-wrap gap-2">
                <template x-for="provider in selectedProviders" :key="provider">
                    <button
                        class="group flex items-center gap-2 px-3 py-1.5 bg-dark-800/60 hover:bg-dark-700/80 text-gray-300 hover:text-white rounded-lg transition-all text-sm"
                        @click="removeProvider(provider)"
                    >
                        <span x-text="provider"></span>
                        <div class="w-5 h-5 rounded-full bg-gray-700 group-hover:bg-[#f43f5e] flex items-center justify-center transition-all">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #374151;
        border-radius: 3px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #4b5563;
    }
</style>

<script>
function filterComponent() {
    return {
        search: '',
        isOpen: false,
        isProcessing: false,
        selectedProviders: @entangle('selectedProviders'),
        providers: @json($providers),

        get filteredProviders() {
            if (!this.search.trim()) {
                return [...this.providers].sort((a, b) => b.games_count - a.games_count);
            }

            return this.providers
                .filter(provider => provider.provider.toLowerCase().includes(this.search.toLowerCase()))
                .sort((a, b) => {
                    const aStartsWithSearch = a.provider.toLowerCase().startsWith(this.search.toLowerCase());
                    const bStartsWithSearch = b.provider.toLowerCase().startsWith(this.search.toLowerCase());

                    if (aStartsWithSearch && !bStartsWithSearch) return -1;
                    if (!aStartsWithSearch && bStartsWithSearch) return 1;

                    return b.games_count - a.games_count;
                });
        },

        toggleDropdown() {
            this.isOpen = !this.isOpen;

            if (this.isOpen) {
                this.search = '';
                setTimeout(() => {
                    const inputEl = document.querySelector('[x-model="search"]');
                    if (inputEl) inputEl.focus();
                }, 50);
            }
        },

        toggleProvider(provider) {
            if (this.isProcessing) return;
            this.isProcessing = true;

            if (window.navigator && window.navigator.vibrate) {
                window.navigator.vibrate(50);
            }

            if (this.selectedProviders.includes(provider)) {
                this.selectedProviders = this.selectedProviders.filter(p => p !== provider);
            } else {
                this.selectedProviders.push(provider);
            }

            this.updateFilters();
            setTimeout(() => { this.isProcessing = false; }, 100);
        },

        removeProvider(provider) {
            if (window.navigator && window.navigator.vibrate) {
                window.navigator.vibrate(30);
            }

            this.selectedProviders = this.selectedProviders.filter(p => p !== provider);
            this.updateFilters();
        },

        updateFilters() {
            this.$wire.filterByProviders(this.selectedProviders);
        },

        clearFilters() {
            if (window.navigator && window.navigator.vibrate) {
                window.navigator.vibrate([30, 20, 30]);
            }

            this.selectedProviders = [];
            this.updateFilters();
        }
    }
}
</script>
