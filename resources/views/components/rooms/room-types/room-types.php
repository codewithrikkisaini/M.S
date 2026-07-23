<?php

use Livewire\Component;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    // Room Type Tariff Properties
    public string $selected_preset = '';
    public string $name = '';
    public string $daily_rate = '59.95';
    public string $weekly_rate = '249.90';
    public string $monthly_rate = '990.00';
    public string $tax_percent = '15';
    public string $status = 'active';

    public ?int $editingId = null;
    public string $editingName = '';
    public string $editingDailyRate = '';
    public string $editingWeeklyRate = '';
    public string $editingMonthlyRate = '';
    public string $editingTaxPercent = '';
    public string $editingStatus = 'active';

    // Physical Room Add Properties
    public string $room_number = '';
    public string $room_type_id_for_room = '';
    public string $room_price = '';
    public string $room_floor = '1';
    public string $room_status = 'Available';

    public function updatedSelectedPreset($val): void
    {
        if ($val === 'Single') {
            $this->name = 'Single';
            $this->daily_rate = '59.95';
            $this->weekly_rate = '249.90';
            $this->monthly_rate = '990.00';
            $this->tax_percent = '15';
            $this->status = 'active';
        } elseif ($val === 'Double') {
            $this->name = 'Double';
            $this->daily_rate = '79.95';
            $this->weekly_rate = '349.90';
            $this->monthly_rate = '1190.00';
            $this->tax_percent = '15';
            $this->status = 'active';
        } elseif ($val === 'Apartment') {
            $this->name = 'Apartment';
            $this->daily_rate = '79.90';
            $this->weekly_rate = '349.90';
            $this->monthly_rate = '1349.00';
            $this->tax_percent = '15';
            $this->status = 'active';
        } elseif ($val === 'custom') {
            $this->name = '';
        }
    }

    public function updatedRoomTypeIdForRoom($val): void
    {
        if (!empty($val)) {
            $type = RoomType::find($val);
            if ($type) {
                $this->room_price = (string) ($type->daily_rate ?: 59.95);
            }
        }
    }

    public function updatedRoomNumber($val): void
    {
        if (!empty($val) && is_numeric($val[0])) {
            $this->room_floor = $val[0];
        }
    }

    public function addType(): void
    {
        $hotel_id = Auth::user()->hotel_id ?? null;

        $this->validate([
            'name'         => 'required|string|max:100',
            'daily_rate'   => 'required|numeric|min:0',
            'weekly_rate'  => 'required|numeric|min:0',
            'monthly_rate' => 'required|numeric|min:0',
            'tax_percent'  => 'required|numeric|min:0|max:100',
            'status'       => 'required|in:active,inactive',
        ]);

        RoomType::updateOrCreate(
            ['name' => $this->name, 'hotel_id' => $hotel_id],
            [
                'daily_rate'   => $this->daily_rate,
                'weekly_rate'  => $this->weekly_rate,
                'monthly_rate' => $this->monthly_rate,
                'tax_percent'  => $this->tax_percent,
                'status'       => $this->status,
            ]
        );

        $this->reset(['selected_preset', 'name', 'daily_rate', 'weekly_rate', 'monthly_rate', 'tax_percent', 'status']);
        $this->daily_rate = '59.95';
        $this->weekly_rate = '249.90';
        $this->monthly_rate = '990.00';
        $this->tax_percent = '15';
        $this->status = 'active';

        $this->dispatch('toast', message: 'Room type and rate tariff saved successfully!', type: 'success');
    }

    public function addRoom(): void
    {
        $hotel_id = Auth::user()->hotel_id ?? null;

        $this->validate([
            'room_number'           => 'required|string|max:50|unique:rooms,room_number',
            'room_type_id_for_room' => 'required|exists:room_types,id',
            'room_price'            => 'required|numeric|min:0',
            'room_floor'            => 'required|string|max:50',
            'room_status'           => 'required|in:Available,Occupied,Reserved,Maintenance',
        ]);

        Room::create([
            'room_number'  => $this->room_number,
            'room_type_id' => $this->room_type_id_for_room,
            'price'        => $this->room_price,
            'floor'        => $this->room_floor,
            'status'       => $this->room_status,
            'hotel_id'     => $hotel_id,
        ]);

        $this->reset(['room_number', 'room_type_id_for_room', 'room_price', 'room_floor']);
        $this->room_status = 'Available';

        $this->dispatch('toast', message: 'Physical Room added successfully!', type: 'success');
    }

    public function deleteRoom(int $id): void
    {
        $room = Room::findOrFail($id);
        $room->delete();
        $this->dispatch('toast', message: 'Room deleted successfully.', type: 'success');
    }

    public function editType(int $id): void
    {
        $this->resetValidation();
        $type = RoomType::findOrFail($id);
        $this->editingId = $type->id;
        $this->editingName = $type->name;
        $this->editingDailyRate = (string) $type->daily_rate;
        $this->editingWeeklyRate = (string) $type->weekly_rate;
        $this->editingMonthlyRate = (string) $type->monthly_rate;
        $this->editingTaxPercent = (string) $type->tax_percent;
        $this->editingStatus = $type->status ?: 'active';
    }

    public function updateType(): void
    {
        $this->validate([
            'editingName'        => 'required|string|max:100',
            'editingDailyRate'   => 'required|numeric|min:0',
            'editingWeeklyRate'  => 'required|numeric|min:0',
            'editingMonthlyRate' => 'required|numeric|min:0',
            'editingTaxPercent'  => 'required|numeric|min:0|max:100',
            'editingStatus'      => 'required|in:active,inactive',
        ]);

        RoomType::findOrFail($this->editingId)->update([
            'name'         => $this->editingName,
            'daily_rate'   => $this->editingDailyRate,
            'weekly_rate'  => $this->editingWeeklyRate,
            'monthly_rate' => $this->editingMonthlyRate,
            'tax_percent'  => $this->editingTaxPercent,
            'status'       => $this->editingStatus,
        ]);

        $this->cancelEdit();
        $this->dispatch('toast', message: 'Room type tariff updated successfully!', type: 'success');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editingName = '';
        $this->editingDailyRate = '';
        $this->editingWeeklyRate = '';
        $this->editingMonthlyRate = '';
        $this->editingTaxPercent = '';
        $this->editingStatus = 'active';
        $this->resetValidation();
    }

    public function deleteType(int $id): void
    {
        $type = RoomType::findOrFail($id);

        if ($type->rooms()->exists()) {
            $this->dispatch('toast', message: "Cannot delete \"{$type->name}\" — rooms are still using this type.", type: 'error');
            return;
        }

        $type->delete();
        $this->dispatch('toast', message: 'Room type deleted.', type: 'success');
    }

    public function render(): mixed
    {
        $hotel_id = Auth::user()->hotel_id ?? null;
        $query = RoomType::with(['rooms'])->withCount('rooms');
        if ($hotel_id) {
            $query->where(function($q) use ($hotel_id) {
                $q->where('hotel_id', $hotel_id)->orWhereNull('hotel_id');
            });
        }
        $roomTypes = $query->orderBy('name')->get();

        $roomsQuery = Room::with('roomType');
        if ($hotel_id) {
            $roomsQuery->where('hotel_id', $hotel_id);
        }
        $rooms = $roomsQuery->orderBy('room_number')->get();

        return $this->view(compact('roomTypes', 'rooms'));
    }
};
