<x-layouts.app>
  <link rel="stylesheet" href="/assets/css/jackpot.css?v3">
  @auth
  <script>
      window.initialGameState = @json($game);
      window.userBalance = {{ auth()->user()->balance ?? 0 }};
      window.userId = {{ auth()->id() }};
      window.gameToken = '{{ $u->game_token }}';

      document.addEventListener('DOMContentLoaded', () => {
          if (!window.gameToken) {
              showNotification('Authorization Error', 'error');
              return;
          }

          window.jackpotGame = new JackpotGame({
              room: '{{ $currentRoom->name }}',
              gameToken: window.gameToken,
              minBet: {{ $currentRoom->min }},
              maxBet: {{ $currentRoom->max }}
          });
      });
  </script>
  @endauth


    <div class="jackpot-game">
        <div class="game-header">
            <div class="current-room">
                {{ number_format($currentRoom->min, 2) }} - {{ number_format($currentRoom->max, 2) }} &#8380;
            </div>
            <div class="game-stats">
                <div class="bank">
                    <span class="label">BANK</span>
                    <!-- <i class="fa-sharp fa-light fa-piggy-bank"></i> -->
                    <span class="value" id="game-bank">0.00</span> AZN
                </div>
                <div class="timer">
                    <img src="/assets/images/timer.gif" width="25px">
                    <span id="game-timer">00:30</span>
                </div>
            </div>
        </div>


        <div class="game-content">
            <div id="winner-select" class="winner-select" style="display: none;">
                <div class="winner-carousel">
                    <div class="carousel-items"></div>
                    <div class="pointer"></div>
                </div>
            </div>


            @auth
            <div class="betting-section">
                <div class="bet-input-container">
                    <input type="number"
                           class="bet-amount"
                           id="bet-amount"
                           placeholder="{{ __('Введите сумму ставки') }}"
                           min="{{ $currentRoom->min }}"
                           max="{{ $currentRoom->max }}">
                    <button class="place-bet-btn" id="place-bet-btn">{{ __('Ставка') }}</button>
                </div>
            </div>
            @else
            <div class="auth-prompt">
                <p>{{ __('Авторизуйтесь') }}</p>
                <a href="#" class="login-button" onclick="openModal('login-modal')">{{ __('Войти') }}</a>
            </div>
            @endauth


            <div class="players-section">
                <div class="chances-container" id="chances-container"></div>
            </div>


            <div class="bets-section">
                <div class="bets-header">{{ __('Последние ставки') }}</div>
                <div class="bets-container" id="bets-container"></div>
            </div>

        </div>
        <a href="{{ route('jackpot.history') }}" class="history-link">{{ __('История игр')}}</a>
        <div class="game-footer">
            <div class="game-info">
                <span>Game #<span id="game-id">-</span></span>
                <span>Hash: <span id="game-hash">-</span></span>
            </div>
        </div>
    </div>


    <div id="spinner" class="spinner" style="display: none;"></div>
    <div id="carousel-container" class="carousel-container" style="display: none;">
        <div class="carousel">
            <div class="carousel-items"></div>
            <div class="carousel-pointer"></div>
        </div>
    </div>
    <div id="winner-popup" class="winner-popup" style="display: none;"></div>

    <script src="/assets/js/core/jackpot.js"></script>
</x-layouts.app>
