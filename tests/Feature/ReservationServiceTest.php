<?php

namespace Tests\Feature;

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Models\Role;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReservationService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ReservationService::class);

        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $this->user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'status' => 'active',
        ]);
    }

    public function test_process_check_in_updates_statuses_and_creates_record(): void
    {
        $guest = Guest::create([
            'guest_id' => 'G-00001',
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'phone' => '1234567890',
        ]);

        $type = RoomType::create(['name' => 'Deluxe', 'slug' => 'deluxe']);
        $room = Room::create([
            'room_number' => '101',
            'room_type_id' => $type->id,
            'price' => 100.00,
            'status' => 'Reserved',
        ]);

        $reservation = Reservation::create([
            'guest_id' => $guest->id,
            'check_in_date' => now()->toDateString(),
            'check_out_date' => now()->addDays(2)->toDateString(),
            'adults' => 2,
            'children' => 0,
            'discount_type' => 'Fixed',
            'discount_value' => 0,
            'tax_rate' => 18,
            'status' => 'Reserved',
        ]);
        $reservation->rooms()->attach($room->id, ['price' => $room->price]);

        $checkin = $this->service->processCheckIn($reservation->id, $this->user->id);

        $this->assertDatabaseHas('checkins', [
            'reservation_id' => $reservation->id,
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals('Checked-In', $reservation->fresh()->status);
        $this->assertEquals('Occupied', $room->fresh()->status);
    }
}
