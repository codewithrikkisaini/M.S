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
            Guest::create([
                'guest_id' => 'G-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'nationality' => $faker->country,
                'passport_number' => $faker->regexify('[A-Z0-9]{8}'),
                'address' => $faker->address,
            ]);
        }
    }
}
