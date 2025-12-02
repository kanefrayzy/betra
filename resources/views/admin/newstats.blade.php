@extends('panel')
@php $baseUrl = 'panel8808'; @endphp

@section('content')
<style>
    .statistic-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    .statistic-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    .statistic-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #333;
    }
    .statistic-value {
        font-size: 24px;
        font-weight: bold;
        color: #00a368;
    }
    .statistic-label {
        font-size: 14px;
        color: #6c757d;
    }
    .profit-card {
        background-color: #e8f5e9;
        border-left: 5px solid #00a368;
    }
    .profit-value {
        color: #00a368;
        font-size: 28px;
    }
    .nav-tabs {
        border-bottom: 2px solid #00a368;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #495057;
        font-weight: 500;
        padding: 10px 15px;
    }
    .nav-tabs .nav-link.active {
        color: #00a368;
        background-color: transparent;
        border-bottom: 2px solid #00a368;
    }
</style>

<div class="container mt-4">
    <h2 class="mb-4">Общая статистика по валютам (с 12 декабря)</h2>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="statistic-card profit-card">
                <div class="statistic-title">Общий профит за сегодня</div>
                <div class="statistic-value profit-value">${{ number_format($payTodayInUSD - $withTodayInUSD, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="statistic-card profit-card">
                <div class="statistic-title">Общий профит за вчера</div>
                <div class="statistic-value profit-value">${{ number_format($payYesterdayInUSD - $withYesterdayInUSD, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="statistic-card profit-card">
                <div class="statistic-title">Общий профит за все время</div>
                <div class="statistic-value profit-value">${{ number_format($totalDepositsInUSD - $totalWithdrawalsInUSD, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="statistic-card">
                <div class="statistic-title">Общая сумма пополнений</div>
                <div class="statistic-value">${{ number_format($totalDepositsInUSD, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="statistic-card">
                <div class="statistic-title">Общая сумма выводов</div>
                <div class="statistic-value">${{ number_format($totalWithdrawalsInUSD, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="statistic-card">
                <div class="statistic-title">Пополнения сегодня</div>
                <div class="statistic-value">${{ number_format($payTodayInUSD, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="statistic-card">
                <div class="statistic-title">Пополнения вчера</div>
                <div class="statistic-value">${{ number_format($payYesterdayInUSD, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="statistic-card">
                <div class="statistic-title">Пополнения за неделю</div>
                <div class="statistic-value">${{ number_format($payWeekInUSD, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="statistic-card">
                <div class="statistic-title">Пополнения за месяц</div>
                <div class="statistic-value">${{ number_format($payMonthInUSD, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <!-- Статистика за сегодня -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Статистика модераторов за сегодня</h4>
                    <span class="text-muted">{{ now()->format('d.m.Y') }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Модератор</th>
                                    <th class="text-center">Выплаты</th>
                                    <th class="text-center">Сумма выплат</th>
                                    <th class="text-center">Отклонено</th>
                                    <th class="text-center">Сумма отклоненных</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($moderatorStatsToday as $stat)
                                <tr>
                                    <td>
                                        <div class="moderator-info">
                                            <div class="moderator-avatar">
                                                {{ strtoupper(substr($stat->username, 0, 2)) }}
                                            </div>
                                            <span class="moderator-name">{{ $stat->username }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="stat-box completed">
                                            <div class="stat-value">{{ $stat->completed_count }}</div>
                                            <div class="stat-label">выплат</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="amount-box completed">
                                            {{ number_format($stat->completed_amount, 2) }} ₼
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="stat-box rejected">
                                            <div class="stat-value">{{ $stat->rejected_count }}</div>
                                            <div class="stat-label">отклонено</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="amount-box rejected">
                                            {{ number_format($stat->rejected_amount, 2) }} ₼
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Нет данных за сегодня
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Статистика за вчера -->
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Статистика модераторов за вчера</h4>
                <span class="text-muted">{{ now()->subDay()->format('d.m.Y') }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>Модератор</th>
                                <th class="text-center">Выплаты</th>
                                <th class="text-center">Сумма выплат</th>
                                <th class="text-center">Отклонено</th>
                                <th class="text-center">Сумма отклоненных</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($moderatorStatsYesterday as $stat)
                            <tr>
                                <td>
                                    <div class="moderator-info">
                                        <div class="moderator-avatar">
                                            {{ strtoupper(substr($stat->username, 0, 2)) }}
                                        </div>
                                        <span class="moderator-name">{{ $stat->username }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="stat-box completed">
                                        <div class="stat-value">{{ $stat->completed_count }}</div>
                                        <div class="stat-label">выплат</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="amount-box completed">
                                        {{ number_format($stat->completed_amount, 2) }} ₼
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="stat-box rejected">
                                        <div class="stat-value">{{ $stat->rejected_count }}</div>
                                        <div class="stat-label">отклонено</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="amount-box rejected">
                                        {{ number_format($stat->rejected_amount, 2) }} ₼
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Нет данных за вчера
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

            <!-- Статистика за все время -->
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Общая статистика модераторов</h4>
                    <span class="text-muted">За все время</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Модератор</th>
                                    <th class="text-center">Выплаты</th>
                                    <th class="text-center">Сумма выплат</th>
                                    <th class="text-center">Отклонено</th>
                                    <th class="text-center">Сумма отклоненных</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($moderatorStats as $stat)
                                <tr>
                                    <td>
                                        <div class="moderator-info">
                                            <div class="moderator-avatar">
                                                {{ strtoupper(substr($stat->username, 0, 2)) }}
                                            </div>
                                            <span class="moderator-name">{{ $stat->username }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="stat-box completed">
                                            <div class="stat-value">{{ $stat->completed_count }}</div>
                                            <div class="stat-label">выплат</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="amount-box completed">
                                            {{ number_format($stat->completed_amount, 2) }} ₼
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="stat-box rejected">
                                            <div class="stat-value">{{ $stat->rejected_count }}</div>
                                            <div class="stat-label">отклонено</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="amount-box rejected">
                                            {{ number_format($stat->rejected_amount, 2) }} ₼
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Нет данных
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(0,0,0,.08);
        border-radius: 12px;
    }

    .card-header {
        padding: 20px 25px;
    }

    .card-header h4 {
        color: #2d3748;
        font-weight: 600;
    }

    .custom-table {
        margin: 0;
    }

    .custom-table thead th {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        padding: 15px 20px;
        letter-spacing: 0.5px;
    }

    .custom-table tbody tr {
        transition: all 0.2s;
    }

    .custom-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .custom-table td {
        padding: 20px;
        border-top: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .moderator-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .moderator-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(45deg, #3b82f6, #2563eb);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
    }

    .moderator-name {
        font-weight: 500;
        color: #1e293b;
    }

    .stat-box {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        padding: 8px 16px;
        border-radius: 8px;
        min-width: 100px;
    }

    .stat-box.completed {
        background-color: #f0fdf4;
    }

    .stat-box.rejected {
        background-color: #fef2f2;
    }

    .stat-value {
        font-size: 18px;
        font-weight: 600;
    }

    .stat-box.completed .stat-value {
        color: #16a34a;
    }

    .stat-box.rejected .stat-value {
        color: #dc2626;
    }

    .stat-label {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .amount-box {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 15px;
    }

    .amount-box.completed {
        background-color: #f0fdf4;
        color: #16a34a;
    }

    .amount-box.rejected {
        background-color: #fef2f2;
        color: #dc2626;
    }

    .amount-box i {
        font-size: 12px;
    }
    /* Добавим стили для пустых данных */
    .text-muted {
        font-size: 13px;
    }

    .card + .card {
        margin-top: 2rem;
    }

    /* Анимация для статистики за сегодня */
    .stat-box {
        position: relative;
        overflow: hidden;
    }

    .stat-box::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
        transform: translateX(-100%);
        animation: shine 3s infinite;
    }

    @keyframes shine {
        to {
            transform: translateX(100%);
        }
    }


    </style>


@endsection
