@extends('panel')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Тикеты пользователей</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Тема</th>
                    <th>Статус</th>
                    <th>Количество сообщений</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->id }}</td>
                        <td>{{ $ticket->user->name }}</td>
                        <td>{{ $ticket->subject }}</td>
                        <td>{{ $ticket->status == 'open' ? 'Открыт' : 'Закрыт' }}</td>
                        <td>{{ $ticket->messages_count }}</td>
                        <td>
                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-primary btn-sm">Просмотр</a>
                            @if($ticket->status == 'open')
                                <form action="{{ route('admin.tickets.close', $ticket->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">Закрыть</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
