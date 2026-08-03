<?php

namespace Database\Seeders;

use App\Models\Guest;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class GuestsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 50; $i++) {
            $guestId = 'G-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            Guest::updateOrCreate(
                ['guest_id' => $guestId],
                [
                    'name' => $faker->name,
                    'email' => 'guest' . $i . '@example.com',
                    'phone' => $faker->phoneNumber,
                    'nationality' => $faker->country,
                    'passport_number' => $faker->regexify('[A-Z0-9]{8}'),
                    'address' => $faker->address,
                ]
            );
        }
    }
}

