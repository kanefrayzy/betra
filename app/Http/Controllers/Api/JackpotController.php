<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use App\Models\Jackpot;
use App\Services\JackpotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class JackpotController extends Controller
{
    protected $jackpotService;

    public function __construct(JackpotService $jackpotService)
    {
        $this->jackpotService = $jackpotService;

        // Применяем auth middleware только к нужным методам
        // $this->middleware('auth:sanctum')->only(['placeBet']);
    }

    public function index(Request $request, $room = null)
    {
        $rooms = Room::where('status', true)->get();

        if (!$room) {
            $currentRoom = $rooms->first();
        } else {
            $currentRoom = Room::where('name', $room)->firstOr(function() use ($rooms) {
                return $rooms->first();
            });
        }

        $game = $this->jackpotService->getGameState($currentRoom->name);

        $user = Auth::user();
        if($user){
        if(!$user->game_token) {
          $user->game_token = Str::uuid()->toString(); // Генерация токена
          $user->save();
        }
        }else{
        $game_token = NULL;
        }

        return view('games.jackpot', compact('rooms', 'currentRoom', 'game'));
    }

    public function getState(string $room): JsonResponse
    {
        try {
            $room = Room::where('name', $room)->firstOrFail();
            $state = $this->jackpotService->getGameState($room->name);

            return response()->json([
                'success' => true,
                'data' => [
                    'game' => $state,
                    'room' => [
                        'name' => $room->name,
                        'min' => $room->min,
                        'max' => $room->max,
                        'time' => $room->time,
                        'bets_limit' => $room->bets
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    protected function getUserByGameToken($token): ?User
    {
        if (!$token) return null;
        return User::where('game_token', $token)->first();
    }

    public function placeBet(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'room' => 'required|string|exists:rooms,name',
                'amount' => 'required|numeric|min:0.01'
            ]);

            $user = $request->user; // Получаем пользователя из middleware

            $result = $this->jackpotService->placeBet(
                $user,
                $validated['room'],
                $validated['amount']
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
}
        public function verifyToken(Request $request): JsonResponse
        {
            try {
                $request->validate([
                    'game_token' => 'required|string'
                ]);

                $user = User::where('game_token', $request->game_token)->first();

                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid game token'
                    ], 401);
                }

                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'balance' => round($user->balance, 2),
                        'avatar' => $user->avatar
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
        }

        public function finishGame(Request $request): JsonResponse
          {
              try {
                  $request->validate([
                      'room' => 'required|string|exists:rooms,name'
                  ]);

                  $result = $this->jackpotService->finishGame($request->room);

                  return response()->json($result);
              } catch (\Exception $e) {
                  return response()->json([
                      'success' => false,
                      'message' => $e->getMessage()
                  ], 400);
              }
          }

          public function createGame(Request $request): JsonResponse
          {
              try {
                  $request->validate([
                      'room' => 'required|string|exists:rooms,name'
                  ]);

                  $game = $this->jackpotService->createNewGame($request->room);

                  return response()->json([
                      'success' => true,
                      'data' => [
                          'game_id' => $game->game_id,
                          'hash' => $game->hash,
                          'bank' => $game->price,
                          'status' => $game->status
                      ]
                  ]);
              } catch (\Exception $e) {
                  return response()->json([
                      'success' => false,
                      'message' => $e->getMessage()
                  ], 400);
              }
          }

          public function history(Request $request)
          {
              $query = Jackpot::where('status', Jackpot::STATUS_FINISHED)
                  ->with(['winner:id,username,avatar', 'room']);

              if ($request->has('room')) {
                  $query->where('room', $request->room);
              }

              $games = $query->orderBy('id', 'desc')
                  ->paginate(25)
                  ->withQueryString();

              $currentRoom = $request->room;

              return view('games.jackpot-history', compact('games', 'currentRoom'));
          }

    public function gameHistory($room, $id)
    {
        $game = Jackpot::with(['winner', 'bets.user'])
            ->where('room', $room)
            ->where('game_id', $id)
            ->firstOrFail();

        return view('pages.jackpot.game-history', compact('game'));
    }
}
