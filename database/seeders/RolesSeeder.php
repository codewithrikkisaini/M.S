<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['slug' => 'superadmin'], ['name' => 'Super Admin']);
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Hotel Admin']);
        Role::firstOrCreate(['slug' => 'receptionist'], ['name' => 'Receptionist']);
    }
}
