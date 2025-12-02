<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Rank;
use App\Models\Transaction;
use App\Models\User;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UpdateUserOborotCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем необходимые данные
        Currency::create(['id' => 1, 'symbol' => 'USD', 'name' => 'US Dollar']);
        Rank::create(['id' => 1, 'name' => 'Beginner', 'oborot_min' => 0, 'rakeback' => 1]);
        Rank::create(['id' => 2, 'name' => 'Intermediate', 'oborot_min' => 1000, 'rakeback' => 2]);
    }

    public function testUpdateUserOborot()
    {
        // Создаем пользователя
        $user = User::factory()->create(['rank_id' => 1, 'oborot' => 0, 'rakeback' => 0]);

        // Создаем транзакции
        $this->createTransactions($user);

        // Запускаем команду
        $this->artisan('oborot:update')->assertSuccessful();

        // Проверяем результаты
        $user->refresh();
        $this->assertEquals(1500, $user->oborot);
        $this->assertEquals(2, $user->rank_id);
        $this->assertEquals(30, $user->rakeback);

        // Проверяем создание транзакции рейкбека
        $rakebackTransaction = Transaction::where('user_id', $user->id)
            ->where('type', TransactionType::Bonus)
            ->first();
        $this->assertNotNull($rakebackTransaction);
        $this->assertEquals(30, $rakebackTransaction->amount);
    }

    private function createTransactions($user)
    {
        $hourAgo = Carbon::now()->subHour();

        Transaction::create([
            'user_id' => $user->id,
            'amount' => 1000,
            'currency_id' => 1,
            'type' => TransactionType::Bet,
            'status' => TransactionStatus::Success,
            'created_at' => $hourAgo->addMinutes(30),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'amount' => 500,
            'currency_id' => 1,
            'type' => TransactionType::Bet,
            'status' => TransactionStatus::Success,
            'created_at' => $hourAgo->addMinutes(45),
        ]);
    }
}
