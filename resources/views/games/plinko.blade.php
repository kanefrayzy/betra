<x-layouts.app>
    <div class="plinko-container">
        <div class="plinko-sidebar">
            <div class="mode-switch">
                <button class="mode-button active" onclick="switchMode('manual')">{{ __('Ручной режим') }}</button>
                <button class="mode-button" onclick="switchMode('auto')">{{ __('Авторежим') }}</button>
            </div>

            <div class="control-group">
                <label for="bet-amount">{{ __('Сумма ставки') }}</label>
                <input type="number" id="bet-amount" min="0.01" step="0.01" value="1">
                <div class="bet-adjustment">
                    <button class="btn-small" onclick="adjustBet('half')">1/2</button>
                    <button class="btn-small" onclick="adjustBet('double')">2x</button>
                </div>
            </div>

            <div class="control-group">
                <label for="risk-level">{{ __('Риск') }}</label>
                <select id="risk-level">
                    <option value="low">{{ __('Низкий') }}</option>
                    <option value="medium">{{ __('Средний') }}</option>
                    <option value="high" selected>{{ __('Высокий') }}</option>
                </select>
            </div>

            <div class="control-group">
                <label for="rows">{{ __('Количество рядов') }}</label>
                <select id="rows">
                    <option value="8">8</option>
                    <option value="12">12</option>
                    <option value="16" selected>16</option>
                </select>
            </div>

            <!-- <button id="play-button" class="btn-play" onclick="startGame()">{{ __('Ставка') }}</button> -->
            <button id="play-button" class="btn-play btn-primary" onclick="startGame()">
                <span class="btn-text">{{ __('Играть') }}</span>
                <div class="btn-loader" style="display: none;">
                    <div class="spinner">...</div>
                </div>
            </button>
            <button id="auto-play-button" class="btn-secondary" onclick="toggleAutoPlay()">{{ __('Авто-игра') }}</button>
        </div>

        <div class="plinko-game">
            <div class="canvas-container">
                <canvas id="plinko-canvas"></canvas>
            </div>
            <div class="plinko-history">
                <div id="game-history"></div>
            </div>
        </div>
    </div>



    <div class="fair-game">
        <div class="fair-game-header" onclick="toggleFairInfo()">
            <h3>{{ __('Честная игра') }}</h3>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="fair-game-content" style="display: none;">
            <p>{{ __('Hash:') }} <span id="game-hash"></span></p>
            <div class="seed-info">
                <div>
                    <label>{{ __('Client Seed:') }}</label>
                    <input type="text" id="client-seed">
                    <button class="btn-small" onclick="generateNewSeed()">{{ __('Сгенерировать') }}</button>
                </div>
                <div>
                    <label>{{ __('Server Seed:') }}</label>
                    <span id="server-seed"></span>
                </div>
            </div>
        </div>
    </div>


    <style>
    .plinko-container {
        display: flex;
        flex-direction: column;
        padding: 10px;
        max-width: 100%;
        margin: auto;
        background-color: #1c1f26;
        border-radius: 10px;
    }

    .plinko-game {
        width: 100%;
        aspect-ratio: 1/1; /* Делаем контейнер квадратным */
        background-color: #161b22;
        border-radius: 10px;
        position: relative;
        overflow: hidden;
    }


       .canvas-container {
           width: 100%;
           padding-top: 75%; /* Соотношение сторон 4:3 */
           position: relative;
           overflow: hidden;
       }

       #plinko-canvas {
           position: absolute;
           top: 0;
           left: 0;
           width: 100% !important; /* Принудительно устанавливаем ширину */
           height: 100% !important; /* Принудительно устанавливаем высоту */
           background-color: #010409;
           border: 2px solid #30363d;
           border-radius: 10px;
       }

  .mode-switch {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
  }

  .mode-button {
      flex: 1;
      padding: 10px;
      border: none;
      background-color: #2a2f38;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.2s;
  }

  .mode-button.active {
      background-color: #3b9a33;
  }

  .control-group {
      margin-bottom: 15px;
  }

  .control-group label {
      display: block;
      margin-bottom: 5px;
      color: #c9d1d9;
  }

  .control-group input, .control-group select {
      width: 100%;
      padding: 8px;
      background-color: #0d1117;
      color: #c9d1d9;
      border: 1px solid #30363d;
      border-radius: 5px;
  }

  .bet-adjustment {
      display: flex;
      justify-content: space-between;
      margin-top: 5px;
  }

  .btn-small {
      padding: 5px 10px;
      background-color: #2a2f38;
      border: 1px solid #30363d;
      color: #c9d1d9;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.2s;
  }

  .btn-small:hover {
      background-color: #3a3f48;
  }

  .btn-play {
      display: block;
      width: 100%;
      padding: 12px;
      background-color: #3b9a33;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.2s;
  }

  .btn-play:hover {
      background-color: #45a946;
  }

  .btn-secondary {
      display: block;
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      background-color: #21262D;
      color: #C9D1D9;
      border: 1px solid #30363D;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.2s;
  }

  .btn-secondary:hover {
      background-color: #30363D;
  }

  .plinko-history {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 4px;
      position: absolute;
      top: 34px;
      right: 56px;
  }

  .history-entry {
      width: 70px;
      padding: 5px;
      margin: 2px auto;
      background-color: #2196f3;
      color: #FFF;
      text-align: center;
      font-weight: bold;
      border-radius: 4px;
      border: 1px solid #131a28;
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.5s ease, transform 0.5s ease;
  }

  .history-entry.show {
      opacity: 1;
      transform: translateY(0);
  }

  .history-entry.remove {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.5s ease, transform 0.5s ease;
  }
  .history-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
  }

  .fair-game {
      margin-top: 20px;
      background: #161B22;
      border: 1px solid #30363D;
      border-radius: 8px;
      padding: 15px;
  }
  .multiplier-container {
      position: absolute;
      bottom: 10px;
      left: 0;
      right: 0;
      display: flex;
      justify-content: center;
      gap: 2px;
  }

  .multiplier-box {
      padding: 5px;
      text-align: center;
      border-radius: 4px;
      color: white;
      font-weight: bold;
  }
    /* Адаптация для мобильных устройств */
  @media (max-width: 768px) {
    .plinko-container {
        padding: 0;
        gap: 10px;
    }

    .plinko-sidebar {
        width: 100%;
        max-width: none;
        margin-bottom: 10px;
    }

    .plinko-game {
        width: 100%;
        padding: 10px;
        border-radius: 0;
        margin: 10px 0;
    }
    .multiplier-box {
    min-width: 40px; /* Минимальная ширина для множителей */
    height: 35px; /* Фиксированная высота */
    font-size: 14px; /* Увеличенный размер шрифта */
}

    .canvas-container {
        padding-top: 100%; /* Делаем квадратным на мобильных */
    }

      .history-entry {
          width: 50px; /* Уменьшаем ширину записей истории для маленьких экранов */
          font-size: 12px; /* Уменьшаем размер шрифта */
      }
  }

  /* Адаптация для планшетов */
  @media (min-width: 768px) and (max-width: 1024px) {
      .plinko-sidebar {
          width: 40%; /* Блок управления занимает меньше места */
      }

      .plinko-game {
          width: 58%; /* Блок игры занимает больше места */
      }
  }
  @media (max-width: 320px) {
      .plinko-container {
          padding: 5px;
      }

      .canvas-container {
          padding-top: 120%; /* Увеличиваем высоту для лучшей видимости */
      }
  }
</style>
    </style>

    <script>
    const GAME_CONFIG = {
        ANIMATION_SPEED: 1000,
        RESULT_DELAY: 500,
        RESET_DELAY: 1000,
        MIN_BET: 0.01,
        MAX_BET: 100
    };

    // Множители только для визуального отображения
    const DISPLAY_MULTIPLIERS = {
        8: {
            low: [5.6, 2.1, 1.1, 0.5, 1.0, 1.1, 2.1, 5.6],
            medium: [5.6, 2.1, 1.1, 0.5, 1.0, 1.1, 2.1, 5.6],
            high: [5.6, 2.1, 1.1, 0.5, 1.0, 1.1, 2.1, 5.6]
        },
        12: {
            low: [8.4, 3.2, 1.7, 1.1, 0.7, 0.4, 0.4, 0.7, 1.1, 1.7, 3.2, 8.4],
            medium: [8.4, 3.2, 1.7, 1.1, 0.7, 0.4, 0.4, 0.7, 1.1, 1.7, 3.2, 8.4],
            high: [8.4, 3.2, 1.7, 1.1, 0.7, 0.4, 0.4, 0.7, 1.1, 1.7, 3.2, 8.4]
        },
        16: {
            low: [1000, 130, 26, 9, 4, 2, 0.2, 0.2, 0.2, 0.2, 0.2, 2, 4, 9, 26, 130, 1000],
            medium: [1000, 130, 26, 9, 4, 2, 0.2, 0.2, 0.2, 0.2, 0.2, 2, 4, 9, 26, 130, 1000],
            high: [1000, 130, 26, 9, 4, 2, 0.2, 0.2, 0.2, 0.2, 0.2, 2, 4, 9, 26, 130, 1000]
        }
    };

    let currentGameState = {
        isPlaying: false,
        ws: null,
        clientSeed: generateRandomSeed()
    };

    document.addEventListener('DOMContentLoaded', function() {
        const riskSelect = document.getElementById('risk-level');
        const rowsSelect = document.getElementById('rows');
        initializeWebSocket();
        initializeGame();
        document.getElementById('client-seed').value = currentGameState.clientSeed;

        function updateGameField() {
            const canvas = document.getElementById('plinko-canvas');
            const ctx = canvas.getContext('2d');
            drawGameField(ctx);
        }

        riskSelect.addEventListener('change', updateGameField);
        rowsSelect.addEventListener('change', updateGameField);

        // Инициализация игрового поля
        updateGameField();
    });

    function initializeWebSocket() {
        currentGameState.ws = new WebSocket('wss://teybetsocket.com:3001');

        currentGameState.ws.onopen = function() {
            // Отправляем все куки
            currentGameState.ws.send(JSON.stringify({
                type: 'auth',
                cookies: document.cookie
            }));
        };

        currentGameState.ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            console.log('Received message:', data);

            switch(data.type) {
                case 'auth_success':
                    console.log('Successfully authenticated');
                    break;
                case 'game_initialized':
                    // console.log('Handling game result:', data);
                    handleGameStart(data);
                    // handleGameResult(data);
                    break;
                case 'error':
                    showNotification(data.message, 'error');
                    resetGame();
                    break;
            }
        };

        currentGameState.ws.onerror = function(error) {
            console.error('WebSocket error:', error);
            showNotification('Connection Error', 'error');
        };

        currentGameState.ws.onclose = function() {
            console.log('WebSocket closed, attempting to reconnect...');
            setTimeout(initializeWebSocket, 1000);
        };
    }

    function initializeGame() {
        const canvas = document.getElementById('plinko-canvas');
        const ctx = canvas.getContext('2d');

        function resizeCanvas() {
            const container = canvas.parentElement;
            const rect = container.getBoundingClientRect();

            // Устанавливаем canvas равным размеру контейнера
            canvas.width = rect.width;
            canvas.height = rect.height;

            // Перерисовываем игровое поле с новыми размерами
            drawGameField(ctx);
        }

        // Вызываем resizeCanvas при загрузке и при изменении размера окна
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        // Добавляем слушатель ориентации для мобильных устройств
        window.addEventListener('orientationchange', function() {
            setTimeout(resizeCanvas, 100); // Небольшая задержка для правильного пересчета размеров
        });
    }

    function scaleCanvas(canvas, ctx) {
        const devicePixelRatio = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        const width = rect.width;
        const height = rect.height;

        canvas.width = width * devicePixelRatio;
        canvas.height = height * devicePixelRatio;
        ctx.scale(devicePixelRatio, devicePixelRatio);
    }


    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    function startGame() {
        if (currentGameState.isPlaying || currentGameState.ws.readyState !== WebSocket.OPEN) return;

        const betAmount = parseFloat(document.getElementById('bet-amount').value);
        const riskLevel = document.getElementById('risk-level').value;
        const rows = parseInt(document.getElementById('rows').value);

        if (betAmount <= 0) {
            showNotification('Enter a valid bet amount', 'error');
            return;
        }

        currentGameState.isPlaying = true;
        togglePlayButton(true);

        currentGameState.ws.send(JSON.stringify({
            type: 'plinko_init',
            user_id: '{{$u->game_token}}',
            bet_amount: betAmount,
            risk_level: riskLevel,
            rows: rows,
            client_seed: currentGameState.clientSeed
        }));
    }

    function handleGameStart(data) {
        const startSound = new Audio('/assets/sounds/start.mp3');
        startSound.play();


        animateBall(data.positions).then(() => {
            handleGameResult(data);
        });
    }

    function handleGameResult(data) {
        const pinSound = new Audio('/assets/sounds/pin.mp3');
        pinSound.play();

        // Обновление баланса и вывод результата
        updateBalance(data.new_balance);
        showGameResult(data.multiplier, data.win_amount);
        addToGameHistory({
            bet_amount: parseFloat(document.getElementById('bet-amount').value),
            win_amount: data.win_amount,
            multiplier: data.multiplier
        });

        setTimeout(resetGame, GAME_CONFIG.RESET_DELAY);
    }


    function animateBall(positions) {
        return new Promise((resolve) => {
            const canvas = document.getElementById('plinko-canvas');
            const ctx = canvas.getContext('2d');
            const rows = parseInt(document.getElementById('rows').value);

            let x = canvas.width / 2;
            let y = 30;
            let velocityY = 0;
            let currentRow = 0;

            const gravity = 0.1;
            const bounce = 0.6;

            // Расчёт отступов
            const topMargin = 50;
            const bottomMargin = 80;
            const availableHeight = canvas.height - topMargin - bottomMargin;
            const verticalSpacing = availableHeight / (rows + 1);

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                drawGameField(ctx);

                velocityY += gravity;
                y += velocityY;

                if (currentRow < positions.length) {
                    const pinY = topMargin + currentRow * verticalSpacing;

                    if (y >= pinY) {
                        const direction = positions[currentRow];
                        x += direction * 45; // Предположим, что `45` — это расстояние между пинами.
                        velocityY *= bounce;
                        currentRow++;
                    }
                }

                drawBall(ctx, x, y);

                if (y < canvas.height - bottomMargin) {
                    requestAnimationFrame(animate);
                } else {
                    console.log('Animation completed');
                    resolve(); // Анимация завершена, вызываем resolve
                }
            }

            animate();
        });
    }

    // Функция для расчета адаптивных отступов
    function calculateSpacing(rows, width, height) {
        const minSpacing = 30;
        const maxSpacing = 80;

        let horizontalSpacing = Math.min(
            Math.max(minSpacing, (width - 200) / (rows + 2)),
            maxSpacing
        );

        // Корректируем размер пинов в зависимости от отступов
        const pinRadius = Math.max(2, Math.min(4, horizontalSpacing / 10));

        return {
            horizontal: horizontalSpacing,
            vertical: Math.min((height - 130) / (rows + 1), maxSpacing),
            topMargin: 50,
            bottomMargin: 80,
            pinRadius: pinRadius
        };
    }

    function drawBallWithTrail(ctx, x, y, velocityX, velocityY) {
        const speed = Math.sqrt(velocityX * velocityX + velocityY * velocityY);
        const trailLength = Math.min(speed * 0.5, 10);

        // Рисуем след
        if (speed > 1) {
            const gradient = ctx.createLinearGradient(
                x - velocityX * trailLength, y - velocityY * trailLength,
                x, y
            );
            gradient.addColorStop(0, 'rgba(0, 255, 0, 0)');
            gradient.addColorStop(1, 'rgba(0, 255, 0, 0.2)');

            ctx.beginPath();
            ctx.strokeStyle = gradient;
            ctx.lineWidth = 6;
            ctx.lineCap = 'round';
            ctx.moveTo(x - velocityX * trailLength, y - velocityY * trailLength);
            ctx.lineTo(x, y);
            ctx.stroke();
        }

        // Рисуем шарик
        const ballGradient = ctx.createRadialGradient(x - 2, y - 2, 0, x, y, 5);
        ballGradient.addColorStop(0, '#FFFFFF');
        ballGradient.addColorStop(0.5, '#00FF00');
        ballGradient.addColorStop(1, '#00CC00');

        ctx.beginPath();
        ctx.fillStyle = ballGradient;
        ctx.arc(x, y, 5, 0, Math.PI * 2);
        ctx.fill();

        // Блик на шарике
        ctx.beginPath();
        ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
        ctx.arc(x - 2, y - 2, 2, 0, Math.PI * 2);
        ctx.fill();
    }

    function drawGameField(ctx) {
        const rows = parseInt(document.getElementById('rows').value);
        ctx.fillStyle = '#1A1D24';
        ctx.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        drawPins(ctx, rows);
        drawMultipliers(ctx);
    }

    function drawPins(ctx, rows) {
        const width = ctx.canvas.width;
        const height = ctx.canvas.height;
        const multiplierHeight = height * 0.12; // Резервируем место для множителей

        const topMargin = height * 0.1; // 10% сверху
        const availableHeight = height - topMargin - multiplierHeight;
        const verticalSpacing = availableHeight / (rows + 1);

        // Рассчитываем горизонтальный отступ в зависимости от количества рядов
        const horizontalSpacing = Math.min(width / (rows + 2), 50);

        const startX = width / 2;
        const pinRadius = Math.min(horizontalSpacing * 0.15, 4); // Адаптивный размер пинов

        // Рисуем пины
        for (let row = 0; row < rows; row++) {
            const pinsInRow = row + 1;
            const rowWidth = pinsInRow * horizontalSpacing;
            const startXForRow = startX - (rowWidth / 2) + (horizontalSpacing / 2);

            for (let pin = 0; pin < pinsInRow; pin++) {
                const x = startXForRow + (pin * horizontalSpacing);
                const y = topMargin + (row * verticalSpacing);

                drawPin(ctx, x, y, pinRadius);
            }
        }
    }

    function drawPin(ctx, x, y, radius) {
        // Внешнее свечение
        const glowSize = radius * 1.5;
        const glow = ctx.createRadialGradient(x, y, 0, x, y, glowSize);
        glow.addColorStop(0, 'rgba(255, 255, 255, 0.8)');
        glow.addColorStop(0.5, 'rgba(255, 255, 255, 0.2)');
        glow.addColorStop(1, 'rgba(255, 255, 255, 0)');

        ctx.beginPath();
        ctx.fillStyle = glow;
        ctx.arc(x, y, glowSize, 0, Math.PI * 2);
        ctx.fill();

        // Основной пин
        const gradient = ctx.createRadialGradient(
            x - radius/3, y - radius/3, 0,
            x, y, radius
        );
        gradient.addColorStop(0, '#ffffff');
        gradient.addColorStop(1, '#cccccc');

        ctx.beginPath();
        ctx.fillStyle = gradient;
        ctx.arc(x, y, radius, 0, Math.PI * 2);
        ctx.fill();

        // Блик
        ctx.beginPath();
        ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
        ctx.arc(x - radius/3, y - radius/3, radius/3, 0, Math.PI * 2);
        ctx.fill();
    }

    function drawBall(ctx, x, y) {
        ctx.beginPath();
        ctx.fillStyle = '#00FF00';
        ctx.shadowColor = '#00FF00';
        ctx.shadowBlur = 8;
        ctx.arc(x, y, 5, 0, Math.PI * 2);
        ctx.fill();
        ctx.shadowBlur = 0;
    }

    function drawMultipliers(ctx) {
        const rows = parseInt(document.getElementById('rows').value);
        const riskLevel = document.getElementById('risk-level').value;
        const multipliers = DISPLAY_MULTIPLIERS[rows][riskLevel];
        const width = ctx.canvas.width;
        const height = ctx.canvas.height;

        // Рассчитываем размеры для множителей
        const boxWidth = Math.min(width / multipliers.length, 60);
        const boxHeight = Math.min(height * 0.08, 35); // 8% от высоты canvas, но не больше 35px
        const bottomMargin = height * 0.05; // 5% от высоты canvas

        multipliers.forEach((mult, index) => {
            const x = (width - (boxWidth * multipliers.length)) / 2 + (index * boxWidth);
            const y = height - boxHeight - bottomMargin;

            // Рисуем множитель с увеличенным размером для мобильных
            drawMultiplierBox(ctx, x, y, boxWidth - 2, boxHeight, mult);
        });
    }

    function getMultiplierColor(multiplier) {
        if (multiplier >= 41) return '#FF4B4B';
        if (multiplier >= 10) return '#FF7A00';
        if (multiplier >= 3) return '#FF9A00';
        if (multiplier >= 1) return '#FFB636';
        return '#FFD700';
    }

    function adjustColor(color, amount) {
        const hex = color.replace('#', '');
        const num = parseInt(hex, 16);
        const r = Math.min(255, Math.max(0, (num >> 16) + amount));
        const g = Math.min(255, Math.max(0, ((num >> 8) & 0x00FF) + amount));
        const b = Math.min(255, Math.max(0, (num & 0x0000FF) + amount));
        return `#${(r << 16 | g << 8 | b).toString(16).padStart(6, '0')}`;
    }

    function drawMultiplierBox(ctx, x, y, width, height, multiplier) {
        const radius = 6;
        const baseColor = getMultiplierColor(multiplier);

        // Градиент для фона
        const gradient = ctx.createLinearGradient(x, y, x, y + height);
        gradient.addColorStop(0, baseColor);
        gradient.addColorStop(1, adjustColor(baseColor, -20));

        // Фон с закруглёнными углами
        ctx.beginPath();
        ctx.roundRect(x, y, width, height, radius);
        ctx.fillStyle = gradient;
        ctx.fill();

        // Текст множителя
        ctx.fillStyle = '#FFFFFF';
        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(`${multiplier}x`, x + width/2, y + height/2);
    }

    // Вспомогательная функция для скругленных прямоугольников
    function roundRect(ctx, x, y, width, height, radius) {
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
    }

    function updateGameInfo(data) {
        if (data.hash) {
            document.getElementById('game-hash').textContent = data.hash;
        }
        if (data.server_seed) {
            document.getElementById('server-seed').textContent = data.server_seed;
        }
        if (data.new_balance !== undefined) {
            updateBalance(data.new_balance);
        }
    }

    function updateBalance(newBalance) {
        const balanceElement = document.querySelector('.balance-amount');
        if (balanceElement) {
            balanceElement.textContent = parseFloat(newBalance).toFixed(2);
        }
    }

    function showGameResult(multiplier, winAmount) {
        const resultText = `Выигрыш: ${winAmount.toFixed(2)} (x${multiplier.toFixed(2)})`;
        showNotification(resultText, multiplier >= 1 ? 'success' : 'info');
    }

    function showNotification(message, type = 'info') {
        new Noty({
            text: `<div class="noty-content">
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
                <span>${message}</span>
            </div>`,
            type: type === 'error' ? 'error' : 'success',
            layout: 'topLeft',
            theme: 'mint',
            timeout: 3000,
            progressBar: true,
            closeWith: ['click', 'button']
        }).show();
    }

    function togglePlayButton(disabled) {
        const playButton = document.getElementById('play-button');
        playButton.disabled = disabled;
        playButton.querySelector('.btn-text').style.display = disabled ? 'none' : 'block';
        playButton.querySelector('.btn-loader').style.display = disabled ? 'block' : 'none';
    }

    function resetGame() {
        currentGameState.isPlaying = false;
        togglePlayButton(false);
    }

    function generateRandomSeed() {
        return Math.random().toString(36).substring(2, 15);
    }

    function generateNewSeed() {
        currentGameState.clientSeed = generateRandomSeed();
        document.getElementById('client-seed').value = currentGameState.clientSeed;
    }
    function addToGameHistory(gameData) {
        const historyContainer = document.getElementById('game-history');
        const multiplier = parseFloat(gameData.multiplier) || 0;

        // Создаем элемент истории с отображением только коэффициента
        const historyEntry = document.createElement('div');
        historyEntry.className = 'history-entry';
        historyEntry.textContent = `${multiplier.toFixed(1)}x`;

        // Добавляем новый элемент в начало истории
        historyContainer.insertBefore(historyEntry, historyContainer.firstChild);

        // Плавное появление
        requestAnimationFrame(() => {
            historyEntry.classList.add('show');
        });

        // Ограничиваем количество записей в истории до 5
        if (historyContainer.children.length > 5) {
            const lastEntry = historyContainer.lastChild;

            // Проверяем, не висит ли элемент на удалении
            if (!lastEntry.classList.contains('remove')) {
                lastEntry.classList.add('remove');

                // Удаление элемента после завершения анимации исчезновения
                lastEntry.addEventListener('transitionend', () => {
                    if (lastEntry.parentElement && lastEntry.classList.contains('remove')) {
                        historyContainer.removeChild(lastEntry);
                    }
                }, { once: true }); // Используем { once: true }, чтобы событие выполнялось только один раз
            }
        }
    }




      </script>
</x-layouts.app>
