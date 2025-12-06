<div x-data="filterComponent()" x-init="$nextTick(() => {})" class="mt-6 relative" x-cloak style="display: block !important;">
    <div class="flex items-center gap-2 mb-6 overflow-x-auto scrollbar-hide pb-2 h-14">
        <button
            @click="clearFilters()"
            :class="selectedProviders.length === 0 ? 'bg-[#3b82f6] text-white shadow-lg shadow-[#3b82f6]/20' : 'bg-[#1a2c38] text-gray-300 hover:bg-[#2d3748] border border-[#2d3748]'"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold whitespace-nowrap transition-all text-sm bg-[#3b82f6] text-white shadow-lg shadow-[#3b82f6]/20"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
            </svg>
            {{ __('Все игры') }}
        </button>

        <button
            @click="toggleDropdown()"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-semibold whitespace-nowrap transition-all text-sm relative bg-[#1a2c38] text-gray-300 hover:bg-[#2d3748] border border-[#2d3748]"
            :class="selectedProviders.length > 0 ? 'bg-[#3b82f6] text-white shadow-lg shadow-[#3b82f6]/20' : 'bg-[#1a2c38] text-gray-300 hover:bg-[#2d3748] border border-[#2d3748]'"
        >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
            </svg>
            <span>{{ __('Провайдеры') }}</span>
            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
            <div
                x-show="selectedProviders.length > 0"
                x-cloak
                style="display: none"
                class="absolute -top-1 -right-1 w-5 h-5 bg-[#4dda30] rounded-full flex items-center justify-center text-xs text-white font-bold shadow-lg"
                x-text="selectedProviders.length"
            ></div>
        </button>
    </div>

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
        class="absolute z-50 mt-3 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden"
        style="display: none"
    >
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-[#3b82f6]/5 to-transparent">
            <h3 class="text-lg font-bold text-gray-900">{{ __('Выберите провайдеров') }}</h3>
            <button
                @click="isOpen = false"
                class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 hover:text-gray-900 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-4 bg-gray-50">
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    x-model="search"
                    placeholder="{{ __('Поиск провайдера...') }}"
                    class="w-full py-3 pl-10 pr-4 bg-white text-gray-900 border border-gray-200 rounded-xl focus:outline-none focus:border-[#3b82f6] focus:ring-2 focus:ring-[#3b82f6]/20 transition-all"
                />
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto p-4 space-y-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent">
            <template x-for="provider in filteredProviders" :key="provider.provider">
                <div
                    @click="toggleProvider(provider.provider)"
                    :class="selectedProviders.includes(provider.provider) ? 'bg-[#3b82f6]/5 border-[#3b82f6]' : 'bg-white hover:bg-gray-50 border-gray-200'"
                    class="p-3 rounded-xl border cursor-pointer transition-all duration-200"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div
                                :class="selectedProviders.includes(provider.provider) ? 'border-[#3b82f6]' : 'border-gray-300'"
                                class="h-5 w-5 rounded border-2 flex items-center justify-center transition-all duration-200 flex-shrink-0"
                            >
                                <svg
                                    x-show="selectedProviders.includes(provider.provider)"
                                    class="w-3.5 h-3.5 text-[#3b82f6]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="3"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span x-text="provider.provider" class="text-gray-900 font-medium text-sm truncate"></span>
                        </div>
                        <span
                            x-text="provider.games_count"
                            class="ml-3 px-2.5 py-1 bg-[#3b82f6] text-white text-xs rounded-full font-semibold flex-shrink-0"
                        ></span>
                    </div>
                </div>
            </template>
        </div>

        <div class="p-4 border-t border-gray-200 flex justify-between items-center bg-gray-50">
            <span class="text-sm text-gray-600">
                {{ __('Выбрано') }}: <span class="text-gray-900 font-bold" x-text="selectedProviders.length"></span>
            </span>
            <div class="flex gap-3">
                <button
                    @click="clearFilters()"
                    class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 hover:text-gray-900 rounded-xl transition-all text-sm font-medium border border-gray-200"
                >
                    {{ __('Сбросить') }}
                </button>
                <button
                    @click="isOpen = false"
                    class="px-4 py-2 bg-[#4dda30] hover:bg-[#3bb825] text-white rounded-xl transition-all text-sm font-bold shadow-lg shadow-[#4dda30]/20"
                >
                    {{ __('Применить') }}
                </button>
            </div>
        </div>
    </div>

    <div x-cloak x-show="selectedProviders.length > 0"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="mt-4"
        style="display: none"
    >
        <div class="p-4 bg-[#1a2c38] border border-[#2d3748] rounded-xl">
            <div class="flex justify-between items-center mb-3">
                <span class="text-sm text-gray-400 font-medium">{{ __('Активные фильтры') }}</span>
                <button
                    @click="clearFilters()"
                    class="text-sm text-red-400 hover:text-red-300 transition-colors flex items-center gap-1 font-medium"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ __('Очистить все') }}
                </button>
            </div>

            <div class="flex flex-wrap gap-2">
                <template x-for="provider in selectedProviders" :key="provider">
                    <button
                        class="group flex items-center gap-2 px-3 py-1.5 bg-[#0f212e] hover:bg-[#2d3748] text-gray-300 hover:text-white rounded-lg transition-all text-sm border border-[#2d3748]"
                        @click="removeProvider(provider)"
                    >
                        <span x-text="provider"></span>
                        <div class="w-5 h-5 rounded-full bg-[#2d3748] group-hover:bg-red-500 flex items-center justify-center transition-all">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>

<script>
function filterComponent() {
    return {
        search: '',
        isOpen: false,
        isProcessing: false,
        selectedProviders: [],
        providers: @json($providers),
        
        init() {
            // Ждем пока Livewire будет готов
            const setupEntangle = () => {
                if (this.$wire) {
                    this.selectedProviders = this.$wire.entangle('selectedProviders');
                } else {
                    // Если $wire еще нет, пробуем через 50мс
                    setTimeout(setupEntangle, 50);
                }
            };
            setupEntangle();
        },

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
            if (this.$wire && this.$wire.filterByProviders) {
                this.$wire.filterByProviders(this.selectedProviders);
            }
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