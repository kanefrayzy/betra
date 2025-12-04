<div x-data="cashModalData()"
     @open-cash-modal.window="open = true"
     @close-cash-modal.window="closeModal()"
     @keydown.escape.window="closeModal()"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center"
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
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative z-10 w-full max-w-[600px] overflow-hidden max-h-[720px] flex flex-col h-[calc(100dvh_-_32px)] bg-[#1e2329] rounded-2xl m-4"
         @click.stop>

        <!-- Header -->
        <div class="relative p-4 desktop:p-6">
            <h2 class="text-[22px] font-semibold leading-7 tracking-[-0.64px] desktop:text-[28px] desktop:leading-8 desktop:tracking-[-0.8px] text-white">
                {{__('Кошелек')}}
            </h2>
            <button @click="closeModal()"
                    class="absolute top-4 right-4 desktop:top-6 desktop:right-6 p-1 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-5 h-5">
                    <path fill="currentColor" d="M17.38 5.38a.876.876 0 0 1 1.24 1.24L13.237 12l5.381 5.38.06.067a.876.876 0 0 1-1.232 1.232l-.066-.06L12 13.24l-5.38 5.38a.876.876 0 0 1-1.24-1.238L10.763 12 5.38 6.62l-.06-.067A.876.876 0 0 1 6.553 5.32l.066.06L12 10.76z"></path>
                </svg>
            </button>
        </div>

        <!-- Content Container -->
        <div class="relative grow px-4 desktop:px-6 min-w-0 max-w-full overflow-y-auto min-h-0 flex flex-col">
            
            <!-- Tabs -->
            <div class="flex w-full min-h-[48px] rounded-xl bg-[#2c3340] p-0.5 mb-4">
                <button @click="switchTab('deposit')"
                        type="button"
                        class="grow h-11 rounded-lg px-4 transition-all duration-300"
                        :class="operation === 'deposit' ? 'bg-[#374151] text-white' : 'text-gray-400 hover:text-white'">
                    <p class="text-sm font-semibold tracking-[-0.12px] leading-5">{{__('Депозит')}}</p>
                </button>
                <button @click="switchTab('withdrawal')"
                        type="button"
                        class="grow h-11 rounded-lg px-4 transition-all duration-300"
                        :class="operation === 'withdrawal' ? 'bg-[#374151] text-white' : 'text-gray-400 hover:text-white'">
                    <p class="text-sm font-semibold tracking-[-0.12px] leading-5">{{__('Вывод')}}</p>
                </button>
            </div>

            <!-- Deposit Tab -->
            <div x-show="operation === 'deposit'" class="grow pb-4">
                
                <!-- Crypto Address View -->
                <div x-show="showCryptoAddress">
                    <!-- Back Button -->
                    <button @click="backToSelection()" type="button"
                            class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="text-sm">{{__('Назад')}}</span>
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
                                <span class="text-gray-400">{{__('Минимальная сумма:')}}</span>
                                <span class="text-white font-semibold" x-text="(cryptoAddressData?.min_amount || '10.00') + ' ' + cryptoAddressData?.currency"></span>
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

                            <!-- Dest Tag -->
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
                                        <ul class="list-disc list-inside space-y-1 text-xs">
                                            <li>{{__('Отправляйте только указанную криптовалюту на этот адрес')}}</li>
                                            <li>{{__('После подтверждения в блокчейне средства автоматически зачислятся')}}</li>
                                            <li>{{__('Время зачисления: обычно 1-30 минут')}}</li>
                                            <li x-show="cryptoAddressData?.dest_tag">{{__('Обязательно укажите тег назначения!')}}</li>
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
                                    <p class="text-xs text-gray-400 mt-1">{{__('Условия бонуса')}}</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="bonusEnabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#ffb300]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#ffb300]"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Payment Form -->
                <div x-show="showBankPayment">
                    <!-- Back Button -->
                    <button @click="backToSelection()" type="button"
                            class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="text-sm">{{__('Назад')}}</span>
                    </button>

                    <h3 class="text-lg font-bold text-white mb-4">{{__('Банковский платеж')}}</h3>

                    <!-- Selected Method -->
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-2">{{__('Способ оплаты')}}</label>
                        <div class="flex items-center w-full p-3 bg-[#252a32] border border-gray-800 rounded-xl">
                            <img :src="selectedHandler?.icon"
                                 :alt="selectedHandler?.name"
                                 class="w-8 h-6 object-contain mr-3 flex-shrink-0">
                            <span class="text-white text-sm font-medium flex-1" x-text="selectedHandler?.name"></span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Amount Input -->
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-2">{{__('Сумма пополнения')}}</label>
                        <div class="relative">
                            <input type="number"
                                   class="w-full bg-[#252a32] border border-gray-800 focus:border-[#ffb300] focus:ring-1 focus:ring-[#ffb300] rounded-xl py-4 px-4 pr-16 text-white text-2xl font-bold placeholder-gray-500 focus:outline-none transition-all duration-200 text-center"
                                   x-model.number="amount"
                                   @focus="if(amount === 0) amount = ''"
                                   placeholder="0"
                                   required
                                   min="1">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-[#ffb300] font-bold text-xl">
                                {{$u->currency->symbol}}
                            </div>
                        </div>
                        <p class="text-gray-500 text-xs mt-2">{{__('Мин. сумма ')}}{{ moneyFormat(toUSD(5, $u->currency->symbol))}} {{$u->currency->symbol}}</p>
                    </div>

                    <!-- Quick Amounts -->
                    <div class="mb-6">
                        <div class="flex flex-wrap gap-2">
                            @php
                                $quickAmounts = [
                                    toUSD(100, $u->currency->symbol),
                                    toUSD(200, $u->currency->symbol),
                                    toUSD(500, $u->currency->symbol),
                                    toUSD(1000, $u->currency->symbol),
                                    toUSD(2000, $u->currency->symbol),
                                    toUSD(5000, $u->currency->symbol),
                                    toUSD(10000, $u->currency->symbol),
                                    toUSD(20000, $u->currency->symbol)
                                ];
                            @endphp
                            @foreach($quickAmounts as $quickAmount)
                            <button type="button"
                                    @click="amount = {{ $quickAmount }}"
                                    class="py-2 px-4 rounded-lg text-sm font-semibold transition-all duration-200"
                                    :class="amount === {{ $quickAmount }} ? 'bg-[#ffb300] text-black' : 'bg-[#252a32] text-white hover:bg-gray-800 border border-gray-800'">
                                {{ moneyFormat($quickAmount) }} {{$u->currency->symbol}}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="flex items-center justify-between mb-4 p-4 bg-[#252a32] rounded-xl border border-gray-800">
                        <span class="text-gray-400">{{__('К пополнению')}}</span>
                        <span class="text-white font-bold text-xl" x-text="amount + ' {{$u->currency->symbol}}'"></span>
                    </div>

                    <!-- Submit Button -->
                    <button @click="submitDeposit()"
                            type="button"
                            class="w-full h-14 bg-[#ffb300] hover:bg-[#e6a000] text-black font-bold rounded-xl transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed text-lg"
                            :disabled="amount < 5 || loading">
                        <span x-show="!loading">{{__('Продолжить')}}</span>
                        <span x-show="loading">{{__('Загрузка...')}}</span>
                    </button>
                </div>

                <!-- Payment Methods List -->
                <div x-show="!showCryptoAddress && !showBankPayment">
                    <!-- Bank Payments -->
                    @if($matchingHandlers->where('network', null)->isNotEmpty() || $otherHandlers->where('network', null)->isNotEmpty())
                    <p class="text-base font-semibold tracking-[-0.12px] leading-6 mb-3 mt-4 text-white">{{__('Банковский платеж')}}</p>
                    <div class="flex flex-wrap gap-3">
                        @foreach($matchingHandlers->where('network', null) as $handler)
                        <button type="button"
                                @click="selectBankMethod({{ $handler->id }})"
                                class="w-full h-[100px] relative overflow-hidden p-3 rounded-xl bg-[#252a32] flex flex-col items-start bg-right bg-contain bg-no-repeat hover:bg-gray-800 transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ffb300] active:opacity-80 tablet:basis-[calc(100%_/_3_-_8px)] basis-[calc(50%_-_6px)]"
                                style="background-image: url('{{ asset('storage/' . $handler->icon) }}');">
                            <p class="text-sm font-semibold tracking-[-0.12px] leading-5 line-clamp-2 max-w-[96px] text-left text-white">{{ $handler->name }}</p>
                            <p class="text-xs tracking-[-0.04px] leading-4 mt-auto flex max-w-[calc(100%-50px)] flex-wrap text-left text-gray-400">{{__('от')}} {{ moneyFormat($handler->min_deposit_limit ?? 100) }} {{$u->currency->symbol}}</p>
                        </button>
                        @endforeach
                        
                        @foreach($otherHandlers->where('network', null) as $handler)
                        <button type="button"
                                @click="selectBankMethod({{ $handler->id }})"
                                class="w-full h-[100px] relative overflow-hidden p-3 rounded-xl bg-[#252a32] flex flex-col items-start bg-right bg-contain bg-no-repeat hover:bg-gray-800 transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ffb300] active:opacity-80 tablet:basis-[calc(100%_/_3_-_8px)] basis-[calc(50%_-_6px)]"
                                style="background-image: url('{{ asset('storage/' . $handler->icon) }}');">
                            <p class="text-sm font-semibold tracking-[-0.12px] leading-5 line-clamp-2 max-w-[96px] text-left text-white">{{ $handler->name }}</p>
                            <p class="text-xs tracking-[-0.04px] leading-4 mt-auto flex max-w-[calc(100%-50px)] flex-wrap text-left text-gray-400">{{__('от')}} {{ moneyFormat($handler->min_deposit_limit ?? 100) }} {{$u->currency->symbol}}</p>
                        </button>
                        @endforeach
                    </div>
                    @endif

                    <!-- Crypto -->
                    @if($matchingHandlers->whereNotNull('network')->isNotEmpty() || $otherHandlers->whereNotNull('network')->isNotEmpty())
                    <p class="text-base font-semibold tracking-[-0.12px] leading-6 mb-3 mt-4 text-white">{{__('Криптовалюта')}}</p>
                    <div class="flex flex-wrap gap-3">
                        @foreach($matchingHandlers->whereNotNull('network') as $handler)
                        <button type="button"
                                @click="selectCryptoMethod({{ $handler->id }}, '{{ $handler->currency }}', '{{ $handler->network }}')"
                                class="w-full h-[100px] relative overflow-hidden p-3 rounded-xl bg-[#252a32] flex flex-col items-start bg-right bg-contain bg-no-repeat hover:bg-gray-800 transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ffb300] active:opacity-80 tablet:basis-[calc(100%_/_3_-_8px)] basis-[calc(50%_-_6px)]"
                                style="background-image: url('{{ asset('storage/' . $handler->icon) }}');">
                            <p class="text-sm font-semibold tracking-[-0.12px] leading-5 line-clamp-2 max-w-[96px] text-left text-white">{{ $handler->name }}</p>
                            @if($handler->deposit_fee == 0)
                            <div class="flex items-center rounded-full border-0 bg-white text-black py-1 px-1 mt-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-4 h-4 mr-1 text-blue-600">
                                    <path fill="currentColor" d="M15.818 2.125c1.727 0 3.126 1.4 3.126 3.124 0 .505-.122.981-.335 1.403h.047l.282.015a2.753 2.753 0 0 1 2.471 2.738v.817a2.64 2.64 0 0 1-1.544 2.402v5.903a3.345 3.345 0 0 1-3.343 3.346h-4.718l-.036.002-.037-.002H7.016a3.346 3.346 0 0 1-3.346-3.346v-5.941a2.75 2.75 0 0 1-1.545-2.47v-.818a2.646 2.646 0 0 1 2.646-2.646h.157a3.125 3.125 0 0 1 2.79-4.527c2.061 0 3.303 1.485 3.972 2.703l.079.148q.037-.075.078-.148c.67-1.218 1.91-2.703 3.971-2.703m-3.175 17.998h3.878c.88 0 1.594-.715 1.594-1.596v-5.659h-5.472zM5.42 18.527c0 .881.715 1.596 1.596 1.596h3.877v-7.255H5.42zM4.77 8.402a.896.896 0 0 0-.896.896v.818c0 .553.449 1.002 1.003 1.002h13.884a.9.9 0 0 0 .897-.896v-.817c0-.519-.394-.946-.9-.998l-.103-.005zm2.947-4.527a1.375 1.375 0 0 0 0 2.75h2.872a7.4 7.4 0 0 0-.433-.953C9.59 4.64 8.806 3.875 7.717 3.875m8.1 0c-1.088 0-1.87.765-2.438 1.797-.18.326-.32.656-.433.953h2.871a1.376 1.376 0 1 0 0-2.75"></path>
                                </svg>
                                <p class="text-xs font-semibold tracking-[-0.04px] leading-4">+5%</p>
                            </div>
                            @endif
                        </button>
                        @endforeach
                        
                        @foreach($otherHandlers->whereNotNull('network') as $handler)
                        <button type="button"
                                @click="selectCryptoMethod({{ $handler->id }}, '{{ $handler->currency }}', '{{ $handler->network }}')"
                                class="w-full h-[100px] relative overflow-hidden p-3 rounded-xl bg-[#252a32] flex flex-col items-start bg-right bg-contain bg-no-repeat hover:bg-gray-800 transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ffb300] active:opacity-80 tablet:basis-[calc(100%_/_3_-_8px)] basis-[calc(50%_-_6px)]"
                                style="background-image: url('{{ asset('storage/' . $handler->icon) }}');">
                            <p class="text-sm font-semibold tracking-[-0.12px] leading-5 line-clamp-2 max-w-[96px] text-left text-white">{{ $handler->name }}</p>
                            @if($handler->deposit_fee == 0)
                            <div class="flex items-center rounded-full border-0 bg-white text-black py-1 px-1 mt-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-4 h-4 mr-1 text-blue-600">
                                    <path fill="currentColor" d="M15.818 2.125c1.727 0 3.126 1.4 3.126 3.124 0 .505-.122.981-.335 1.403h.047l.282.015a2.753 2.753 0 0 1 2.471 2.738v.817a2.64 2.64 0 0 1-1.544 2.402v5.903a3.345 3.345 0 0 1-3.343 3.346h-4.718l-.036.002-.037-.002H7.016a3.346 3.346 0 0 1-3.346-3.346v-5.941a2.75 2.75 0 0 1-1.545-2.47v-.818a2.646 2.646 0 0 1 2.646-2.646h.157a3.125 3.125 0 0 1 2.79-4.527c2.061 0 3.303 1.485 3.972 2.703l.079.148q.037-.075.078-.148c.67-1.218 1.91-2.703 3.971-2.703m-3.175 17.998h3.878c.88 0 1.594-.715 1.594-1.596v-5.659h-5.472zM5.42 18.527c0 .881.715 1.596 1.596 1.596h3.877v-7.255H5.42zM4.77 8.402a.896.896 0 0 0-.896.896v.818c0 .553.449 1.002 1.003 1.002h13.884a.9.9 0 0 0 .897-.896v-.817c0-.519-.394-.946-.9-.998l-.103-.005zm2.947-4.527a1.375 1.375 0 0 0 0 2.75h2.872a7.4 7.4 0 0 0-.433-.953C9.59 4.64 8.806 3.875 7.717 3.875m8.1 0c-1.088 0-1.87.765-2.438 1.797-.18.326-.32.656-.433.953h2.871a1.376 1.376 0 1 0 0-2.75"></path>
                                </svg>
                                <p class="text-xs font-semibold tracking-[-0.04px] leading-4">+5%</p>
                            </div>
                            @endif
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Withdrawal Tab -->
            <div x-show="operation === 'withdrawal'" class="grow pb-4">
                
                <!-- Withdrawal Form -->
                <div x-show="showWithdrawalForm">
                    <!-- Back Button -->
                    <button @click="backToSelection()" type="button"
                            class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="text-sm">{{__('Назад')}}</span>
                    </button>

                    <h3 class="text-lg font-bold text-white mb-4">{{__('Вывод средств')}}</h3>

                    <!-- Selected Method -->
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-2">{{__('Способ вывода')}}</label>
                        <div class="flex items-center w-full p-3 bg-[#252a32] border border-gray-800 rounded-xl">
                            <img :src="selectedWithdrawalSystem?.icon"
                                 :alt="selectedWithdrawalSystem?.name"
                                 class="w-8 h-6 object-contain mr-3 flex-shrink-0">
                            <span class="text-white text-sm font-medium flex-1" x-text="selectedWithdrawalSystem?.name"></span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Details Input -->
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-2">{{__('Реквизиты')}}</label>
                        <input type="text"
                               class="w-full bg-[#252a32] border border-gray-800 focus:border-[#ffb300] focus:ring-1 focus:ring-[#ffb300] rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none transition-all duration-200"
                               x-model="details"
                               placeholder="{{__('Введите номер карты или кошелька')}}"
                               required>
                    </div>

                    <!-- Amount Input -->
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-2">{{__('Сумма вывода')}}</label>
                        <div class="relative">
                            <input type="number"
                                   class="w-full bg-[#252a32] border border-gray-800 focus:border-[#ffb300] focus:ring-1 focus:ring-[#ffb300] rounded-xl py-4 px-4 pr-16 text-white text-2xl font-bold placeholder-gray-500 focus:outline-none transition-all duration-200 text-center"
                                   x-model.number="amount"
                                   @focus="if(amount === 0) amount = ''"
                                   placeholder="0"
                                   required
                                   min="1">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-[#ffb300] font-bold text-xl">
                                {{$u->currency->symbol}}
                            </div>
                        </div>
                        <p class="text-gray-500 text-xs mt-2">{{__('Мин. сумма ')}}{{ moneyFormat(toUSD(5, $u->currency->symbol))}} {{$u->currency->symbol}}</p>
                    </div>

                    <!-- Quick Amounts -->
                    <div class="mb-6">
                        <div class="flex flex-wrap gap-2">
                            @php
                                $quickAmounts = [
                                    toUSD(100, $u->currency->symbol),
                                    toUSD(200, $u->currency->symbol),
                                    toUSD(500, $u->currency->symbol),
                                    toUSD(1000, $u->currency->symbol),
                                    toUSD(2000, $u->currency->symbol),
                                    toUSD(5000, $u->currency->symbol),
                                    toUSD(10000, $u->currency->symbol),
                                    toUSD(20000, $u->currency->symbol)
                                ];
                            @endphp
                            @foreach($quickAmounts as $quickAmount)
                            <button type="button"
                                    @click="amount = {{ $quickAmount }}"
                                    class="py-2 px-4 rounded-lg text-sm font-semibold transition-all duration-200"
                                    :class="amount === {{ $quickAmount }} ? 'bg-[#ffb300] text-black' : 'bg-[#252a32] text-white hover:bg-gray-800 border border-gray-800'">
                                {{ moneyFormat($quickAmount) }} {{$u->currency->symbol}}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Commission Info -->
                    <div x-show="amount > 0" class="mb-4">
                        <div class="bg-[#252a32] rounded-xl p-4 border border-gray-800">
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
                                    <span class="text-[#ffb300] font-bold text-xl" x-text="actualAmount.toFixed(2) + ' {{$u->currency->symbol}}'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button @click="submitWithdrawal()"
                            type="button"
                            class="w-full h-14 bg-[#ffb300] hover:bg-[#e6a000] text-black font-bold rounded-xl transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed text-lg"
                            :disabled="!details || amount < 5 || loading">
                        <span x-show="!loading">{{__('Подать заявку')}}</span>
                        <span x-show="loading">{{__('Обработка...')}}</span>
                    </button>
                </div>

                <!-- Withdrawal Methods List -->
                <div x-show="!showWithdrawalForm">
                    <!-- Methods Cards -->
                    <p class="text-base font-semibold tracking-[-0.12px] leading-6 mb-3 mt-4 text-white">{{__('Способы вывода')}}</p>
                    <div class="flex flex-wrap gap-3">
                        @foreach($matchingSystems as $system)
                        <button type="button"
                                @click="selectWithdrawalMethod({{ $system->id }})"
                                class="w-full h-[100px] relative overflow-hidden p-3 rounded-xl bg-[#252a32] flex flex-col items-start bg-right bg-contain bg-no-repeat hover:bg-gray-800 transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ffb300] active:opacity-80 tablet:basis-[calc(100%_/_3_-_8px)] basis-[calc(50%_-_6px)]"
                                style="background-image: url('{{ asset('storage/' . $system->icon) }}');">
                            <p class="text-sm font-semibold tracking-[-0.12px] leading-5 line-clamp-2 max-w-[96px] text-left text-white">{{ $system->name }}</p>
                            <p class="text-xs tracking-[-0.04px] leading-4 mt-auto flex max-w-[calc(100%-50px)] flex-wrap text-left text-gray-400">{{__('комиссия')}} {{ $system->commission ?? 5 }}%</p>
                        </button>
                        @endforeach
                        
                        @foreach($otherSystems as $system)
                        <button type="button"
                                @click="selectWithdrawalMethod({{ $system->id }})"
                                class="w-full h-[100px] relative overflow-hidden p-3 rounded-xl bg-[#252a32] flex flex-col items-start bg-right bg-contain bg-no-repeat hover:bg-gray-800 transition-all focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#ffb300] active:opacity-80 tablet:basis-[calc(100%_/_3_-_8px)] basis-[calc(50%_-_6px)]"
                                style="background-image: url('{{ asset('storage/' . $system->icon) }}');">
                            <p class="text-sm font-semibold tracking-[-0.12px] leading-5 line-clamp-2 max-w-[96px] text-left text-white">{{ $system->name }}</p>
                            <p class="text-xs tracking-[-0.04px] leading-4 mt-auto flex max-w-[calc(100%-50px)] flex-wrap text-left text-gray-400">{{__('комиссия')}} {{ $system->commission ?? 5 }}%</p>
                        </button>
                        @endforeach
                    </div>
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
        showBankPayment: false,
        showWithdrawalForm: false,
        selectedHandler: null,
        selectedWithdrawalSystem: null,
        cryptoAddressData: null,
        bonusEnabled: false,
        
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
            '{{ $system->id }}': { 
                name: '{{ $system->name }}', 
                icon: '{{ asset('storage/' . $system->icon) }}', 
                commission: {{ $system->commission ?? 5 }} 
            },
            @endforeach
            @foreach($otherSystems as $system)
            '{{ $system->id }}': { 
                name: '{{ $system->name }}', 
                icon: '{{ asset('storage/' . $system->icon) }}', 
                commission: {{ $system->commission ?? 5 }} 
            },
            @endforeach
        },

        closeModal() {
            this.open = false;
            setTimeout(() => {
                this.showCryptoAddress = false;
                this.showBankPayment = false;
                this.showWithdrawalForm = false;
                this.selectedSystem = '';
                this.selectedCrypto = '';
                this.selectedNetwork = '';
                this.selectedHandler = null;
                this.selectedWithdrawalSystem = null;
                this.amount = 0;
                this.details = '';
                this.cryptoAddressData = null;
            }, 300);
        },

        switchTab(tab) {
            this.operation = tab;
            this.selectedSystem = '';
            this.selectedHandler = null;
            this.selectedWithdrawalSystem = null;
            this.amount = 0;
            this.details = '';
            this.showCryptoAddress = false;
            this.showBankPayment = false;
            this.showWithdrawalForm = false;
        },

        selectBankMethod(handlerId) {
            this.selectedSystem = handlerId;
            this.selectedHandler = this.paymentHandlers[handlerId];
            this.showBankPayment = true;
            this.amount = 0;
        },

        selectWithdrawalMethod(systemId) {
            this.selectedSystem = systemId;
            this.selectedWithdrawalSystem = this.withdrawalSystems[systemId];
            this.commission = this.withdrawalSystems[systemId]?.commission || 5;
            this.showWithdrawalForm = true;
            this.amount = 0;
            this.details = '';
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
                    
                    this.$nextTick(() => {
                        const qrElement = document.getElementById('qrcode');
                        if (qrElement && window.QRCode) {
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
                    if (window.Noty) {
                        new Noty({
                            type: 'error',
                            text: data.message || '{{__('Ошибка при получении адреса')}}',
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 3000
                        }).show();
                    }
                    this.showCryptoAddress = false;
                }
            } catch (error) {
                console.error('Load crypto address error:', error);
                if (window.Noty) {
                    new Noty({
                        type: 'error',
                        text: '{{__('Ошибка соединения с сервером')}}',
                        theme: 'mint',
                        layout: 'topRight',
                        timeout: 3000
                    }).show();
                }
                this.showCryptoAddress = false;
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
                        timeout: 2000
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
                        timeout: 2000
                    }).show();
                }
            });
        },

        backToSelection() {
            this.showCryptoAddress = false;
            this.showBankPayment = false;
            this.showWithdrawalForm = false;
            this.selectedSystem = '';
            this.selectedCrypto = '';
            this.selectedNetwork = '';
            this.selectedHandler = null;
            this.selectedWithdrawalSystem = null;
            this.cryptoAddressData = null;
        },

        get commissionAmount() {
            return (this.amount * this.commission) / 100;
        },

        get actualAmount() {
            return this.amount > 0 ? this.amount - this.commissionAmount : 0;
        },

        async submitDeposit() {
            this.loading = true;

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
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 2000
                        }).show();
                    }
                    
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else if (data.url) {
                            window.location.href = data.url;
                        }
                    }, 500);
                } else {
                    if (window.Noty) {
                        new Noty({
                            type: 'error',
                            text: data.message || 'Произошла ошибка',
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 3000
                        }).show();
                    }
                }
            } catch (error) {
                console.error('Deposit error:', error);
                if (window.Noty) {
                    new Noty({
                        type: 'error',
                        text: 'Ошибка соединения с сервером',
                        theme: 'mint',
                        layout: 'topRight',
                        timeout: 3000
                    }).show();
                }
            } finally {
                this.loading = false;
            }
        },

        async submitWithdrawal() {
            this.loading = true;

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
                    if (window.Noty) {
                        new Noty({
                            type: 'success',
                            text: data.message || 'Заявка на вывод принята!',
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 3000
                        }).show();
                    }
                    
                    if (data.balance !== undefined) {
                        const balanceElement = document.querySelector('[data-balance]');
                        if (balanceElement) {
                            balanceElement.textContent = data.balance;
                        }
                    }

                    this.closeModal();
                } else {
                    if (window.Noty) {
                        new Noty({
                            type: 'error',
                            text: data.message || 'Произошла ошибка',
                            theme: 'mint',
                            layout: 'topRight',
                            timeout: 3000
                        }).show();
                    }
                }
            } catch (error) {
                console.error('Withdrawal error:', error);
                if (window.Noty) {
                    new Noty({
                        type: 'error',
                        text: 'Ошибка соединения с сервером',
                        theme: 'mint',
                        layout: 'topRight',
                        timeout: 3000
                    }).show();
                }
            } finally {
                this.loading = false;
            }
        }
    };
}


// Глобальные функции для открытия/закрытия модалки
window.openCashModal = function() {
    window.dispatchEvent(new CustomEvent('open-cash-modal'));
};

window.closeCashModal = function() {
    window.dispatchEvent(new CustomEvent('close-cash-modal'));
};
</script>
