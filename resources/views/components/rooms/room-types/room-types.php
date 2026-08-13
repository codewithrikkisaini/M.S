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
        $user = Auth::user();
        $hotel_id = $user?->hotel_id ?? \App\Models\Hotel::where('status', 'approved')->first()?->id ?? \App\Models\Hotel::first()?->id;

        $type = RoomType::where('name', $val)->when($hotel_id, fn($q) => $q->where('hotel_id', $hotel_id))->first()
            ?? RoomType::where('name', $val)->first();

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
            } elseif ($val === 'Twin') {
                $this->room_type_name = 'Twin';
                $this->daily_rate = '69.95';
                $this->weekly_rate = '299.90';
                $this->monthly_rate = '1090.00';
                $this->tax_percent = '15';
            } elseif ($val === 'Deluxe') {
                $this->room_type_name = 'Deluxe';
                $this->daily_rate = '99.95';
                $this->weekly_rate = '449.90';
                $this->monthly_rate = '1590.00';
                $this->tax_percent = '15';
            } elseif ($val === 'Executive') {
                $this->room_type_name = 'Executive Suite';
                $this->daily_rate = '129.95';
                $this->weekly_rate = '599.90';
                $this->monthly_rate = '1990.00';
                $this->tax_percent = '15';
            } elseif ($val === 'Apartment') {
                $this->room_type_name = 'Apartment';
                $this->daily_rate = '79.90';
                $this->weekly_rate = '349.90';
                $this->monthly_rate = '1349.00';
                $this->tax_percent = '15';
            } else {
                $this->room_type_name = $val;
            }
        }
    }

    public function saveRoom(): void
    {
        $hotel_id = Auth::user()->hotel_id ?? null;

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
            'room_type_name' => 'required|string|max:100',
            'daily_rate'     => 'required|numeric|min:0',
            'weekly_rate'    => 'required|numeric|min:0',
            'monthly_rate'   => 'required|numeric|min:0',
            'tax_percent'    => 'required|numeric|min:0|max:100',
            'status'         => 'required|in:Available,Occupied,Reserved,Maintenance',
        ]);

        try {
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
