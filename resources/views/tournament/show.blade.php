<x-layouts.app>
    <style>
      .tournament-wrapper {
          background: linear-gradient(180deg, #131A28 0%, #19243D 100%);
          border-radius: 12px;
          padding: 20px;
          position: relative;
          overflow: hidden;
          margin: 25px auto;
      }
        .tournament-content {
            position: relative;
            z-index: 2;
        }

        /* Новогодние элементы */
        .christmas-lights {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: repeating-linear-gradient(90deg,
                #ff3d3d 0px, #ff3d3d 10px,
                #3dff3d 10px, #3dff3d 20px,
                #3d3dff 20px, #3d3dff 30px);
            animation: lights 2s linear infinite;
            opacity: 0.7;
        }

        @keyframes lights {
            0% { background-position: 0 0; }
            100% { background-position: 30px 0; }
        }

        .snow {
            position: absolute;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            opacity: 0.6;
            animation: fall linear infinite;
        }

        @keyframes fall {
            0% { transform: translateY(-10px) rotate(0deg); }
            100% { transform: translateY(100vh) rotate(360deg); }
        }

        /* Основной контент */
        .title {
            color: #FFD700;
            font-size: 24px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .prize-pool {
            color: #FFD700;
            font-size: 32px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
        }

        .description {
            color: #fff;
            font-size: 14px;
            text-align: center;
            max-width: 800px;
            margin: 0 auto 20px;
            line-height: 1.4;
            display: flex;
            align-items: center;
        }

        /* Статистика */
        .stats-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: rgba(19, 26, 40, 0.6);
            padding: 12px 20px;
            border-radius: 8px;
            text-align: center;
            min-width: 120px;
        }

        .stat-value {
            color: #6CBEFF;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .stat-label {
            color: #00EFD9;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* Таймер */
        .timer {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .timer-box {
            background: rgba(19, 26, 40, 0.6);
            padding: 10px 15px;
            border-radius: 8px;
            text-align: center;
            min-width: 70px;
        }

        .timer-value {
            color: #00EFD9;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .timer-label {
            color: #6CBEFF;
            font-size: 10px;
            text-transform: uppercase;
        }
          /* Таблица лидеров */
                .leaderboard {
                    background: rgba(13,18,30,0.8);
                    border: 1px solid rgba(255,255,255,0.1);
                    border-radius: 20px;
                    padding: 25px;
                    margin-top: 40px;
                }

                .leaderboard-table {
                    width: 100%;
                    border-collapse: separate;
                    border-spacing: 0 8px;
                }

                .leaderboard-table th {
                    color: #6cbeff;
                    padding: 15px;
                    font-weight: 500;
                    text-transform: uppercase;
                    font-size: 0.9rem;
                }

                .leaderboard-table td {
                    padding: 15px;
                    background: rgba(19,26,40,0.5);
                    border: 1px solid rgba(255,255,255,0.05);
                }

                .leaderboard-table tr:hover td {
                    background: rgba(19,26,40,0.8);
                }

                /* Позиции в таблице */
                .position {
                    font-size: 1.1rem;
                    font-weight: bold;
                }

                .position-1 { color: #ffd700; }
                .position-2 { color: #c0c0c0; }
                .position-3 { color: #cd7f32; }

                .user-cell {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }

                .user-avatar {
                    width: 40px;
                    height: 40px;
                    border-radius: 10px;
                    border: 2px solid #6cbeff;
                }

                .prize-value {
                    color: #00efd9;
                    font-weight: bold;
                }

                .turnover-value {
                    color: #6cbeff;
                }

        /* Адаптивность */
        @media (max-width: 768px) {
            .title { font-size: 20px; }
            .prize-pool { font-size: 28px; }
            .description { font-size: 12px; }

            .stats-container {
                flex-wrap: wrap;
                gap: 10px;
            }

            .stat-box {
                min-width: 100px;
                padding: 8px 15px;
            }

            .timer {
                gap: 10px;
            }

            .timer-box {
                min-width: 60px;
                padding: 8px 12px;
            }

            .timer-value {
                font-size: 20px;
            }
            .description {
                  flex-direction: column;
              }
        }
        .stats-and-timer {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin: 30px 0;
        }

        /* Секция статистики */
        .stats-section {
            background: linear-gradient(180deg, rgba(19,26,40,0.6) 0%, rgba(19,26,40,0.3) 100%);
            border-radius: 12px;
            border: 1px solid rgba(108,190,255,0.1);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-label {
            color: #00EFD9;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .stat-big-value {
            color: #6CBEFF;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(108,190,255,0.3);
        }

        /* Секция таймера */
        .countdown-section {
            padding: 20px;
        }

        .countdown-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .time-item {
            background: rgba(19,26,40,0.6);
            border-radius: 8px;
            padding: 15px 10px;
            text-align: center;
        }

        .time-big-value {
            color: #00EFD9;
            font-size: 32px;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(0,239,217,0.3);
            margin-bottom: 5px;
        }

        .time-label {
            color: #6CBEFF;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }

        /* Таблица лидеров */
        .tournament-table {
            margin-top: 30px;
            background: rgba(19,26,40,0.4);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid rgba(108,190,255,0.1);
        }

        .table-header {
            color: #00EFD9;
            font-size: 12px;
            font-weight: 500;
            padding: 10px 15px;
            text-transform: uppercase;
        }

        .table-row {
            display: grid;
            grid-template-columns: 0.5fr 2fr 1fr 1fr;
            align-items: center;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: rgba(19,26,40,0.6);
            transition: all 0.3s ease;
        }

        .table-row:hover {
            background: rgba(19,26,40,0.8);
        }

        .position-cell {
            font-size: 16px;
            font-weight: bold;
        }

        .top-1 { color: #FFD700; }
        .top-2 { color: #C0C0C0; }
        .top-3 { color: #CD7F32; }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 2px solid #6CBEFF;
        }

        .turnover-cell,
        .prize-cell {
            text-align: right;
            color: #6CBEFF;
            font-weight: 500;
        }

        .prize-cell {
            color: #00EFD9;
        }

        .santa {
          margin: 0 auto;
          width: 200px;
        }



        @media (max-width: 768px) {
            .table-row {
                grid-template-columns: 0.5fr 1.5fr 1fr 1fr;
                font-size: 12px;
                padding: 10px;
            }

            .time-big-value {
                font-size: 24px;
            }

            .stat-big-value {
                font-size: 22px;
            }

            .user-avatar {
                width: 24px;
                height: 24px;
            }
            .leaderboard {
                padding: 5px;
                overflow: auto;
            }
            .leaderboard-table td {
                padding: 2px;
                font-size: 9px;
            }
            .leaderboard-table th {
                padding: 3px;
                font-size: 9px;
            }
        }
    </style>

    <div class="tournament-wrapper">
        <!-- Новогодние эффекты -->
        <div class="christmas-lights"></div>
        @for($i = 0; $i < 50; $i++)
            <div class="snow" style="
                left: {{ rand(0, 100) }}%;
                animation-duration: {{ rand(5, 15) }}s;
                animation-delay: -{{ rand(0, 15) }}s;
            "></div>
        @endfor

        <div class="tournament-content">
            <div class="title">{{ __('НОВОГОДНЯЯ ГОНКА С ПРИЗОВЫМ ФОНДОМ') }}</div>

            <div class="prize-pool">
                {{ number_format(toUSD($tournament->prize_pool, $currency), 2) }} {{ $currency }}
            </div>


            <div class="description"><img src="/assets/images/santa.png" class="santa"> {{ __('Примите участие в праздничном турнире от Flash с призовым фондом в $100,000!В течение следующих 30 дней играйте в свои любимые игры в нашем казино, зарабатывайте очки и поднимайтесь в турнирной таблице. Закрепитесь среди 50 лучших игроков, чтобы получить свой заслуженный приз! По завершении турнира награда будет мгновенно зачислена на ваш баланс. Не упустите шанс отпраздновать этот сезон с невероятными выигрышами от Flash!')}}</div>

  <div class="stats-and-timer">
            @auth

                <div class="stats-section">
                    <div class="stat-grid">
                        <div class="stat-item">
                            <div class="stat-label">{{ __('ПОЗИЦИЯ') }}</div>
                            <div class="stat-big-value">{{ $userPosition ?? '-' }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-label">{{ __('Оборот') }} {{ $currency }}</div>
                            <div class="stat-big-value">{{ number_format(toUSD($userTurnover ?? 0, $currency), 2) }}</div>
                        </div>
                    </div>
                </div>

            @endauth


                            <div class="countdown-section">
                                <div class="countdown-grid">
                                    <div class="time-item">
                                        <div class="time-big-value" id="days">00</div>
                                        <div class="time-label">{{ __('Дней') }}</div>
                                    </div>
                                    <div class="time-item">
                                        <div class="time-big-value" id="hours">00</div>
                                        <div class="time-label">{{ __('Часов') }}</div>
                                    </div>
                                    <div class="time-item">
                                        <div class="time-big-value" id="minutes">00</div>
                                        <div class="time-label">{{ __('Минут') }}</div>
                                    </div>
                                    <div class="time-item">
                                        <div class="time-big-value" id="seconds">00</div>
                                        <div class="time-label">{{ __('Секунд') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

        <div class="leaderboard">
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>{{ __('Позиция') }}</th>
                        <th>{{ __('Игрок') }}</th>
                        <th>{{ __('Оборот') }}</th>
                        <th>{{ __('Приз') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaderboard as $index => $entry)
                    <tr class="{{ Auth::check() && $entry->user_id == Auth::id() ? 'bg-blue-900 bg-opacity-50' : '' }}">
                        <td>
                            <span class="position position-{{ $index + 1 }}">{{ $index + 1 }}</span>
                        </td>
                        <td>
                            <div class="user-cell">
                                <img src="{{ $entry->user->avatar }}" alt="{{ $entry->user->username }}" class="user-avatar">
                                <span>{{ $entry->user->username }}</span>
                            </div>
                        </td>
                        <td class="turnover-value">
                            {{ moneyFormat(toUSD($entry->turnover, $currency)) }} {{ $currency }}
                        </td>
                        <td class="prize-value">
                            @if(isset($prizes[$index]))
                                {{ moneyFormat(toUSD($prizes[$index], $currency)) }} {{ $currency }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    <script>
        function updateCountdown() {
            const endDate = new Date('{{ $tournament->end_date }}').getTime();

            function update() {
                const now = new Date().getTime();
                const distance = endDate - now;

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('days').textContent = String(days).padStart(2, '0');
                document.getElementById('hours').textContent = String(hours).padStart(2, '0');
                document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
                document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
            }

            update();
            setInterval(update, 1000);
        }

        updateCountdown();
    </script>
</x-layouts.app>
