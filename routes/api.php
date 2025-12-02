<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\JackpotController;

Route::prefix('jackpot')->group(function () {
    // Проверка game_token
    Route::post('/verify-token', [JackpotController::class, 'verifyToken']);
    Route::get('/state/{room}', [JackpotController::class, 'getState']);

    Route::middleware('game-token')->group(function () {
        Route::post('/bet', [JackpotController::class, 'placeBet']);
        Route::post('/finish', [JackpotController::class, 'finishGame']);
        Route::post('/new-game', [JackpotController::class, 'createGame']);
    });
});

// Route::post('livechat/webhook', [LiveChatController::class, 'handleWebhook']);


Route::post('/aes/callback', [App\Http\Controllers\Games\AesCallbackController::class, 'handleCallback'])
    ->middleware(\App\Http\Middleware\AesPerformanceMonitor::class);


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
