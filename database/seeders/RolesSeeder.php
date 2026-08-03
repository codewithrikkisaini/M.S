<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(['slug' => 'superadmin'], ['name' => 'Super Admin']);
        Role::updateOrCreate(['slug' => 'admin'], ['name' => 'Hotel Admin']);
        Role::updateOrCreate(['slug' => 'receptionist'], ['name' => 'Receptionist']);
    }
}

