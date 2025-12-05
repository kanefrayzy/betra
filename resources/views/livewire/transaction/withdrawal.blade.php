<div class="min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">{{ __('Выводы') }}</h1>
            <p class="text-gray-400">{{ __('История ваших выводов средств') }}</p>
        </div>

        <x-UI.transaction-tab/>

        <div class="bg-[#1a2c38] rounded-xl border border-[#2d3748] overflow-hidden" wire:poll.1000ms>
            
            <div class="hidden md:block">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[#0f212e] border-b border-[#2d3748]">
                                <th class="text-left py-4 px-6 text-gray-400 font-semibold text-xs uppercase tracking-wider">
                                    {{ __('ID') }}
                                </th>
                                <th class="text-left py-4 px-6 text-gray-400 font-semibold text-xs uppercase tracking-wider">
                                    {{ __('Сумма') }}
                                </th>
                                <th class="text-left py-4 px-6 text-gray-400 font-semibold text-xs uppercase tracking-wider">
                                    {{ __('Статус') }}
                                </th>
                                <th class="text-left py-4 px-6 text-gray-400 font-semibold text-xs uppercase tracking-wider">
                                    {{ __('Дата и время') }}
                                </th>
                                <th class="text-right py-4 px-6 text-gray-400 font-semibold text-xs uppercase tracking-wider">
                                    {{ __('Действия') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3748]">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-[#2d3748]/30 transition group">
                                    
                                    <td class="py-4 px-6">
                                        <span class="text-gray-400 font-mono text-sm">#{{ $transaction->id }}</span>
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-white font-bold">{{ moneyFormat($transaction->amount) }}</div>
                                                <div class="text-gray-500 text-xs">{{ $transaction->currency?->symbol ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6">
                                        @if($transaction->status->value === 'success')
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#4dda30]/10 text-[#4dda30] text-xs font-semibold border border-[#4dda30]/20">
                                                <div class="w-1.5 h-1.5 rounded-full bg-[#4dda30]"></div>
                                                {{ __('status.' . $transaction->status->value) }}
                                            </span>
                                        @elseif($transaction->status->value === 'pending')
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-yellow-500/10 text-yellow-400 text-xs font-semibold border border-yellow-500/20">
                                                <div class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></div>
                                                {{ __('status.' . $transaction->status->value) }}
                                            </span>
                                        @elseif($transaction->status->value === 'cancelled')
                                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 text-xs font-semibold border border-red-500/20">
                                                <div class="w-1.5 h-1.5 rounded-full bg-red-400"></div>
                                                {{ __('status.' . $transaction->status->value) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="text-white text-sm">{{ $transaction->created_at->format('d.m.Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $transaction->created_at->format('H:i:s') }}</div>
                                    </td>

                                    <td class="py-4 px-6 text-right">
                                        @if($transaction->hash)
                                            <button onclick="copyToClipboard('{{ $transaction->hash }}')" 
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-gray-400 hover:text-[#3b82f6] text-xs font-medium transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="hidden lg:inline">{{ __('Копировать') }}</span>
                                            </button>
                                        @else
                                            <span class="text-gray-600 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-[#2d3748] rounded-xl flex items-center justify-center mb-4">
                                                <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-white text-lg font-bold mb-2">{{ __('Нет выводов') }}</h3>
                                            <p class="text-gray-500 text-sm">{{ __('История выводов пуста') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="md:hidden">
                <div class="divide-y divide-[#2d3748]">
                    @forelse($transactions as $transaction)
                        <div class="p-4 hover:bg-[#2d3748]/30 transition">
                            
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-white font-bold text-lg">{{ moneyFormat($transaction->amount) }}</div>
                                        <div class="text-gray-500 text-xs">{{ $transaction->currency?->symbol ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                @if($transaction->status->value === 'success')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#4dda30]/10 text-[#4dda30] text-xs font-semibold border border-[#4dda30]/20">
                                        <div class="w-1.5 h-1.5 rounded-full bg-[#4dda30]"></div>
                                        {{ __('status.' . $transaction->status->value) }}
                                    </span>
                                @elseif($transaction->status->value === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-yellow-500/10 text-yellow-400 text-xs font-semibold border border-yellow-500/20">
                                        <div class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-pulse"></div>
                                        {{ __('status.' . $transaction->status->value) }}
                                    </span>
                                @elseif($transaction->status->value === 'cancelled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-500/10 text-red-400 text-xs font-semibold border border-red-500/20">
                                        <div class="w-1.5 h-1.5 rounded-full bg-red-400"></div>
                                        {{ __('status.' . $transaction->status->value) }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <div class="text-gray-500">
                                    <span class="font-mono">#{{ $transaction->id }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $transaction->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                
                                @if($transaction->hash)
                                    <button onclick="copyToClipboard('{{ $transaction->hash }}')" 
                                            class="text-[#3b82f6] hover:text-[#2563eb] font-medium">
                                        {{ __('Копировать') }}
                                    </button>
                                @endif
                            </div>

                            @if($transaction->hash)
                                <div class="mt-3 p-2 bg-[#0f212e] rounded-lg">
                                    <code class="text-gray-400 text-xs font-mono break-all">{{ Illuminate\Support\Str::limit($transaction->hash, 40) }}</code>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-[#2d3748] rounded-xl flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-white text-lg font-bold mb-2">{{ __('Нет выводов') }}</h3>
                                <p class="text-gray-500 text-sm">{{ __('История выводов пуста') }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        @if(count($transactions) < $totalTransactions)
            <div class="mt-8 flex flex-col items-center gap-4">
                <div class="text-gray-400 text-sm">
                    {{ __('Показано') }} 
                    <span class="text-white font-semibold">{{ count($transactions) }}</span> 
                    {{ __('из') }} 
                    <span class="text-white font-semibold">{{ $totalTransactions }}</span>
                </div>

                <button wire:click="loadMore"
                        wire:loading.attr="disabled"
                        class="px-6 py-3 bg-[#3b82f6] hover:bg-[#2563eb] text-white rounded-xl font-semibold text-sm transition-all shadow-lg shadow-[#3b82f6]/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    <span wire:loading.remove wire:target="loadMore">
                        {{ __('Показать еще') }}
                    </span>
                    <span wire:loading wire:target="loadMore" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Загрузка...') }}
                    </span>
                </button>
            </div>
        @else
            @if(count($transactions) > 0)
                <div class="mt-8 text-center">
                    <div class="inline-flex items-center gap-2 text-gray-500 text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('Все транзакции загружены') }}
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        const notification = document.createElement('div');
        notification.className = 'fixed top-6 right-6 z-50 bg-[#4dda30] text-white px-4 py-3 rounded-xl shadow-xl flex items-center gap-3 transition-all duration-300';
        
        notification.innerHTML = `
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-semibold">{{ __("Скопировано!") }}</span>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    });
}
</script>