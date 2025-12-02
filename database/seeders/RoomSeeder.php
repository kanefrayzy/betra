<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run()
    {
        $rooms = [
            [
                'name' => 'small',
                'min' => 1,
                'max' => 100,
                'time' => 30,
                'bets' => 10,
                'status' => true
            ],
            [
                'name' => 'medium',
                'min' => 10,
                'max' => 1000,
                'time' => 45,
                'bets' => 15,
                'status' => true
            ],
            [
                'name' => 'high',
                'min' => 100,
                'max' => 10000,
                'time' => 60,
                'bets' => 20,
                'status' => true
            ]
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(
                ['name' => $room['name']],
                $room
            );
        }
    }
}
