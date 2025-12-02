<x-layouts.app>
  <style>
  .leaderboard-container {
      background: linear-gradient(to bottom, #131a28, #19243d);
      color: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      margin-top: 20px;
      }
      .content-header {
          margin-bottom: 30px;
          text-align: center;
          display: flex;
          align-items: center;
          justify-content: center;
      }
      .content-header h1 {
          font-size: 28px;
          margin-bottom: 10px;
          text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }
      .content-header p {
          font-size: 16px;
          opacity: 0.8;
      }
      .user-position-alert {
          background: linear-gradient(to right, #4299e1, #3182ce);
          padding: 15px;
          margin-bottom: 30px;
          border-radius: 8px;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }
      .user-position-alert p {
          margin: 0;
          font-weight: bold;
      }
      .leaderboard-table {
          overflow-x: auto;
      }
      table {
          width: 100%;
          border-collapse: separate;
          border-spacing: 0 8px;
      }
      th, td {
          text-align: left;
          padding: 15px;
      }
      th {
          background-color: #2d3748;
          color: #ffffff;
          font-weight: bold;
          text-transform: uppercase;
          font-size: 14px;
      }
      tr {
          background: linear-gradient(to right, #2d3748, #3d4756);
          transition: all 0.3s ease;
      }
      tr:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      tr.current-user {
          background: linear-gradient(to right, #2c5282, #3182ce);
      }
      td:first-child {
          border-top-left-radius: 8px;
          border-bottom-left-radius: 8px;
      }
      td:last-child {
          border-top-right-radius: 8px;
          border-bottom-right-radius: 8px;
      }
      .pagination {
          margin-top: 30px;
          display: flex;
          justify-content: center;
      }
      .pagination > * {
          margin: 0 5px;
          padding: 8px 12px;
          background-color: #2d3748;
          color: #ffffff;
          border-radius: 4px;
          transition: all 0.3s ease;
      }
      .pagination > *:hover {
          background-color: #4299e1;
      }

      .leader-ava {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
    }

      /* Responsive */
      @media (max-width: 768px) {
          table, th, td {
              font-size: 12px;
              padding: 10px;
          }
          .content-header h1 {
              font-size: 22px;
          }
          .content-header p {
              font-size: 14px;
          }
      }

      @media (max-width: 480px) {
        .leaderboard-container {
            padding: 20px 3px;
        }
        table, th, td {
            font-size: 11px;
            padding: 5px;
        }
        .leader-ava {
            width: 30px;
            height: 30px;
        }
          .content-header h1 {
              font-size: 20px;
          }
          .content-header p {
              font-size: 12px;
          }
          .pagination > * {
              padding: 6px 10px;
          }
          .user-position-alert {
              padding: 10px;
              margin: 0 auto;
          }
      }
      .countdown-container {
          display: flex;
          justify-content: center;
          align-items: center;
          margin-bottom: 20px;
          padding: 10px;
          background: linear-gradient(to right, #2c5282, #3182ce);
          border-radius: 8px;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .countdown-box {
          display: flex;
          flex-direction: column;
          align-items: center;
          margin: 0 5px;
      }

      .countdown-value {
          font-size: 28px;
          font-weight: bold;
          color: #ffffff;
      }

      .countdown-label {
          font-size: 12px;
          color: #e2e8f0;
          text-transform: uppercase;
      }

      .countdown-separator {
          font-size: 28px;
          font-weight: bold;
          color: #ffffff;
          margin: 0 5px;
      }

      @media (max-width: 768px) {
          .countdown-value, .countdown-separator {
              font-size: 24px;
          }
          .countdown-label {
              font-size: 10px;
          }
      }

      @media (max-width: 480px) {
          .countdown-container {
              flex-wrap: wrap;
          }
          .countdown-box {
              margin: 5px;
          }
          .countdown-value, .countdown-separator {
              font-size: 20px;
          }
          .countdown-label {
              font-size: 9px;
          }
      }

      .yesterday-winner {
          background: #3077bc;
          color: #ffffff;
          padding: 20px;
          border-radius: 12px;
          margin-bottom: 30px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          transition: transform .3s;
      }

      .yesterday-winner:hover {
            transform: translateY(-5px);
      }

  .yesterday-winner h2 {
      text-align: center;
      margin-bottom: 15px;
      font-size: 24px;
  }

  .winner-info {
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      width: max-content;
      margin: 0 auto;
  }

  .winner-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
      border: 2px solid #fff;
  }

  .winner-details {
      display: flex;
      flex-direction: column;
  }

  .winner-name {
      font-size: 18px;
      font-weight: bold;
  }

  .winner-oborot {
      font-size: 16px;
      color: #36ff2b;
      font-weight: bold;
  }
  .winner-crown {
      margin-left: 15px;
      color: #fac809;
      font-size: 25px;
      left: -26px;
      top: -11px;
      transform: rotate(317deg);
  }
  @media (max-width: 768px) {
      .yesterday-winner h2 {
          font-size: 20px;
      }

      .winner-avatar {
          width: 50px;
          height: 50px;
      }

      .winner-name {
          font-size: 16px;
      }

      .winner-oborot {
          font-size: 14px;
      }

  }

  </style>
  <div class="leaderboard-container">

        <header class="content-header">
            <div><img src="https://uxwing.com/wp-content/themes/uxwing/download/sport-and-awards/trophy-icon.png" width="100"></div>
            <div style="margin-left: 20px">
              <h1>{{ __('Ежедневная гонка') }}</h1>
              <p>{{ __('Топ игроки по обороту за сегодня') }}</p>
            </div>
        </header>
        @if(isset($yesterdayWinner))
        <div class="yesterday-winner">
            <h2>{{ __('Победитель вчерашней гонки') }}</h2>
            <div class="winner-info">
                <img src="{{ $yesterdayWinner->user->avatar }}" class="winner-avatar" alt="{{ $yesterdayWinner->user->username }}">
                <div class="winner-details">
                    <span class="winner-name">{{ $yesterdayWinner->user->username }}</span>
                    <span class="winner-oborot">{{ moneyFormat(toUSD($yesterdayWinner->daily_oborot, $currency)) }} {{ $currency }}</span>
                </div>
                <i style="position: absolute" class="fa-sharp-duotone fa-solid fa-crown winner-crown"></i>
            </div>
        </div>
        @endif
        <div class="countdown-container">
            <div class="countdown-box">
                <span class="countdown-value" id="hours">00</span>
                <span class="countdown-label">{{ __('Часов') }}</span>
            </div>
            <span class="countdown-separator">:</span>
            <div class="countdown-box">
                <span class="countdown-value" id="minutes">00</span>
                <span class="countdown-label">{{ __('Минут') }}</span>
            </div>
            <span class="countdown-separator">:</span>
            <div class="countdown-box">
                <span class="countdown-value" id="seconds">00</span>
                <span class="countdown-label">{{ __('Секунд') }}</span>
            </div>
        </div>
        @auth
            @if(isset($currentUserPosition) && $currentUserPosition > 0)
            <div class="user-position-alert">
                <p>{{ __('Ваша текущая позиция') }}: {{ $currentUserPosition }}</p>
            </div>
            @endif
        @endauth

        <section class="leaderboard-table">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Позиция') }}</th>
                        <th>{{ __('Пользователь') }}</th>
                        <th>{{ __('Оборот') }}</th>
                        <th>{{ __('Приз') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaderboard as $index => $player)
                    <tr class="{{ auth()->check() && $player->user_id == auth()->id() ? 'current-user' : '' }}">
                        <td>{{ $index + 1 }}</td>
                        <td style="display: flex; align-items: center"><img src="{{ $player->user->avatar }}" class="leader-ava">{{ $player->user->username }}</td>
                        <td><span style="color: #6cbeff;font-weight:bold">{{ moneyFormat(toUSD($player->daily_oborot, $currency)) }} {{ $currency }}</span></td>
                        <td><span style="color: #00efd9;font-weight:bold">{{ isset($prizes[$index]) ? moneyFormat(toUSD($prizes[$index], $currency)) . ' ' . $currency : '-' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <!-- <div class="pagination">
            {{ $leaderboard->links() }}
        </div> -->
    </div>
    <script>
    function updateCountdown() {
      const now = new Date().toLocaleString("en-US", {timeZone: "Europe/Moscow"});
      const currentDateTime = new Date(now);

      const midnight = new Date(currentDateTime.getFullYear(), currentDateTime.getMonth(), currentDateTime.getDate() + 1);
      const diff = midnight - currentDateTime;

        const hours = Math.floor(diff / 3600000);
        const minutes = Math.floor((diff % 3600000) / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);

        document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
  </script>
</x-layouts.app>
