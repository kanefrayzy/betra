@extends('panel')
@php $baseUrl = 'qwdkox1i20'; @endphp

@section('content')

<style>
/* Reusing styles from withdrawals page with deposits-specific adjustments */
.stats-card {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 16px;
    padding: 24px;
    color: white;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.filter-toggle {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.filter-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    color: white;
}

.filters-container {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin: 24px 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
}

.dark .filters-container {
    background: #1f2937;
    border-color: #374151;
}

.form-group label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    display: block;
}

.dark .form-group label {
    color: #e5e7eb;
}

.form-control {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.dark .form-control {
    background: #374151;
    border-color: #4b5563;
    color: white;
}

.form-control:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    outline: none;
}

.btn-apply {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-apply:hover {
    transform: translateY(-2px);
    color: white;
}

.btn-reset {
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    color: #6b7280;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-reset:hover {
    background: #e5e7eb;
    color: #4b5563;
    text-decoration: none;
}

.deposits-table {
    width: 100%;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
}

.dark .deposits-table {
    background: #1f2937;
    border-color: #374151;
}

.deposits-table thead {
    background: linear-gradient(135deg, #f8fafc 0%, #e5e7eb 100%);
}

.dark .deposits-table thead {
    background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
}

.deposits-table th {
    padding: 20px 16px;
    font-weight: 700;
    color: #374151;
    border: none;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
    position: relative;
}

.dark .deposits-table th {
    color: #e5e7eb;
}

.deposits-table td {
    padding: 20px 16px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.dark .deposits-table td {
    border-bottom-color: #374151;
    color: #e5e7eb;
}

.deposits-table tbody tr:hover {
    background: #f8fafc;
}

.dark .deposits-table tbody tr:hover {
    background: #374151;
}

.user-card {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: inherit;
}

.user-card:hover {
    text-decoration: none;
    color: inherit;
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid #e5e7eb;
}

.user-info h6 {
    margin: 0;
    font-weight: 700;
    color: #1f2937;
    font-size: 14px;
}

.dark .user-info h6 {
    color: #f9fafb;
}

.user-info .user-id {
    font-size: 12px;
    color: #6b7280;
    font-family: 'Monaco', 'Menlo', monospace;
}

.amount-display {
    font-weight: 800;
    font-size: 16px;
    color: #059669;
    background: #ecfdf5;
    padding: 8px 12px;
    border-radius: 8px;
    display: inline-block;
}

.dark .amount-display {
    color: #10b981;
    background: #064e3b;
}

.payment-system-badge {
    background: #f3f4f6;
    color: #374151;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.dark .payment-system-badge {
    background: #4b5563;
    color: #e5e7eb;
}

.date-display {
    color: #6b7280;
}

.id-badge {
    background: #eff6ff;
    color: #1d4ed8;
    padding: 4px 8px;
    border-radius: 6px;
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 12px;
    font-weight: 600;
}

.dark .id-badge {
    background: #1e3a8a;
    color: #93c5fd;
}

.sort-header {
    cursor: pointer;
    user-select: none;
    transition: all 0.3s ease;
}

.sort-header:hover {
    color: #10b981;
}

.sort-header i {
    margin-left: 8px;
    opacity: 0.7;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.pagination-custom {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 24px;
}

.pagination-custom a {
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    color: #6b7280;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.pagination-custom a:hover:not(.disabled) {
    background: #f3f4f6;
    color: #374151;
    text-decoration: none;
}

.pagination-custom a.active {
    background: #10b981;
    color: white;
    border-color: #10b981;
}

.pagination-custom a.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.stat-item {
    background: white;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
    position: relative;
    overflow: hidden;
}

.dark .stat-item {
    background: #1f2937;
    border-color: #374151;
}

.stat-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: #10b981;
    margin-bottom: 8px;
}

.dark .stat-value {
    color: #34d399;
}

.stat-label {
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

@media (max-width: 768px) {
    .deposits-table {
        font-size: 12px;
    }

    .deposits-table th,
    .deposits-table td {
        padding: 12px 8px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
    }

    .filters-container {
        padding: 16px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Управление пополнениями</h1>
                <p class="text-gray-600 dark:text-gray-400">Мониторинг и анализ входящих платежей</p>
            </div>
            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value">{{ $payments->total() }}</div>
            <div class="stat-label">Всего пополнений</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ number_format($payments->sum('amount'), 2) }}</div>
            <div class="stat-label">Общая сумма</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $payments->where('created_at', '>=', now()->startOfDay())->count() }}</div>
            <div class="stat-label">Сегодня</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
        <!-- Filters Section -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <button id="toggleSearch" class="filter-toggle">
                <i class="fas fa-search mr-2"></i>
                Поиск и фильтры
            </button>

            <div id="searchForm" style="display: none;" class="filters-container mt-6">
                <form method="GET" action="{{ url('/'.$baseUrl.'/inserts') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="form-group">
                            <label for="search">Поиск по логину</label>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Введите имя пользователя">
                        </div>

                        <div class="form-group">
                            <label for="amount_from">Сумма от</label>
                            <input type="number"
                                   name="amount_from"
                                   id="amount_from"
                                   value="{{ request('amount_from') }}"
                                   class="form-control"
                                   placeholder="0">
                        </div>

                        <div class="form-group">
                            <label for="amount_to">Сумма до</label>
                            <input type="number"
                                   name="amount_to"
                                   id="amount_to"
                                   value="{{ request('amount_to') }}"
                                   class="form-control"
                                   placeholder="Без ограничений">
                        </div>

                        <div class="form-group">
                            <label for="date_from">Дата от</label>
                            <input type="date"
                                   name="date_from"
                                   id="date_from"
                                   value="{{ request('date_from') }}"
                                   class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="date_to">Дата до</label>
                            <input type="date"
                                   name="date_to"
                                   id="date_to"
                                   value="{{ request('date_to') }}"
                                   class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="payment_system">Платежная система</label>
                            <select name="payment_system" id="payment_system" class="form-control">
                                <option value="">Все системы</option>
                                <option value="card" {{ request('payment_system') == 'card' ? 'selected' : '' }}>Банковская карта</option>
                                <option value="wallet" {{ request('payment_system') == 'wallet' ? 'selected' : '' }}>Электронный кошелек</option>
                                <option value="crypto" {{ request('payment_system') == 'crypto' ? 'selected' : '' }}>Криптовалюта</option>
                                <option value="bank" {{ request('payment_system') == 'bank' ? 'selected' : '' }}>Банковский перевод</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="btn-apply">
                            <i class="fas fa-search mr-2"></i>
                            Применить фильтры
                        </button>
                        <a href="{{ url('/'.$baseUrl.'/inserts') }}" class="btn-reset">
                            <i class="fas fa-times mr-2"></i>
                            Сбросить
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="deposits-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th class="sort-header" onclick="updateSort('amount')">
                            Сумма
                            @if ($sort == 'amount')
                                @if ($direction == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort"></i>
                            @endif
                        </th>
                        <th class="sort-header" onclick="updateSort('created_at')">
                            Дата создания
                            @if ($sort == 'created_at')
                                @if ($direction == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort"></i>
                            @endif
                        </th>
                        <th>Платежная система</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>
                                <span class="id-badge">#{{ $payment->id }}</span>
                            </td>

                            <td>
                                <a href="/{{$baseUrl}}/user/{{$payment->user_id}}" class="user-card" target="_blank">
                                    <img src="{{ $payment->avatar }}"
                                         alt="{{ $payment->username }}"
                                         class="user-avatar">
                                    <div class="user-info">
                                        <h6>{{ $payment->username }}</h6>
                                        <div class="user-id">ID: {{ $payment->user_id }}</div>
                                    </div>
                                </a>
                            </td>

                            <td>
                                <div class="amount-display">
                                    {{ $payment->amount }} {{ $payment->currency_symbol }}
                                </div>
                            </td>

                            <td>
                                <div class="date-display">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($payment->created_at)->format('d.m.Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($payment->created_at)->format('H:i:s') }}
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="payment-system-badge">
                                    {{ $payment->payment_handler ?: 'Не указано' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach

                    @if($payments->isEmpty())
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fas fa-credit-card"></i>
                                <div class="text-lg font-semibold mb-2">Нет пополнений</div>
                                <div class="text-sm">По текущим фильтрам не найдено ни одного пополнения</div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($u->is_admin && $payments->hasPages())
        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            <div class="pagination-custom">
                @if ($payments->onFirstPage())
                    <a class="disabled">&laquo; Назад</a>
                @else
                    <a href="{{ $payments->previousPageUrl() }}">&laquo; Назад</a>
                @endif

                @for ($i = 1; $i <= $payments->lastPage(); $i++)
                    @if ($i == $payments->currentPage())
                        <a class="active">{{ $i }}</a>
                    @else
                        <a href="{{ $payments->url($i) }}">{{ $i }}</a>
                    @endif
                @endfor

                @if ($payments->hasMorePages())
                    <a href="{{ $payments->nextPageUrl() }}">Вперёд &raquo;</a>
                @else
                    <a class="disabled">Вперёд &raquo;</a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<script>
// Sort functionality
function updateSort(column) {
    const urlParams = new URLSearchParams(window.location.search);
    const currentSort = urlParams.get('sort');
    const currentDirection = urlParams.get('direction');

    let newDirection = 'asc';
    if (currentSort === column && currentDirection === 'asc') {
        newDirection = 'desc';
    }

    urlParams.set('sort', column);
    urlParams.set('direction', newDirection);

    window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
}

// Toggle search form
document.addEventListener('DOMContentLoaded', function() {
    const toggleSearch = document.getElementById('toggleSearch');
    const searchForm = document.getElementById('searchForm');

    // Restore form state from localStorage
    const isFormVisible = localStorage.getItem('depositsFilterFormVisible') === 'true';
    searchForm.style.display = isFormVisible ? 'block' : 'none';

    toggleSearch.addEventListener('click', function() {
        const newState = searchForm.style.display === 'none';
        searchForm.style.display = newState ? 'block' : 'none';
        localStorage.setItem('depositsFilterFormVisible', newState);
    });
});

// Auto-hide filters after successful search
if (new URLSearchParams(window.location.search).get('search')) {
    localStorage.setItem('depositsFilterFormVisible', 'true');
    document.getElementById('searchForm').style.display = 'block';
}
</script>

@endsection
