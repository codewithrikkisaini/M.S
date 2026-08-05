<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\UniqueConstraintViolationException;

new class extends Component
{
    use WithFileUploads;

    public string $room_number = '';
    public string $floor = '1';
    public string $room_type_select = 'Single';
    public string $room_type_name = 'Single';
    public string $daily_rate = '59.95';
    public string $weekly_rate = '249.90';
    public string $monthly_rate = '990.00';
    public string $tax_percent = '15';
    public string $status = 'Available';
    public string $image_path = '';
    public $photos = [];
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

    private function parseRoomNumbers(string $input): array
    {
        $numbers = [];
        $parts = explode(',', $input);

        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $part, $matches)) {
                $start = (int) $matches[1];
                $end = (int) $matches[2];
                if ($start <= $end && ($end - $start) <= 50) {
                    for ($i = $start; $i <= $end; $i++) {
                        $numbers[] = (string) $i;
                    }
                } else {
                    $numbers[] = $part;
                }
            } else {
                $numbers[] = $part;
            }
        }

        return array_unique($numbers);
    }

    public function saveRoom(): void
    {
        $user = Auth::user();
        $hotel_id = $user?->hotel_id ?? \App\Models\Hotel::where('status', 'approved')->first()?->id ?? \App\Models\Hotel::first()?->id;

        if (!$hotel_id) {
            $this->addError('room_number', 'No active hotel found to assign room.');
            $this->dispatch('toast', message: "No active hotel found.", type: 'error');
            return;
        }

        $this->validate([
            'room_number'    => 'required|string',
            'floor'          => 'required|string|max:50',
            'room_type_name' => 'required|string|max:100',
            'daily_rate'     => 'required|numeric|min:0',
            'weekly_rate'    => 'required|numeric|min:0',
            'monthly_rate'   => 'required|numeric|min:0',
            'tax_percent'    => 'required|numeric|min:0|max:100',
            'status'         => 'required|in:Available,Occupied,Reserved,Maintenance',
            'image_path'     => 'nullable|string',
            'photos.*'       => 'nullable|image|max:4096',
        ]);

        $roomNumbers = $this->parseRoomNumbers($this->room_number);
        if (empty($roomNumbers)) {
            $this->addError('room_number', 'Please enter at least one valid room number.');
            return;
        }

        if ($hotel_id) {
            $activeSub = \App\Models\Subscription::where('hotel_id', $hotel_id)
                ->whereIn('status', ['active', 'trialing'])
                ->with('plan')
                ->latest()
                ->first();

            if ($activeSub && $activeSub->plan && $activeSub->plan->max_rooms !== null) {
                $currentRoomsCount = Room::where('hotel_id', $hotel_id)->count();
                if (($currentRoomsCount + count($roomNumbers)) > $activeSub->plan->max_rooms) {
                    $this->addError('room_number', "Plan limit reached ({$activeSub->plan->max_rooms} rooms max). Please upgrade your subscription plan.");
                    $this->dispatch('toast', message: "Plan limit reached ({$activeSub->plan->max_rooms} rooms max). Please upgrade your subscription plan.", type: 'error');
                    return;
                }
            }
        }

        $paths = [];
        if (!empty($this->image_path)) {
            $urlList = preg_split('/[\r\n,]+/', $this->image_path);
            foreach ($urlList as $u) {
                $u = trim($u);
                if ($u !== '') {
                    $paths[] = $u;
                }
            }
        }

        if (!empty($this->photos) && is_array($this->photos)) {
            foreach ($this->photos as $p) {
                if ($p) {
                    try {
                        $paths[] = $p->store('rooms', 'public');
                    } catch (\Throwable $e) {}
                }
            }
        }

        $finalImagePath = count($paths) > 0 ? json_encode(array_values($paths)) : null;

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

            // 2. Create or Update Physical Rooms
            foreach ($roomNumbers as $num) {
                $floorToUse = $this->floor;
                if (!empty($num) && is_numeric($num[0]) && $floorToUse === '1' && strlen($num) >= 3) {
                    $floorToUse = $num[0];
                }

                $roomData = [
                    'room_number'  => $num,
                    'room_type_id' => $roomType->id,
                    'price'        => $this->daily_rate,
                    'floor'        => $floorToUse,
                    'status'       => $this->status,
                    'hotel_id'     => $hotel_id,
                ];

                if (\Illuminate\Support\Facades\Schema::hasColumn('rooms', 'image_path')) {
                    $roomData['image_path'] = $finalImagePath;
                }

                Room::updateOrCreate(
                    ['hotel_id' => $hotel_id, 'room_number' => $num],
                    $roomData
                );
            }

            $addedStr = implode(', ', $roomNumbers);
            $msg = "Room(s) {$addedStr} saved successfully under {$roomType->name}!";
            $this->reset(['room_number', 'image_path', 'photos']);
            $this->dispatch('toast', message: $msg, type: 'success');
        } catch (UniqueConstraintViolationException $e) {
            try {
                foreach ($roomNumbers as $num) {
                    $existing = Room::where('room_number', $num)->first();
                    if ($existing) {
                        $existing->update($roomData);
                    }
                }
                $addedStr = implode(', ', $roomNumbers);
                $msg = "Room(s) {$addedStr} updated successfully!";
                $this->reset(['room_number', 'image_path', 'photos']);
                $this->dispatch('toast', message: $msg, type: 'success');
            } catch (\Throwable $ex) {
                $this->addError('room_number', 'Room number saving error: ' . $ex->getMessage());
                $this->dispatch('toast', message: "Error saving room.", type: 'error');
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Room save failed: ' . $e->getMessage());
            $this->addError('room_number', 'Could not save room: ' . $e->getMessage());
            $this->dispatch('toast', message: "Error saving room: " . $e->getMessage(), type: 'error');
        }
    }

    public function deleteRoom(int $id): void
    {
        $room = Room::findOrFail($id);
        $room->delete();
        $this->dispatch('toast', message: 'Room deleted successfully.', type: 'success');
    }

    public function render(): mixed
    {
        $hotel_id = Auth::user()->hotel_id ?? \App\Models\Hotel::first()?->id ?? null;
        $roomTypes = RoomType::all();

        $roomsQuery = Room::with('roomType');
        if ($hotel_id) {
            $roomsQuery->where('hotel_id', $hotel_id);
        }
        $rooms = $roomsQuery->orderBy('room_number')->get();

        return $this->view(compact('roomTypes', 'rooms'));
    }
};
