<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run()
    {
        $rooms = [
            [
                'room_number' => '101',
                'name' => 'スタンダードシングル',
                'capacity' => 1,
                'price' => 8000,
                'description' => 'ビジネスに最適なシンプルな個室です。',
            ],
            [
                'room_number' => '201',
                'name' => 'デラックスツイン',
                'capacity' => 2,
                'price' => 15000,
                'description' => '広々とした空間でゆったりとお過ごしいただけます。',
            ],
            [
                'room_number' => '301',
                'name' => 'スイートルーム',
                'capacity' => 4,
                'price' => 45000,
                'description' => '最上階で見晴らしの良い豪華な客室です。',
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}