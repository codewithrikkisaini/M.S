<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReservationsSeeder extends Seeder
{
    public function run(): void
    {
        $guests = Guest::all();
        $rooms = Room::all();

        if ($guests->count() === 0 || $rooms->count() === 0) return;

        for ($i = 1; $i <= 100; $i++) {
            $checkIn = Carbon::today()->addDays(rand(-30, 30));
            $checkOut = (clone $checkIn)->addDays(rand(1, 7));
            $statusOptions = ['Confirmed', 'Checked-In', 'Checked-Out', 'Cancelled'];
            $status = $statusOptions[array_rand($statusOptions)];

            $room = $rooms->random();

            $reservation = Reservation::create([
                'guest_id' => $guests->random()->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'adults' => rand(1, 2),
                'children' => rand(0, 2),
                'special_notes' => 'Seeded reservation',
                'status' => $status,
            ]);

            $reservation->rooms()->attach($room->id, ['price' => $room->price]);
        }
    }
}
