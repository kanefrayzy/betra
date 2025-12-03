<div x-data="cashModalData()"
     @open-cash-modal.window="open = true"
     @close-cash-modal.window="open = false"
     @keydown.escape.window="closeModal()"
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
         @click="closeModal()"></div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="closeModal()"
             class="relative w-full max-w-4xl bg-[#1e2329] rounded-2xl shadow-2xl border border-gray-800">

            <!-- Header -->
            <div class="relative px-6 py-5 border-b border-gray-800">
                <button @click="closeModal()"
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
                <button @click="switchTab('deposit')"
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
                <button @click="switchTab('withdrawal')"
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
                    
                    <!-- Crypto Address Display -->
                    <div x-show="showCryptoAddress" class="space-y-6">
                        <!-- Back Button -->
                        <button @click="backToSelection()" type="button"
                                class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            <span>{{__('Назад к выбору')}}</span>
                        </button>

                        <!-- Loading -->
                        <div x-show="loadingCryptoAddress" class="flex flex-col items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#ffb300] mb-4"></div>
                            <p class="text-gray-400">{{__('Загрузка адреса...')}}</p>
                        </div>

                        <!-- Crypto Address Card -->
                        <div x-show="!loadingCryptoAddress && cryptoAddressData" class="bg-[#252a32] rounded-xl p-6 border border-gray-800">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-bold text-white">{{__('Криптовалютный платеж')}}</h3>
                                <span class="px-3 py-1 bg-[#ffb300]/20 text-[#ffb300] text-sm font-semibold rounded-lg" 
                                      x-text="cryptoAddressData?.currency + (cryptoAddressData?.network ? ' (' + cryptoAddressData.network + ')' : '')"></span>
                            </div>

                            <!-- QR Code -->
                            <div class="flex justify-center mb-6">
                                <div class="bg-white p-4 rounded-xl" x-show="cryptoAddressData?.address">
                                    <div id="qrcode"></div>
                                </div>
                            </div>

                            <!-- Minimum Amount -->
                            <div class="mb-4 p-3 bg-[#1e2329] rounded-lg border border-gray-800">
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-[#ffb300]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-400">{{__('Минимальная сумма:')}}</span>
                                    <span class="text-white font-semibold" x-text="cryptoAddressData?.min_amount || '10.00'"></span>
                                    <span class="text-gray-400" x-text="cryptoAddressData?.currency"></span>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">{{__('Ваш постоянный адрес')}}</label>
                                    <div class="flex gap-2">
                                        <input type="text" 
                                               :value="cryptoAddressData?.address" 
                                               readonly
                                               class="flex-1 px-4 py-3 bg-[#1e2329] border border-gray-800 rounded-lg text-white text-sm focus:outline-none focus:border-[#ffb300] font-mono break-all">
                                        <button @click="copyAddress()" type="button"
                                                class="px-4 py-3 bg-[#ffb300] hover:bg-[#e6a000] text-black font-semibold rounded-lg transition-colors flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Dest Tag (if required) -->
                                <div x-show="cryptoAddressData?.dest_tag">
                                    <label class="block text-gray-400 text-sm mb-2">{{__('Тег назначения (обязательно!)')}}</label>
                                    <div class="flex gap-2">
                                        <input type="text" 
                                               :value="cryptoAddressData?.dest_tag" 
                                               readonly
                                               class="flex-1 px-4 py-3 bg-[#1e2329] border border-gray-800 rounded-lg text-white text-sm focus:outline-none focus:border-[#ffb300] font-mono">
                                        <button @click="copyDestTag()" type="button"
                                                class="px-4 py-3 bg-[#ffb300] hover:bg-[#e6a000] text-black font-semibold rounded-lg transition-colors flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Network -->
                                <div x-show="cryptoAddressData?.network">
                                    <label class="block text-gray-400 text-sm mb-2">{{__('Сеть')}}</label>
                                    <div class="px-4 py-3 bg-[#1e2329] border border-gray-800 rounded-lg text-white text-sm font-semibold">
                                        <span x-text="cryptoAddressData?.network"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Warnings -->
                            <div class="mt-6 space-y-3">
                                <div class="p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-yellow-300">
                                            <p class="font-semibold mb-1">{{__('Вы переводите валюту')}} <span x-text="cryptoAddressData?.currency"></span> <span x-show="cryptoAddressData?.network">{{__('в сети')}} <span x-text="cryptoAddressData?.network"></span></span>.</p>
                                            <p class="text-xs">{{__('Использование другой сети приведёт к потере средств.')}}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-blue-300">
                                            <p class="font-semibold mb-1">{{__('Важная информация:')}}</p>
                                            <ul class="list-disc list-inside space-y-1 text-xs">
                                                <li>{{__('Отправляйте только указанную криптовалюту на этот адрес')}}</li>
                                                <li>{{__('После подтверждения в блокчейне средства автоматически зачислятся')}}</li>
                                                <li>{{__('Время зачисления: обычно 1-30 минут в зависимости от сети')}}</li>
                                                <li x-show="cryptoAddressData?.dest_tag">{{__('Обязательно укажите тег назначения при переводе!')}}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bonus Toggle -->
                            <div class="mt-6 p-4 bg-gradient-to-r from-[#ffb300]/10 to-transparent border border-[#ffb300]/20 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-white font-semibold">{{__('Бонус 50% на депозит')}}</p>
                                        <button @click="showBonusConditions = !showBonusConditions" class="text-xs text-gray-400 hover:text-white flex items-center gap-1 mt-1">
                                            {{__('Условия бонуса')}}
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="bonusEnabled" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#ffb300]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#ffb300]"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods Selection -->
                    <form @submit.prevent="submitDeposit" class="space-y-6" x-show="!showCryptoAddress">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Payment Methods -->
                            <div class="space-y-4">
                                <label class="block text-gray-400 text-sm mb-3 font-medium">{{__('Способ оплаты')}}</label>
                                
                                <!-- Bank Methods -->
                                @if($matchingHandlers->where('network', null)->isNotEmpty() || $otherHandlers->where('network', null)->isNotEmpty())
                                <div class="mb-4">
                                    <p class="text-white font-semibold text-sm mb-3">{{__('Банковский платеж')}}</p>
                                    <div class="space-y-2">
                                        @foreach($matchingHandlers->where('network', null) as $handler)
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

                                        @foreach($otherHandlers->where('network', null) as $handler)
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
                                @endif

                                <!-- Crypto Methods -->
                                @if($matchingHandlers->whereNotNull('network')->isNotEmpty() || $otherHandlers->whereNotNull('network')->isNotEmpty())
                                <div>
                                    <p class="text-white font-semibold text-sm mb-3">{{__('Криптовалюта')}}</p>
                                    <div class="grid grid-cols-2 gap-3">
                                        @foreach($matchingHandlers->whereNotNull('network') as $handler)
                                        <button type="button"
                                                @click="selectCryptoMethod({{ $handler->id }}, '{{ $handler->currency }}', '{{ $handler->network }}')"
                                                class="p-3 bg-[#252a32] hover:bg-gray-800 border border-gray-800 hover:border-[#ffb300] rounded-lg cursor-pointer transition-all duration-200 group">
                                            <div class="flex flex-col items-center gap-2">
                                                <img src="{{ asset('storage/' . $handler->icon) }}"
                                                     alt="{{ $handler->name }}"
                                                     class="w-12 h-12 object-contain">
                                                <span class="text-white text-xs font-medium text-center">{{ $handler->name }}</span>
                                                @if($handler->deposit_fee == 0)
                                                <span class="px-2 py-1 bg-green-500/20 text-green-400 text-xs font-semibold rounded">0% fee</span>
                                                @endif
                                            </div>
                                        </button>
                                        @endforeach

                                        @foreach($otherHandlers->whereNotNull('network') as $handler)
                                        <button type="button"
                                                @click="selectCryptoMethod({{ $handler->id }}, '{{ $handler->currency }}', '{{ $handler->network }}')"
                                                class="p-3 bg-[#252a32] hover:bg-gray-800 border border-gray-800 hover:border-[#ffb300] rounded-lg cursor-pointer transition-all duration-200 group">
                                            <div class="flex flex-col items-center gap-2">
                                                <img src="{{ asset('storage/' . $handler->icon) }}"
                                                     alt="{{ $handler->name }}"
                                                     class="w-12 h-12 object-contain">
                                                <span class="text-white text-xs font-medium text-center">{{ $handler->name }}</span>
                                                @if($handler->deposit_fee == 0)
                                                <span class="px-2 py-1 bg-green-500/20 text-green-400 text-xs font-semibold rounded">0% fee</span>
                                                @endif
                                            </div>
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Amount Input (only for non-crypto) -->
                            <div class="space-y-4" x-show="selectedSystem && !isCryptoHandler(selectedSystem)">
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
                                x-show="selectedSystem && !isCryptoHandler(selectedSystem)"
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
                                <div class="space-y-2 max-h-96 overflow-y-auto custom-scrollbar">
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
        selectedCrypto: '',
        selectedNetwork: '',
        details: '',
        loading: false,
        loadingCryptoAddress: false,
        showCryptoAddress: false,
        cryptoAddressData: null,
        bonusEnabled: false,
        showBonusConditions: false,
        errors: {},
        successMessage: '',
        errorMessage: '',
        
        // Данные платёжных систем
        paymentHandlers: {
            @foreach($matchingHandlers as $handler)
            '{{ $handler->id }}': { 
                name: '{{ $handler->name }}', 
                icon: '{{ asset('storage/' . $handler->icon) }}', 
                currency: '{{ $handler->currency }}',
                network: '{{ $handler->network }}'
            },
            @endforeach
            @foreach($otherHandlers as $handler)
            '{{ $handler->id }}': { 
                name: '{{ $handler->name }}', 
                icon: '{{ asset('storage/' . $handler->icon) }}', 
                currency: '{{ $handler->currency }}',
                network: '{{ $handler->network }}'
            },
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

        closeModal() {
            this.open = false;
            // Reset state
            setTimeout(() => {
                this.showCryptoAddress = false;
                this.selectedSystem = '';
                this.selectedCrypto = '';
                this.selectedNetwork = '';
                this.amount = 0;
                this.details = '';
                this.cryptoAddressData = null;
            }, 300);
        },

        switchTab(tab) {
            this.operation = tab;
            this.selectedSystem = '';
            this.amount = 0;
            this.details = '';
            this.showCryptoAddress = false;
        },

        isCryptoHandler(handlerId) {
            const handler = this.paymentHandlers[handlerId];
            return handler && handler.network !== null && handler.network !== '';
        },

        async selectCryptoMethod(handlerId, currency, network) {
            this.selectedSystem = handlerId;
            this.selectedCrypto = currency;
            this.selectedNetwork = network;
            this.showCryptoAddress = true;
            await this.loadCryptoAddress(currency, network);
        },

        async loadCryptoAddress(currency, network) {
            this.loadingCryptoAddress = true;
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route("crypto.get-address") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ 
                        currency: currency,
                        network: network 
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.cryptoAddressData = data.data;
                    
                    // Генерируем QR код
                    this.$nextTick(() => {
                        const qrElement = document.getElementById('qrcode');
                        if (qrElement && window.QRCode) {
                            // Очищаем предыдущий QR код
                            qrElement.innerHTML = '';
                            
                            new QRCode(qrElement, {
                                text: data.data.qr_data || data.data.address,
                                width: 200,
                                height: 200,
                                colorDark: '#000000',
                                colorLight: '#ffffff',
                                correctLevel: QRCode.CorrectLevel.H
                            });
                        }
                    });
                } else {
                    this.errorMessage = data.message || '{{__('Ошибка при получении адреса')}}';
                    this.showCryptoAddress = false;
                    
                    if (window.Noty) {
                        new Noty({
                            type: 'error',
                            text: this.errorMessage,
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 3000,
                            progressBar: true
                        }).show();
                    }
                }
            } catch (error) {
                console.error('Load crypto address error:', error);
                this.errorMessage = '{{__('Ошибка соединения с сервером')}}';
                this.showCryptoAddress = false;
                
                if (window.Noty) {
                    new Noty({
                        type: 'error',
                        text: this.errorMessage,
                        theme: 'mint',
                        layout: 'topRight',
                        timeout: 3000,
                        progressBar: true
                    }).show();
                }
            } finally {
                this.loadingCryptoAddress = false;
            }
        },

        copyAddress() {
            if (!this.cryptoAddressData?.address) return;
            
            navigator.clipboard.writeText(this.cryptoAddressData.address).then(() => {
                if (window.Noty) {
                    new Noty({
                        type: 'success',
                        text: '{{__('Адрес скопирован!')}}',
                        theme: 'mint',
                        layout: 'topRight',
                        timeout: 2000,
                        progressBar: true
                    }).show();
                }
            });
        },

        copyDestTag() {
            if (!this.cryptoAddressData?.dest_tag) return;
            
            navigator.clipboard.writeText(this.cryptoAddressData.dest_tag).then(() => {
                if (window.Noty) {
                    new Noty({
                        type: 'success',
                        text: '{{__('Тег скопирован!')}}',
                        theme: 'mint',
                        layout: 'topRight',
                        timeout: 2000,
                        progressBar: true
                    }).show();
                }
            });
        },

        backToSelection() {
            this.showCryptoAddress = false;
            this.selectedSystem = '';
            this.selectedCrypto = '';
            this.selectedNetwork = '';
            this.cryptoAddressData = null;
        },

        get commissionAmount() {
            return (this.amount * this.commission) / 100;
        },

        get actualAmount() {
            return this.amount > 0 ? this.amount - this.commissionAmount : 0;
        },

        async submitDeposit() {
            // Для крипто не отправляем форму
            if (this.isCryptoHandler(this.selectedSystem)) {
                return;
            }

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

                    this.closeModal();
                    
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

function openCryptoGuide() {
    // Логика открытия гайда по крипто
    console.log('Open crypto guide');
}

function openCashModal() {
    window.dispatchEvent(new CustomEvent('open-cash-modal'));
}

function closeCashModal() {
    window.dispatchEvent(new CustomEvent('close-cash-modal'));
}
</script>

<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>