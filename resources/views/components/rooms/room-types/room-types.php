<?php

use Livewire\Component;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\UniqueConstraintViolationException;

new class extends Component
{
    public string $room_number = '';
    public string $floor = '1';
    public string $room_type_select = 'Single';
    public string $room_type_name = 'Single';
    public string $daily_rate = '';
    public string $weekly_rate = '';
    public string $monthly_rate = '';
    public string $tax_percent = '';
    public string $status = 'Available';
    public bool $is_custom_type = false;

    public function mount(): void
    {
        $this->room_type_name = 'Standard Room';
        $this->daily_rate = '';
        $this->weekly_rate = '';
        $this->monthly_rate = '';
        $this->tax_percent = '';
    }

    public function updatedDailyRate($val): void
    {
        if (is_numeric($val) && (float)$val > 0) {
            $daily = (float)$val;
            $this->weekly_rate = (string) round($daily * 7 * 0.9, 2);
            $this->monthly_rate = (string) round($daily * 30 * 0.8, 2);
            if ($this->tax_percent === '' || $this->tax_percent === null) {
                $this->tax_percent = '15';
            }
        } else {
            $this->weekly_rate = '';
            $this->monthly_rate = '';
        }
    }

    public function updatedRoomNumber($val): void
    {
        if (!empty($val) && is_numeric($val[0])) {
            $this->floor = $val[0];
        }
    }

    public function saveRoom(): void
    {
        $hotel_id = Auth::user()->hotel_id ?? null;

        if (empty($this->room_type_name)) {
            $this->room_type_name = 'Standard Room';
        }

        $this->validate([
            'room_number'    => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms', 'room_number')->where(function ($query) use ($hotel_id) {
                    return $query->where('hotel_id', $hotel_id);
                }),
            ],
            'floor'          => 'required|string|max:50',
            'room_type_name' => 'nullable|string|max:100',
            'daily_rate'     => 'required|numeric|min:0',
            'weekly_rate'    => 'nullable|numeric|min:0',
            'monthly_rate'   => 'nullable|numeric|min:0',
            'tax_percent'    => 'nullable|numeric|min:0|max:100',
            'status'         => 'required|in:Available,Occupied,Reserved,Maintenance',
        ]);

        $daily = (float) $this->daily_rate;
        $weekly = ($this->weekly_rate !== '' && $this->weekly_rate !== null) ? (float) $this->weekly_rate : round($daily * 7 * 0.9, 2);
        $monthly = ($this->monthly_rate !== '' && $this->monthly_rate !== null) ? (float) $this->monthly_rate : round($daily * 30 * 0.8, 2);
        $tax = ($this->tax_percent !== '' && $this->tax_percent !== null) ? (float) $this->tax_percent : 0;

        try {
            // 1. Create or Update Room Type Tariff
            $roomType = RoomType::updateOrCreate(
                ['name' => $this->room_type_name, 'hotel_id' => $hotel_id],
                [
                    'daily_rate'   => $daily,
                    'weekly_rate'  => $weekly,
                    'monthly_rate' => $monthly,
                    'tax_percent'  => $tax,
                    'status'       => 'active',
                ]
            );

            // 2. Create Physical Room
            $newRoom = Room::create([
                'room_number'  => $this->room_number,
                'room_type_id' => $roomType->id,
                'price'        => $daily,
                'floor'        => $this->floor,
                'status'       => $this->status,
                'hotel_id'     => $hotel_id,
            ]);

            $createdNum = $this->room_number;
            $this->reset(['room_number', 'daily_rate', 'weekly_rate', 'monthly_rate', 'tax_percent']);
            $this->dispatch('toast', message: "Room {$createdNum} added successfully under {$roomType->name}!", type: 'success');
        } catch (UniqueConstraintViolationException $e) {
            $this->addError('room_number', 'This room number already exists for this hotel.');
            $this->dispatch('toast', message: "Room number '{$this->room_number}' already exists.", type: 'error');
        }
    }

    public function deleteRoomType(int $id): void
    {
        $roomType = RoomType::findOrFail($id);

        $fallbackType = RoomType::where('hotel_id', $roomType->hotel_id)
            ->whereKeyNot($roomType->id)
            ->first();

        if (!$fallbackType) {
            $fallbackType = RoomType::create([
                'name' => 'Standard Room',
                'hotel_id' => $roomType->hotel_id,
                'daily_rate' => 59.95,
                'weekly_rate' => 249.90,
                'monthly_rate' => 990.00,
                'tax_percent' => 15,
                'status' => 'active',
            ]);
        }

        $roomType->rooms()->update(['room_type_id' => $fallbackType->id]);
        $roomType->delete();

        $this->dispatch('toast', message: 'Room type deleted successfully.', type: 'success');
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
        $rooms = $roomsQuery->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')->get();

        return $this->view(compact('roomTypes', 'rooms'));
    }
};
