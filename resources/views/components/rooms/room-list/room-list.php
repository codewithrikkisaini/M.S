<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public function boot(): void
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function updatedSearch(): void { $this->resetPage(); }

    public function delete(int $id): void
    {
        Room::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Room deleted.', type: 'success');
    }

    public function render(): mixed
    {
        $rooms = Room::with(['roomType', 'latestHousekeeping', 'activeMaintenanceTickets'])
            ->where(function ($q) {
                $q->where('room_number', 'like', "%{$this->search}%")
                  ->orWhere('status', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return $this->view([
            'rooms'     => $rooms,
            'roomTypes' => RoomType::all(),
        ]);
    }
};
