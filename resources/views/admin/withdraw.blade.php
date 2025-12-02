@extends('panel')
@php
    $baseUrl = 'betrika';
    use App\Enums\PaymentStatus;
@endphp

@section('content')

<style>
/* Custom styles for withdrawals page */
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.filter-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
}

.filters-container {
    background: white;
    dark:bg-gray-800;
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
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

.nav-tabs-custom {
    background: #f8fafc;
    padding: 8px;
    border-radius: 16px;
    display: flex;
    gap: 4px;
    margin-bottom: 24px;
    border: none;
}

.dark .nav-tabs-custom {
    background: #374151;
}

.nav-tab-custom {
    flex: 1;
    padding: 12px 20px;
    text-align: center;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    color: #6b7280;
    transition: all 0.3s ease;
    position: relative;
}

.nav-tab-custom.active {
    background: white;
    color: #667eea;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.dark .nav-tab-custom.active {
    background: #1f2937;
    color: #667eea;
}

.nav-tab-custom:hover:not(.active) {
    color: #4b5563;
    text-decoration: none;
}

.withdrawals-table {
    width: 100%;
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e5e7eb;
}

.dark .withdrawals-table {
    background: #1f2937;
    border-color: #374151;
}

.withdrawals-table thead {
    background: linear-gradient(135deg, #f8fafc 0%, #e5e7eb 100%);
}

.dark .withdrawals-table thead {
    background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
}

.withdrawals-table th {
    padding: 20px 16px;
    font-weight: 700;
    color: #374151;
    border: none;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.dark .withdrawals-table th {
    color: #e5e7eb;
}

.withdrawals-table td {
    padding: 20px 16px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.dark .withdrawals-table td {
    border-bottom-color: #374151;
    color: #e5e7eb;
}

.withdrawals-table tbody tr:hover {
    background: #f8fafc;
}

.dark .withdrawals-table tbody tr:hover {
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
}

.dark .amount-display {
    color: #10b981;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.status-refunded {
    background: #fee2e2;
    color: #991b1b;
}

.payment-details {
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 13px;
    color: #374151;
    background: #f3f4f6;
    padding: 8px 12px;
    border-radius: 8px;
    max-width: 200px;
    word-break: break-all;
}

.dark .payment-details {
    background: #4b5563;
    color: #e5e7eb;
}

.verification-status {
    display: flex;
    align-items: center;
    gap: 8px;
}

.verified-badge {
    background: #d1fae5;
    color: #065f46;
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
}

.unverified-badge {
    background: #fee2e2;
    color: #991b1b;
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
}

.btn-action {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    margin: 2px;
    display: inline-block;
}

.btn-confirm {
    background: #10b981;
    color: white;
}

.btn-confirm:hover {
    background: #059669;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

.btn-return {
    background: #ef4444;
    color: white;
}

.btn-return:hover {
    background: #dc2626;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

.btn-verify {
    background: #f59e0b;
    color: white;
}

.btn-verify:hover {
    background: #d97706;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
}

.pending-count-badge {
    background: #fef3c7;
    color: #92400e;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.pending-count-badge:hover {
    background: #fde68a;
    transform: scale(1.05);
}

.linked-accounts-indicator {
    background: #fef3c7;
    border: 1px solid #fbbf24;
    color: #92400e;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
}

.linked-accounts-indicator:hover {
    background: #fde68a;
    transform: translateY(-1px);
}

.sort-header {
    cursor: pointer;
    user-select: none;
    position: relative;
    transition: all 0.3s ease;
}

.sort-header:hover {
    color: #667eea;
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

.modal-custom {
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
}

.modal-content-custom {
    background: white;
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.dark .modal-content-custom {
    background: #1f2937;
}

.verification-preview img {
    border-radius: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.verification-preview img:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .withdrawals-table {
        font-size: 12px;
    }

    .withdrawals-table th,
    .withdrawals-table td {
        padding: 12px 8px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
    }

    .filters-container {
        padding: 16px;
    }
}
</style>

<div class="space-y-6">
    <!-- Header with Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Управление выводами</h1>
                        <p class="text-gray-600 dark:text-gray-400">Обработка запросов на вывод средств</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
        <!-- Filters Section -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <button id="toggleSearch" class="filter-toggle">
                <i class="fas fa-filter mr-2"></i>
                Фильтры и поиск
            </button>

            <div id="searchForm" style="display: none;" class="filters-container mt-6">
                <form method="GET" action="{{ url('/'.$baseUrl.'/withdraw') }}">
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
                                @foreach($paymentSystems as $system)
                                    <option value="{{ $system->id }}" {{ request('payment_system') == $system->id ? 'selected' : '' }}>
                                        {{ $system->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="tab" value="{{ $activeTab }}">

                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="btn-apply">
                            <i class="fas fa-search mr-2"></i>
                            Применить фильтры
                        </button>
                        <a href="{{ url('/'.$baseUrl.'/withdraw?tab='.$activeTab) }}" class="btn-reset">
                            <i class="fas fa-times mr-2"></i>
                            Сбросить
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabs -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="nav-tabs-custom">
                <a href="{{ url('/'.$baseUrl.'/withdraw?tab=pending') }}"
                   class="nav-tab-custom {{ $activeTab == 'pending' ? 'active' : '' }}">
                    <i class="fas fa-clock mr-2"></i>
                    Ожидающие
                </a>
                <a href="{{ url('/'.$baseUrl.'/withdraw?tab=completed') }}"
                   class="nav-tab-custom {{ $activeTab == 'completed' ? 'active' : '' }}">
                    <i class="fas fa-check mr-2"></i>
                    Принятые
                </a>
                <a href="{{ url('/'.$baseUrl.'/withdraw?tab=refunded') }}"
                   class="nav-tab-custom {{ $activeTab == 'refunded' ? 'active' : '' }}">
                    <i class="fas fa-times mr-2"></i>
                    Отклоненные
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="withdrawals-table">
                <thead>
                    <tr>
                        <th class="sort-header {{ $sort === 'bonuses' ? ($direction === 'asc' ? 'sort-asc' : 'sort-desc') : '' }}"
                            onclick="updateSort('bonuses')">
                            Пользователь
                            @if($sort === 'bonuses')
                                <i class="fa fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fa fa-sort"></i>
                            @endif
                        </th>
                        <th class="sort-header {{ $sort === 'amount' ? ($direction === 'asc' ? 'sort-asc' : 'sort-desc') : '' }}"
                            onclick="updateSort('amount')">
                            Сумма
                            @if($sort === 'amount')
                                <i class="fa fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fa fa-sort"></i>
                            @endif
                        </th>
                        <th class="sort-header {{ $sort === 'created_at' ? ($direction === 'asc' ? 'sort-asc' : 'sort-desc') : '' }}"
                            onclick="updateSort('created_at')">
                            Дата создания
                            @if($sort === 'created_at')
                                <i class="fa fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fa fa-sort"></i>
                            @endif
                        </th>
                        <th>Реквизиты</th>
                        <th>Система</th>
                        @if($u->is_admin)
                        <th>Верификация</th>
                        @endif
                        <th class="sort-header {{ $sort === 'duplicates' ? ($direction === 'asc' ? 'sort-asc' : 'sort-desc') : '' }}"
                            onclick="updateSort('duplicates')">
                            Связанные
                            @if($sort === 'duplicates')
                                <i class="fa fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="fa fa-sort"></i>
                            @endif
                        </th>
                        @if($activeTab == 'pending')
                        <th>Действия</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawals as $withdrawal)
                        <tr>
                            <td>
                                <a href="/{{$baseUrl}}/user/{{$withdrawal['user_id']}}" class="user-card">
                                    <img src="{{ $withdrawal['avatar'] }}"
                                         alt="{{ $withdrawal['username'] }}"
                                         class="user-avatar">
                                    <div class="user-info">
                                        <h6>{{ $withdrawal['username'] }}</h6>
                                        <div class="user-id">ID: {{ $withdrawal['user_id'] }}</div>
                                        @if($withdrawal['pending_count'] > 0)
                                            <span class="pending-count-badge user-pending-filter mt-1"
                                                  title="Кликните чтобы показать все ожидающие выплаты"
                                                  data-username="{{ $withdrawal['username'] }}">
                                                {{ $withdrawal['pending_count'] }} ожидает
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            </td>

                            <td>
                                <div class="amount-display">
                                    {{ $withdrawal['value'] }} {{ $withdrawal['currency'] }}
                                </div>
                            </td>

                            <td>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $withdrawal['created_at']->format('d.m.Y') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">
                                    {{ $withdrawal['created_at']->format('H:i:s') }}
                                </div>
                            </td>

                            <td>
                                <div class="payment-details">
                                    {{ $withdrawal['details'] ?: 'Не указано' }}
                                </div>
                            </td>

                            <td>
                                <div class="space-y-1">
                                    <span class="status-badge">
                                        {{ $withdrawal['system'] ?: 'Не указано' }}
                                    </span>
                                    
                                    {{-- DEBUG: показываем данные автовыплат --}}
                                    @if(isset($withdrawal['withdrawal_mode']) || isset($withdrawal['auto_enabled']))
                                        <div class="text-xs text-gray-500 mt-1">
                                            Mode: {{ $withdrawal['withdrawal_mode'] ?? 'N/A' }} | 
                                            Enabled: {{ $withdrawal['auto_enabled'] ? 'YES' : 'NO' }}
                                        </div>
                                    @endif
                                    
                                    @if($withdrawal['auto_enabled'] ?? false)
                                        <div class="flex items-center gap-1 mt-1">
                                            @if($withdrawal['withdrawal_mode'] === 'instant')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    <i class="fas fa-bolt text-xs mr-1"></i>
                                                    Мгновенная
                                                </span>
                                            @elseif($withdrawal['withdrawal_mode'] === 'semi_auto')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    <i class="fas fa-magic text-xs mr-1"></i>
                                                    Полуавтомат
                                                </span>
                                            @endif
                                            
                                            @if($withdrawal['auto_processed'])
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                    <i class="fas fa-robot text-xs mr-1"></i>
                                                    Авто
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>

                            @if($u->is_admin)
                            <td>
                                <div class="verification-status">
                                    @if($withdrawal['is_verified'])
                                        <span class="verified-badge">
                                            <i class="fas fa-check mr-1"></i>
                                            Верифицирован
                                        </span>
                                        @if($withdrawal['verification_docs'])
                                            <div class="verification-preview mt-2">
                                                <img src="{{ asset('storage/' . $withdrawal['verification_docs']['selfie']) }}"
                                                     class="w-16 h-12 object-cover rounded-lg cursor-pointer"
                                                     data-toggle="modal"
                                                     data-target="#docsModal-{{ $withdrawal['id'] }}"
                                                     alt="Документы">
                                            </div>
                                        @endif
                                    @else
                                        <span class="unverified-badge">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Не верифицирован
                                        </span>
                                    @endif
                                </div>

                                @if($withdrawal['verification_docs'])
                                <!-- Modal for verification docs -->
                                <div class="modal fade modal-custom" id="docsModal-{{ $withdrawal['id'] }}">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content modal-content-custom">
                                            <div class="modal-header">
                                                <h5>Документы {{ $withdrawal['username'] }}</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('storage/' . $withdrawal['verification_docs']['selfie']) }}"
                                                     class="img-fluid rounded-lg" alt="Документы">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                            @endif

                            <td>
                                @if($withdrawal['has_duplicates'])
                                    <div class="linked-accounts-indicator"
                                         onclick="showLinkedAccounts({{ json_encode($withdrawal['duplicate_users']) }})">
                                        <i class="fas fa-users"></i>
                                        <span>{{ count($withdrawal['duplicate_users']) }} аккаунт(ов)</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>

                            @if($activeTab == 'pending')
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <a href="/{{$baseUrl}}/withdraw/{{$withdrawal['id']}}" class="btn-action btn-confirm">
                                        <i class="fas fa-check mr-1"></i>
                                        Подтвердить
                                    </a>
                                    <a href="/{{$baseUrl}}/return/{{$withdrawal['id']}}" class="btn-action btn-return">
                                        <i class="fas fa-times mr-1"></i>
                                        Вернуть
                                    </a>
                                    @if(!$withdrawal['is_verified'] && ($u->is_admin))
                                        <a href="/{{$baseUrl}}/verify/{{$withdrawal['id']}}" class="btn-action btn-verify">
                                            <i class="fas fa-user-check mr-1"></i>
                                            Верификация
                                        </a>
                                    @endif
                                    @if($u->is_admin)
                                        <a href="/{{$baseUrl}}/wban/{{$withdrawal['id']}}"
                                           class="btn-action"
                                           style="background: #d946ef; color: white;"
                                           onmouseover="this.style.background='#c026d3'"
                                           onmouseout="this.style.background='#d946ef'">
                                            <i class="fas fa-ban mr-1"></i>
                                            В бан
                                        </a>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                    @endforeach

                    @if($withdrawals->isEmpty())
                        <tr>
                            <td colspan="{{ $activeTab == 'pending' ? ($u->is_admin ? 8 : 7) : ($u->is_admin ? 7 : 6) }}" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <div class="text-lg font-semibold mb-2">Нет выводов</div>
                                <div class="text-sm">По текущим фильтрам не найдено ни одного вывода</div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($withdrawals->hasPages())
        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            {{ $withdrawals->appends(['tab' => $activeTab])->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Linked Accounts Modal -->
<div id="linkedAccountsModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-lg w-full shadow-2xl">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Связанные аккаунты</h3>
                    <button onclick="closeLinkedModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                <div id="linkedAccountsList" class="space-y-3"></div>
            </div>
        </div>
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
    const isFormVisible = localStorage.getItem('withdrawalFilterFormVisible') === 'true';
    searchForm.style.display = isFormVisible ? 'block' : 'none';

    toggleSearch.addEventListener('click', function() {
        const newState = searchForm.style.display === 'none';
        searchForm.style.display = newState ? 'block' : 'none';
        localStorage.setItem('withdrawalFilterFormVisible', newState);
    });

    // User pending filter
    document.querySelectorAll('.user-pending-filter').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const username = this.dataset.username;
            const urlParams = new URLSearchParams(window.location.search);

            urlParams.set('tab', 'pending');
            urlParams.set('search', username);
            urlParams.delete('page');
            urlParams.delete('amount_from');
            urlParams.delete('amount_to');
            urlParams.delete('date_from');
            urlParams.delete('date_to');
            urlParams.delete('payment_system');

            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
        });
    });
});

// Linked accounts modal
function showLinkedAccounts(accounts) {
    if (!Array.isArray(accounts)) {
        console.error('Accounts is not an array:', accounts);
        return;
    }

    const modal = document.getElementById('linkedAccountsModal');
    const listContainer = document.getElementById('linkedAccountsList');

    let html = '';

    try {
        accounts.forEach(account => {
            html += `
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <img src="${account.avatar || '/default-avatar.png'}"
                         class="w-10 h-10 rounded-lg object-cover"
                         alt="${account.username}">
                    <div class="flex-1">
                        <a href="/betrika/user/${account.user_id}"
                           class="font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400"
                           target="_blank">
                            ${account.username}
                        </a>
                        <div class="text-xs text-gray-500 font-mono">ID: ${account.user_id}</div>
                    </div>
                    <i class="fas fa-external-link-alt text-gray-400"></i>
                </div>
            `;
        });

        listContainer.innerHTML = html;
        modal.classList.remove('hidden');
    } catch (error) {
        console.error('Error rendering accounts:', error);
    }
}

function closeLinkedModal() {
    document.getElementById('linkedAccountsModal').classList.add('hidden');
}

// Close modal on outside click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('linkedAccountsModal');
    if (e.target === modal) {
        closeLinkedModal();
    }
});
</script>

@endsection
