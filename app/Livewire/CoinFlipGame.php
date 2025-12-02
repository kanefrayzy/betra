<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\CoinFlip;
use App\Services\ExchangeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class CoinFlipGame extends Component
{
    public $bet = 0;
    public $selectedSides = [];
    public $playing = false;
    public $result = null;
    public $message = null;
    public $won = false;
    public $history = [];
    public $animationComplete = false;

    public $gameCurrency = null;
    public $gameBetAmount = 0;

    const MIN_BET_AZN = 1;
    const MAX_BET_AZN = 1000;

    protected ExchangeService $exchangeService;

    public function boot(ExchangeService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    public function mount()
    {
        $this->loadHistory();
    }


    protected function validateBet()
    {
        try {
            $user = Auth::user();
            $this->bet = round((float)$this->bet, 2);
            $totalBet = $this->bet * count($this->selectedSides);

            // Конвертируем ставку в AZN для проверки лимитов
            $betAmountAZN = $totalBet;
            if ($user->currency->symbol !== 'AZN') {
                $betAmountAZN = $this->exchangeService->convert(
                    $totalBet,
                    $user->currency->symbol,
                    'AZN'
                );
            }

            // Проверяем минимальную ставку
            if ($betAmountAZN < self::MIN_BET_AZN) {
                $minInUserCurrency = $this->exchangeService->convert(
                    self::MIN_BET_AZN,
                    'AZN',
                    $user->currency->symbol
                );
                throw new Exception(__('Минимальная ставка :amount :currency', [
                    'amount' => $minInUserCurrency,
                    'currency' => $user->currency->symbol
                ]));
            }

            // Проверяем максимальную ставку
            if ($betAmountAZN > self::MAX_BET_AZN) {
                $maxInUserCurrency = $this->exchangeService->convert(
                    self::MAX_BET_AZN,
                    'AZN',
                    $user->currency->symbol
                );
                $this->bet = $maxInUserCurrency / count($this->selectedSides);
                throw new Exception(__('Максимальная ставка :amount :currency', [
                    'amount' => $maxInUserCurrency,
                    'currency' => $user->currency->symbol
                ]));
            }

            // Проверяем баланс
            if ($user->balance < $totalBet) {
                $this->bet = floor($user->balance / max(1, count($this->selectedSides)));
                throw new Exception(__('Недостаточно средств'));
            }

        } catch (Exception $e) {
            $this->message = $e->getMessage();
        }
    }

    public function toggleSide($side)
    {
        if ($this->playing) return;

        if (in_array($side, $this->selectedSides)) {
            $this->selectedSides = array_values(array_diff($this->selectedSides, [$side]));
        } else {
            $this->selectedSides[] = $side;
        }

        $this->validateBet();
    }

    #[On('animationComplete')]
    public function handleAnimationComplete()
    {
        $this->processResults();
    }

    public function play()
    {
        if ($this->playing) {
            return;
        }

        try {
            $this->validate([
                'bet' => 'required|numeric|min:0.01',
                'selectedSides' => 'required|array|min:1|max:2',
            ]);

            $this->bet = round((float)$this->bet, 2);

            $user = Auth::user();
            $totalBet = $this->bet * count($this->selectedSides);


            // Фиксируем валюту и сумму ставки в момент начала игры
            $this->gameCurrency = $user->currency->symbol;
            $this->gameBetAmount = $this->bet;

            // Конвертируем для проверки лимитов
            $betAmountAZN = $totalBet;
            if ($this->gameCurrency !== 'AZN') {
                $betAmountAZN = $this->exchangeService->convert(
                    $totalBet,
                    $this->gameCurrency,
                    'AZN'
                );
            }

            if ($betAmountAZN < self::MIN_BET_AZN || $betAmountAZN > self::MAX_BET_AZN) {
                throw new Exception(__('Неверная сумма ставки'));
            }

            if ($user->balance < $totalBet) {
                throw new Exception(__('Недостаточно средств'));
            }

            $this->message = null;
            $this->playing = true;
            $this->animationComplete = false;

            // Изменяем шансы в зависимости от количества выбранных сторон
            if (count($this->selectedSides) === 1) {
                // При ставке на один цвет шанс 40%
                $winChance = 25;

                // Если выбрана красная сторона
                if ($this->selectedSides[0] === 'red') {
                    $this->result = random_int(1, 100) <= $winChance ? 'red' : 'blue';
                }
                // Если выбрана синяя сторона
                else {
                    $this->result = random_int(1, 100) <= $winChance ? 'blue' : 'red';
                }
            } else {
                // При ставке на оба цвета шанс 50/50
                $this->result = random_int(0, 100) < 50 ? 'red' : 'blue';
            }

        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->playing = false;
        }
    }

    protected function processResults()
    {
        if (!$this->playing) return;

        try {
            $user = Auth::user();
            $totalWin = 0;

            // Конвертируем ставку если нужно
            $currentBet = $this->gameBetAmount;
            if ($user->currency->symbol !== $this->gameCurrency) {
                $currentBet = $this->exchangeService->convert(
                    $this->gameBetAmount,
                    $this->gameCurrency,
                    $user->currency->symbol
                );
            }

            DB::transaction(function() use ($user, &$totalWin, $currentBet) {
                $totalBet = $currentBet * count($this->selectedSides);

                if ($user->fresh()->balance < $totalBet) {
                    throw new Exception(__('Недостаточно средств'));
                }

                // Списываем ставку
                $user->decrement('balance', $totalBet);

                // Обрабатываем вагеринг если нужно
                if ($user->hasActiveWagering()) {
                    $betAmountWag = $totalBet;
                    if ($user->currency->symbol !== 'AZN') {
                        $betAmountWag = $this->exchangeService->convert(
                            $totalBet,
                            $user->currency->symbol,
                            'AZN'
                        );
                    }
                    $user->addToWageringAmount($betAmountWag);
                }

                // Проверяем выигрыш для каждой выбранной стороны
                foreach ($this->selectedSides as $side) {
                    $hasWon = $side === $this->result;

                    if ($hasWon) {
                        // Рассчитываем выигрыш
                        $winAmountAZN = $currentBet * 2;
                        if ($user->currency->symbol !== 'AZN') {
                            $winAmountAZN = $this->exchangeService->convert(
                                $currentBet * 2,
                                $user->currency->symbol,
                                'AZN'
                            );
                        }

                        $winAmount = $winAmountAZN;
                        if ($user->currency->symbol !== 'AZN') {
                            $winAmount = $this->exchangeService->convert(
                                $winAmountAZN,
                                'AZN',
                                $user->currency->symbol
                            );
                        }

                        // Начисляем выигрыш
                        $totalWin += $winAmount;
                        $user->increment('balance', $winAmount);
                    }

                    // Сохраняем историю
                    CoinFlip::create([
                        'user_id' => $user->id,
                        'bet' => $this->gameBetAmount,
                        'side' => $side,
                        'choice' => $side,
                        'result' => $hasWon,
                        'won' => $hasWon,
                        'is_winner' => $hasWon ? 1 : 0,
                        'currency' => $this->gameCurrency
                    ]);
                }
            });

            // Устанавливаем сообщение о результате
            $this->won = $totalWin > 0;
            if ($totalWin > 0) {
                $this->message = __('Вы выиграли :amount :currency', [
                    'amount' => number_format($totalWin, 2),
                    'currency' => $user->currency->symbol
                ]);
            } else {
                $this->message = __('Вы проиграли :amount :currency', [
                    'amount' => number_format($currentBet * count($this->selectedSides), 2),
                    'currency' => $user->currency->symbol
                ]);
            }

        } catch (Exception $e) {
            $this->message = __('Произошла ошибка при обработке результатов');
            \Log::error('CoinFlip processing error: ' . $e->getMessage());
        }

        $this->loadHistory();
        $this->playing = false;
        $this->gameCurrency = null;
        $this->gameBetAmount = 0;
    }
    public function loadHistory()
    {
        $this->history = CoinFlip::where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();
    }

    public function render()
    {
        $user = Auth::user();

        $minBet = self::MIN_BET_AZN;
        $maxBet = self::MAX_BET_AZN;

        if ($user->currency->symbol !== 'AZN') {
            $minBet = $this->exchangeService->convert(
                self::MIN_BET_AZN,
                'AZN',
                $user->currency->symbol
            );
            $maxBet = $this->exchangeService->convert(
                self::MAX_BET_AZN,
                'AZN',
                $user->currency->symbol
            );
        }

        return view('livewire.coin-flip-game', [
            'minBet' => $minBet,
            'maxBet' => $maxBet
        ]);
    }
}
