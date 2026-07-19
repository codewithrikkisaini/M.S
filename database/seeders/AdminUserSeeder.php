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
        $superadminRole = Role::where('slug', 'superadmin')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $receptionistRole = Role::where('slug', 'receptionist')->first();

        User::updateOrCreate(
            ['email' => 'superadmin@merahkie.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123456'),
                'role_id' => $superadminRole->id ?? null,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@merahkie.com'],
            [
                'name' => 'Hotel Admin',
                'password' => Hash::make('123456'),
                'role_id' => $adminRole->id ?? null,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'receptionist@merahkie.com'],
            [
                'name' => 'Reception Staff',
                'password' => Hash::make('123456'),
                'role_id' => $receptionistRole->id ?? null,
                'status' => 'active',
            ]
        );
    }
}
