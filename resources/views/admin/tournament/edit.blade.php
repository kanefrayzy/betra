@extends('panel')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Редактирование турнира</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tournament.update', $tournament->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Название турнира</label>
                                    <input type="text" name="name" class="form-control" value="{{ $tournament->name }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Описание</label>
                                    <textarea name="description" class="form-control" rows="3">{{ $tournament->description }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Призовой фонд ($)</label>
                                    <input type="number" name="prize_pool" class="form-control" step="0.01" value="{{ $tournament->prize_pool }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Минимальный оборот для участия ($)</label>
                                    <input type="number" name="min_turnover" class="form-control" step="0.01" value="{{ $tournament->min_turnover }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Дата начала</label>
                                    <input type="datetime-local" name="start_date" class="form-control" value="{{ $tournament->start_date->format('Y-m-d\TH:i') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Дата окончания</label>
                                    <input type="datetime-local" name="end_date" class="form-control" value="{{ $tournament->end_date->format('Y-m-d\TH:i') }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Распределение призов</label>
                                    <div id="prize-distribution">
                                        @foreach($tournament->prize_distribution as $index => $prize)
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ $index + 1 }} место</span>
                                            </div>
                                            <input type="number" name="prize_distribution[]" class="form-control" step="0.01" value="{{ $prize }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">$</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
