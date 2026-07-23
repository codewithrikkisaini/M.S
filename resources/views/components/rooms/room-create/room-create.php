<?php

use Livewire\Component;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public string $room_number = '';
    public string $floor = '1';
    public string $room_type_select = 'Single';
    public string $room_type_name = 'Single';
    public string $daily_rate = '59.95';
    public string $weekly_rate = '249.90';
    public string $monthly_rate = '990.00';
    public string $tax_percent = '15';
    public string $status = 'Available';
    public bool $is_custom_type = false;

    public function mount(): void
    {
        $this->applyPreset('Single');
    }

    public function updatedRoomNumber($val): void
    {
        if (!empty($val) && is_numeric($val[0])) {
            $this->floor = $val[0];
        }
    }

    public function updatedRoomTypeSelect($val): void
    {
        if ($val === 'custom') {
            $this->is_custom_type = true;
            $this->room_type_name = '';
            $this->daily_rate = '';
            $this->weekly_rate = '';
            $this->monthly_rate = '';
            $this->tax_percent = '15';
        } else {
            $this->is_custom_type = false;
            $this->applyPreset($val);
        }
    }

    private function applyPreset(string $val): void
    {
        $type = RoomType::where('name', $val)->first();
        if ($type) {
            $this->room_type_name = $type->name;
            $this->daily_rate = (string) ($type->daily_rate ?: 59.95);
            $this->weekly_rate = (string) ($type->weekly_rate ?: 249.90);
            $this->monthly_rate = (string) ($type->monthly_rate ?: 990.00);
            $this->tax_percent = (string) ($type->tax_percent ?: 15);
        } else {
            if ($val === 'Single') {
                $this->room_type_name = 'Single';
                $this->daily_rate = '59.95';
                $this->weekly_rate = '249.90';
                $this->monthly_rate = '990.00';
                $this->tax_percent = '15';
            } elseif ($val === 'Double') {
                $this->room_type_name = 'Double';
                $this->daily_rate = '79.95';
                $this->weekly_rate = '349.90';
                $this->monthly_rate = '1190.00';
                $this->tax_percent = '15';
            } elseif ($val === 'Apartment') {
                $this->room_type_name = 'Apartment';
                $this->daily_rate = '79.90';
                $this->weekly_rate = '349.90';
                $this->monthly_rate = '1349.00';
                $this->tax_percent = '15';
            }
        }
    }

    public function saveRoom(): void
    {
        $hotel_id = Auth::user()->hotel_id ?? null;

        $this->validate([
            'room_number'    => 'required|string|max:50|unique:rooms,room_number',
            'floor'          => 'required|string|max:50',
            'room_type_name' => 'required|string|max:100',
            'daily_rate'     => 'required|numeric|min:0',
            'weekly_rate'    => 'required|numeric|min:0',
            'monthly_rate'   => 'required|numeric|min:0',
            'tax_percent'    => 'required|numeric|min:0|max:100',
            'status'         => 'required|in:Available,Occupied,Reserved,Maintenance',
        ]);

        // 1. Create or Update Room Type Tariff
        $roomType = RoomType::updateOrCreate(
            ['name' => $this->room_type_name, 'hotel_id' => $hotel_id],
            [
                'daily_rate'   => $this->daily_rate,
                'weekly_rate'  => $this->weekly_rate,
                'monthly_rate' => $this->monthly_rate,
                'tax_percent'  => $this->tax_percent,
                'status'       => 'active',
            ]
        );

        // 2. Create Physical Room
        $newRoom = Room::create([
            'room_number'  => $this->room_number,
            'room_type_id' => $roomType->id,
            'price'        => $this->daily_rate,
            'floor'        => $this->floor,
            'status'       => $this->status,
            'hotel_id'     => $hotel_id,
        ]);

        $createdNum = $this->room_number;
        $this->reset(['room_number']);
        $this->dispatch('toast', message: "Room {$createdNum} added successfully under {$roomType->name}!", type: 'success');
    }

    public function deleteRoom(int $id): void
    {
        $room = Room::findOrFail($id);
        $room->delete();
        $this->dispatch('toast', message: 'Room deleted successfully.', type: 'success');
    }

    public function render(): mixed
    {
        $hotel_id = Auth::user()->hotel_id ?? null;
        $roomTypes = RoomType::all();

        $roomsQuery = Room::with('roomType');
        if ($hotel_id) {
            $roomsQuery->where('hotel_id', $hotel_id);
        }
        $rooms = $roomsQuery->orderBy('room_number')->get();

        return $this->view(compact('roomTypes', 'rooms'));
    }
};
