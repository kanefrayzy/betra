@extends('panel')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Создание турнира</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tournaments.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Название турнира</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Описание</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Призовой фонд ($)</label>
                                    <input type="number" name="prize_pool" class="form-control" step="0.01" required>
                                </div>

                                <div class="form-group">
                                    <label>Минимальный оборот ($)</label>
                                    <input type="number" name="min_turnover" class="form-control" step="0.01" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Дата начала</label>
                                    <input type="datetime-local" name="start_date" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Дата окончания</label>
                                    <input type="datetime-local" name="end_date" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Призы (первые 10 мест)</label>
                                    <div id="prizes">
                                        @for($i = 1; $i <= 10; $i++)
                                        <div class="input-group mb-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ $i }} место ($)</span>
                                            </div>
                                            <input type="number" name="prizes[]" class="form-control prize-input" step="0.01">
                                        </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Создать турнир</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const prizePoolInput = document.querySelector('input[name="prize_pool"]');
    const prizeInputs = document.querySelectorAll('.prize-input');

    prizePoolInput.addEventListener('input', function() {
        const total = parseFloat(this.value);
        if (isNaN(total)) return;

        // Распределение призового фонда
        const distribution = [0.30, 0.20, 0.15, 0.10, 0.05, 0.05, 0.05, 0.05, 0.025, 0.025];

        distribution.forEach((percentage, index) => {
            prizeInputs[index].value = (total * percentage).toFixed(2);
        });
    });
});
</script>
@endsection
