<div x-data="cashModalData()"
     @open-cash-modal.window="open = true"
     @close-cash-modal.window="open = false"
     @keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto modaler"
     style="display: none;">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm"
         @click="open = false"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="open = false"
             class="relative w-full max-w-4xl bg-[#1e2329] rounded-2xl shadow-2xl border border-gray-800">

            <!-- Header -->
            <div class="relative px-6 py-5 border-b border-gray-800">
                <button @click="open = false"
                        class="absolute top-4 right-4 p-1.5 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h2 class="text-xl font-bold text-white">{{__('Кошелек')}}</h2>
                <p class="text-gray-400 text-sm mt-1">{{__('Быстрые финансовые операции')}}</p>
            </div>

            <!-- Tabs -->
            <div class="flex bg-[#252a32] border-b border-gray-800">
                <button @click="operation = 'deposit'"
                        class="flex-1 py-4 text-center font-medium transition-all duration-200 relative text-sm"
                        :class="operation === 'deposit' ? 'text-white' : 'text-gray-400 hover:text-white'">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>{{__('Пополнение')}}</span>
                    </div>
                    <div x-show="operation === 'deposit'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#ffb300]"></div>
                </button>
                <button @click="operation = 'withdrawal'"
                        class="flex-1 py-4 text-center font-medium transition-all duration-200 relative text-sm"
                        :class="operation === 'withdrawal' ? 'text-white' : 'text-gray-400 hover:text-white'">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{__('Вывод')}}</span>
                    </div>
                    <div x-show="operation === 'withdrawal'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#ffb300]"></div>
                </button>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <!-- Info Banner -->
                <div @click="openCryptoGuide()" 
                     class="relative flex w-full flex-col items-start rounded-xl border border-gray-800 overflow-hidden p-4 bg-cover bg-no-repeat bg-center cursor-pointer hover:border-[#ffb300] transition-all duration-300 mb-6 min-h-[96px] group"
                     style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.6)), url('/assets/images/banner-cashier.webp');">
                    <p class="text-base font-semibold text-white max-w-[75%] line-clamp-2 relative z-10">
                        {{__('Читай как пополнять и выводить в криптовалюте')}} 💎
                    </p>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-white/60 group-hover:text-[#ffb300] transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>

                <!-- Deposit Form -->
                <div x-show="operation === 'deposit'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-4"
                     x-transition:enter-end="opacity-100 transform translate-x-0">
                    <form @submit.prevent="submitDeposit" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Payment Methods -->
                            <div class="space-y-4">
                                <label class="block text-gray-400 text-sm mb-3 font-medium">{{__('Способ оплаты')}}</label>
                                
                                <!-- Кастомный Dropdown для мобильных -->
                                <div class="lg:hidden relative" x-data="{ dropdownOpen: false }">
                                    <!-- Кнопка dropdown -->
                                    <button type="button"
                                            @click="dropdownOpen = !dropdownOpen"
                                            class="flex items-center justify-between w-full p-3 bg-[#252a32] border border-gray-800 rounded-lg cursor-pointer transition-all duration-200"
                                            :class="selectedSystem ? 'border-[#ffb300]' : ''">
                                        <div class="flex items-center gap-3 flex-1">
                                            <template x-if="selectedSystem">
                                                <img :src="getSystemIcon(selectedSystem, 'deposit')" 
                                                     :alt="getSystemName(selectedSystem, 'deposit')"
                                                     class="w-8 h-6 object-contain flex-shrink-0">
                                            </template>
                                            <span class="text-white text-sm font-medium" x-text="selectedSystem ? getSystemName(selectedSystem, 'deposit') : '{{__('Выберите способ оплаты')}}'"></span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 flex-shrink-0" 
                                             :class="dropdownOpen ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Выпадающий список -->
                                    <div x-show="dropdownOpen"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 transform scale-95"
                                         x-transition:enter-end="opacity-100 transform scale-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 transform scale-100"
                                         x-transition:leave-end="opacity-0 transform scale-95"
                                         @click.away="dropdownOpen = false"
                                         class="absolute z-50 left-0 right-0 mt-2 bg-[#1e2329] border border-gray-800 rounded-lg shadow-2xl max-h-80 overflow-y-auto custom-scrollbar">
                                        
                                        @if($matchingHandlers->isNotEmpty())
                                        <div class="p-2 border-b border-gray-800">
                                            <p class="px-2 py-1 text-xs text-gray-500 font-semibold">{{__('Рекомендуемые')}}</p>
                                        </div>
                                        @foreach($matchingHandlers as $handler)
                                        <button type="button"
                                                @click="selectedSystem = '{{ $handler->id }}'; dropdownOpen = false"
                                                class="flex items-center w-full p-3 hover:bg-[#252a32] transition-all duration-200"
                                                :class="selectedSystem === '{{ $handler->id }}' ? 'bg-[#ffb300]/10 border-l-2 border-[#ffb300]' : ''">
                                            <img src="{{ asset('storage/' . $handler->icon) }}"
                                                 alt="{{ $handler->name }}"
                                                 class="w-8 h-6 object-contain mr-3 flex-shrink-0">
                                            <span class="text-white text-sm font-medium flex-1 text-left">{{ $handler->name }}</span>
                                            <svg x-show="selectedSystem === '{{ $handler->id }}'" 
                                                 class="w-5 h-5 text-[#ffb300]" 
                                                 fill="currentColor" 
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                        @endforeach
                                        @endif

                                        @if($otherHandlers->isNotEmpty())
                                        <div class="p-2 border-b border-gray-800">
                                            <p class="px-2 py-1 text-xs text-gray-500 font-semibold">{{__('Другие методы')}}</p>
                                        </div>
                                        @foreach($otherHandlers as $handler)
                                        <button type="button"
                                                @click="selectedSystem = '{{ $handler->id }}'; dropdownOpen = false"
                                                class="flex items-center w-full p-3 hover:bg-[#252a32] transition-all duration-200"
                                                :class="selectedSystem === '{{ $handler->id }}' ? 'bg-[#ffb300]/10 border-l-2 border-[#ffb300]' : ''">
                                            <img src="{{ asset('storage/' . $handler->icon) }}"
                                                 alt="{{ $handler->name }}"
                                                 class="w-8 h-6 object-contain mr-3 flex-shrink-0">
                                            <span class="text-white text-sm font-medium flex-1 text-left">{{ $handler->name }}</span>
                                            <svg x-show="selectedSystem === '{{ $handler->id }}'" 
                                                 class="w-5 h-5 text-[#ffb300]" 
                                                 fill="currentColor" 
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Список для десктопа -->
                                <div class="hidden lg:block space-y-2 max-h-64 overflow-y-auto custom-scrollbar">
                                    @foreach($matchingHandlers as $handler)
                                    <div>
                                        <input type="radio"
                                               name="system"
                                               value="{{ $handler->id }}"
                                               id="payment_handler_{{ $handler->id }}"
                                               class="hidden peer"
                                               x-model="selectedSystem">
                                        <label for="payment_handler_{{ $handler->id }}"
                                               class="flex items-center w-full p-3 bg-[#252a32] hover:bg-gray-800 border border-gray-800 peer-checked:border-[#ffb300] peer-checked:bg-[#ffb300]/10 rounded-lg cursor-pointer transition-all duration-200 group">
                                            <img src="{{ asset('storage/' . $handler->icon) }}"
                                                 alt="{{ $handler->name }}"
                                                 class="w-8 h-6 object-contain mr-3 flex-shrink-0">
                                            <span class="text-white text-sm font-medium flex-1">{{ $handler->name }}</span>
                                            <svg class="w-5 h-5 text-[#ffb300] opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </label>
                                    </div>
                                    @endforeach

                                    @foreach($otherHandlers as $handler)
                                    <div>
                                        <input type="radio"
                                               name="system"
                                               value="{{ $handler->id }}"
                                               id="payment_handler_{{ $handler->id }}"
                                               class="hidden peer"
                                               x-model="selectedSystem">
                                        <label for="payment_handler_{{ $handler->id }}"
                                               class="flex items-center w-full p-3 bg-[#252a32] hover:bg-gray-800 border border-gray-800 peer-checked:border-[#ffb300] peer-checked:bg-[#ffb300]/10 rounded-lg cursor-pointer transition-all duration-200 group">
                                            <img src="{{ asset('storage/' . $handler->icon) }}"
                                                 alt="{{ $handler->name }}"
                                                 class="w-8 h-6 object-contain mr-3 flex-shrink-0">
                                            <span class="text-white text-sm font-medium flex-1">{{ $handler->name }}</span>
                                            <svg class="w-5 h-5 text-[#ffb300] opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Amount Input -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-400 text-sm mb-3 font-medium">{{__('Сумма пополнения')}}</label>
                                    <div class="relative">
                                        <input type="number"
                                               class="w-full bg-[#252a32] border border-gray-800 focus:border-[#ffb300] focus:ring-1 focus:ring-[#ffb300] rounded-lg py-4 px-4 pr-16 text-white text-xl font-bold placeholder-gray-500 focus:outline-none transition-all duration-200 text-center"
                                               name="amount"
                                               x-model.number="amount"
                                               @focus="if(amount === 0) amount = ''"
                                               placeholder="0"
                                               required
                                               min="1">
                                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-[#ffb300] font-bold text-lg">
                                            {{$u->currency->symbol}}
                                        </div>
                                    </div>
                                    <p class="text-gray-500 text-xs mt-2">{{__('Минимум: ')}}{{ moneyFormat(toUSD(5, $u->currency->symbol))}} {{$u->currency->symbol}}</p>
                                </div>

                                <!-- Quick Amounts -->
                                <div>
                                    <label class="block text-gray-400 text-sm mb-3 font-medium">{{__('Быстрые суммы')}}</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <button type="button"
                                                @click="amount = 100"
                                                class="py-2 px-3 bg-[#252a32] hover:bg-gray-800 border border-gray-800 hover:border-[#ffb300] rounded-lg text-white text-sm font-medium transition-all duration-200">
                                            100 {{$u->currency->symbol}}
                                        </button>
                                        <button type="button"
                                                @click="amount = 500"
                                                class="py-2 px-3 bg-[#252a32] hover:bg-gray-800 border border-gray-800 hover:border-[#ffb300] rounded-lg text-white text-sm font-medium transition-all duration-200">
                                            500 {{$u->currency->symbol}}
                                        </button>
                                        <button type="button"
                                                @click="amount = 1000"
                                                class="py-2 px-3 bg-[#252a32] hover:bg-gray-800 border border-gray-800 hover:border-[#ffb300] rounded-lg text-white text-sm font-medium transition-all duration-200">
                                            1000 {{$u->currency->symbol}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full h-12 bg-[#ffb300] hover:bg-[#e6a000] text-black font-bold rounded-lg transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!selectedSystem || amount < 5 || loading">
                            <span x-show="!loading">{{__('Перейти к оплате')}}</span>
                            <span x-show="loading">{{__('Загрузка...')}}</span>
                        </button>
                    </form>
                </div>

                <!-- Withdrawal Form -->
                <div x-show="operation === 'withdrawal'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-x-4"
                     x-transition:enter-end="opacity-100 transform translate-x-0">
                    <form @submit.prevent="submitWithdrawal" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Withdrawal Methods -->
                            <div class="space-y-4">
                                <label class="block text-gray-400 text-sm mb-3 font-medium">{{__('Способ вывода')}}</label>
                                
                                <!-- Кастомный Dropdown для мобильных -->
                                <div class="lg:hidden relative" x-data="{ dropdownOpen: false }">
                                    <!-- Кнопка dropdown -->
                                    <button type="button"
                                            @click="dropdownOpen = !dropdownOpen"
                                            class="flex items-center justify-between w-full p-3 bg-[#252a32] border border-gray-800 rounded-lg cursor-pointer transition-all duration-200"
                                            :class="selectedSystem ? 'border-[#ffb300]' : ''">
                                        <div class="flex items-center gap-3 flex-1">
                                            <template x-if="selectedSystem">
                                                <img :src="getSystemIcon(selectedSystem, 'withdrawal')" 
                                                     :alt="getSystemName(selectedSystem, 'withdrawal')"
                                                     class="w-8 h-6 object-contain flex-shrink-0">
                                            </template>
                                            <span class="text-white text-sm font-medium" x-text="selectedSystem ? getSystemName(selectedSystem, 'withdrawal') : '{{__('Выберите способ вывода')}}'"></span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 flex-shrink-0" 
                                             :class="dropdownOpen ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Выпадающий список -->
                                    <div x-show="dropdownOpen"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 transform scale-95"
                                         x-transition:enter-end="opacity-100 transform scale-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 transform scale-100"
                                         x-transition:leave-end="opacity-0 transform scale-95"
                                         @click.away="dropdownOpen = false"
                                         class="absolute z-50 left-0 right-0 mt-2 bg-[#1e2329] border border-gray-800 rounded-lg shadow-2xl max-h-80 overflow-y-auto custom-scrollbar">
                                        
                                        @if($matchingSystems->isNotEmpty())
                                        <div class="p-2 border-b border-gray-800">
                                            <p class="px-2 py-1 text-xs text-gray-500 font-semibold">{{__('Рекомендуемые')}}</p>
                                        </div>
                                        @foreach($matchingSystems as $system)
                                        <button type="button"
                                                @click="selectedSystem = '{{ $system->id }}'; commission = {{ $system->commission ?? 5 }}; dropdownOpen = false"
                                                class="flex items-center w-full p-3 hover:bg-[#252a32] transition-all duration-200"
                                                :class="selectedSystem === '{{ $system->id }}' ? 'bg-[#ffb300]/10 border-l-2 border-[#ffb300]' : ''">
                                            <img src="{{ asset('storage/' . $system->icon) }}"
                                                 alt="{{ $system->name }}"
                                                 class="w-8 h-6 object-contain mr-3 flex-shrink-0">
                                            <span class="text-white text-sm font-medium flex-1 text-left">{{ $system->name }}</span>
                                            <svg x-show="selectedSystem === '{{ $system->id }}'" 
                                                 class="w-5 h-5 text-[#ffb300]" 
                                                 fill="currentColor" 
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                        @endforeach
                                        @endif

                                        @if($otherSystems->isNotEmpty())
                                        <div class="p-2 border-b border-gray-800">
                                            <p class="px-2 py-1 text-xs text-gray-500 font-semibold">{{__('Другие методы')}}</p>
                                        </div>
                                        @foreach($otherSystems as $system)
                                        <button type="button"
                                                @click="selectedSystem = '{{ $system->id }}'; commission = {{ $system->commission ?? 5 }}; dropdownOpen = false"
                                                class="flex items-center w-full p-3 hover:bg-[#252a32] transition-all duration-200"
                                                :class="selectedSystem === '{{ $system->id }}' ? 'bg-[#ffb300]/10 border-l-2 border-[#ffb300]' : ''">
                                            <img src="{{ asset('storage/' . $system->icon) }}"
                                                 alt="{{ $system->name }}"
                                                 class="w-8 h-6 object-contain mr-3 flex-shrink-0">
                                            <span class="text-white text-sm font-medium flex-1 text-left">{{ $system->name }}</span>
                                            <svg x-show="selectedSystem === '{{ $system->id }}'" 
                                                 class="w-5 h-5 text-[#ffb300]" 
                                                 fill="currentColor" 
                                                 viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Список для десктопа -->
                                <div class="hidden lg:block space-y-2 max-h-64 overflow-y-auto custom-scrollbar">
                                    @foreach($matchingSystems as $system)
                                    <div>
                                        <input type="radio"
                                               name="system"
                                               value="{{ $system->id }}"
                                               id="system_{{ $system->id }}"
                                               class="hidden peer"
                                               x-model="selectedSystem"
                                               @change="commission = {{ $system->commission ?? 5 }}">
                                        <label for="system_{{ $system->id }}"
                                               class="flex items-center w-full p-3 bg-[#252a32] hover:bg-gray-800 border border-gray-800 peer-checked:border-[#ffb300] peer-checked:bg-[#ffb300]/10 rounded-lg cursor-pointer transition-all duration-200 group">
                                            <img src="{{ asset('storage/' . $system->icon) }}"
                                                 alt="{{ $system->name }}"
                                                 class="w-8 h-6 object-contain mr-3 flex-shrink-0">
                                            <span class="text-white text-sm font-medium flex-1">{{ $system->name }}</span>
                                            <svg class="w-5 h-5 text-[#ffb300] opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </label>
                                    </div>
                                    @endforeach

                                    @foreach($otherSystems as $system)
                                    <div>
                                        <input type="radio"
                                               name="system"
                                               value="{{ $system->id }}"
                                               id="system_{{ $system->id }}"
                                               class="hidden peer"
                                               x-model="selectedSystem"
                                               @change="commission = {{ $system->commission ?? 5 }}">
                                        <label for="system_{{ $system->id }}"
                                               class="flex items-center w-full p-3 bg-[#252a32] hover:bg-gray-800 border border-gray-800 peer-checked:border-[#ffb300] peer-checked:bg-[#ffb300]/10 rounded-lg cursor-pointer transition-all duration-200 group">
                                            <img src="{{ asset('storage/' . $system->icon) }}"
                                                 alt="{{ $system->name }}"
                                                 class="w-8 h-6 object-contain mr-3 flex-shrink-0">
                                            <span class="text-white text-sm font-medium flex-1">{{ $system->name }}</span>
                                            <svg class="w-5 h-5 text-[#ffb300] opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Withdrawal Details -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-400 text-sm mb-3 font-medium">{{__('Реквизиты')}}</label>
                                    <input type="text"
                                           class="w-full bg-[#252a32] border border-gray-800 focus:border-[#ffb300] focus:ring-1 focus:ring-[#ffb300] rounded-lg py-3 px-4 text-white placeholder-gray-500 focus:outline-none transition-all duration-200"
                                           name="details"
                                           x-model="details"
                                           placeholder="{{__('Введите реквизиты')}}"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-gray-400 text-sm mb-3 font-medium">{{__('Сумма вывода')}}</label>
                                    <div class="relative">
                                        <input type="number"
                                               class="w-full bg-[#252a32] border border-gray-800 focus:border-[#ffb300] focus:ring-1 focus:ring-[#ffb300] rounded-lg py-4 px-4 pr-16 text-white text-xl font-bold placeholder-gray-500 focus:outline-none transition-all duration-200 text-center"
                                               name="amount"
                                               x-model.number="amount"
                                               @focus="if(amount === 0) amount = ''"
                                               placeholder="0"
                                               required
                                               min="1">
                                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-[#ffb300] font-bold text-lg">
                                            {{$u->currency->symbol}}
                                        </div>
                                    </div>
                                    <p class="text-gray-500 text-xs mt-2">{{__('Минимум: ')}}{{ moneyFormat(toUSD(5, $u->currency->symbol))}} {{$u->currency->symbol}}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Commission Info -->
                        <div x-show="amount > 0"
                             x-transition
                             class="bg-[#252a32] rounded-lg p-4 border border-gray-800">
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">{{__('К списанию')}}:</span>
                                    <span class="text-white font-medium" x-text="amount + ' {{$u->currency->symbol}}'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">{{__('Комиссия')}} (<span x-text="commission"></span>%):</span>
                                    <span class="text-orange-400 font-medium" x-text="commissionAmount.toFixed(2) + ' {{$u->currency->symbol}}'"></span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-800">
                                    <span class="text-white font-semibold">{{__('К получению')}}:</span>
                                    <span class="text-[#ffb300] font-bold text-lg" x-text="actualAmount.toFixed(2) + ' {{$u->currency->symbol}}'"></span>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full h-12 bg-[#ffb300] hover:bg-[#e6a000] text-black font-bold rounded-lg transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!selectedSystem || !details || amount < 5 || loading">
                            <span x-show="!loading">{{__('Подать заявку на вывод')}}</span>
                            <span x-show="loading">{{__('Обработка...')}}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
function cashModalData() {
    return {
        open: false,
        operation: 'deposit',
        amount: 0,
        commission: 5,
        selectedSystem: '',
        details: '',
        loading: false,
        errors: {},
        successMessage: '',
        errorMessage: '',
        
        // Данные платёжных систем
        paymentHandlers: {
            @foreach($matchingHandlers as $handler)
            '{{ $handler->id }}': { name: '{{ $handler->name }}', icon: '{{ asset('storage/' . $handler->icon) }}' },
            @endforeach
            @foreach($otherHandlers as $handler)
            '{{ $handler->id }}': { name: '{{ $handler->name }}', icon: '{{ asset('storage/' . $handler->icon) }}' },
            @endforeach
        },
        
        withdrawalSystems: {
            @foreach($matchingSystems as $system)
            '{{ $system->id }}': { name: '{{ $system->name }}', icon: '{{ asset('storage/' . $system->icon) }}' },
            @endforeach
            @foreach($otherSystems as $system)
            '{{ $system->id }}': { name: '{{ $system->name }}', icon: '{{ asset('storage/' . $system->icon) }}' },
            @endforeach
        },
        
        getSystemName(systemId, type) {
            if (type === 'deposit') {
                return this.paymentHandlers[systemId]?.name || '{{__('Не выбрано')}}';
            } else {
                return this.withdrawalSystems[systemId]?.name || '{{__('Не выбрано')}}';
            }
        },
        
        getSystemIcon(systemId, type) {
            if (type === 'deposit') {
                return this.paymentHandlers[systemId]?.icon || '';
            } else {
                return this.withdrawalSystems[systemId]?.icon || '';
            }
        },

        get commissionAmount() {
            return (this.amount * this.commission) / 100;
        },

        get actualAmount() {
            return this.amount > 0 ? this.amount - this.commissionAmount : 0;
        },

        async submitDeposit() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';
            this.errorMessage = '';

            try {
                const formData = new FormData();
                formData.append('amount', this.amount);
                formData.append('system', this.selectedSystem);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const response = await fetch('{{ route("cash.operation", "deposit") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (window.Noty) {
                        new Noty({
                            type: 'success',
                            text: data.message || 'Переход к оплате...',
                            theme: 'premium',
                            layout: 'topRight',
                            timeout: 2000,
                            progressBar: true
                        }).show();
                    }
                    
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else if (data.url) {
                            window.location.href = data.url;
                        }
                    }, 500);
                } else if (response.status === 422) {
                    this.errors = data.errors || {};
                    const errorMsg = data.message || 'Ошибка валидации';
                    
                    if (window.Noty) {
                        new Noty({
                            type: 'error',
                            text: errorMsg,
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 3000,
                            progressBar: true
                        }).show();
                    }
                    this.errorMessage = errorMsg;
                } else {
                    const errorMsg = data.message || 'Произошла ошибка';
                    
                    if (window.Noty) {
                        new Noty({
                            type: 'error',
                            text: errorMsg,
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 3000,
                            progressBar: true
                        }).show();
                    }
                    this.errorMessage = errorMsg;
                }
            } catch (error) {
                console.error('Deposit error:', error);
                const errorMsg = 'Ошибка соединения с сервером';
                
                if (window.Noty) {
                    new Noty({
                        type: 'error',
                        text: errorMsg,
                        theme: 'mint',
                        layout: 'topRight',
                        timeout: 3000,
                        progressBar: true
                    }).show();
                }
                this.errorMessage = errorMsg;
            } finally {
                this.loading = false;
            }
        },

        async submitWithdrawal() {
            this.loading = true;
            this.errors = {};
            this.successMessage = '';
            this.errorMessage = '';

            try {
                const formData = new FormData();
                formData.append('amount', this.amount);
                formData.append('system', this.selectedSystem);
                formData.append('details', this.details);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const response = await fetch('{{ route("cash.operation", "withdrawal") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const successMsg = data.message || 'Заявка на вывод принята!';
                    
                    if (window.Noty) {
                        new Noty({
                            type: 'success',
                            text: successMsg,
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 3000,
                            progressBar: true
                        }).show();
                    }
                    
                    if (data.balance !== undefined) {
                        const balanceElement = document.querySelector('[data-balance]');
                        if (balanceElement) {
                            balanceElement.textContent = data.balance;
                        }
                        
                        const allBalanceElements = document.querySelectorAll('.user-balance');
                        allBalanceElements.forEach(el => {
                            if (el.textContent && !isNaN(parseFloat(el.textContent))) {
                                el.textContent = data.balance;
                            }
                        });
                    }

                    this.open = false;
                    this.amount = 0;
                    this.details = '';
                    this.selectedSystem = '';
                    
                    if (data.showWithdrawalModal) {
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                } else if (response.status === 422) {
                    this.errors = data.errors || {};
                    const errorMsg = data.message || 'Ошибка валидации';
                    
                    if (window.Noty) {
                        new Noty({
                            type: 'error',
                            text: errorMsg,
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 3000,
                            progressBar: true
                        }).show();
                    }
                    this.errorMessage = errorMsg;
                } else {
                    const errorMsg = data.message || 'Произошла ошибка';
                    
                    if (window.Noty) {
                        new Noty({
                            type: 'error',
                            text: errorMsg,
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 3000,
                            progressBar: true
                        }).show();
                    }
                    this.errorMessage = errorMsg;
                }
            } catch (error) {
                console.error('Withdrawal error:', error);
                const errorMsg = 'Ошибка соединения с сервером';
                
                if (window.Noty) {
                    new Noty({
                        type: 'error',
                        text: errorMsg,
                        theme: 'mint',
                        layout: 'topRight',
                        timeout: 3000,
                        progressBar: true
                    }).show();
                }
                this.errorMessage = errorMsg;
            } finally {
                this.loading = false;
            }
        }
    };
}

function openCashModal() {
    window.dispatchEvent(new CustomEvent('open-cash-modal'));
}

function closeCashModal() {
    window.dispatchEvent(new CustomEvent('close-cash-modal'));
}
</script>
