<x-layouts.app>
    <div class="min-h-screen bg-[#0f1419] py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6" x-data="{ activeTab: 'overview' }">
            <!-- Header Section -->
            <div class="bg-gradient-to-br from-[#1e2329] to-[#0f1419] rounded-2xl shadow-2xl overflow-hidden mb-8 border border-gray-800 relative">
                <!-- Декоративные элементы -->
                <div class="absolute top-0 right-0 w-40 h-40 bg-[#ffb300]/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-[#ffb300]/5 rounded-full blur-2xl"></div>

                <div class="relative px-6 md:px-8 py-8 md:py-12">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div>
                            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-3">
                                {{ __('Партнерская программа') }}
                            </h1>
                            <p class="text-gray-400 text-base md:text-lg max-w-2xl">
                                {{ __('Приглашайте друзей и зарабатывайте с каждого пополнения') }}
                            </p>
                        </div>

                        <!-- Процент карточка -->
                        <div class="bg-gradient-to-br from-[#252a32] to-[#1e2329] rounded-2xl p-6 md:p-8 border border-gray-800 shadow-xl">
                            <div class="text-center">
                                <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-[#ffb300] to-[#e6a000] rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 md:w-10 md:h-10 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-4xl md:text-5xl font-bold text-[#ffb300] mb-2">20%</div>
                                <div class="text-gray-400 text-sm font-medium">{{ __('Реферальный бонус') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="bg-[#1e2329] rounded-xl shadow-xl mb-8 border border-gray-800 overflow-hidden">
                <div class="flex">
                    <button @click="activeTab = 'overview'"
                            class="flex-1 py-4 px-4 md:px-6 text-center font-medium transition-all duration-200 relative"
                            :class="activeTab === 'overview' ? 'text-white bg-[#ffb300]/10' : 'text-gray-400 hover:text-white hover:bg-gray-800/50'">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="text-sm md:text-base">{{ __('Обзор') }}</span>
                        </div>
                        <div x-show="activeTab === 'overview'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#ffb300]"></div>
                    </button>

                    <button @click="activeTab = 'referrals'"
                            class="flex-1 py-4 px-4 md:px-6 text-center font-medium transition-all duration-200 relative"
                            :class="activeTab === 'referrals' ? 'text-white bg-[#ffb300]/10' : 'text-gray-400 hover:text-white hover:bg-gray-800/50'">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span class="text-sm md:text-base">{{ __('Рефералы') }}</span>
                        </div>
                        <div x-show="activeTab === 'referrals'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#ffb300]"></div>
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="min-h-[600px]">
                <!-- Overview Tab -->
                <div x-show="activeTab === 'overview'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0">

                    <!-- VIP Badge -->
                    @if($u->ref_percentage && $u->ref_percentage != 20)
                        <div class="bg-gradient-to-r from-[#ffb300]/10 to-transparent rounded-2xl shadow-xl p-6 md:p-8 mb-8 border border-[#ffb300]/30">
                            <div class="flex flex-col sm:flex-row items-center gap-6">
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <div class="w-20 h-20 bg-gradient-to-br from-[#ffb300] to-[#e6a000] rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-10 h-10 text-black" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                        <div class="absolute -top-2 -right-2 h-8 w-8 bg-[#ffb300] rounded-full flex items-center justify-center animate-pulse border-2 border-[#1e2329]">
                                            <svg class="h-4 w-4 text-black" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center sm:text-left">
                                    <h3 class="text-xl md:text-2xl font-bold text-white mb-2">{{ __('VIP-статус активирован!') }}</h3>
                                    <p class="text-gray-300 text-base md:text-lg">
                                        {{ __('Ваш специальный реферальный процент:') }}
                                        <span class="text-2xl md:text-3xl font-bold ml-2 text-[#ffb300]">{{ $u->ref_percentage }}%</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Referral Link Card -->
                    <div class="bg-[#1e2329] rounded-2xl shadow-xl p-6 md:p-8 mb-8 border border-gray-800">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-[#ffb300] to-[#e6a000] rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl md:text-2xl font-bold text-white">{{ __('Ваша реферальная ссылка') }}</h2>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-grow">
                                <input type="text"
                                       id="refLink"
                                       value="{{ $refLink }}"
                                       readonly
                                       class="w-full py-3 md:py-4 px-4 md:px-6 bg-[#252a32] border border-gray-800 focus:border-[#ffb300] rounded-xl text-white font-mono text-sm md:text-base focus:outline-none transition-colors pr-12">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                </div>
                            </div>
                            <button onclick="copyToClipboard('{{ $refLink }}')"
                                    class="py-3 md:py-4 px-6 md:px-8 bg-[#ffb300] hover:bg-[#e6a000] text-black font-bold rounded-xl transition-all duration-200 shadow-lg flex items-center justify-center whitespace-nowrap">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                </svg>
                                {{ __('Копировать') }}
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                        <!-- Card 1: Referral Count -->
                        <div class="bg-[#1e2329] rounded-xl shadow-xl overflow-hidden border border-gray-800 hover:border-cyan-500/50 transition-all duration-300 transform hover:scale-105">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-white">{{ __('Рефералы') }}</h3>
                                </div>
                                <div class="text-3xl font-bold text-cyan-400 mb-1">{{ $referralsCount }}</div>
                                <p class="text-gray-500 text-xs">{{ __('Всего приглашено') }}</p>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-cyan-500 to-blue-600"></div>
                        </div>

                        <!-- Card 2: Total Profit -->
                        <div class="bg-[#1e2329] rounded-xl shadow-xl overflow-hidden border border-gray-800 hover:border-emerald-500/50 transition-all duration-300 transform hover:scale-105">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-white">{{ __('Прибыль') }}</h3>
                                </div>
                                <div class="text-2xl md:text-3xl font-bold text-emerald-400 mb-1">
                                    {{ moneyFormat($refProfit) }} <small class="text-base">{{ $u->currency->symbol }}</small>
                                </div>
                                <p class="text-gray-500 text-xs">{{ __('За все время') }}</p>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-emerald-500 to-green-600"></div>
                        </div>

                        <!-- Card 3: Current Balance -->
                        <div class="bg-[#1e2329] rounded-xl shadow-xl overflow-hidden border border-gray-800 hover:border-purple-500/50 transition-all duration-300 transform hover:scale-105">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-white">{{ __('Баланс') }}</h3>
                                </div>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <div class="text-2xl md:text-3xl font-bold text-purple-400 mb-1">
                                            {{ moneyFormat($refBalance) }} <small class="text-base">{{ $u->currency->symbol }}</small>
                                        </div>
                                        <p class="text-gray-500 text-xs">{{ __('Доступно') }}</p>
                                    </div>
                                    @if($refBalance > 0)
                                        <form action="{{ route('account.take-bonus') }}" method="POST">
                                            @csrf
                                            <button class="px-4 py-2 bg-gradient-to-r from-purple-500 to-fuchsia-600 hover:from-purple-600 hover:to-fuchsia-700 text-white font-bold rounded-lg transition-all duration-200 text-sm">
                                                {{ __('Забрать') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-purple-500 to-fuchsia-600"></div>
                        </div>

                        <!-- Card 4: Total Deposits -->
                        <div class="bg-[#1e2329] rounded-xl shadow-xl overflow-hidden border border-gray-800 hover:border-[#ffb300]/50 transition-all duration-300 transform hover:scale-105">
                            <div class="p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-[#ffb300] to-[#e6a000] rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-white">{{ __('Депозиты') }}</h3>
                                </div>
                                <div class="text-2xl md:text-3xl font-bold text-[#ffb300] mb-1">
                                    {{ moneyFormat($totalDeposits) }} <small class="text-base">{{ $userCurrency }}</small>
                                </div>
                                <p class="text-gray-500 text-xs">{{ __('Суммарно') }}</p>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-[#ffb300] to-[#e6a000]"></div>
                        </div>
                    </div>
                </div>

                <!-- Referrals Tab -->
                <div x-show="activeTab === 'referrals'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0">

                    <div class="bg-[#1e2329] rounded-2xl shadow-xl overflow-hidden border border-gray-800">
                        <div class="px-6 md:px-8 py-6 border-b border-gray-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-[#ffb300] to-[#e6a000] rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl md:text-2xl font-bold text-white">{{ __('Мои рефералы') }} <span class="text-[#ffb300]">({{ $referralsCount }})</span></h2>
                            </div>
                        </div>

                        @if($referrals->isEmpty())
                            <div class="flex flex-col items-center justify-center py-16 px-4">
                                <div class="w-20 h-20 md:w-24 md:h-24 bg-gray-800 rounded-xl flex items-center justify-center mb-6">
                                    <svg class="h-10 w-10 md:h-12 md:w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-white text-lg md:text-xl font-bold mb-2 text-center">{{ __('У вас пока нет рефералов') }}</h3>
                                <p class="text-gray-400 text-center mb-6 max-w-md text-sm md:text-base">{{ __('Поделитесь своей реферальной ссылкой с друзьями и начните зарабатывать') }}</p>
                                <button @click="activeTab = 'overview'"
                                        class="px-6 py-3 bg-[#ffb300] hover:bg-[#e6a000] text-black font-bold rounded-xl transition-all duration-200">
                                    {{ __('Получить ссылку') }}
                                </button>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-[#252a32] border-b border-gray-800">
                                            <th class="px-4 md:px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">ID</th>
                                            <th class="px-4 md:px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('Username') }}</th>
                                            <th class="px-4 md:px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider hidden md:table-cell">Email</th>
                                            <th class="px-4 md:px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                                <a href="{{ route('account.referrals', ['sort_by' => 'created_at', 'sort_order' => ($sortBy == 'created_at' && $sortOrder == 'asc') ? 'desc' : 'asc']) }}"
                                                   class="flex items-center hover:text-[#ffb300] transition-colors">
                                                    {{ __('Дата') }}
                                                    @if ($sortBy == 'created_at')
                                                        <span class="ml-1 text-[#ffb300]">{!! $sortOrder == 'asc' ? '↑' : '↓' !!}</span>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="px-4 md:px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                                <a href="{{ route('account.referrals', ['sort_by' => 'from_ref', 'sort_order' => ($sortBy == 'from_ref' && $sortOrder == 'asc') ? 'desc' : 'asc']) }}"
                                                   class="flex items-center hover:text-[#ffb300] transition-colors">
                                                    {{ __('Доход') }}
                                                    @if ($sortBy == 'from_ref')
                                                        <span class="ml-1 text-[#ffb300]">{!! $sortOrder == 'asc' ? '↑' : '↓' !!}</span>
                                                    @endif
                                                </a>
                                            </th>
                                            <th class="px-4 md:px-8 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider hidden lg:table-cell">
                                                <a href="{{ route('account.referrals', ['sort_by' => 'total_deposits', 'sort_order' => ($sortBy == 'total_deposits' && $sortOrder == 'asc') ? 'desc' : 'asc']) }}"
                                                   class="flex items-center hover:text-[#ffb300] transition-colors">
                                                    {{ __('Депозиты') }}
                                                    @if ($sortBy == 'total_deposits')
                                                        <span class="ml-1 text-[#ffb300]">{!! $sortOrder == 'asc' ? '↑' : '↓' !!}</span>
                                                    @endif
                                                </a>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-800">
                                        @foreach($referrals as $referral)
                                            <tr class="hover:bg-gray-800/30 transition-colors">
                                                <td class="px-4 md:px-8 py-4 text-sm text-gray-400 font-mono">{{ $referral->id }}</td>
                                                <td class="px-4 md:px-8 py-4">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-[#ffb300] to-[#e6a000] flex items-center justify-center text-black font-bold text-sm overflow-hidden">
                                                            @if($referral->avatar)
                                                                <img src="{{ $referral->avatar }}" alt="{{ $referral->username }}" class="w-full h-full object-cover">
                                                            @else
                                                                {{ substr($referral->username, 0, 1) }}
                                                            @endif
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-semibold text-white">{{ $referral->username }}</div>
                                                            <div class="text-xs text-gray-500 md:hidden">{{ $referral->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 md:px-8 py-4 text-sm text-gray-400 hidden md:table-cell">{{ $referral->email }}</td>
                                                <td class="px-4 md:px-8 py-4 text-sm text-gray-400 font-mono">{{ $referral->created_at->format('d.m.Y') }}</td>
                                                <td class="px-4 md:px-8 py-4 text-sm font-bold text-emerald-400">
                                                    {{ moneyFormat(toUSD($referral->from_ref, $u->currency->symbol)) }} <small>{{ $u->currency->symbol }}</small>
                                                </td>
                                                <td class="px-4 md:px-8 py-4 text-sm text-white font-semibold hidden lg:table-cell">
                                                    {{ moneyFormat(toUSD($referral->total_deposits_usd, $u->currency->symbol)) }} <small>{{ $u->currency->symbol }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="px-6 md:px-8 py-6 border-t border-gray-800">
                                {{ $referrals->appends(['sort_by' => $sortBy, 'sort_order' => $sortOrder])->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            const notification = document.createElement('div');
            notification.className = 'fixed top-6 right-6 bg-gradient-to-r from-[#ffb300] to-[#e6a000] text-black px-6 py-4 rounded-xl shadow-2xl transform transition-all duration-500 z-50 flex items-center';

            notification.innerHTML = `
                <div class="mr-3 bg-white/20 rounded-lg p-2">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="font-bold">{{ __("Успешно!") }}</div>
                    <div class="text-sm opacity-90">{{ __("Ссылка скопирована") }}</div>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        });
    }
    </script>
</x-layouts.app>
