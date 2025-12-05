@php use Illuminate\Support\Str; @endphp
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">{{ __('Операции') }}</h1>
            <p class="text-gray-400">{{ __('История всех операций') }}</p>
        </div>

        <x-UI.transaction-tab/>

        <div class="bg-[#1a2c38] rounded-xl border border-[#2d3748] overflow-hidden">
            
            <div class="hidden lg:block">
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
                                    {{ __('Тип') }}
                                </th>
                                <th class="text-left py-4 px-6 text-gray-400 font-semibold text-xs uppercase tracking-wider">
                                    {{ __('Дата и время') }}
                                </th>
                                <th class="text-left py-4 px-6 text-gray-400 font-semibold text-xs uppercase tracking-wider">
                                    {{ __('Описание') }}
                                </th>
                                <th class="text-right py-4 px-6 text-gray-400 font-semibold text-xs uppercase tracking-wider">
                                    {{ __('Действия') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3748]">
                            @forelse($transactions as $transaction)
                                @php
                                    $context = $transaction->context;
                                    $isWin = $transaction->type == \App\Enums\TransactionType::Win;
                                @endphp
                                <tr class="hover:bg-[#2d3748]/30 transition group">
                                    
                                    <td class="py-4 px-6">
                                        <span class="text-gray-400 font-mono text-sm">#{{ $transaction->id }}</span>
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                                {{ $isWin ? 'bg-[#4dda30]/10' : 'bg-red-500/10' }}">
                                                @if($isWin)
                                                    <svg class="w-5 h-5 text-[#4dda30]" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold {{ $isWin ? 'text-[#4dda30]' : 'text-white' }}">
                                                    {{ moneyFormat($transaction->amount) }}
                                                </div>
                                                <div class="text-gray-500 text-xs">{{ $transaction->currency?->symbol ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6">
                                        @switch($transaction->type->value ?? $transaction->type)
                                            @case('win')
                                            @case(\App\Enums\TransactionType::Win)
                                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#4dda30]/10 text-[#4dda30] text-xs font-semibold border border-[#4dda30]/20">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-[#4dda30]"></div>
                                                    {{ __('Выигрыш') }}
                                                </span>
                                                @break
                                            @case('bet')
                                            @case(\App\Enums\TransactionType::Bet)
                                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 text-xs font-semibold border border-red-500/20">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-red-400"></div>
                                                    {{ __('Ставка') }}
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#3b82f6]/10 text-[#3b82f6] text-xs font-semibold border border-[#3b82f6]/20">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-[#3b82f6]"></div>
                                                    {{ is_object($transaction->type) ? $transaction->type->value : $transaction->type }}
                                                </span>
                                        @endswitch
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="text-white text-sm">{{ $transaction->created_at->format('d.m.Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $transaction->created_at->format('H:i:s') }}</div>
                                    </td>

                                    <td class="py-4 px-6">
                                        <p class="text-gray-400 text-sm">{{ $context['description'] ?? 'N/A' }}</p>
                                    </td>

                                    <td class="py-4 px-6 text-right">
                                        @if($transaction->hash)
                                            <button onclick="copyToClipboard('{{ $transaction->hash }}')"
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-gray-400 hover:text-[#3b82f6] text-xs font-medium transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="hidden xl:inline">{{ __('Копировать') }}</span>
                                            </button>
                                        @else
                                            <span class="text-gray-600 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-20 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-[#2d3748] rounded-xl flex items-center justify-center mb-4">
                                                <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-white text-lg font-bold mb-2">{{ __('Нет операций') }}</h3>
                                            <p class="text-gray-500 text-sm">{{ __('История операций пуста') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="lg:hidden">
                <div class="divide-y divide-[#2d3748]">
                    @forelse($transactions as $transaction)
                        @php
                            $context = $transaction->context;
                            $isWin = $transaction->type == \App\Enums\TransactionType::Win;
                        @endphp
                        <div class="p-4 hover:bg-[#2d3748]/30 transition">
                            
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center
                                        {{ $isWin ? 'bg-[#4dda30]/10' : 'bg-red-500/10' }}">
                                        @if($isWin)
                                            <svg class="w-6 h-6 text-[#4dda30]" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold {{ $isWin ? 'text-[#4dda30]' : 'text-white' }} text-lg">
                                            {{ moneyFormat($transaction->amount) }} {{ $transaction->currency?->symbol ?? '' }}
                                        </div>
                                        <div class="text-gray-500 text-sm">{{ $transaction->created_at->format('d.m.Y H:i') }}</div>
                                    </div>
                                </div>

                                @switch($transaction->type->value ?? $transaction->type)
                                    @case('win')
                                    @case(\App\Enums\TransactionType::Win)
                                        <span class="px-2 py-1 rounded-lg bg-[#4dda30]/10 text-[#4dda30] text-xs font-semibold border border-[#4dda30]/20">
                                            Win
                                        </span>
                                        @break
                                    @case('bet')
                                    @case(\App\Enums\TransactionType::Bet)
                                        <span class="px-2 py-1 rounded-lg bg-red-500/10 text-red-400 text-xs font-semibold border border-red-500/20">
                                            Bet
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 py-1 rounded-lg bg-[#3b82f6]/10 text-[#3b82f6] text-xs font-semibold border border-[#3b82f6]/20">
                                            {{ is_object($transaction->type) ? $transaction->type->value : $transaction->type }}
                                        </span>
                                @endswitch
                            </div>

                            @if($context['description'] ?? null)
                                <div class="mb-3 p-3 bg-[#0f212e] rounded-lg">
                                    <p class="text-gray-400 text-sm">{{ $context['description'] }}</p>
                                </div>
                            @endif

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500 font-mono">#{{ $transaction->id }}</span>
                                @if($transaction->hash)
                                    <button onclick="copyToClipboard('{{ $transaction->hash }}')" 
                                            class="text-[#3b82f6] hover:text-[#2563eb] font-medium">
                                        {{ __('Копировать хэш') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-[#2d3748] rounded-xl flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <h3 class="text-white text-lg font-bold mb-2">{{ __('Нет операций') }}</h3>
                                <p class="text-gray-500 text-sm">{{ __('История операций пуста') }}</p>
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