<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Services\JackpotService;
use Illuminate\Console\Command;

class InitJackpotGames extends Command
{
    protected $signature = 'jackpot:init';
    protected $description = 'Initialize jackpot games in all rooms';

    protected $jackpotService;

    public function __construct(JackpotService $jackpotService)
    {
        parent::__construct();
        $this->jackpotService = $jackpotService;
    }

    public function handle()
    {
        $rooms = Room::where('status', true)->get();

        foreach ($rooms as $room) {
            $this->info("Initializing game for room {$room->name}...");

            try {
                $game = $this->jackpotService->createNewGame($room->name);
                $this->info("Created game #{$game->game_id} for room {$room->name}");
            } catch (\Exception $e) {
                $this->error("Error creating game for room {$room->name}: {$e->getMessage()}");
            }
        }

        $this->info('Game initialization completed');
    }
}
