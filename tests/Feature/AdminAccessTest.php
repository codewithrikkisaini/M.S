<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $receptionist;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $receptionistRole = Role::create(['name' => 'Receptionist', 'slug' => 'receptionist']);

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);

        $this->receptionist = User::create([
            'name' => 'Receptionist User',
            'email' => 'receptionist@test.com',
            'password' => bcrypt('password'),
            'role_id' => $receptionistRole->id,
            'status' => 'active',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/settings');
        $response->assertRedirect('/login');
    }

    public function test_receptionist_cannot_access_settings(): void
    {
        $response = $this->actingAs($this->receptionist)->get('/settings');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_settings(): void
    {
        $response = $this->actingAs($this->admin)->get('/settings');
        $response->assertStatus(200);
    }
}
