<?php

namespace Tests\Feature;

use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RoomTypeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_type_can_be_deleted_from_management_screen(): void
    {
        $user = User::factory()->create();
        $type = RoomType::create([
            'name' => 'Executive Suite',
            'hotel_id' => $user->hotel_id,
        ]);

        Livewire::actingAs($user)
            ->test('rooms.room-types')
            ->call('deleteRoomType', $type->id);

        $this->assertDatabaseMissing('room_types', ['id' => $type->id]);
    }
}
