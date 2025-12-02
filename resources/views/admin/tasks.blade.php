@extends('panel')

@section('content')
    <h2>Задания для админов и модераторов</h2>

    <form action="{{ route('adminCreateTask') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="assignee_id">Назначить для:</label>
            <select name="assignee_id" id="assignee_id" class="form-control" required>
                @foreach($admins as $admin)
                    <option value="{{ $admin->id }}">{{ $admin->username }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="description">Описание задания:</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label for="screenshot">Скриншот (опционально):</label>
            <input type="file" name="screenshot" id="screenshot" class="form-control-file">
        </div>
        <button type="submit" class="btn btn-primary">Создать задание</button>
    </form>

    <hr>

    <table class="table">
        <thead>
            <tr>
                <th>Создатель</th>
                <th>Назначено</th>
                <th>Описание</th>
                <th>Фото</th>
                <th>Статус</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
          @foreach($tasks as $task)
              <tr>
                  <td>{{ $task->creator->username }}</td>
                  <td>{{ $task->assignee->username }}</td>
                  <td>{{ $task->description }}</td>
                  <td>
                      @if($task->screenshot)
                          <a href="{{ asset('storage/task_screenshots/' . $task->screenshot) }}" target="_blank">
                              <img src="{{ asset('storage/task_screenshots/' . $task->screenshot) }}" alt="Screenshot" style="max-width: 100px;">
                          </a>
                      @else
                          Нет скриншота
                      @endif
                  </td>
                  <td>{{ $task->completed ? 'Выполнено' : 'В процессе' }}</td>
                    <td>{{ $task->created_at }}</td>
                  <td>
                      @if(!$task->completed && $task->assignee_id == auth()->id())
                          <form action="{{ route('adminCompleteTask', $task->id) }}" method="POST">
                              @csrf
                              @method('PATCH')
                              <button type="submit" class="btn btn-success">Отметить как выполненное</button>
                          </form>
                      @endif
                  </td>
              </tr>
          @endforeach
        </tbody>
    </table>
@endsection
