<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\TelegramWebViewController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ManualDepositController;
use App\Http\Controllers\Games\SlotsController;
use App\Http\Controllers\Games\Tbs2Controller;
use App\Http\Controllers\Games\UnifiedSlotsController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\AdminPromocodeController;
use App\Http\Controllers\PromocodeController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TelegramBonusController;
use App\Http\Controllers\PayKassaIPNController;
use App\Http\Controllers\FreeKassaCallbackController;
use App\Http\Controllers\StreamPayCallbackController;
use App\Http\Controllers\BetaTransferCallbackController;
use App\Http\Controllers\Admin\RankController;
use App\Http\Controllers\Admin\PaymentSystemController;
use App\Http\Controllers\Admin\PaymentHandlerController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\TelegramBroadcastController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WebSocketController;
use App\Http\Controllers\DailyBonusController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\DetailedStatisticsController;
use App\Http\Controllers\AccountDetailedStatisticsController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\Admin\TournamentAdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\VerificationController;


Route::get('/messages', [ChatController::class, 'getMessages']);
Route::post('/send-message', [ChatController::class, 'sendMessage'])->middleware('auth');
Route::get('/public-messages', [ChatController::class, 'getPublicMessages']);
Route::get('/chat/user-info/{id}', [ChatController::class, 'getUserInfo']);
Route::post('/add-forbidden-word', [ChatController::class, 'addForbiddenWord']);
Route::post('/delete-forbidden-word', [ChatController::class, 'deleteForbiddenWord']);
Route::post('/filter-message', [ChatController::class, 'filterMessage']);
Route::delete('/delete-message/{id}', [ChatController::class, 'deleteMessage']);
Route::post('/ban-user/{id}', [ChatController::class, 'banUser']);
Route::post('/transfer-money/{userId}', [ChatController::class, 'transferMoney']);

Route::middleware(['auth'])->group(function () {

  Route::prefix('deposit')->name('manual-deposit.')->group(function () {
      Route::get('/{id}', [ManualDepositController::class, 'show'])->name('show');
      Route::post('/store', [ManualDepositController::class, 'store'])->name('store');
      Route::get('/my-deposits', [ManualDepositController::class, 'myDeposits'])->name('my-deposits');
  });
  Route::get('/daily-bonus/{token}', [DailyBonusController::class, 'showDailyBonus'])->name('daily-bonus.show');
  Route::post('/claim-daily-bonus', [DailyBonusController::class, 'claimDailyBonus'])->name('daily-bonus.claim');
    Route::post('/collect-rakeback', [AccountController::class, 'collectRakeback'])->name('collect.rakeback');
});
Route::get('/', [MainController::class, 'index'])->name('home');
Route::get('rules', [MainController::class, 'rules'])->name('rules');
// Route::get('contacts', [MainController::class, 'contacts'])->name('contacts');


Route::get('r/{affiliate_id?}', [AccountController::class, 'activateReferral'])->name('referral.activate');


Route::middleware('guest')->prefix('auth')->group(function () {
    Route::post('register', [RegisterController::class, 'register'])->name('auth.register');
    Route::post('login', [LoginController::class, 'login'])->name('auth.login');
    Route::post('ulogin', [SocialLoginController::class, 'handler'])->name('auth.ulogin');
    Route::post('ulogin/complete', [SocialLoginController::class, 'completeSocialRegistration'])->name('auth.ulogin.complete');
    
    Route::post('telegram-webview', [TelegramWebViewController::class, 'authenticate'])->name('auth.telegram-webview');
    Route::post('telegram-webview/complete', [TelegramWebViewController::class, 'completeTelegramRegistration'])->name('auth.telegram-webview.complete');

    Route::post('/forgot-password', [PasswordResetController::class, 'resetPassword'])
        ->name('password.email');
    Route::get('/password-recovery', [PasswordResetController::class, 'resetPasswordWithToken'])
        ->name('password.recovery');

//    Route::post('/reset', [AuthController::class, 'reset'])->name('reset');
//    Route::post('/password', [AuthController::class, 'password'])->name('password');
});
Route::get('/telegram/connect', [TelegramController::class, 'connect'])->name('telegram.connect');
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);
Route::get('/telegram/check-membership', [TelegramController::class, 'checkMembership'])->name('telegram.check-membership');

Route::post('/telegram/generate-auth-token', [TelegramController::class, 'generateAuthToken'])->name('telegram.generate-token');
Route::post('/telegram/check-auth-status', [TelegramController::class, 'checkAuthStatus'])->name('telegram.check-status');
Route::post('/auth/telegram-code/complete', [TelegramController::class, 'completeCodeAuth'])->name('auth.telegram-code.complete');

// GitHub Webhook для автодеплоя
Route::post('/deploy/webhook', [\App\Http\Controllers\DeployController::class, 'webhook'])->name('deploy.webhook');

// Telegram WebView API роуты
Route::get('/api/telegram/user', [TelegramWebViewController::class, 'getCurrentUser'])->name('api.telegram.user');


// leaderboard
// Route::get('/tournament', [TournamentController::class, 'index'])->name('tournament.index');
// Route::get('/tournament/show', [TournamentController::class, 'show'])->name('tournament.show');

// Route::get('/leaderboard', [LeaderboardController::class, 'getDailyLeaderboard'])->name('leaderboard');
// Route::get('/leaderboard/daily', [LeaderboardController::class, 'getDailyLeaderboard']);
Route::get('/auth/check', function () {
    return response()->json(['authenticated' => Auth::check()]);
});

Route::middleware('auth')->group(function () {
  Route::post('/promocodes/activate', [PromocodeController::class, 'activate'])
      ->name('promocodes.activate');
    Route::any('logout', [LoginController::class, 'logout'])->name('auth.logout');
    Route::get('/ref', [PagesController::class, 'ref'])->name('ref');


    Route::prefix('account')->group(function () {
        Route::get('', [AccountController::class, 'account'])->name('account');
        Route::post('avatar', [AccountController::class, 'uploadAvatar'])->name('account.avatar');
        Route::post('update', [AccountController::class, 'update'])->name('account.update');
        Route::post('password', [AccountController::class, 'password'])->name('account.password');
        Route::get('balance', [AccountController::class, 'showBalance'])->name('account.balance');
        Route::post('change-currency/{currency}', [AccountController::class, 'changeCurrency'])->name('account.change-currency');
        Route::get('referrals', [AccountController::class, 'referrals'])->name('account.referrals');
        Route::post('take-bonus', [AccountController::class, 'takeBonus'])->name('account.take-bonus');
    });

    Route::prefix('cash')->group(function () {
        Route::post('{operation}', [CashController::class, 'handler'])->name('cash.operation');
        Route::post('cancel/{transaction}', [CashController::class, 'cancel'])->name('cash.cancel');
    });

    Route::prefix('transactions')->group(function () {
        Route::get('', [TransactionController::class, 'index'])->name('transaction');
        Route::get('deposit', [TransactionController::class, 'deposit'])->name('transaction.deposit');
        Route::get('withdrawal', [TransactionController::class, 'withdrawal'])->name('transaction.withdrawal');
        Route::get('games', [TransactionController::class, 'games'])->name('transaction.games');
        Route::get('others', [TransactionController::class, 'others'])->name('transaction.others');
    });

    // telegram
    Route::get('bonus', [TelegramBonusController::class, 'show'])->name('telegram-bonus.show');
    Route::post('bonus/claim', [TelegramBonusController::class, 'claim'])->name('telegram-bonus.claim');
    Route::post('/verification', [VerificationController::class, 'store'])->name('verification.store');

});

Route::prefix('slots')->group(function () {
    Route::get('lobby', \App\Livewire\Game\Main::class)->name('slots.lobby');
    Route::get('live', \App\Livewire\Game\Live::class)->name('slots.live');
    Route::get('blackjack', \App\Livewire\Game\Blackjack::class)->name('slots.blackjack');
    Route::get('table', \App\Livewire\Game\Table::class)->name('slots.table');
    Route::get('roulette', \App\Livewire\Game\Roulette::class)->name('slots.roulette');
    Route::get('higher', \App\Livewire\Game\Higher::class)->name('slots.higher');
    Route::get('new', \App\Livewire\Game\NewGames::class)->name('slots.new');
    Route::get('limits', [SlotsController::class, 'limits'])->name('slots.limits');
    Route::get('popular', App\Livewire\Game\Popular::class)->name('slots.popular');

    // Dynamic category routes
    Route::get('category/{slug}', \App\Livewire\Game\Category::class)->name('slots.category');

    //  автоматически определяют провайдера
    Route::get('fun/{slug}', [UnifiedSlotsController::class, 'launchDemoGame'])->name('slots.fun');
    Route::post('balance', [SlotsController::class, 'getBalance'])->name('slots.balance');
    Route::get('info-slot', [SlotsController::class, 'infoSlot'])->name('slots.info');

    Route::middleware('auth')->group(function () {
        Route::get('play/{slug}', [UnifiedSlotsController::class, 'launchGame'])->name('slots.play');
        Route::get('play-mobile/{slug}', [UnifiedSlotsController::class, 'launchGame'])->name('slots.mobile');
        Route::get('favorites', \App\Livewire\Game\Favorites::class)->name('slots.favorites');
        Route::get('history', \App\Livewire\Game\History::class)->name('slots.history');
    });
});

// TBS2 callback'и
Route::post('games/tbs2/getBalance', [Tbs2Controller::class, 'getBalance'])->name('tbs2.getBalance');
Route::post('games/tbs2/writeBet', [Tbs2Controller::class, 'writeBet'])->name('tbs2.writeBet');
Route::get('games/close', [Tbs2Controller::class, 'closeGame'])->name('tbs2.close');
Route::any('games/tbs2/test', function() {
    return response()->json([
        'status' => 'test_ok',
        'time' => now(),
        'config' => [
            'hall_id' => config('services.tbs2.hall_id'),
            'has_hall_key' => !empty(config('services.tbs2.hall_key')),
        ]
    ]);
});

# Slotegraotr
Route::post('games/slotegrator/callback', [SlotsController::class, 'callback'])->name('slots.callback');

# B2B
Route::post('games/b2b-slots/callback', [B2bSlotsController::class, 'callback'])->name('b2b-slots.callback');

# TBS 2
Route::post('games/tbs2/callback', [Tbs2Controller::class, 'callback'])->name('tbs2.callback');


Route::get('/latest-transactions', [TransactionController::class, 'getLatestTransactions']);

Route::post('pay/paykassa', [PayKassaIPNController::class, 'handle']);
Route::post('pay/freekassa', [FreeKassaCallbackController::class, 'handle']);
Route::post('pay/payteez', [PayteezCallbackController::class, 'handle']);
Route::post('pay/streampay', [StreamPayCallbackController::class, 'handle']);
Route::post('pay/betatransfer', [BetaTransferCallbackController::class, 'handle'])->name('betatransfer.callback');
Route::post('pay/westwallet', [App\Http\Controllers\WestWalletCallbackController::class, 'handle'])->name('westwallet.callback');

// Crypto wallet address endpoint
Route::middleware('auth')->post('/crypto/get-address', [CashController::class, 'getCryptoAddress'])->name('crypto.get-address');

Route::get('/success', function () {
    return redirect('/')->with('success', __('Вы успешно пополнили баланс'));
})->name('payment.success');

Route::get('/fail', function () {
    return redirect('/')->with('error', __('Ошибка при пополнении баланса'));
})->name('payment.fail');

Route::get('/test-westwallet', function() {
    $service = new \App\Services\WestWalletService();
    $currencies = $service->getCurrenciesData();
    
    // Выводим весь ответ
    dd([
        'full_response' => $currencies,
        'has_data' => isset($currencies['data']),
        'has_error' => isset($currencies['error']),
        'error_value' => $currencies['error'] ?? null,
    ]);
});
Route::middleware(['auth', 'access:Admin'])->group(function () {
    $base = 'betrika';
    Route::get('/' . $base, [AdminController::class, 'index'])->name('Admin');
    Route::get('/' . $base . '/stats', [AdminController::class, 'stats'])->name('adminStats');
    Route::get('/' . $base . '/statscategory', [AdminController::class, 'statsCategory'])->name('statsCategory');
    Route::get('/' . $base . '/statistics', [AdminController::class, 'statistics'])->name('statisticsAll');
    Route::get('/' . $base . '/newstat', [AdminController::class, 'statsFromDate'])->name('newstat');
    Route::get('/' . $base . '/users', [AdminController::class, 'users'])->name('adminUsers');
    Route::get('/' . $base . '/user/{id}', [AdminController::class, 'user'])->name('adminUser');
  	Route::get('/' . $base . '/userIdAuth/{id}', [AdminController::class, 'userIdAuth']);
    Route::get('/' . $base . '/settings', [AdminController::class, 'settings'])->name('adminSettings');
  	Route::post('/' . $base . '/addSmile', [AdminController::class, 'addSmile']);
    Route::post('/' . $base . '/deleteSmile', [AdminController::class, 'deleteSmile']);
    Route::get('/' . $base . '/withdraw', [AdminController::class, 'withdraw'])->name('adminWithdraw');
    Route::get('/' . $base . '/inserts', [AdminController::class, 'inserts'])->name('adminInserts');
    Route::get('/' . $base . '/promo', [AdminController::class, 'promo'])->name('adminPromo');
    Route::get('/' . $base . '/user/delete/{id}', [AdminController::class, 'userDelete']);
    Route::post('/' . $base . '/userSave', [AdminController::class, 'userSave']);
    Route::post('/' . $base . '/usersAjax', [AdminController::class, 'usersAjax']);
    Route::post('/' . $base . '/promoNew', [AdminController::class, 'promoNew']);
    Route::post('/' . $base . '/promoSave', [AdminController::class, 'promoSave']);
    Route::get('/' . $base . '/promoDelete/{id}', [AdminController::class, 'promoDelete']);
    Route::post('/' . $base . '/settingSave', [AdminController::class, 'settingsSave']);
    Route::get('/' . $base . '/withdraw/{id}', [AdminController::class, 'withdrawSend']);
    Route::get('/' . $base . '/inserts/{id}', [AdminController::class, 'InsertsSend']);
    Route::get('/' . $base . '/return/{id}', [AdminController::class, 'withdrawReturn']);
    Route::get('/' . $base . '/verify/{id}', [AdminController::class, 'verifyUser']);
    Route::get('/' . $base . '/wban/{id}', [AdminController::class, 'wbanUser']);

    Route::get('/' . $base . '/payhistory/{id}', [AdminController::class, 'payHistory']);
    Route::get('/' . $base . '/gamehistory/{id}', [AdminController::class, 'gameHistory']);
    Route::get('/' . $base . '/transferhistory/{id}', [AdminController::class, 'transferHistory']);

    Route::get('/' . $base . '/words', [AdminController::class, 'showForbiddenWords'])->name('adminWords');
    Route::post('/' . $base . '/words/add', [AdminController::class, 'addForbiddenWord'])->name('adminWords.add');
    Route::post('/' . $base . '/words/delete', [AdminController::class, 'DeleteForbiddenWord'])->name('adminWords.delete');

    Route::get('/' . $base . '/slots', [AdminController::class, 'slots'])->name('slotegrator_games.slots');
    Route::get('/' . $base . '/slots/{id}/edit', [AdminController::class, 'editSlot'])->name('slotegrator_games.edit');
    Route::post('/' . $base . '/slots/{id}', [AdminController::class, 'updateSlot'])->name('slotegrator_games.update');

    // Game Categories
    Route::prefix($base . '/categories')->name('admin.categories.')->group(function () {
        Route::get('/', [\App\Http\Controllers\GameCategoryController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\GameCategoryController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\GameCategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [\App\Http\Controllers\GameCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [\App\Http\Controllers\GameCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [\App\Http\Controllers\GameCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/update-order', [\App\Http\Controllers\GameCategoryController::class, 'updateOrder'])->name('updateOrder');
        Route::post('/{category}/add-games', [\App\Http\Controllers\GameCategoryController::class, 'addGames'])->name('addGames');
        Route::delete('/{category}/games/{game}', [\App\Http\Controllers\GameCategoryController::class, 'removeGame'])->name('removeGame');
        Route::post('/{category}/update-games-order', [\App\Http\Controllers\GameCategoryController::class, 'updateGamesOrder'])->name('updateGamesOrder');
        Route::get('/{category}/search-games', [\App\Http\Controllers\GameCategoryController::class, 'searchAvailableGames'])->name('searchGames');
    });

    Route::resource('payment_handlers', PaymentHandlerController::class);

    // Ranks
    Route::prefix($base . '/ranks')->name('admin.ranks.')->group(function () {
        Route::get('/', [RankController::class, 'index'])->name('index');
        Route::get('/create', [RankController::class, 'create'])->name('create');
        Route::post('/', [RankController::class, 'store'])->name('store');
        Route::get('/{rank}/edit', [RankController::class, 'edit'])->name('edit');
        Route::put('/{rank}', [RankController::class, 'update'])->name('update');
        Route::delete('/{rank}', [RankController::class, 'destroy'])->name('destroy');
    });

    // Payment Systems
    Route::prefix($base . '/payment_systems')->name('admin.payment_systems.')->group(function () {
        Route::get('/', [PaymentSystemController::class, 'index'])->name('index');
        Route::get('/create', [PaymentSystemController::class, 'create'])->name('create');
        Route::post('/', [PaymentSystemController::class, 'store'])->name('store');
        Route::get('/{payment_system}/edit', [PaymentSystemController::class, 'edit'])->name('edit');
        Route::put('/{payment_system}', [PaymentSystemController::class, 'update'])->name('update');
        Route::delete('/{payment_system}', [PaymentSystemController::class, 'destroy'])->name('destroy');
    });

    // Payment Handlers
    Route::prefix($base . '/payment_handlers')->name('admin.payment_handlers.')->group(function () {
        Route::get('/', [PaymentHandlerController::class, 'index'])->name('index');
        Route::get('/create', [PaymentHandlerController::class, 'create'])->name('create');
        Route::post('/', [PaymentHandlerController::class, 'store'])->name('store');
        Route::get('/{payment_handler}/edit', [PaymentHandlerController::class, 'edit'])->name('edit');
        Route::post('/{payment_handler}', [PaymentHandlerController::class, 'update'])->name('update');
        Route::delete('/{payment_handler}', [PaymentHandlerController::class, 'destroy'])->name('destroy');
        Route::get('/show', [PaymentHandlerController::class, 'create'])->name('show');
    });

    Route::get('/' . $base . '/tasks', [AdminController::class, 'tasks'])->name('adminTasks');
    Route::post('/' . $base . '/tasks', [AdminController::class, 'createTask'])->name('adminCreateTask');
    Route::patch('/' . $base . '/tasks/{id}/complete', [AdminController::class, 'completeTask'])->name('adminCompleteTask');

    //
    Route::get('/' . $base . '/banned-users', [AdminController::class, 'showBannedUsers'])->name('admin.bannedUsers');
    Route::post('/' . $base . '/unban-user/{id}', [AdminController::class, 'unbanUser'])->name('admin.unbanUser');

    //
    Route::get('/' . $base . '/detailed-statistics', [DetailedStatisticsController::class, 'index'])->name('admin.detailed_statistics');
    Route::get('/' . $base . '/detailed-statistics/data', [DetailedStatisticsController::class, 'getStatistics']);
    Route::get('/' . $base . '/detailed-statistics/chart-data', [DetailedStatisticsController::class, 'getChartData']);

    // тикет
    Route::get('/' . $base . '/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
   Route::get('/' . $base . '/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
   Route::post('/' . $base . '/tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('admin.tickets.close');
   Route::post('/' . $base . '/tickets/{ticket}/message', [AdminTicketController::class, 'storeMessage'])->name('admin.tickets.message.store');


    //
    Route::post('/' . $base . '/send-mass-notification', [AdminController::class, 'sendMassNotification'])
        ->name('admin.sendMassNotification');

    Route::get('/' . $base . '/notify', [AdminController::class, 'notifyPage'])
        ->name('admin.notify');

      Route::get('/' . $base . '/get-user-info', [AdminController::class, 'getUserInfo'])->name('admin.getUserInfo');

    // promo
    Route::get('/' . $base . '/promocodes', [AdminPromocodeController::class, 'index'])->name('admin.promo.index');
    Route::get('/' . $base . '/promocodes/create', [AdminPromocodeController::class, 'create'])->name('admin.promo.create');
    Route::post('/' . $base . '/promocodes', [AdminPromocodeController::class, 'store'])->name('admin.promo.store');
    Route::delete('/' . $base . '/promocodes/{promocode}', [AdminPromocodeController::class, 'destroy'])->name('admin.promo.destroy');
    Route::post('/' . $base . '/promocodes/{promocode}/toggle', [AdminPromocodeController::class, 'toggle'])->name('admin.promo.toggle');


      // записки
      Route::get('/' . $base . '/expenses', [AdminController::class, 'expenses'])->name('adminExpenses');
      Route::post('/' . $base . '/expenses', [AdminController::class, 'storeExpense'])->name('adminStoreExpense');
      Route::post('/' . $base . '/expenses/{id}/status', [AdminController::class, 'updateExpenseStatus'])->name('adminUpdateExpenseStatus');

      // tournament
      Route::get('/' . $base . '/tournaments', [TournamentAdminController::class, 'index'])->name('admin.tournaments.index');
      Route::get('/' . $base . '/tournaments/create', [TournamentAdminController::class, 'create'])->name('admin.tournaments.create');
      Route::post('/' . $base . '/tournaments', [TournamentAdminController::class, 'store'])->name('admin.tournaments.store');
      Route::get('/' . $base . '/tournaments/{id}', [TournamentAdminController::class, 'show'])->name('admin.tournaments.show');
      Route::get('/' . $base . '/tournaments/{id}/edit', [TournamentAdminController::class, 'edit'])->name('admin.tournaments.edit');
      Route::put('/' . $base . '/tournaments/{id}', [TournamentAdminController::class, 'update'])->name('admin.tournament.update');
      Route::delete('/' . $base . '/tournaments/{id}', [TournamentAdminController::class, 'destroy'])->name('admin.tournaments.destroy');
      Route::post('/' . $base . '/tournaments/{id}/complete', [TournamentAdminController::class, 'complete'])->name('admin.tournaments.complete');

      // verif
      Route::get('/' . $base . '/verifications', [AdminController::class, 'verifications'])->name('admin.verifications');
      Route::get('/' . $base . '/verifications/{id}', [AdminController::class, 'verificationDetails'])->name('admin.verification.details');
      Route::post('/' . $base . '/verifications/{id}/update', [AdminController::class, 'updateVerificationStatus'])->name('admin.verification.update');

      // Telegram Broadcast
      Route::get('/' . $base . '/telegram-broadcast', [TelegramBroadcastController::class, 'index'])->name('admin.telegram.broadcast');
      Route::post('/' . $base . '/telegram-broadcast/send', [TelegramBroadcastController::class, 'send'])->name('admin.telegram.broadcast.send');
      Route::post('/' . $base . '/telegram-broadcast/preview', [TelegramBroadcastController::class, 'preview'])->name('admin.telegram.broadcast.preview');
      Route::get('/' . $base . '/telegram-broadcast/stats', [TelegramBroadcastController::class, 'getUserStats'])->name('admin.telegram.broadcast.stats');
      Route::get('/' . $base . '/telegram-broadcast/search-user', [TelegramBroadcastController::class, 'searchUser'])->name('admin.telegram.broadcast.searchUser');
      Route::get('/' . $base . '/telegram-broadcast/template/{id}', [TelegramBroadcastController::class, 'getTemplate'])->name('admin.telegram.broadcast.getTemplate');
      Route::post('/' . $base . '/telegram-broadcast/template', [TelegramBroadcastController::class, 'saveTemplate'])->name('admin.telegram.broadcast.saveTemplate');
      Route::delete('/' . $base . '/telegram-broadcast/template/{id}', [TelegramBroadcastController::class, 'deleteTemplate'])->name('admin.telegram.broadcast.deleteTemplate');

      // управления провайдерами
      Route::get('/' . $base . '/providers', [AdminController::class, 'providers'])->name('admin.providers');
      Route::post('/' . $base . '/providers/toggle-type', [AdminController::class, 'toggleProviderType'])->name('admin.providers.toggleType');
      Route::post('/' . $base . '/providers/toggle-provider', [AdminController::class, 'toggleProvider'])->name('admin.providers.toggleProvider');
      Route::get('/' . $base . '/providers/stats', [AdminController::class, 'getProviderStats'])->name('admin.providers.stats');

      // Ручные пополнения для админов
     Route::prefix($base . '/manual-deposits')->name('admin.manual-deposits.')->group(function () {
     Route::get('/', [ManualDepositController::class, 'adminIndex'])->name('index');
     Route::post('/process/{id}', [ManualDepositController::class, 'adminProcess'])->name('process');
 });

      // Управление курсами валют
      Route::get('/' . $base . '/rates', [AdminController::class, 'rates'])->name('adminRates');
      Route::post('/' . $base . '/rates/update', [AdminController::class, 'updateRates'])->name('adminRatesUpdate');
      Route::post('/' . $base . '/rates/auto-update', [AdminController::class, 'autoUpdateRates'])->name('adminRatesAutoUpdate');


      // Управление валютами
      Route::post('/' . $base . '/currency/create', [AdminController::class, 'createCurrency'])->name('adminCurrencyCreate');
      Route::post('/' . $base . '/currency/delete/{id}', [AdminController::class, 'deleteCurrency'])->name('adminCurrencyDelete');
      Route::post('/' . $base . '/currency/toggle/{id}', [AdminController::class, 'toggleCurrency'])->name('adminCurrencyToggle');

      // Управление баннерами
      Route::prefix($base . '/banners')->name('admin.banners.')->group(function () {
          Route::get('/', [BannerController::class, 'index'])->name('index');
          Route::get('/create', [BannerController::class, 'create'])->name('create');
          Route::post('/', [BannerController::class, 'store'])->name('store');
          Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
          Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
          Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
      });

    // Betvio Admin Routes
    Route::prefix($base . '/betvio')->name('admin.betvio.')->group(function () {
        Route::get('/agent-info', [BetvioController::class, 'getAgentInfo'])->name('agent.info');
        Route::get('/providers', [BetvioController::class, 'getProviders'])->name('providers');
        Route::get('/games', [BetvioController::class, 'getGames'])->name('games');
        Route::post('/agent-rtp', [BetvioController::class, 'setAgentRtp'])->name('agent.rtp');
        Route::post('/user-rtp', [BetvioController::class, 'setUserRtp'])->name('user.rtp');
    });
});


Route::middleware(['auth', 'moder:Moder'])->group(function () {
    $base = 'betrika';
    Route::get('/' . $base, [AdminController::class, 'index'])->name('Admin');
    Route::get('/' . $base . '/stats', [AdminController::class, 'stats'])->name('adminStats');
    //Route::get('/' . $base . '/statscategory', [AdminController::class, 'statsCategory'])->name('statsCategory');
    //
    //Route::get('/' . $base . '/detailed-statistics', [DetailedStatisticsController::class, 'index'])->name('admin.detailed_statistics');
    //Route::get('/' . $base . '/detailed-statistics/data', [DetailedStatisticsController::class, 'getStatistics']);
    //Route::get('/' . $base . '/detailed-statistics/chart-data', [DetailedStatisticsController::class, 'getChartData']);
  	Route::get('/day-statistics', [AccountDetailedStatisticsController::class, 'index'])->name('day_statistics');
    Route::get('/day-statistics/data', [AccountDetailedStatisticsController::class, 'getStatistics']);
    Route::get('/day-statistics/chart-data', [AccountDetailedStatisticsController::class, 'getChartData']);

    Route::get('/' . $base . '/detailed-statistics', [DetailedStatisticsController::class, 'index'])->name('admin.detailed_statistics');
    Route::get('/' . $base . '/detailed-statistics/data', [DetailedStatisticsController::class, 'getStatistics']);
    Route::get('/' . $base . '/detailed-statistics/chart-data', [DetailedStatisticsController::class, 'getChartData']);

    // Route::get('/' . $base . '/users', [AdminController::class, 'users'])->name('adminUsers');
    // Route::get('/' . $base . '/user/{id}', [AdminController::class, 'user'])->name('adminUser');
    // Route::post('/' . $base . '/usersAjax', [AdminController::class, 'usersAjax']);
    // // Route::get('/' . $base . '/promo', ['as' => 'adminPromo', 'uses' => 'AdminController@promo']);
    // Route::get('/' . $base . '/withdraw', [AdminController::class, 'withdraw'])->name('adminWithdraw');
    // // Route::get('/' . $base . '/inserts', [AdminController::class, 'inserts'])->name('adminInserts');
    // Route::get('/' . $base . '/withdraw/{id}', [AdminController::class, 'withdrawSend']);
    // Route::get('/' . $base . '/payhistory/{id}', [AdminController::class, 'payHistory']);
    // Route::get('/' . $base . '/gamehistory/{id}', [AdminController::class, 'gameHistory']);
    // Route::get('/' . $base . '/aviators/{id}', [AdminController::class, 'aviatorBets']);
    // Route::get('/' . $base . '/lives/{id}', [AdminController::class, 'LiveBets']);
    // Route::get('/' . $base . '/return/{id}', [AdminController::class, 'withdrawReturn']);
    //
    // Route::get('/' . $base . '/tasks', [AdminController::class, 'tasks'])->name('adminTasks');
    // Route::post('/' . $base . '/tasks', [AdminController::class, 'createTask'])->name('adminCreateTask');
    // Route::patch('/' . $base . '/tasks/{id}/complete', [AdminController::class, 'completeTask'])->name('adminCompleteTask');
    // Route::get('/' . $base . '/banned-users', [AdminController::class, 'showBannedUsers'])->name('admin.bannedUsers');
    // Route::post('/' . $base . '/unban-user/{id}', [AdminController::class, 'unbanUser'])->name('admin.unbanUser');
    // Route::post('/' . $base . '/userSave', [AdminController::class, 'userSave']);
//    Route::get('/' . $base . '/inserts/{id}', 'AdminController@InsertsSend')->name('inserts');
//    Route::post('/' . $base . '/promoNew', 'AdminController@promoNew')->name('promo.new');
//    Route::post('/' . $base . '/promoSave', 'AdminController@promoSave')->name('promo.save');
//    Route::get('/' . $base . '/promoDelete/{id}', 'AdminController@promoDelete')->name('promo.delete');
//    Route::get('/' . $base . '/return/{id}', 'AdminController@withdrawReturn')->name('withdraw.return');
//    Route::get('/' . $base . '/delets/{id}', 'AdminController@DeleteSend')->name('delete.send');
//    Route::post('/chatdel', 'OldChatController@delete_message')->name('real-time.delete');
});

Route::middleware(['auth', 'withdrawModer:WithdrawModer'])->group(function () {
    $base = 'betrika';

    Route::get('/' . $base . '/withdraw', [AdminController::class, 'withdraw'])->name('adminWithdraw');
    Route::get('/' . $base . '/withdraw/{id}', [AdminController::class, 'withdrawSend']);
    Route::get('/' . $base . '/return/{id}', [AdminController::class, 'withdrawReturn']);
    Route::get('/' . $base . '/user/{id}', [AdminController::class, 'user'])->name('adminUser');
    Route::get('/' . $base . '/payhistory/{id}', [AdminController::class, 'payHistory']);
    Route::get('/' . $base . '/gamehistory/{id}', [AdminController::class, 'gameHistory']);
    Route::get('/' . $base . '/transferhistory/{id}', [AdminController::class, 'transferHistory']);

});

Route::get('setlocale/{locale}', function ($locale) {
    if (in_array($locale, config('app.locales'))) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('setlocale');

