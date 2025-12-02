@extends('panel')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Информация о турнире -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Информация о турнире</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Название:</th>
                            <td>{{ $tournament->name }}</td>
                        </tr>
                        <tr>
                            <th>Призовой фонд:</th>
                            <td>${{ number_format($tournament->prize_pool, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Статус:</th>
                            <td>
                                <span class="badge badge-{{ $tournament->status === 'active' ? 'success' : ($tournament->status === 'completed' ? 'secondary' : 'warning') }}">
                                    {{ $tournament->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Начало:</th>
                            <td>{{ $tournament->start_date->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Окончание:</th>
                            <td>{{ $tournament->end_date->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Мин. оборот:</th>
                            <td>${{ number_format($tournament->min_turnover, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Призовые места -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Призовые места</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Место</th>
                                    <th>Приз</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tournament->prize_distribution as $index => $prize)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>${{ number_format($prize, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Таблица лидеров -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Таблица лидеров</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Место</th>
                                    <th>Пользователь</th>
                                    <th>Оборот</th>
                                    <th>Приз</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaderboard as $index => $entry)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $entry->user->avatar }}" class="rounded-circle mr-2" width="30" height="30">
                                            {{ $entry->user->username }}
                                        </div>
                                    </td>
                                    <td>${{ number_format($entry->turnover, 2) }}</td>
                                    <td>
                                        @if($entry->prize > 0)
                                            <span class="text-success">${{ number_format($entry->prize, 2) }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $leaderboard->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
