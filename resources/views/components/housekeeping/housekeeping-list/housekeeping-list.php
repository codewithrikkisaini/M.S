<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Housekeeping;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $activeTab = 'dashboard'; // dashboard, room_status, cleaning_tasks, inspections, lost_found, task_history

    public string $search = '', $statusFilter = '';
    public bool $showDrawer = false, $isEditMode = false, $showLostFoundModal = false;
    public ?int $housekeepingId = null;
    public string $room_id = '', $status = 'Clean', $notes = '';

    // Lost & Found fields & state
    public string $lf_item_name = '', $lf_room_id = '', $lf_founder = '', $lf_status = 'Stored in Safe', $lf_notes = '';

    public array $lostFoundItems = [
        [
            'id' => 1,
            'item_name' => 'Black Leather Wallet',
            'room_number' => '102',
            'found_by' => 'Anita Sharma (Housekeeper)',
            'status' => 'Stored in Safe',
            'found_date' => '14 Aug 2026, 10:30 AM',
            'notes' => 'Contains driving license and cards. Kept at front desk reception.'
        ],
        [
            'id' => 2,
            'item_name' => 'Apple iPhone Charger (USB-C)',
            'room_number' => '204',
            'found_by' => 'Rajesh Kumar (Housekeeper)',
            'status' => 'Stored in Safe',
            'found_date' => '13 Aug 2026, 02:15 PM',
            'notes' => 'Found near bedside drawer.'
        ],
        [
            'id' => 3,
            'item_name' => 'Ray-Ban Sunglasses',
            'room_number' => '301',
            'found_by' => 'Suman Roy (Housekeeper)',
            'status' => 'Returned to Guest',
            'found_date' => '12 Aug 2026, 11:00 AM',
            'notes' => 'Handed over to guest on check-out.'
        ],
    ];

    public function mount(): void
    {
        $tab = request()->get('tab');
        if ($tab && in_array($tab, ['dashboard', 'room_status', 'cleaning_tasks', 'inspections', 'lost_found', 'task_history'])) {
            $this->setTab($tab);
        }
    }

    public function addLostFound(): void
    {
        $this->validate([
            'lf_item_name' => 'required|min:2',
            'lf_room_id'   => 'required',
            'lf_founder'   => 'required',
        ]);

        $roomNo = $this->lf_room_id;
        $room = Room::find($this->lf_room_id);
        if ($room) $roomNo = $room->room_number;

        array_unshift($this->lostFoundItems, [
            'id'          => count($this->lostFoundItems) + 1,
            'item_name'   => $this->lf_item_name,
            'room_number' => (string)$roomNo,
            'found_by'    => $this->lf_founder,
            'status'      => $this->lf_status ?: 'Stored in Safe',
            'found_date'  => now()->format('d M Y, h:i A'),
            'notes'       => $this->lf_notes ?: 'Logged by Housekeeping',
        ]);

        $this->lf_item_name = '';
        $this->lf_room_id   = '';
        $this->lf_founder   = '';
        $this->lf_notes     = '';
        $this->showLostFoundModal = false;

        $this->dispatch('toast', message: 'Lost & Found item registered successfully.', type: 'success');
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
        if ($tab === 'cleaning_tasks') {
            $this->statusFilter = 'Dirty';
        } elseif ($tab === 'inspections') {
            $this->statusFilter = 'Inspecting';
        } elseif ($tab === 'room_status') {
            $this->statusFilter = '';
        } elseif ($tab === 'dashboard') {
            $this->statusFilter = '';
        }
    }

    public function openCreate(): void
    {
        $this->resetFields();
        $this->showDrawer = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $hotelId = Auth::user()?->hotel_id;
        $rec = Housekeeping::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->findOrFail($id);
        $this->housekeepingId = $rec->id;
        $this->room_id        = (string)$rec->room_id;
        $this->status         = $rec->status;
        $this->notes          = $rec->notes ?? '';
        $this->isEditMode     = true;
        $this->showDrawer     = true;
    }

    public function store(): void
    {
        $hotelId = Auth::user()?->hotel_id;

        $this->validate([
            'room_id' => 'required|exists:rooms,id',
            'status'  => 'required|in:Clean,Dirty,Inspecting',
        ]);

        $room = Room::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->findOrFail($this->room_id);

        Housekeeping::updateOrCreate(['id' => $this->housekeepingId], [
            'room_id'    => $this->room_id,
            'status'     => $this->status,
            'updated_by' => Auth::id(),
            'notes'      => $this->notes ?: null,
            'hotel_id'   => $hotelId ?? $room->hotel_id,
        ]);

        $this->resetFields();
        $this->showDrawer = false;
        $this->dispatch('toast', message: 'Housekeeping record updated.', type: 'success');
    }

    public function updateRoomStatus(int $id, string $newStatus): void
    {
        if (!in_array($newStatus, ['Clean', 'Dirty', 'Inspecting'])) return;

        $hotelId = Auth::user()?->hotel_id;
        $rec = Housekeeping::with('room')->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->findOrFail($id);
        $rec->update([
            'status'     => $newStatus,
            'updated_by' => Auth::id(),
        ]);

        if ($newStatus === 'Clean' && $rec->room && $rec->room->status === 'Dirty') {
            $rec->room->update(['status' => 'Available']);
        }

        $roomNo = $rec->room->room_number ?? "#{$id}";
        $this->dispatch('toast', message: "Room {$roomNo} status updated to {$newStatus}.", type: 'success');
    }

    public function delete(int $id): void
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('receptionist') || Auth::user()->hasRole('housekeeping')) {
            $hotelId = Auth::user()?->hotel_id;
            $rec = Housekeeping::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->findOrFail($id);
            $rec->delete();
            $this->dispatch('toast', message: 'Record deleted.', type: 'success');
        } else {
            $this->dispatch('toast', message: 'Unauthorized.', type: 'error');
        }
    }

    private function resetFields(): void
    {
        $this->housekeepingId = null;
        $this->room_id        = '';
        $this->status         = 'Clean';
        $this->notes          = '';
        $this->isEditMode     = false;
        $this->resetValidation();
    }

    public function render(): mixed
    {
        $hotelId = Auth::user()?->hotel_id;

        $query = Housekeeping::with(['room', 'updater'])
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->whereIn('status', ['Clean', 'Dirty', 'Inspecting']);

        if ($this->search) {
            $query->whereHas('room', fn ($q) => $q->where('room_number', 'like', "%{$this->search}%"));
        }

        if ($this->statusFilter && in_array($this->statusFilter, ['Clean', 'Dirty', 'Inspecting'])) {
            $query->where('status', $this->statusFilter);
        }

        // Group status count optimization for housekeeping cleanliness statuses scoped by hotel_id
        $statusCounts = Housekeeping::whereIn('status', ['Clean', 'Dirty', 'Inspecting'])
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $rooms = Room::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->orderBy('room_number')
            ->get();

        $totalRoomsTracked = Room::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->count();
        $cleanCount = $statusCounts['Clean'] ?? 0;
        $dirtyCount = $statusCounts['Dirty'] ?? 0;
        $inspectingCount = $statusCounts['Inspecting'] ?? 0;

        return $this->view([
            'records' => $query->latest()->paginate(10),
            'rooms'   => $rooms,
            'counts'  => [
                'total'      => $totalRoomsTracked,
                'clean'      => $cleanCount,
                'dirty'      => $dirtyCount,
                'inspecting' => $inspectingCount,
                'pending'    => $dirtyCount + $inspectingCount,
            ],
        ]);
    }
};
