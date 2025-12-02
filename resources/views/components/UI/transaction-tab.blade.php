<div class="mb-8">
    <div class="flex flex-wrap gap-2 p-2 bg-[#1e2329] rounded-xl border border-gray-800">
        <a href="{{ route('transaction.deposit') }}"
           class="flex-1 min-w-[120px] group">
            <button class="w-full px-4 py-3 rounded-lg font-medium text-sm transition-all duration-200
                {{ request()->routeIs('transaction.deposit')
                    ? 'bg-[#ffb300] text-black shadow-lg shadow-[#ffb300]/30'
                    : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                    {{ __('Депозиты') }}
                </span>
            </button>
        </a>

        <a href="{{ route('transaction.withdrawal') }}"
           class="flex-1 min-w-[120px] group">
            <button class="w-full px-4 py-3 rounded-lg font-medium text-sm transition-all duration-200
                {{ request()->routeIs('transaction.withdrawal')
                    ? 'bg-[#ffb300] text-black shadow-lg shadow-[#ffb300]/30'
                    : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    {{ __('Выводы') }}
                </span>
            </button>
        </a>

        <a href="{{ route('transaction.games') }}"
           class="flex-1 min-w-[120px] group">
            <button class="w-full px-4 py-3 rounded-lg font-medium text-sm transition-all duration-200
                {{ request()->routeIs('transaction.games')
                    ? 'bg-[#ffb300] text-black shadow-lg shadow-[#ffb300]/30'
                    : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    {{ __('Операции') }}
                </span>
            </button>
        </a>

        <a href="{{ route('transaction.others') }}"
           class="flex-1 min-w-[120px] group">
            <button class="w-full px-4 py-3 rounded-lg font-medium text-sm transition-all duration-200
                {{ request()->routeIs('transaction.others')
                    ? 'bg-[#ffb300] text-black shadow-lg shadow-[#ffb300]/30'
                    : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    {{ __('Другое') }}
                </span>
            </button>
        </a>
    </div>
</div>
