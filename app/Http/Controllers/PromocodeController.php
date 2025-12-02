<?php

namespace App\Http\Controllers;
use App\Models\Promocode;
use App\Services\ExchangeService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\Notify;

class PromocodeController extends Controller
{
   protected $exchangeService;
   protected $telegramService;

   public function __construct(TelegramService $telegramService, ExchangeService $exchangeService)
   {
       $this->exchangeService = $exchangeService;
       $this->telegramService = $telegramService;
   }

   public function activate(Request $request)
   {
       $request->validate([
           'code' => 'required|string'
       ]);

       try {
           DB::beginTransaction();

           $user = auth()->user();
           $currencySymbol = $user->currency->symbol;

           $promocode = Promocode::where('code', $request->code)
               ->where('is_active', true)
               ->first();

           if (!$promocode) {
               return response()->json([
                   'success' => false,
                   'message' => __('Такой промокод не существует')
               ], 404);
           }

           if (!$user->telegram_id) {
             return response()->json([
                 'success' => false,
                 'message' => __('Для получения бонуса необходимо привязать Telegram аккаунт в настройках')
             ], 404);
           }

           if (!$this->telegramService->isUserMember($user->telegram_id)) {
             return response()->json([
                 'success' => false,
                 'message' => __('Для получения промокода необходимо быть участником нашей группы в Telegram')
             ], 404);
           }

           if (!$promocode->isAvailable()) {
               return response()->json([
                   'success' => false,
                   'message' => __('Промокод больше не действителен')
               ]);
           }

           if ($promocode->isUsedByUser($user->id)) {
               return response()->json([
                   'success' => false,
                   'message' => __('Вы уже активировали этот промокод')
               ]);
           }

           // Определяем сумму
           $amount = $promocode->amount_type === 'fixed'
               ? $promocode->amount
               : rand($promocode->min_amount * 100, $promocode->max_amount * 100) / 100;

           // Конвертируем если не AZN
           if ($currencySymbol != 'AZN') {
               $amount = $this->exchangeService->convert($amount, 'AZN', $currencySymbol);
           }

           // Создаем запись об использовании
           $promocode->claims()->create([
               'user_id' => $user->id,
               'amount' => $amount
           ]);

           // Увеличиваем счетчик использований
           $promocode->increment('used_count');

           // Начисляем бонус пользователю
           $user->increment('balance', $amount);

           DB::commit();

           $messageNotify = __('Вы получили бонус :amount :currency', [
               'amount' => number_format($amount, 2),
               'currency' => $currencySymbol
           ]);

           $user->notify(Notify::send('bonus', ['message' => $messageNotify]));


           return response()->json([
               'success' => true,
               'message' => __('Вы получили бонус :amount :currency', [
                   'amount' => number_format($amount, 2),
                   'currency' => $currencySymbol
               ]),
               'amount' => $amount
           ]);

       } catch (\Exception $e) {
           DB::rollBack();
           \Log::error('Promocode error: ' . $e->getMessage());

           return response()->json([
               'success' => false,
               'message' => __('Произошла ошибка при активации промокода')
           ], 400);
       }
   }
}
