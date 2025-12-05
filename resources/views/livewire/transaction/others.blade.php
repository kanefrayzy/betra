<div class="min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">{{ __('Другое') }}</h1>
            <p class="text-gray-400">{{ __('Дополнительные транзакции') }}</p>
        </div>

        <x-UI.transaction-tab/>

        <div class="bg-[#1a2c38] rounded-xl border border-[#2d3748] overflow-hidden">
            
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
                                    {{ __('Тип') }}
                                </th>
                                <th class="text-left py-4 px-6 text-gray-400 font-semibold text-xs uppercase tracking-wider">
                                    {{ __('Дата и время') }}
                                </th>
                                <th class="text-left py-4 px-6 text-gray-400 font-semibold text-xs uppercase tracking-wider">
                                    {{ __('Описание') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#2d3748]">
                            @forelse($transactions as $transaction)
                                @php
                                    $context = $transaction->context;
                                @endphp
                                <tr class="hover:bg-[#2d3748]/30 transition">
                                    
                                    <td class="py-4 px-6">
                                        <span class="text-gray-400 font-mono text-sm">#{{ $transaction->id }}</span>
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-[#3b82f6]/10 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-white font-bold">{{ moneyFormat($transaction->amount) }}</div>
                                                <div class="text-gray-500 text-xs">{{ $transaction->currency?->symbol ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#3b82f6]/10 text-[#3b82f6] text-xs font-semibold border border-[#3b82f6]/20">
                                            @switch($transaction->type)
                                                @case('bonus')
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                    @break
                                                @case('refund')
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                                    </svg>
                                                    @break
                                                @default
                                                    <div class="w-1.5 h-1.5 rounded-full bg-[#3b82f6]"></div>
                                            @endswitch
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-6">
                                        <div class="text-white text-sm">{{ $transaction->created_at->format('d.m.Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $transaction->created_at->format('H:i:s') }}</div>
                                    </td>

                                    <td class="py-4 px-6">
                                        <p class="text-gray-400 text-sm">{{ $context['description'] ?? 'N/A' }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-[#2d3748] rounded-xl flex items-center justify-center mb-4">
                                                <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-white text-lg font-bold mb-2">{{ __('Нет транзакций') }}</h3>
                                            <p class="text-gray-500 text-sm">{{ __('История транзакций пуста') }}</p>
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
                        @php
                            $context = $transaction->context;
                        @endphp
                        <div class="p-4 hover:bg-[#2d3748]/30 transition">
                            
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-[#3b82f6]/10 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-white font-bold text-lg">{{ moneyFormat($transaction->amount) }} {{ $transaction->currency?->symbol ?? '' }}</div>
                                        <div class="text-gray-500 text-sm">{{ $transaction->created_at->format('d.m.Y H:i') }}</div>
                                    </div>
                                </div>

                                <span class="px-3 py-1 rounded-lg bg-[#3b82f6]/10 text-[#3b82f6] text-xs font-semibold border border-[#3b82f6]/20">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </div>

                            @if($context['description'] ?? null)
                                <div class="p-3 bg-[#0f212e] rounded-lg mb-3">
                                    <p class="text-gray-400 text-sm">{{ $context['description'] }}</p>
                                </div>
                            @endif

                            <div class="text-gray-500 font-mono text-xs">#{{ $transaction->id }}</div>
                        </div>
                    @empty
                        <div class="py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-[#2d3748] rounded-xl flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-white text-lg font-bold mb-2">{{ __('Нет транзакций') }}</h3>
                                <p class="text-gray-500 text-sm">{{ __('История транзакций пуста') }}</p>
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