<div class="coin-game">
    <div class="game-container">
        <div class="bet-section">
            <input type="number"
                   wire:model.debounce.500ms="bet"
                   placeholder="0"
                   class="bet-input"
                   min="1"
                   :disabled="playing"
            >
        </div>
        @if($message)
            <div class="result-message {{ $won ? 'win' : 'lose' }}">
                {{ $message }}
            </div>
        @endif
        <div class="sides-container">
            <button wire:click="toggleSide('red')"
                    class="side-btn red {{ in_array('red', $selectedSides) ? 'selected' : '' }}"
                    :disabled="playing">
                <span>{{__('Красный')}}</span>
                <div class="win-multiplier">x2</div>
            </button>

            <button wire:click="toggleSide('blue')"
                    class="side-btn blue {{ in_array('blue', $selectedSides) ? 'selected' : '' }}"
                    :disabled="playing">
                <span>{{__('Синий')}}</span>
                <div class="win-multiplier">x2</div>
            </button>
        </div>

        <div class="coin-container">
            <div class="coin-wrapper">
                <div class="coin"
                     :class="{
                         'flipping': $wire.playing,
                         'red-landed': $wire.result === 'red',
                         'blue-landed': $wire.result === 'blue'
                     }"
                     @animationend="$wire.dispatch('animationComplete')">
                    <div class="side front red"></div>
                    <div class="side back blue"></div>
                </div>
            </div>
        </div>

        <button wire:click="play"
                class="play-btn"
                :disabled="!$wire.selectedSides.length || $wire.playing || $wire.bet < 1"
                wire:loading.attr="disabled">
            {{__('Играть')}}
        </button>

    </div>
</div>
