<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $receptionistRole = Role::where('slug', 'receptionist')->first();

        User::create([
            'name' => 'System Admin',
            'email' => 'admin@merahkie.com',
            'password' => Hash::make('123456'),
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Reception Staff',
            'email' => 'receptionist@merahkie.com',
            'password' => Hash::make('123456'),
            'role_id' => $receptionistRole->id,
            'status' => 'active',
        ]);
    }
}
