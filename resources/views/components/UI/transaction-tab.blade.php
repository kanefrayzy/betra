<div class="mb-6">
    <div class="flex flex-wrap gap-2 p-1.5 bg-[#1a2c38] rounded-xl border border-[#2d3748]">
        <a href="{{ route('transaction.deposit') }}" class="flex-1 min-w-[100px]">
            <button class="w-full px-4 py-2.5 rounded-lg font-medium text-sm transition-all duration-200
                {{ request()->routeIs('transaction.deposit')
                    ? 'bg-[#3b82f6] text-white shadow-lg shadow-[#3b82f6]/20'
                    : 'text-gray-400 hover:text-white hover:bg-[#2d3748]' }}">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                    {{ __('Депозиты') }}
                </span>
            </button>
        </a>

        <a href="{{ route('transaction.withdrawal') }}" class="flex-1 min-w-[100px]">
            <button class="w-full px-4 py-2.5 rounded-lg font-medium text-sm transition-all duration-200
                {{ request()->routeIs('transaction.withdrawal')
                    ? 'bg-[#3b82f6] text-white shadow-lg shadow-[#3b82f6]/20'
                    : 'text-gray-400 hover:text-white hover:bg-[#2d3748]' }}">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    {{ __('Выводы') }}
                </span>
            </button>
        </a>

        <a href="{{ route('transaction.games') }}" class="flex-1 min-w-[100px]">
            <button class="w-full px-4 py-2.5 rounded-lg font-medium text-sm transition-all duration-200
                {{ request()->routeIs('transaction.games')
                    ? 'bg-[#3b82f6] text-white shadow-lg shadow-[#3b82f6]/20'
                    : 'text-gray-400 hover:text-white hover:bg-[#2d3748]' }}">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    {{ __('Операции') }}
                </span>
            </button>
        </a>

        <a href="{{ route('transaction.others') }}" class="flex-1 min-w-[100px]">
            <button class="w-full px-4 py-2.5 rounded-lg font-medium text-sm transition-all duration-200
                {{ request()->routeIs('transaction.others')
                    ? 'bg-[#3b82f6] text-white shadow-lg shadow-[#3b82f6]/20'
                    : 'text-gray-400 hover:text-white hover:bg-[#2d3748]' }}">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    {{ __('Другое') }}
                </span>
            </button>
        </a>
    </div>
</div>