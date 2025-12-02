<?php

namespace App\Console\Commands;

use App\Services\FakeBetsService;
use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FakeBets extends Command
{
    protected $signature = 'jackpot:fake-bets {--active} {--interval=5}';
    protected $description = 'Run fake bets for jackpot games';

    protected FakeBetsService $fakeBetsService;

    public function __construct(FakeBetsService $fakeBetsService)
    {
        parent::__construct();
        $this->fakeBetsService = $fakeBetsService;
    }

    public function handle()
    {
        $active = $this->option('active');
        $interval = $this->option('interval');

        $this->info('Starting fake bets service...');

        while (true) {
            try {
                if (!$active) {
                    $active = cache()->get('fake_bets_enabled', false);
                }

                if ($active) {
                    $rooms = Room::where('status', true)->get();

                    foreach ($rooms as $room) {
                        $result = $this->fakeBetsService->placeFakeBet($room->name);

                        if ($result['success']) {
                            $this->info("Placed fake bet in room {$room->name}: " . $result['bet']->sum);
                        } else {
                            $this->warn("Failed to place fake bet in room {$room->name}: " . $result['error']);
                        }
                    }
                }

                sleep($interval);
            } catch (\Exception $e) {
                Log::error('Error in fake bets service: ' . $e->getMessage());
                $this->error('Error occurred: ' . $e->getMessage());
                sleep(5);
            }
        }
    }
}
