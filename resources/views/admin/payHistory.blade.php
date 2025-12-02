@extends('panel')
@php $baseUrl = 'betrika'; @endphp

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --danger-gradient: linear-gradient(135deg, #fd746c 0%, #ff9068 100%);
}

.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.dark .glass-card {
    background: rgba(17, 24, 39, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.slide-in {
    animation: slideIn 0.6s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.tab-button {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.tab-button::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 3px;
    background: var(--primary-gradient);
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.tab-button.active::before {
    width: 100%;
}

.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}
</style>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50 dark:from-gray-900 dark:via-blue-900 dark:to-purple-900 p-6">
    <!-- Header -->
    <div class="glass-card rounded-2xl p-6 mb-8 slide-in">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">История платежей</h1>
                    <p class="text-gray-600 dark:text-gray-400 font-medium">Пополнения и выводы средств</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="glass-card rounded-2xl overflow-hidden slide-in mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <div class="flex gap-2 p-4">
                <button onclick="showTab('deposits')" 
                        class="tab-button active px-6 py-3 rounded-xl font-semibold text-sm bg-gradient-to-r from-green-600 to-emerald-600 text-white"
                        data-tab="deposits">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                        <span>Пополнения</span>
                    </div>
                </button>
                
                <button onclick="showTab('withdrawals')" 
                        class="tab-button px-6 py-3 rounded-xl font-semibold text-sm bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600"
                        data-tab="withdrawals">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                        </svg>
                        <span>Выводы</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Deposits Table -->
    <div id="deposits-content" class="tab-content glass-card rounded-2xl overflow-hidden slide-in">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Тип</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Сумма</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Баланс до</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Баланс после</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Дата</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Хэш</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pays as $pay)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors hover-lift">
                            <td class="py-4 px-4">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                    💰 Пополнение
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-green-600 dark:text-green-400">
                                    +{{ $pay->amount }} {{ $pay->currency->symbol }}
                                </div>
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                {{ $pay->context['balance_before'] ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                {{ $pay->context['balance_after'] ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $pay->created_at ? $pay->created_at->format('d.m.Y H:i') : '-' }}
                            </td>
                            <td class="py-4 px-4">
                                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-600 dark:text-gray-400">
                                    {{ substr($pay->hash, 0, 16) }}...
                                </code>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Показано {{ $pays->firstItem() }} - {{ $pays->lastItem() }} из {{ $pays->total() }}
                </div>
                <div class="flex gap-2">
                    @if ($pays->onFirstPage())
                        <span class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed">
                            ← Назад
                        </span>
                    @else
                        <a href="{{ $pays->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            ← Назад
                        </a>
                    @endif

                    @for ($i = 1; $i <= $pays->lastPage(); $i++)
                        @if ($i == $pays->currentPage())
                            <span class="px-4 py-2 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $pays->url($i) }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    @if ($pays->hasMorePages())
                        <a href="{{ $pays->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            Вперёд →
                        </a>
                    @else
                        <span class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed">
                            Вперёд →
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div id="withdrawals-content" class="tab-content hidden glass-card rounded-2xl overflow-hidden slide-in">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Тип</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Сумма</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Баланс до</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Баланс после</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Дата</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Хэш</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdraws as $withs)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors hover-lift">
                            <td class="py-4 px-4">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                    💸 Вывод
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-red-600 dark:text-red-400">
                                    -{{ $withs->amount }} {{ $withs->currency->symbol }}
                                </div>
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                {{ $withs->context['balance_before'] ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                {{ $withs->context['balance_after'] ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $withs->created_at ? $withs->created_at->format('d.m.Y H:i') : '-' }}
                            </td>
                            <td class="py-4 px-4">
                                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-600 dark:text-gray-400">
                                    {{ substr($withs->hash, 0, 16) }}...
                                </code>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Показано {{ $withdraws->firstItem() }} - {{ $withdraws->lastItem() }} из {{ $withdraws->total() }}
                </div>
                <div class="flex gap-2">
                    @if ($withdraws->onFirstPage())
                        <span class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed">
                            ← Назад
                        </span>
                    @else
                        <a href="{{ $withdraws->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            ← Назад
                        </a>
                    @endif

                    @for ($i = 1; $i <= $withdraws->lastPage(); $i++)
                        @if ($i == $withdraws->currentPage())
                            <span class="px-4 py-2 rounded-lg bg-gradient-to-r from-red-600 to-orange-600 text-white font-semibold">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $withdraws->url($i) }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    @if ($withdraws->hasMorePages())
                        <a href="{{ $withdraws->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            Вперёд →
                        </a>
                    @else
                        <span class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed">
                            Вперёд →
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Скрываем все контенты
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Убираем активный класс у всех кнопок
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('bg-gradient-to-r', 'from-green-600', 'to-emerald-600', 'from-red-600', 'to-orange-600', 'text-white', 'active');
        button.classList.add('bg-white', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
    });
    
    // Показываем выбранный контент
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Активируем выбранную кнопку
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    activeButton.classList.remove('bg-white', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
    
    if (tabName === 'deposits') {
        activeButton.classList.add('bg-gradient-to-r', 'from-green-600', 'to-emerald-600', 'text-white', 'active');
    } else {
        activeButton.classList.add('bg-gradient-to-r', 'from-red-600', 'to-orange-600', 'text-white', 'active');
    }
}
</script>
@endsection
