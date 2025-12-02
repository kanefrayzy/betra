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
    }
    .statistic-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
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
</style>

<div class="row">
    <div class="col-md-12">
        <h2 class="text-center mb-4">Общая статистика в USD</h2>

        @foreach(['total', 'today', 'yesterday', 'week', 'month'] as $period)
            <div class="col-md-12 mb-4">
                <div class="statistic-card">
                    <h3 class="statistic-title">
                        @if($period == 'total')
                            За все время
                        @elseif($period == 'today')
                            Сегодня
                        @elseif($period == 'yesterday')
                            Вчера
                        @elseif($period == 'week')
                            За неделю
                        @elseif($period == 'month')
                            За месяц
                        @endif
                    </h3>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="statistic-value">${{ number_format($stats[$period]['bets'], 2) }}</div>
                            <div class="statistic-label">Общая сумма ставок</div>
                        </div>
                        <div class="col-md-3">
                            <div class="statistic-value">${{ number_format($stats[$period]['wins'], 2) }}</div>
                            <div class="statistic-label">Общая сумма выигрышей</div>
                        </div>
                        <div class="col-md-3">
                            <div class="statistic-value">${{ number_format($stats[$period]['profit'], 2) }}</div>
                            <div class="statistic-label">Профит</div>
                        </div>
                        <div class="col-md-3">
                            <div class="statistic-value">${{ number_format($stats[$period]['provider_debt'], 2) }}</div>
                            <div class="statistic-label">Долг провайдеру (15%)</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
