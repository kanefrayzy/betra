@extends('panel')
@php $baseUrl = 'panel8808'; @endphp

@section('content')
<style>
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        width: 100%;
        flex-wrap: wrap;
    }
    .pagination a {
        color: black;
        padding: 8px 16px;
        text-decoration: none;
        transition: background-color .3s;
    }
    .pagination a.active {
        background-color: #4CAF50;
        color: white;
    }
    .pagination a:hover:not(.active) {
        background-color: #ddd;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default border-panel card-view">
            <div class="panel-heading">
                <h6 class="panel-title txt-dark">Переводы Денег</h6>
                <div class="clearfix"></div>
            </div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <table class="table table-hover display pb-30 dataTable" aria-describedby="datatable_info">
                        <thead>
                            <tr>
                                <th>Описание</th>
                                <th>Сумма</th>
                                <th>Баланс до</th>
                                <th>Баланс по</th>
                                <th>Дата</th>
                                <th>Хэш</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transfers as $transfer)
                            <tr>
                                <td><div class="type">{{ $transfer->context['description'] ?? 'Описание отсутствует' }}</div></td>
                                <td><div class="sum ok">{{ $transfer->amount }} {{ $transfer->currency->symbol }}</div></td>
                                <td><div class="type">{{ $transfer->context['balance_before'] ?? '-' }}</div></td>
                                <td><div class="type">{{ $transfer->context['balance_after'] ?? '-' }}</div></td>
                                <td><div>{{ $transfer->created_at ?: $transfer->updated_at }}</div></td>
                                <td><div class="id">{{ $transfer->hash }}</div></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination">
                        @if ($transfers->onFirstPage())
                            <a class="disabled">&laquo; Назад</a>
                        @else
                            <a href="{{ $transfers->previousPageUrl() }}">&laquo; Назад</a>
                        @endif

                        @for ($i = 1; $i <= $transfers->lastPage(); $i++)
                            @if ($i == $transfers->currentPage())
                                <a class="active">{{ $i }}</a>
                            @else
                                <a href="{{ $transfers->url($i) }}">{{ $i }}</a>
                            @endif
                        @endfor

                        @if ($transfers->hasMorePages())
                            <a href="{{ $transfers->nextPageUrl() }}">Вперёд &raquo;</a>
                        @else
                            <a class="disabled">Вперёд &raquo;</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
