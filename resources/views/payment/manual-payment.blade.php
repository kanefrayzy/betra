<x-layouts.app>
    <div class="min-h-screen py-4 sm:py-8">
        <div class="max-w-md lg:max-w-4xl mx-auto px-4">

            @if(!$manualPayment->receipt_path)
                <!-- Header -->
                <div class="flex items-center justify-between mb-6 lg:mb-8">
                    <button onclick="history.back()" class="text-gray-400 hover:text-white lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <h1 class="text-lg lg:text-3xl font-semibold lg:font-bold text-white lg:mx-auto">{{ __('Пополнение баланса') }}</h1>
                    <div class="w-6 lg:hidden"></div>
                </div>

                <!-- Desktop Layout -->
                <div class="lg:grid lg:grid-cols-3 lg:gap-8 lg:items-start">

                    <!-- Left: Amount & Progress -->
                    <div class="lg:space-y-6">
                        <!-- Amount Card -->
                        <div class="bg-gray-800 rounded-2xl p-6 mb-6 lg:mb-0 text-center lg:text-left">
                            <div class="text-gray-400 text-sm mb-2">{{ __('Сумма к оплате') }}</div>
                            <div class="text-3xl lg:text-4xl font-bold text-white">{{ number_format($transaction->amount, 2) }}</div>
                            <div class="text-orange-400 font-medium text-lg">{{ $transaction->currency->symbol }}</div>
                        </div>

                        <!-- Step Indicator -->
                        <div class="hidden lg:block bg-gray-800 rounded-2xl p-6">
                            <h3 class="text-white font-medium mb-4">{{ __('Прогресс') }}</h3>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-white text-sm font-bold">1</span>
                                    </div>
                                    <div>
                                        <div class="text-white font-medium">{{ __('Реквизиты') }}</div>
                                        <div class="text-gray-400 text-sm">{{ __('Скопируйте данные для перевода') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center opacity-50" id="step2">
                                    <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-gray-400 text-sm font-bold">2</span>
                                    </div>
                                    <div>
                                        <div class="text-gray-400 font-medium">{{ __('Подтверждение') }}</div>
                                        <div class="text-gray-500 text-sm">{{ __('Загрузите чек об оплате') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Step Indicator -->
                        <div class="flex items-center justify-center mb-8 lg:hidden">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-bold">1</span>
                                </div>
                                <div class="w-8 h-0.5 bg-gray-600"></div>
                                <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center">
                                    <span class="text-gray-400 text-sm font-bold">2</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Center: Payment Details -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Payment Details -->
                        <div class="bg-gray-800 rounded-2xl p-5 lg:p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-orange-500/20 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-white font-medium lg:text-lg">{{ __('Реквизиты получателя') }}</div>
                                    <div class="text-gray-400 text-sm">{{ $manualPayment->paymentHandler->name }}</div>
                                </div>
                            </div>

                            <div class="bg-gray-900 rounded-xl p-4 mb-4">
                                <pre class="text-gray-200 text-sm whitespace-pre-wrap font-mono">{{ $manualPayment->paymentHandler->manual_requisites }}</pre>
                            </div>

                            <button onclick="copyRequisites()"
                                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 rounded-xl transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                {{ __('Скопировать данные') }}
                            </button>
                        </div>

                        <!-- Important Notice -->
                        <div class="bg-amber-500/10 border-l-4 border-amber-500 rounded-r-xl p-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-amber-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"></path>
                                </svg>
                                <div>
                                    <div class="text-amber-200 font-medium text-sm">{{ __('Важно!') }}</div>
                                    <div class="text-amber-100 text-sm mt-1">
                                        {{ __('Переведите точно') }} <span class="font-bold text-white">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency->symbol }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions for Desktop -->
                        <div class="hidden lg:block bg-gray-800 rounded-2xl p-6">
                            <h3 class="text-white font-medium mb-4">{{ __('Инструкция') }}</h3>
                            <div class="space-y-3 text-sm text-gray-300">
                                <div class="flex items-start">
                                    <div class="w-6 h-6 bg-orange-500/20 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                        <span class="text-orange-400 text-xs font-bold">1</span>
                                    </div>
                                    <div>{{ __('Скопируйте реквизиты выше') }}</div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-6 h-6 bg-orange-500/20 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                        <span class="text-orange-400 text-xs font-bold">2</span>
                                    </div>
                                    <div>{{ __('Переведите точную сумму через ваш банк') }}</div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-6 h-6 bg-orange-500/20 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                        <span class="text-orange-400 text-xs font-bold">3</span>
                                    </div>
                                    <div>{{ __('Сделайте скриншот подтверждения') }}</div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-6 h-6 bg-orange-500/20 rounded-full flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                        <span class="text-orange-400 text-xs font-bold">4</span>
                                    </div>
                                    <div>{{ __('Загрузите чек в форму ниже') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Next Step Button -->
                        <button onclick="showUploadStep()"
                                class="w-full bg-gray-700 hover:bg-gray-600 text-white font-medium py-4 rounded-xl transition-colors">
                                                            {{ __('Я сделал перевод') }}
                        </button>

                        <!-- Upload Step (Hidden by default) -->
                        <div id="uploadStep" class="hidden space-y-6">
                            <div class="bg-gray-800 rounded-2xl p-5 lg:p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 bg-orange-500/20 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-white font-medium lg:text-lg">{{ __('Прикрепите чек') }}</div>
                                        <div class="text-gray-400 text-sm">{{ __('Скриншот или фото подтверждения') }}</div>
                                    </div>
                                </div>

                                <form action="{{ route('manual-deposit.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

                                    <div x-data="{ selectedFile: null, fileName: '' }">
                                        <div class="border-2 border-dashed border-gray-600 rounded-xl p-8 lg:p-12 text-center mb-4 cursor-pointer hover:border-gray-500 transition-colors"
                                             :class="selectedFile ? 'border-green-500 bg-green-500/5' : ''"
                                             onclick="document.getElementById('receipt').click()">

                                            <input type="file" id="receipt" name="receipt" accept="image/*" class="hidden" required
                                                   @change="selectedFile = $event.target.files[0]; fileName = selectedFile ? selectedFile.name : ''">

                                            <div x-show="!selectedFile">
                                                <div class="w-12 h-12 lg:w-16 lg:h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-6 h-6 lg:w-8 lg:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </div>
                                                <div class="text-white font-medium mb-1">{{ __('Выбрать файл') }}</div>
                                                <div class="text-gray-400 text-sm">{{ __('JPG, PNG до 5MB') }}</div>
                                                <div class="text-gray-500 text-xs mt-1 hidden lg:block">{{ __('Перетащите файл сюда или нажмите для выбора') }}</div>
                                            </div>

                                            <div x-show="selectedFile">
                                                <div class="w-12 h-12 lg:w-16 lg:h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-6 h-6 lg:w-8 lg:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="text-green-400 font-medium mb-1">{{ __('Файл выбран') }}</div>
                                                <div class="text-gray-300 text-sm" x-text="fileName"></div>
                                            </div>
                                        </div>

                                        <button type="submit" id="submitBtn" disabled
                                                class="w-full bg-orange-500 hover:bg-orange-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-medium py-4 rounded-xl transition-colors">
                                            {{ __('Отправить на проверку') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                <!-- Success State -->
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-2">{{ __('Готово!') }}</h2>
                    <p class="text-gray-400 mb-8">{{ __('Чек отправлен на проверку.') }}<br>{{ __('Обычно это занимает 5-30 минут') }}</p>

                    <div class="space-y-3">
                        <a href="{{ route('manual-deposit.my-deposits') }}"
                           class="block w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-4 rounded-xl transition-colors">
                            {{ __('Посмотреть статус') }}
                        </a>
                        <a href="/"
                           class="block w-full bg-gray-700 hover:bg-gray-600 text-white font-medium py-4 rounded-xl transition-colors">
                            {{ __('На главную') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showUploadStep() {
            // Update step indicator for mobile
            const mobileIndicator = document.querySelector('.flex.items-center.space-x-2');
            if (mobileIndicator) {
                mobileIndicator.innerHTML = `
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="w-8 h-0.5 bg-orange-500"></div>
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-bold">2</span>
                    </div>
                `;
            }

            // Update desktop step indicator
            const step2 = document.getElementById('step2');
            if (step2) {
                step2.classList.remove('opacity-50');
                step2.innerHTML = `
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center mr-4">
                        <span class="text-white text-sm font-bold">2</span>
                    </div>
                    <div>
                        <div class="text-white font-medium">{{ __('Подтверждение') }}</div>
                        <div class="text-gray-400 text-sm">{{ __('Загрузите чек об оплате') }}</div>
                    </div>
                `;
            }

            // Show upload step
            document.getElementById('uploadStep').classList.remove('hidden');

            // Hide the button
            event.target.style.display = 'none';

            // Smooth scroll to upload section
            document.getElementById('uploadStep').scrollIntoView({ behavior: 'smooth' });
        }

        function copyRequisites() {
            const text = `{{ $manualPayment->paymentHandler->manual_requisites }}`;
            navigator.clipboard.writeText(text).then(() => {
                const btn = event.target;
                const originalHTML = btn.innerHTML;
                btn.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('Скопировано!') }}
                `;
                btn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                btn.classList.add('bg-green-500');

                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('bg-green-500');
                    btn.classList.add('bg-orange-500', 'hover:bg-orange-600');
                }, 2000);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('receipt');
            const submitBtn = document.getElementById('submitBtn');

            if (fileInput && submitBtn) {
                function updateButton() {
                    submitBtn.disabled = fileInput.files.length === 0;
                }
                fileInput.addEventListener('change', updateButton);
                updateButton();
            }
        });
    </script>
</x-layouts.app>
