<x-layouts.app>
    <div class="min-h-screen">
        
        <div class="relative overflow-hidden border-b border-[#2d3748]">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSg1OSwxMzAsMjQ2LDAuMDUpIiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-40"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 md:py-16 relative">
                <div class="text-center mb-12">
                    <div class="inline-flex items-center gap-2 bg-[#3b82f6]/10 border border-[#3b82f6]/20 rounded-full px-4 py-2 mb-6">
                        <div class="w-2 h-2 bg-[#4dda30] rounded-full animate-pulse"></div>
                        <span class="text-[#3b82f6] text-sm font-semibold">{{ __('Активная программа') }}</span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4">
                        {{ __('Зарабатывай') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#3b82f6] to-[#4dda30]">вместе с нами</span>
                    </h1>
                    <p class="text-gray-400 text-lg md:text-xl max-w-3xl mx-auto">
                        {{ __('Получай до 20% от каждого пополнения приглашённых друзей навсегда') }}
                    </p>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 max-w-5xl mx-auto">
                    <div class="bg-[#0f212e] backdrop-blur-sm border border-[#2d3748] rounded-2xl p-6 text-center">
                        <div class="text-3xl md:text-4xl font-bold text-[#3b82f6] mb-2">{{ $referralsCount }}</div>
                        <div class="text-gray-400 text-sm">{{ __('Рефералов') }}</div>
                    </div>
                    <div class="bg-[#0f212e] backdrop-blur-sm border border-[#2d3748] rounded-2xl p-6 text-center">
                        <div class="text-3xl md:text-4xl font-bold text-[#4dda30] mb-2">{{ moneyFormat($refProfit) }}</div>
                        <div class="text-gray-400 text-sm">{{ __('Заработано') }}</div>
                    </div>
                    <div class="bg-[#0f212e] backdrop-blur-sm border border-[#2d3748] rounded-2xl p-6 text-center">
                        <div class="text-3xl md:text-4xl font-bold text-[#3b82f6] mb-2">{{ moneyFormat($refBalance) }}</div>
                        <div class="text-gray-400 text-sm">{{ __('Доступно') }}</div>
                    </div>
                    <div class="bg-[#0f212e] backdrop-blur-sm border border-[#2d3748] rounded-2xl p-6 text-center">
                        <div class="text-3xl md:text-4xl font-bold text-[#4dda30] mb-2">20%</div>
                        <div class="text-gray-400 text-sm">{{ __('Ваш %') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 md:py-12">
            
            @if($u->ref_percentage && $u->ref_percentage != 20)
            <div class="relative overflow-hidden bg-[#0f212e] rounded-2xl p-8 mb-8 shadow-2xl shadow-[#3b82f6]/20">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                <div class="relative flex items-center gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-20 h-20 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-2xl font-bold text-white">{{ __('VIP Партнёр') }}</h3>
                            <span class="px-3 py-1 bg-[#4dda30] text-white text-sm font-bold rounded-lg">{{ __('Активен') }}</span>
                        </div>
                        <p class="text-white/90 text-lg">
                            {{ __('Вам доступен повышенный процент:') }}
                            <span class="text-3xl font-bold ml-2">{{ $u->ref_percentage }}%</span>
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-[#0f212e] border border-[#2d3748] rounded-2xl p-6 md:p-8 mb-8 shadow-xl">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#3b82f6] to-[#2563eb] rounded-xl flex items-center justify-center shadow-lg shadow-[#3b82f6]/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ __('Реферальная ссылка') }}</h2>
                        <p class="text-gray-400 text-sm">{{ __('Поделись и начни зарабатывать') }}</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-3">
                    <div class="relative flex-1">
                        <input type="text"
                               id="refLink"
                               value="{{ $refLink }}"
                               readonly
                               class="w-full h-14 px-6 bg-[#0f212e] border-2 border-[#2d3748] focus:border-[#3b82f6] rounded-xl text-white font-mono text-base focus:outline-none transition-all pr-12">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2">
                            <div class="w-6 h-6 bg-[#3b82f6]/10 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <button onclick="copyToClipboard('{{ $refLink }}')"
                            class="h-14 px-8 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] hover:from-[#2563eb] hover:to-[#1d4ed8] text-white font-bold rounded-xl transition-all duration-200 shadow-lg shadow-[#3b82f6]/20 flex items-center justify-center gap-2 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                        </svg>
                        {{ __('Копировать') }}
                    </button>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                
                <div class="bg-[#0f212e] border border-[#2d3748] rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#4dda30]/10 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#4dda30]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">{{ __('Баланс') }}</h3>
                        </div>
                        @if($refBalance > 0)
                        <form action="{{ route('account.take-bonus') }}" method="POST">
                            @csrf
                            <button class="px-4 py-2 bg-[#4dda30] hover:bg-[#3bb825] text-white font-bold rounded-lg transition-all shadow-lg shadow-[#4dda30]/20 text-sm">
                                {{ __('Вывести') }}
                            </button>
                        </form>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-end justify-between">
                            <div>
                                <div class="text-gray-400 text-sm mb-1">{{ __('Доступно к выводу') }}</div>
                                <div class="text-3xl font-bold text-white">
                                    {{ moneyFormat($refBalance) }}
                                    <span class="text-lg text-gray-400 ml-1">{{ $u->currency->symbol }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-gray-400 text-xs mb-1">{{ __('Всего заработано') }}</div>
                                <div class="text-xl font-bold text-[#4dda30]">
                                    {{ moneyFormat($refProfit) }}
                                    <span class="text-sm">{{ $u->currency->symbol }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-[#0f212e] rounded-xl p-4 border border-[#2d3748]">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-400">{{ __('Выплат получено') }}</span>
                                <span class="text-white font-semibold">{{ moneyFormat($refProfit - $refBalance) }} {{ $u->currency->symbol }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#0f212e] border border-[#2d3748] rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-[#3b82f6]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">{{ __('Статистика') }}</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-[#0f212e] rounded-xl border border-[#2d3748]">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-[#3b82f6]/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <span class="text-gray-400 text-sm">{{ __('Приглашено') }}</span>
                            </div>
                            <span class="text-2xl font-bold text-white">{{ $referralsCount }}</span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-[#0f212e] rounded-xl border border-[#2d3748]">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-[#4dda30]/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-[#4dda30]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <span class="text-gray-400 text-sm">{{ __('Депозиты') }}</span>
                            </div>
                            <span class="text-2xl font-bold text-white">{{ moneyFormat($totalDeposits) }}</span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-[#3b82f6]/10 to-transparent rounded-xl border border-[#3b82f6]/20">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-[#3b82f6]/20 rounded-lg flex items-center justify-center">
                                    <span class="text-[#3b82f6] font-bold text-lg">%</span>
                                </div>
                                <span class="text-gray-400 text-sm">{{ __('Ваша ставка') }}</span>
                            </div>
                            <span class="text-2xl font-bold text-[#3b82f6]">{{ $u->ref_percentage ?? 20 }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#0f212e] border border-[#2d3748] rounded-2xl shadow-xl overflow-hidden">
                <div class="px-6 md:px-8 py-6 border-b border-[#2d3748] flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#3b82f6]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#3b82f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-white">{{ __('Список рефералов') }}</h2>
                            <p class="text-gray-400 text-sm">{{ __('Всего приглашено:') }} <span class="text-[#3b82f6] font-semibold">{{ $referralsCount }}</span></p>
                        </div>
                    </div>
                </div>

                @if($referrals->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 px-4">
                        <div class="w-24 h-24 bg-[#2d3748] rounded-2xl flex items-center justify-center mb-6 relative">
                            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-[#3b82f6] rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-bold">0</span>
                            </div>
                        </div>
                        <h3 class="text-white text-xl font-bold mb-3">{{ __('Пока нет рефералов') }}</h3>
                        <p class="text-gray-400 text-center max-w-md mb-6">
                            {{ __('Поделитесь своей реферальной ссылкой с друзьями и начните получать пассивный доход прямо сейчас') }}
                        </p>
                        <button onclick="copyToClipboard('{{ $refLink }}')"
                                class="px-6 py-3 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#3b82f6]/20 hover:shadow-[#3b82f6]/30">
                            {{ __('Скопировать ссылку') }}
                        </button>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-[#0f212e]">
                                    <th class="px-6 py-4 text-left">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Реферал') }}</span>
                                    </th>
                                    <th class="px-6 py-4 text-left hidden md:table-cell">
                                        <a href="{{ route('account.referrals', ['sort_by' => 'created_at', 'sort_order' => ($sortBy == 'created_at' && $sortOrder == 'asc') ? 'desc' : 'asc']) }}"
                                           class="text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-[#3b82f6] transition-colors inline-flex items-center gap-1">
                                            {{ __('Дата') }}
                                            @if ($sortBy == 'created_at')
                                                <span class="text-[#3b82f6]">{!! $sortOrder == 'asc' ? '↑' : '↓' !!}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-4 text-left">
                                        <a href="{{ route('account.referrals', ['sort_by' => 'total_deposits', 'sort_order' => ($sortBy == 'total_deposits' && $sortOrder == 'asc') ? 'desc' : 'asc']) }}"
                                           class="text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-[#3b82f6] transition-colors inline-flex items-center gap-1">
                                            {{ __('Депозиты') }}
                                            @if ($sortBy == 'total_deposits')
                                                <span class="text-[#3b82f6]">{!! $sortOrder == 'asc' ? '↑' : '↓' !!}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-4 text-right">
                                        <a href="{{ route('account.referrals', ['sort_by' => 'from_ref', 'sort_order' => ($sortBy == 'from_ref' && $sortOrder == 'asc') ? 'desc' : 'asc']) }}"
                                           class="text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-[#3b82f6] transition-colors inline-flex items-center gap-1">
                                            {{ __('Доход') }}
                                            @if ($sortBy == 'from_ref')
                                                <span class="text-[#3b82f6]">{!! $sortOrder == 'asc' ? '↑' : '↓' !!}</span>
                                            @endif
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#2d3748]">
                                @foreach($referrals as $referral)
                                    <tr class="hover:bg-[#2d3748]/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                <div class="relative flex-shrink-0">
                                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#3b82f6] to-[#2563eb] flex items-center justify-center text-white font-bold overflow-hidden shadow-lg">
                                                        @if($referral->avatar)
                                                            <img src="{{ $referral->avatar }}" alt="{{ $referral->username }}" class="w-full h-full object-cover">
                                                        @else
                                                            {{ strtoupper(substr($referral->username, 0, 2)) }}
                                                        @endif
                                                    </div>
                                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#4dda30] border-2 border-[#0f212e] rounded-full"></div>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-white mb-1">{{ $referral->username }}</div>
                                                    <div class="text-xs text-gray-500 font-mono">{{ $referral->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 hidden md:table-cell">
                                            <div class="text-sm text-gray-400">{{ $referral->created_at->format('d.m.Y') }}</div>
                                            <div class="text-xs text-gray-600">{{ $referral->created_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#4dda30]/10 border border-[#4dda30]/20 rounded-lg">
                                                <svg class="w-4 h-4 text-[#4dda30]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                                </svg>
                                                <span class="text-sm font-bold text-[#4dda30]">
                                                    {{ moneyFormat(toUSD($referral->total_deposits_usd, $u->currency->symbol)) }}
                                                    <span class="text-xs">{{ $u->currency->symbol }}</span>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-lg font-bold text-white">
                                                +{{ moneyFormat(toUSD($referral->from_ref, $u->currency->symbol)) }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $u->currency->symbol }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 md:px-8 py-6 border-t border-[#2d3748] bg-[#0f212e]">
                        {{ $referrals->appends(['sort_by' => $sortBy, 'sort_order' => $sortOrder])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            const notification = document.createElement('div');
            notification.className = 'fixed top-6 right-6 z-50 transform transition-all duration-500';
            
            notification.innerHTML = `
                <div class="bg-gradient-to-r from-[#4dda30] to-[#3bb825] text-white px-6 py-4 rounded-xl shadow-2xl shadow-[#4dda30]/30 flex items-center gap-4 min-w-[320px]">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-bold text-lg">{{ __("Успешно!") }}</div>
                        <div class="text-sm opacity-90">{{ __("Реферальная ссылка скопирована") }}</div>
                    </div>
                    <button onclick="this.closest('.fixed').remove()" class="text-white/70 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 500);
            }, 4000);
        }).catch(() => {
            alert('{{ __("Не удалось скопировать") }}');
        });
    }
    </script>
</x-layouts.app>