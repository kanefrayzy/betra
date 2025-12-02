<?php

namespace App\Events;

use App\Models\PlinkoGame;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PlinkoGameStarted implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $game;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\PlinkoGame  $game
     * @return void
     */
    public function __construct(PlinkoGame $game)
    {
        $this->game = $game;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('plinko.' . $this->game->user_id);
    }

    public function broadcastWith()
    {
        return [
            'game_id' => $this->game->id,
            'bet_amount' => $this->game->bet_amount,
            'positions' => json_decode($this->game->positions),
            'win_amount' => $this->game->win_amount,
        ];
    }
}
