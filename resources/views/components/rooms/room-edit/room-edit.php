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
    public string $image_path = '';
    public $photos = [];

    public function boot(): void
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function mount(Room $room): void
    {
        $this->room = $room;
        $this->room_number = $room->room_number;
        $this->image_path = $room->image_path ?? '';
        $this->room_type_id = (string) $room->room_type_id;
        $this->price = (string) $room->price;
        $this->status = $room->status;
        $this->floor = (string) ($room->floor ?? '');
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
            'price'        => 'required|numeric|min:0',
            'status'       => 'required|in:Available,Occupied,Reserved,Maintenance',
            'floor'        => 'required|string|max:50',
            'image_path'   => 'nullable|string',
            'photos.*'     => 'image|max:4096',
        ]);

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
                    $paths[] = $p->store('rooms', 'public');
                }
            }
        }

        $finalImagePath = count($paths) > 0 ? (count($paths) === 1 ? $paths[0] : json_encode(array_values($paths))) : null;

        try {
            $this->room->update([
                'room_number'  => $this->room_number,
                'image_path'   => $finalImagePath,
                'room_type_id' => $this->room_type_id,
                'price'        => $this->price,
                'status'       => $this->status,
                'floor'        => $this->floor,
            ]);

            session()->flash('toast', ['message' => 'Room updated successfully!', 'type' => 'success']);
            $this->redirect(route('rooms.index'), navigate: true);
        } catch (UniqueConstraintViolationException $e) {
            $this->addError('room_number', 'This room number already exists for this hotel.');
        }
    }

    public function render(): mixed
    {
        return $this->view(['roomTypes' => RoomType::all()]);
    }
};
