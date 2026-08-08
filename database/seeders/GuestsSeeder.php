<?php

namespace Database\Seeders;

use App\Models\Guest;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class GuestsSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = ['John', 'Jane', 'Michael', 'Emily', 'David', 'Sarah', 'Alex', 'Laura', 'Robert', 'Lisa'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];
        $countries = ['United States', 'Canada', 'United Kingdom', 'Australia', 'Germany', 'France', 'India', 'Japan'];

        for ($i = 1; $i <= 20; $i++) {
            $guestId = 'G-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $fn = $firstNames[$i % count($firstNames)];
            $ln = $lastNames[$i % count($lastNames)];
            $name = "$fn $ln";
            $country = $countries[$i % count($countries)];

            Guest::updateOrCreate(
                ['guest_id' => $guestId],
                [
                    'name' => $name,
                    'email' => 'guest' . $i . '@example.com',
                    'phone' => '+1 555 ' . str_pad($i * 123, 4, '0', STR_PAD_LEFT),
                    'nationality' => $country,
                    'passport_number' => 'PASS' . str_pad($i * 987, 6, '0', STR_PAD_LEFT),
                    'address' => "100 Guest Street #$i, $country",
                ]
            );
        }
    }
}

