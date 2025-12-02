<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\SlotegratorGame;
use App\Models\User;
use App\Models\UserGameHistory;
use App\Services\Betvio\BetvioClient;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class BetvioController extends Controller
{
    protected BetvioClient $client;

    public function __construct(BetvioClient $client)
    {
        $this->client = $client;
    }

    /**
     * Запуск игры Betvio
     */
    public function launchGame($game)
    {
        try {
            $game = Cache::remember("betvio_game:{$game}", 3600, function () use ($game) {
                return SlotegratorGame::where('name', $game)
                    ->where('provider_type', 'betvio')
                    ->where('is_active', 1)
                    ->firstOrFail();
            });

            return $this->launchGameDirect($game);

        } catch (\Exception $e) {
            Log::error('Betvio game launch error', [
                'game' => $game,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }

    /**
     * Прямой запуск игры
     */
    public function launchGameDirect(SlotegratorGame $game)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('home')->with('error', 'Please log in to play');
            }

            // Создаем клиент для валюты пользователя
            $this->client = new BetvioClient($user->currency->symbol);

            // Создаем запись в истории игр
            $user->gamesHistory()->create([
                'slotegrator_game_id' => $game->id,
                'session_token' => Str::uuid()->toString(),
                'ip' => request()->ip(),
                'device' => (new Agent())->device(),
            ]);

            // Парсим game_code
            $gameCodeData = $this->parseGameCode($game->game_code);

            // Получаем URL игры от Betvio
            $response = $this->client->launchGame([
                'user_code' => $user->username, // Betvio использует username как user_code
                'game_type' => $this->mapGameType($game->type),
                'provider_code' => $gameCodeData['provider_code'],
                'game_code' => $gameCodeData['game_code'],
                'lang' => $this->client->mapLanguage(App::getLocale()),
                'user_balance' => (float)$user->balance,
            ]);

            if (!$this->client->isSuccess($response)) {
                Log::error('Betvio game launch failed', [
                    'user_id' => $user->id,
                    'game' => $game->name,
                    'error' => $this->client->getErrorMessage($response)
                ]);
                return redirect()->back()->with('error', __('errors.game_unavailable'));
            }

            $gameUrl = $response['launch_url'] ?? null;
            if (!$gameUrl) {
                return redirect()->back()->with('error', __('errors.game_unavailable'));
            }

            // Логируем успешный запуск
            Log::info('Betvio game launched', [
                'user_id' => $user->id,
                'game' => $game->name,
                'provider' => $gameCodeData['provider_code'],
                'user_created' => $response['user_created'] ?? false
            ]);

            $view = (new Agent())->isMobile() ? 'games.mobile' : 'games.play';
            return view($view, [
                'game' => $game,
                'gameUrl' => $gameUrl,
                'iframe' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Betvio game launch error', [
                'game' => $game->name ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }

    /**
     * Запуск игры по game_code
     */
    public function launchGameByCode($gameCode)
    {
        try {
            $game = Cache::remember("betvio_game_code:{$gameCode}", 3600, function () use ($gameCode) {
                return SlotegratorGame::where('game_code', 'like', "%{$gameCode}%")
                    ->where('provider_type', 'betvio')
                    ->where('is_active', 1)
                    ->firstOrFail();
            });

            return $this->launchGameDirect($game);

        } catch (\Exception $e) {
            Log::error('Betvio game launch by code error', [
                'game_code' => $gameCode,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', __('Недоступно'));
        }
    }

    /**
     * Демо режим (Betvio может не поддерживать, зависит от провайдера)
     */
    public function launchDemoGame($game)
    {
        return redirect()->back()->with('error', __('Demo mode not available for this provider'));
    }

    public function launchDemoGameByCode($gameCode)
    {
        return redirect()->back()->with('error', __('Demo mode not available for this provider'));
    }

    /**
     * Парсинг game_code (JSON формат)
     */
    protected function parseGameCode(string $gameCode): array
    {
        $decoded = json_decode($gameCode, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return [
                'provider_code' => $decoded['provider_code'] ?? 'PRAGMATIC',
                'game_code' => $decoded['game_code'] ?? $gameCode
            ];
        }

        // Fallback
        Log::warning('Could not parse Betvio game_code', ['game_code' => $gameCode]);
        return [
            'provider_code' => 'PRAGMATIC',
            'game_code' => $gameCode
        ];
    }

    /**
     * Маппинг типа игры
     */
    protected function mapGameType(?string $type): string
    {

        if (empty($type)) {
            return 'slot';
        }

        $typeMap = [
            'slots' => 'slot',
            'slot' => 'slot',
            'live' => 'casino',
            'casino' => 'casino',
            'table' => 'slot',
        ];

        return $typeMap[$type] ?? 'slot';
    }

    /**
     * Получить информацию об агенте
     */
    public function getAgentInfo(Request $request)
    {
        try {
            $currency = $request->input('currency');
            $this->client = new BetvioClient($currency);

            $response = $this->client->getAgentInfo();

            if (!$this->client->isSuccess($response)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->client->getErrorMessage($response)
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting Betvio agent info', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get agent info'
            ]);
        }
    }

    /**
     * Получить список провайдеров
     */
    public function getProviders(Request $request)
    {
        try {
            $currency = $request->input('currency');
            $gameType = $request->input('game_type', 'slot');

            $this->client = new BetvioClient($currency);
            $response = $this->client->getProviders($gameType);

            if (!$this->client->isSuccess($response)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->client->getErrorMessage($response)
                ]);
            }

            return response()->json([
                'success' => true,
                'providers' => $response['providers'] ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting Betvio providers', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get providers'
            ]);
        }
    }

    /**
     * Получить список игр провайдера
     */
    public function getGames(Request $request)
    {
        try {
            $currency = $request->input('currency');
            $providerCode = $request->input('provider_code');
            $lang = $request->input('lang', 'en');

            if (!$providerCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider code is required'
                ]);
            }

            $this->client = new BetvioClient($currency);
            $response = $this->client->getGames($providerCode, $lang);

            if (!$this->client->isSuccess($response)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->client->getErrorMessage($response)
                ]);
            }

            return response()->json([
                'success' => true,
                'games' => $response['games'] ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting Betvio games', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get games'
            ]);
        }
    }

    /**
     * Изменить RTP агента
     */
    public function setAgentRtp(Request $request)
    {
        try {
            $currency = $request->input('currency');
            $rtp = (int)$request->input('rtp', 95);

            if ($rtp < 0 || $rtp > 95) {
                return response()->json([
                    'success' => false,
                    'message' => 'RTP must be between 0 and 95'
                ]);
            }

            $this->client = new BetvioClient($currency);
            $response = $this->client->setAgentRtp($rtp);

            if (!$this->client->isSuccess($response)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->client->getErrorMessage($response)
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error setting Betvio agent RTP', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to set agent RTP'
            ]);
        }
    }

    /**
     * Изменить RTP пользователя
     */
    public function setUserRtp(Request $request)
    {
        try {
            $currency = $request->input('currency');
            $userCode = $request->input('user_code');
            $providerCode = $request->input('provider_code');
            $rtp = (int)$request->input('rtp', 95);

            if (!$userCode || !$providerCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'User code and provider code are required'
                ]);
            }

            if ($rtp < 0 || $rtp > 95) {
                return response()->json([
                    'success' => false,
                    'message' => 'RTP must be between 0 and 95'
                ]);
            }

            $this->client = new BetvioClient($currency);
            $response = $this->client->setUserRtp($userCode, $providerCode, $rtp);

            if (!$this->client->isSuccess($response)) {
                return response()->json([
                    'success' => false,
                    'message' => $this->client->getErrorMessage($response)
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error setting Betvio user RTP', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to set user RTP'
            ]);
        }
    }
}
