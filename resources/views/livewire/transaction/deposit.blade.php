<div class="min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">{{ __('Депозиты') }}</h1>
            <p class="text-gray-400">{{ __('История ваших пополнений') }}</p>
        </div>

        <!-- Tabs -->
        <x-UI.transaction-tab/>

        <!-- Table Card -->
        <div class="bg-[#1e2329] rounded-2xl shadow-xl overflow-hidden border border-gray-800">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-800">
                            <th class="text-left py-4 px-6 text-gray-400 font-semibold text-sm uppercase tracking-wider">{{ __('Сумма') }}</th>
                            <th class="text-left py-4 px-6 text-gray-400 font-semibold text-sm uppercase tracking-wider">{{ __('Статус') }}</th>
                            <th class="text-left py-4 px-6 text-gray-400 font-semibold text-sm uppercase tracking-wider">{{ __('Дата') }}</th>
                            <th class="text-left py-4 px-6 text-gray-400 font-semibold text-sm uppercase tracking-wider">{{ __('Хэш') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-800/50 transition group">
                                <!-- Amount -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-10 h-10 rounded-lg bg-[#ffb300]/10 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-[#ffb300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-white font-bold">{{ moneyFormat($transaction->amount) }}</p>
                                            <p class="text-gray-500 text-sm">{{ $transaction->currency->symbol }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-6">
                                    @if($transaction->status->value === 'success')
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-500/10 text-green-400 text-sm font-semibold">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('status.' . $transaction->status->value) }}
                                        </span>
                                    @elseif($transaction->status->value === 'pending')
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-yellow-500/10 text-yellow-400 text-sm font-semibold">
                                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ __('status.' . $transaction->status->value) }}
                                        </span>
                                    @elseif($transaction->status->value === 'cancelled')
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 text-sm font-semibold">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ __('status.' . $transaction->status->value) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Date -->
                                <td class="py-4 px-6">
                                    <div class="text-white text-sm">{{ $transaction->created_at->format('d.m.Y') }}</div>
                                    <div class="text-gray-500 text-xs">{{ $transaction->created_at->format('H:i:s') }}</div>
                                </td>

                                <!-- Hash -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <code class="text-gray-400 text-sm font-mono">{{ Illuminate\Support\Str::limit($transaction->hash, 16) }}</code>
                                        @if($transaction->hash)
                                            <button onclick="copyToClipboard('{{ $transaction->hash }}')" class="opacity-0 group-hover:opacity-100 transition text-gray-500 hover:text-[#ffb300]">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-400 text-lg font-medium mb-1">{{ __('Нет транзакций') }}</p>
                                        <p class="text-gray-600 text-sm">{{ __('История депозитов пуста') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-800">
                @forelse($transactions as $transaction)
                    <div class="p-4 hover:bg-gray-800/50 transition">
                        <!-- Amount & Status -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-[#ffb300]/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-[#ffb300]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-white font-bold">{{ moneyFormat($transaction->amount) }} {{ $transaction->currency->symbol }}</p>
                                    <p class="text-gray-500 text-sm">{{ $transaction->created_at->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>

                            @if($transaction->status->value === 'success')
                                <span class="px-2 py-1 rounded-lg bg-green-500/10 text-green-400 text-xs font-semibold">
                                    {{ __('status.' . $transaction->status->value) }}
                                </span>
                            @elseif($transaction->status->value === 'pending')
                                <span class="px-2 py-1 rounded-lg bg-yellow-500/10 text-yellow-400 text-xs font-semibold">
                                    {{ __('status.' . $transaction->status->value) }}
                                </span>
                            @elseif($transaction->status->value === 'cancelled')
                                <span class="px-2 py-1 rounded-lg bg-red-500/10 text-red-400 text-xs font-semibold">
                                    {{ __('status.' . $transaction->status->value) }}
                                </span>
                            @endif
                        </div>

                        <!-- Hash -->
                        @if($transaction->hash)
                            <div class="flex items-center gap-2 p-2 bg-gray-800/50 rounded-lg">
                                <code class="text-gray-400 text-xs font-mono flex-1 overflow-hidden text-ellipsis">{{ $transaction->hash }}</code>
                                <button onclick="copyToClipboard('{{ $transaction->hash }}')" class="text-gray-500 hover:text-[#ffb300]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                            <p class="text-gray-400 text-lg font-medium mb-1">{{ __('Нет транзакций') }}</p>
                            <p class="text-gray-600 text-sm">{{ __('История депозитов пуста') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Load More Button -->
        @if(count($transactions) < $totalTransactions)
            <div class="mt-8 flex flex-col items-center gap-4">
                <div class="text-gray-400 text-sm">
                    {{ __('Показано') }} <span class="text-white font-semibold">{{ count($transactions) }}</span> {{ __('из') }} <span class="text-white font-semibold">{{ $totalTransactions }}</span> {{ __('транзакций') }}
                </div>

                <button wire:click="loadMore"
                        wire:loading.attr="disabled"
                        class="px-8 py-3 bg-[#ffb300] hover:bg-[#f5a300] text-black rounded-lg font-bold text-base transition-all duration-300 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    <span wire:loading.remove wire:target="loadMore">
                        {{ __('Показать еще') }}
                    </span>
                    <span wire:loading wire:target="loadMore" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                    </span>
                </button>
            </div>
        @else
            @if(count($transactions) > 0)
                <div class="mt-8 text-center text-gray-400">
                    <p>{{ __('Все транзакции загружены') }} ({{ $totalTransactions }} {{ __('транзакций') }})</p>
                </div>
            @endif
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        if (typeof showSuccessNotification === 'function') {
            showSuccessNotification('{{ __("Скопировано") }}');
        }
    });
}
</script>
