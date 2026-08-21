<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\UniqueConstraintViolationException;

new class extends Component
{
    use WithFileUploads;

    public Room $room;
    public string $room_number = '';
    public string $room_type_id = '';
    public string $daily_rate = '';
    public string $weekly_rate = '';
    public string $monthly_rate = '';
    public string $tax_percent = '';
    public string $price = '';
    public string $status = 'Available';
    public string $floor = '';
    public string $bed_type = 'King Bed';
    public array $room_option = [];
    public string $description = '';
    public string $image_path = '';
    public $photos = [];

    public function getBedTypesProperty(): array
    {
        return [
            'Single / Twin Bed',
            'Twin Beds',
            'Full / Double Bed',
            '2 Double / Twin Beds',
            'Queen Bed',
            'King Bed',
            'California King',
            'Super King',
            'Sofa Bed / Sleeper Sofa',
            'Bunk Bed',
            'Murphy / Wall Bed',
            'Rollaway / Extra Bed',
            'Crib / Baby Cot',
        ];
    }

    public function getAvailableOptionsProperty(): array
    {
        return [
            'Smoking',
            'Non-Smoking',
            'Handicap Non-Smoking',
            'Suites with Jacuzzi Hot Tub',
            'Suite with Hot Tub',
            'King Bed and Rolling Bed for Extra Guest',
        ];
    }

    public function boot(): void
    {
        if (!Auth::check() || (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('receptionist'))) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function mount(Room $room): void
    {
        $this->room = $room;
        $this->room_number = $room->room_number;
        
        $img = $room->image_path ?? '';
        if (!empty($img)) {
            $decoded = json_decode($img, true);
            if (is_array($decoded)) {
                $this->image_path = implode("\n", $decoded);
            } else {
                $this->image_path = $img;
            }
        } else {
            $this->image_path = '';
        }

        $roomType = $room->roomType;
        $this->room_type_id = (string) $room->room_type_id;
        $this->daily_rate = (string) ($room->price ?: ($roomType?->daily_rate ?? ''));
        $this->weekly_rate = (string) ($roomType?->weekly_rate ?? ($this->daily_rate ? round((float)$this->daily_rate * 7 * 0.9, 2) : ''));
        $this->monthly_rate = (string) ($roomType?->monthly_rate ?? ($this->daily_rate ? round((float)$this->daily_rate * 30 * 0.8, 2) : ''));
        $this->tax_percent = (string) ($roomType?->tax_percent ?? '15');
        $this->price = $this->daily_rate;
        $this->status = $room->status;
        $this->floor = (string) ($room->floor ?? '');
        $this->bed_type = $room->bed_type ?: 'King Bed';
        
        $rawOption = $room->room_option ?? '';
        if (!empty($rawOption)) {
            if (is_array(json_decode($rawOption, true))) {
                $this->room_option = json_decode($rawOption, true);
            } else {
                $this->room_option = array_values(array_filter(array_map('trim', explode(',', $rawOption))));
            }
        } else {
            $this->room_option = [];
        }

        $this->description = (string) ($room->description ?? '');
    }

    public function updatedBedType($val): void
    {
        $allowed = $this->getAvailableOptionsProperty();
        if (is_array($this->room_option)) {
            $this->room_option = array_values(array_intersect($this->room_option, $allowed));
        } else {
            $this->room_option = [];
        }
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

    public function updatedRoomNumber(string $value): void
    {
        if (!empty($value) && is_numeric($value[0])) {
            $this->floor = $value[0];
        }
    }

    public function save(): void
    {
        $hotel_id = Auth::user()->hotel_id ?? $this->room->hotel_id;
        $allowedOptions = $this->getAvailableOptionsProperty();

        $this->validate([
            'room_number'  => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms', 'room_number')->where(function ($query) use ($hotel_id) {
                    return $query->where('hotel_id', $hotel_id);
                })->ignore($this->room->id),
            ],
            'room_type_id' => 'nullable|exists:room_types,id',
            'bed_type'     => 'nullable|string',
            'room_option'  => 'nullable|array',
            'daily_rate'   => 'required|numeric|min:0',
            'weekly_rate'  => 'nullable|numeric|min:0',
            'monthly_rate' => 'nullable|numeric|min:0',
            'tax_percent'  => 'nullable|numeric|min:0|max:100',
            'status'       => 'required|in:Available,Occupied,Reserved,Maintenance',
            'floor'        => 'required|string|max:50',
            'description'  => 'nullable|string',
            'image_path'   => 'nullable|string',
            'photos.*'     => 'image|max:4096',
        ]);

        $daily = (float) $this->daily_rate;
        $weekly = ($this->weekly_rate !== '' && $this->weekly_rate !== null) ? (float) $this->weekly_rate : round($daily * 7 * 0.9, 2);
        $monthly = ($this->monthly_rate !== '' && $this->monthly_rate !== null) ? (float) $this->monthly_rate : round($daily * 30 * 0.8, 2);
        $tax = ($this->tax_percent !== '' && $this->tax_percent !== null) ? (float) $this->tax_percent : 0;

        // Auto-assign / update room type based on bed type
        $typeName = $this->bed_type ?: 'Standard Room';
        $roomType = RoomType::updateOrCreate(
            ['name' => $typeName, 'hotel_id' => $hotel_id],
            [
                'daily_rate'   => $daily,
                'weekly_rate'  => $weekly,
                'monthly_rate' => $monthly,
                'tax_percent'  => $tax,
                'status'       => 'active',
            ]
        );
        $this->room_type_id = (string) $roomType->id;
        $this->price = (string) $daily;

        $formattedRoomOption = is_array($this->room_option) ? implode(', ', $this->room_option) : $this->room_option;

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

        if (!empty($this->photos)) {
            $photoList = is_array($this->photos) ? $this->photos : [$this->photos];
            foreach ($photoList as $p) {
                if ($p && is_object($p) && method_exists($p, 'store')) {
                    $paths[] = $p->store('rooms', 'public');
                }
            }
        }

        $finalImagePath = count($paths) > 0 ? json_encode(array_values($paths)) : null;

        try {
            $updateData = [
                'room_number'  => $this->room_number,
                'room_type_id' => $this->room_type_id,
                'price'        => $this->price,
                'status'       => $this->status,
                'bed_type'     => $this->bed_type,
                'room_option'  => $formattedRoomOption,
                'description'  => $this->description,
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('rooms', 'floor')) {
                $updateData['floor'] = $this->floor;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('rooms', 'image_path')) {
                $updateData['image_path'] = $finalImagePath;
            }

            $this->room->update($updateData);

            session()->flash('toast', ['message' => 'Room updated successfully!', 'type' => 'success']);
            $this->redirect(route('rooms.index'), navigate: true);
        } catch (UniqueConstraintViolationException $e) {
            $this->addError('room_number', 'This room number already exists for this hotel.');
        }
    }

    public function removeExistingImage(int $index): void
    {
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

        if (isset($paths[$index])) {
            unset($paths[$index]);
        }

        $this->image_path = implode("\n", array_values($paths));
    }

    public function removeUploadedImage(int $index): void
    {
        if (isset($this->photos[$index])) {
            unset($this->photos[$index]);
            $this->photos = array_values($this->photos);
        }
    }

    public function render(): mixed
    {
        return $this->view(['roomTypes' => RoomType::all()]);
    }
};
