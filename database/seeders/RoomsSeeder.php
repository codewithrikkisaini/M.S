<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomsSeeder extends Seeder
{
    public function run(): void
    {
        $kingType = RoomType::create(['name' => 'King']);
        $twinType = RoomType::create(['name' => 'Twin']);

        // 10 King Rooms: 101-110
        for ($i = 101; $i <= 110; $i++) {
            Room::create([
                'room_number' => (string)$i,
                'room_type_id' => $kingType->id,
                'price' => 150.00,
                'status' => 'Available',
            ]);
        }

        // 10 Twin Rooms: 201-210
        for ($i = 201; $i <= 210; $i++) {
            Room::create([
                'room_number' => (string)$i,
                'room_type_id' => $twinType->id,
                'price' => 120.00,
                'status' => 'Available',
            ]);
        }
    }
}
