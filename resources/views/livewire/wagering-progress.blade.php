<div wire:poll.3s="updateProgress">
    @if($wageringRequirement > 0)
    <div class="wagering-progress">
        @if($wageringRequirement > 0)
            <div class="progress-info">
                <h6>{{__('Прогресс отыгрыша')}}</h6>
                <div class="progress-bar-wrapper2">
                    <div class="progress-bar2">
                        <div class="progress2" style="width: {{ $progress }}%"></div>
                    </div>
                    <div class="progress-text2">
                        {{ number_format($progress, 1) }}%
                    </div>
                </div>
                <div class="progress-details">
                    <div>{{__('Отыграно')}}: {{ moneyFormat($wageredAmount) }} {{ $u->currency->symbol }}</div>
                    <div>{{__('Осталось')}}: {{ moneyFormat($wageringRequirement - $wageredAmount) }} {{ $u->currency->symbol }}</div>
                </div>
            </div>
        @endif
    </div>
    @endif
</div>
