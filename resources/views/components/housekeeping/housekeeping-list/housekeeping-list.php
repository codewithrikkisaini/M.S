<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Housekeeping;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;

    public string $search = '', $statusFilter = '';
    public bool $showDrawer = false, $isEditMode = false;
    public ?int $housekeepingId = null;
    public string $room_id = '', $status = 'Clean', $notes = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetFields();
        $this->showDrawer = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $rec = Housekeeping::findOrFail($id);
        $this->housekeepingId = $rec->id;
        $this->room_id        = (string)$rec->room_id;
        $this->status         = $rec->status;
        $this->notes          = $rec->notes ?? '';
        $this->isEditMode     = true;
        $this->showDrawer     = true;
    }

    public function store(): void
    {
        $this->validate([
            'room_id' => 'required|exists:rooms,id',
            'status'  => 'required|in:Clean,Dirty,Inspecting',
        ]);

        $room = Room::findOrFail($this->room_id);

        Housekeeping::updateOrCreate(['id' => $this->housekeepingId], [
            'room_id'    => $this->room_id,
            'status'     => $this->status,
            'updated_by' => Auth::id(),
            'notes'      => $this->notes ?: null,
            'hotel_id'   => $room->hotel_id ?? Auth::user()?->hotel_id,
        ]);

        $this->resetFields();
        $this->showDrawer = false;
        $this->dispatch('toast', message: 'Housekeeping record updated.', type: 'success');
    }

    public function delete(int $id): void
    {
        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('receptionist')) {
            $rec = Housekeeping::findOrFail($id);
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
        $query = Housekeeping::with(['room', 'updater'])
            ->whereIn('status', ['Clean', 'Dirty', 'Inspecting'])
            ->whereHas('room', fn ($q) => $q->where('room_number', 'like', "%{$this->search}%"));

        if ($this->statusFilter && in_array($this->statusFilter, ['Clean', 'Dirty', 'Inspecting'])) {
            $query->where('status', $this->statusFilter);
        }

        // Group status count optimization for housekeeping cleanliness statuses
        $statusCounts = Housekeeping::whereIn('status', ['Clean', 'Dirty', 'Inspecting'])
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return $this->view([
            'records' => $query->latest()->paginate(10),
            'rooms'   => Room::orderBy('room_number')->get(),
            'counts'  => [
                'clean'      => $statusCounts['Clean'] ?? 0,
                'dirty'      => $statusCounts['Dirty'] ?? 0,
                'inspecting' => $statusCounts['Inspecting'] ?? 0,
            ],
        ]);
    }
};
