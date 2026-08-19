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

        $this->room_type_id = (string) $room->room_type_id;
        $this->price = (string) $room->price;
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

    public function updatedRoomNumber(string $value): void
    {
        if (!empty($value) && is_numeric($value[0])) {
            $this->floor = $value[0];
        }
    }

    public function updatedRoomTypeId($value): void
    {
        if (!empty($value)) {
            $type = RoomType::find($value);
            if ($type) {
                $this->price = (string) ($type->daily_rate ?: $type->base_price);
            }
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
            'room_type_id' => 'required|exists:room_types,id',
            'bed_type'     => 'nullable|string',
            'room_option'   => 'nullable|array',
            'price'        => 'required|numeric|min:0',
            'status'       => 'required|in:Available,Occupied,Reserved,Maintenance',
            'floor'        => 'required|string|max:50',
            'description'  => 'nullable|string',
            'image_path'   => 'nullable|string',
            'photos.*'     => 'image|max:4096',
        ]);

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
