<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Housekeeping;
use App\Models\MaintenanceTicket;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    // Quick Maintenance Modal Properties
    public bool $showTicketModal = false;
    public ?int $selectedRoomId = null;
    public string $selectedRoomNumber = '';
    public string $ticketIssue = '';
    public string $ticketPriority = 'Medium';
    public string $ticketStatus = 'Open';
    public string $ticketNotes = '';

    public function boot(): void
    {
        if (!Auth::check() || (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('receptionist'))) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function updatedSearch(): void { $this->resetPage(); }

    public function delete(int $id): void
    {
        Room::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Room deleted.', type: 'success');
    }

    public function updateHousekeepingStatus(int $roomId, string $status): void
    {
        $room = Room::findOrFail($roomId);

        Housekeeping::updateOrCreate(
            ['room_id' => $roomId],
            [
                'status' => $status,
                'updated_by' => Auth::id(),
                'hotel_id' => $room->hotel_id ?? Auth::user()?->hotel_id,
                'notes' => "Quick update from Room Directory by " . Auth::user()->name
            ]
        );

        if ($status === 'Maintenance') {
            $room->update(['status' => 'Maintenance']);
        } elseif ($status === 'Clean' && $room->status === 'Maintenance') {
            $room->update(['status' => 'Available']);
        }

        $this->dispatch('toast', message: "Room #{$room->room_number} housekeeping set to {$status}.", type: 'success');
    }

    public function openMaintenanceModal(int $roomId): void
    {
        $room = Room::findOrFail($roomId);
        $this->selectedRoomId = $room->id;
        $this->selectedRoomNumber = $room->room_number;
        $this->ticketIssue = '';
        $this->ticketPriority = 'Medium';
        $this->ticketStatus = 'Open';
        $this->ticketNotes = '';
        $this->showTicketModal = true;
    }

    public function closeMaintenanceModal(): void
    {
        $this->showTicketModal = false;
    }

    public function saveMaintenanceTicket(): void
    {
        $this->validate([
            'selectedRoomId' => 'required|exists:rooms,id',
            'ticketIssue' => 'required|string|max:500',
            'ticketPriority' => 'required|in:Low,Medium,High,Critical',
            'ticketStatus' => 'required|in:Open,In Progress,Completed,Cancelled',
        ]);

        $room = Room::findOrFail($this->selectedRoomId);

        $ticket = MaintenanceTicket::create([
            'room_id' => $this->selectedRoomId,
            'issue' => $this->ticketIssue,
            'priority' => $this->ticketPriority,
            'status' => $this->ticketStatus,
            'notes' => $this->ticketNotes ?: null,
            'reported_by' => Auth::id(),
            'hotel_id' => $room->hotel_id ?? Auth::user()?->hotel_id,
        ]);

        if (in_array($this->ticketStatus, ['Open', 'In Progress']) && in_array($this->ticketPriority, ['High', 'Critical'])) {
            $room->update(['status' => 'Maintenance']);
        }

        $this->showTicketModal = false;
        $this->dispatch('toast', message: "Maintenance ticket created for Room #{$room->room_number}.", type: 'success');
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
