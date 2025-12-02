@extends('panel')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Управление турнирами</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tournaments.create') }}" class="btn btn-primary">
                            Создать турнир
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Призовой фонд</th>
                                    <th>Мин. оборот</th>
                                    <th>Начало</th>
                                    <th>Окончание</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tournaments as $tournament)
                                <tr>
                                    <td>{{ $tournament->id }}</td>
                                    <td>{{ $tournament->name }}</td>
                                    <td>${{ number_format($tournament->prize_pool, 2) }}</td>
                                    <td>${{ number_format($tournament->min_turnover, 2) }}</td>
                                    <td>{{ $tournament->start_date->format('d.m.Y H:i') }}</td>
                                    <td>{{ $tournament->end_date->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $tournament->status === 'active' ? 'success' : ($tournament->status === 'completed' ? 'secondary' : 'warning') }}">
                                            {{ $tournament->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.tournaments.edit', $tournament->id) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
          
                                            <form action="{{ route('admin.tournaments.destroy', $tournament->id) }}"
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Удалить турнир?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
