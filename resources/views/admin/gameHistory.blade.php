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
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-purple-500 to-blue-600 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">История игр</h1>
                    <p class="text-gray-600 dark:text-gray-400 font-medium">Полная история ставок и выигрышей</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="glass-card rounded-2xl overflow-hidden slide-in mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <div class="flex gap-2 p-4">
                <button onclick="showTab('bets')" 
                        class="tab-button active px-6 py-3 rounded-xl font-semibold text-sm bg-gradient-to-r from-blue-600 to-purple-600 text-white"
                        data-tab="bets">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/>
                            <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/>
                            <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"/>
                        </svg>
                        <span>Все ставки</span>
                    </div>
                </button>
                
                <button onclick="showTab('wins')" 
                        class="tab-button px-6 py-3 rounded-xl font-semibold text-sm bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600"
                        data-tab="wins">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>Выигрыши</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Bets Table -->
    <div id="bets-content" class="tab-content glass-card rounded-2xl overflow-hidden slide-in">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Игра</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Сумма</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Баланс до</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Баланс после</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Дата</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Хэш</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bets as $bet)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors hover-lift">
                            <td class="py-4 px-4">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $bet->context['description'] ?? 'Описание отсутствует' }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                    {{ $bet->amount }} {{ $bet->currency->symbol }}
                                </div>
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                {{ $bet->context['balance_before'] ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                {{ $bet->context['balance_after'] ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $bet->created_at ? $bet->created_at->format('d.m.Y H:i') : '-' }}
                            </td>
                            <td class="py-4 px-4">
                                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-600 dark:text-gray-400">
                                    {{ substr($bet->hash, 0, 16) }}...
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
                    Показано {{ $bets->firstItem() }} - {{ $bets->lastItem() }} из {{ $bets->total() }}
                </div>
                <div class="flex gap-2">
                    @if ($bets->onFirstPage())
                        <span class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed">
                            ← Назад
                        </span>
                    @else
                        <a href="{{ $bets->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            ← Назад
                        </a>
                    @endif

                    @for ($i = 1; $i <= $bets->lastPage(); $i++)
                        @if ($i == $bets->currentPage())
                            <span class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $bets->url($i) }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    @if ($bets->hasMorePages())
                        <a href="{{ $bets->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
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

    <!-- Wins Table -->
    <div id="wins-content" class="tab-content hidden glass-card rounded-2xl overflow-hidden slide-in">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Игра</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Выигрыш</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Баланс до</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Баланс после</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Дата</th>
                            <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Хэш</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($win as $wins)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors hover-lift">
                            <td class="py-4 px-4">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $wins->context['description'] ?? 'Описание отсутствует' }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                    🎉 {{ $wins->amount }} {{ $wins->currency->symbol }}
                                </div>
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                {{ $wins->context['balance_before'] ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-gray-600 dark:text-gray-400">
                                {{ $wins->context['balance_after'] ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $wins->created_at ? $wins->created_at->format('d.m.Y H:i') : '-' }}
                            </td>
                            <td class="py-4 px-4">
                                <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-gray-600 dark:text-gray-400">
                                    {{ substr($wins->hash, 0, 16) }}...
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
                    Показано {{ $win->firstItem() }} - {{ $win->lastItem() }} из {{ $win->total() }}
                </div>
                <div class="flex gap-2">
                    @if ($win->onFirstPage())
                        <span class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed">
                            ← Назад
                        </span>
                    @else
                        <a href="{{ $win->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            ← Назад
                        </a>
                    @endif

                    @for ($i = 1; $i <= $win->lastPage(); $i++)
                        @if ($i == $win->currentPage())
                            <span class="px-4 py-2 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $win->url($i) }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    @if ($win->hasMorePages())
                        <a href="{{ $win->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
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
        button.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-purple-600', 'text-white', 'active');
        button.classList.add('bg-white', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
    });
    
    // Показываем выбранный контент
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Активируем выбранную кнопку
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    activeButton.classList.remove('bg-white', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
    activeButton.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-purple-600', 'text-white', 'active');
}
</script>
@endsection
