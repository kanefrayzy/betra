@extends('panel')
@php $baseUrl = 'panel8808'; @endphp

@section('content')
<style>
    .stat-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    .stat-card-header {
        background-color: #00a368;
        color: white;
        padding: 10px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    .stat-card-body {
        padding: 15px;
    }
    .stat-value {
        font-size: 24px;
        font-weight: bold;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center">Статистика платежей</h2>
        </div>
    </div>

    @foreach(['m10' => 'M10', 'card' => 'CARD AZ', 'mPay' => 'MPAY', 'freekassa' => 'Freekassa', 'paykassa' => 'Paykassa'] as $key => $name)
        <div class="row mb-4">
            <div class="col-12">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h5 class="mb-0">{{ $name }}</h5>
                    </div>
                    <div class="stat-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Сегодня</h6>
                                <p class="stat-value">{{ moneyFormat(${$key . 'PaymentsToday'} - ${$key . 'WithdrawalsToday'}) }} $,<br> <br><font color="green">(+{{ moneyFormat(${$key . 'PaymentsToday'}) }} $)</font><br> <font color="red">(-{{ moneyFormat(${$key . 'WithdrawalsToday'}) }} $)</font></p>
                            </div>
                            <div class="col-md-4">
                                <h6>Вчера</h6>
                                <p class="stat-value">{{ moneyFormat(${$key . 'PaymentsYesterday'} - ${$key . 'WithdrawalsYesterday'}) }} $,<br> <br><font color="green">(+{{ moneyFormat(${$key . 'PaymentsYesterday'}) }} $)</font><br> <font color="red">(-{{ moneyFormat(${$key . 'WithdrawalsYesterday'}) }} $)</font></p>
                            </div>
                            <div class="col-md-4">
                                <h6>Всего</h6>
                                <p class="stat-value">{{ moneyFormat(${$key . 'PaymentsTotal'} - ${$key . 'WithdrawalsTotal'}) }} $, ({{ moneyFormat(${$key . 'PaymentsTotal'}) }} $)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
