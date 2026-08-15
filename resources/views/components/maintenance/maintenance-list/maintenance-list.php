<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Room;
use App\Models\User;
use App\Models\Housekeeping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public string $activeTab = 'dashboard'; // dashboard, tickets, my_tasks, preventive, equipment, history

    public string $search = '', $priorityFilter = '', $statusFilter = '';
    public bool $showDrawer = false, $isEditMode = false;
    public ?int $ticketId = null;
    public string $room_id = '', $issue = '', $priority = 'Medium',
                  $assigned_to = '', $status = 'Open', $notes = '';

    public array $preventiveSchedules = [
        [
            'id' => 1,
            'title' => 'Central HVAC System Servicing & Filter Replace',
            'equipment' => 'HVAC Unit #1 (Main Block)',
            'frequency' => 'Quarterly',
            'last_completed' => '15 Jun 2026',
            'next_due' => '15 Sep 2026',
            'assigned_to' => 'Vikram Singh (HVAC Specialist)',
            'status' => 'Scheduled',
        ],
        [
            'id' => 2,
            'title' => 'Elevator Safety Inspection & Lubrication',
            'equipment' => 'Otis Passenger Elevator A',
            'frequency' => 'Monthly',
            'last_completed' => '01 Aug 2026',
            'next_due' => '01 Sep 2026',
            'assigned_to' => 'Otis Service Team',
            'status' => 'Pending Inspection',
        ],
        [
            'id' => 3,
            'title' => 'Diesel Generator 500kVA Fuel & Battery Check',
            'equipment' => 'Kirloskar Silent Generator',
            'frequency' => 'Bi-Weekly',
            'last_completed' => '10 Aug 2026',
            'next_due' => '24 Aug 2026',
            'assigned_to' => 'Ramesh Kumar (Electrical Lead)',
            'status' => 'Operational',
        ],
        [
            'id' => 4,
            'title' => 'Commercial Water Boiler & Pump Pressure Flush',
            'equipment' => 'Thermax Central Boiler B',
            'frequency' => 'Monthly',
            'last_completed' => '20 Jul 2026',
            'next_due' => '20 Aug 2026',
            'assigned_to' => 'Plumbing Maintenance',
            'status' => 'Due Soon',
        ],
    ];

    public array $equipmentList = [
        [
            'id' => 1,
            'tag_number' => 'EQ-HVAC-001',
            'name' => 'Central VRF Air Conditioning Unit',
            'category' => 'HVAC & Climate',
            'location' => 'Rooftop - Main Building',
            'status' => 'Good Condition',
            'installed_date' => '12 Jan 2024',
        ],
        [
            'id' => 2,
            'tag_number' => 'EQ-GEN-002',
            'name' => '500 kVA Diesel Generator Set',
            'category' => 'Power Backup',
            'location' => 'Basement Utility Room',
            'status' => 'Operational',
            'installed_date' => '05 Mar 2023',
        ],
        [
            'id' => 3,
            'tag_number' => 'EQ-BLR-003',
            'name' => 'Commercial Gas Water Heater Boiler',
            'category' => 'Plumbing & Heating',
            'location' => 'Service Block - Plant Room',
            'status' => 'Requires Servicing',
            'installed_date' => '18 Nov 2022',
        ],
        [
            'id' => 4,
            'tag_number' => 'EQ-ELV-004',
            'name' => '10-Passenger Hydraulic Elevator',
            'category' => 'Vertical Transport',
            'location' => 'Main Lobby Shaft A',
            'status' => 'Good Condition',
            'installed_date' => '10 Aug 2021',
        ],
        [
            'id' => 5,
            'tag_number' => 'EQ-WTR-005',
            'name' => 'RO Industrial Water Purification Plant',
            'category' => 'Water Treatment',
            'location' => 'Basement - Pump Room',
            'status' => 'Operational',
            'installed_date' => '02 Feb 2025',
        ],
    ];

    public function mount(): void
    {
        $tab = request()->get('tab');
        if ($tab && in_array($tab, ['dashboard', 'tickets', 'my_tasks', 'preventive', 'equipment', 'history'])) {
            $this->activeTab = $tab;
        }

        if (request()->get('action') === 'create') {
            $this->openCreate();
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['dashboard', 'tickets', 'my_tasks', 'preventive', 'equipment', 'history'])) {
            $this->activeTab = $tab;
            $this->resetPage();
            $this->statusFilter = '';
            $this->priorityFilter = '';
        }
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }
    public function updatedPriorityFilter(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetFields();
        $this->showDrawer = true;
    }

    public function openCreateForAsset(string $assetName): void
    {
        $this->resetFields();
        $this->issue = "Maintenance / Inspection required for asset: {$assetName}";
        $this->priority = 'High';
        $this->showDrawer = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $hotelId = Auth::user()?->hotel_id;
        $ticketQuery = DB::table('maintenance_tickets')->where('id', $id);
        if ($hotelId) {
            $ticketQuery->where('hotel_id', $hotelId);
        }
        $ticket = $ticketQuery->first();
        if (!$ticket) return;
        $this->ticketId   = $ticket->id;
        $this->room_id    = (string)$ticket->room_id;
        $this->issue      = $ticket->issue;
        $this->priority   = $ticket->priority;
        $this->assigned_to = (string)($ticket->assigned_to ?? '');
        $this->status     = $ticket->status;
        $this->notes      = $ticket->notes ?? '';
        $this->isEditMode = true;
        $this->showDrawer = true;
    }

    public function store(): void
    {
        $hotelId = Auth::user()?->hotel_id;

        $this->validate([
            'room_id'  => 'required|exists:rooms,id',
            'issue'    => 'required|string|max:500',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'status'   => 'required|in:Open,In Progress,Completed,Cancelled',
        ]);

        $roomQuery = Room::query();
        if ($hotelId) {
            $roomQuery->where('hotel_id', $hotelId);
        }
        $room = $roomQuery->findOrFail($this->room_id);

        $data = [
            'room_id'     => $this->room_id,
            'issue'       => $this->issue,
            'priority'    => $this->priority,
            'assigned_to' => $this->assigned_to ?: null,
            'status'      => $this->status,
            'notes'       => $this->notes ?: null,
            'hotel_id'    => $hotelId ?? $room->hotel_id,
            'updated_at'  => now(),
        ];

        if ($this->isEditMode) {
            $updateQuery = DB::table('maintenance_tickets')->where('id', $this->ticketId);
            if ($hotelId) {
                $updateQuery->where('hotel_id', $hotelId);
            }
            $updateQuery->update($data);
        } else {
            $insertedId = DB::table('maintenance_tickets')->insertGetId(array_merge($data, [
                'reported_by' => Auth::id(),
                'created_at'  => now(),
            ]));
            $this->ticketId = $insertedId;
        }

        // Automatic Room & Housekeeping Sync Loop
        if (in_array($this->status, ['Open', 'In Progress']) && in_array($this->priority, ['High', 'Critical'])) {
            $room->update(['status' => 'Maintenance']);
        } elseif (in_array($this->status, ['Completed', 'Cancelled'])) {
            $hasOtherActive = DB::table('maintenance_tickets')
                ->where('room_id', $this->room_id)
                ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
                ->whereIn('status', ['Open', 'In Progress'])
                ->where('id', '!=', $this->ticketId)
                ->exists();
                
            if (!$hasOtherActive) {
                if ($room->status === 'Maintenance') {
                    $room->update(['status' => 'Available']);
                }
                
                Housekeeping::updateOrCreate(
                    ['room_id' => $this->room_id],
                    [
                        'status' => 'Inspecting', 
                        'updated_by' => Auth::id(), 
                        'hotel_id' => $room->hotel_id ?? $hotelId,
                        'notes' => "Maintenance Ticket #{$this->ticketId} resolved. Pending inspection."
                    ]
                );
            }
        }

        $this->resetFields();
        $this->showDrawer = false;
        $this->dispatch('toast', message: 'Ticket saved.', type: 'success');
    }

    public function updateTicketStatus(int $id, string $newStatus): void
    {
        if (!in_array($newStatus, ['Open', 'In Progress', 'Completed', 'Cancelled'])) return;

        $hotelId = Auth::user()?->hotel_id;
        $ticketQuery = DB::table('maintenance_tickets')->where('id', $id);
        if ($hotelId) {
            $ticketQuery->where('hotel_id', $hotelId);
        }
        $ticket = $ticketQuery->first();
        if (!$ticket) return;

        DB::table('maintenance_tickets')->where('id', $id)->update([
            'status'     => $newStatus,
            'updated_at' => now(),
        ]);

        $room = Room::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->find($ticket->room_id);

        if ($room) {
            if (in_array($newStatus, ['Open', 'In Progress']) && in_array($ticket->priority, ['High', 'Critical'])) {
                $room->update(['status' => 'Maintenance']);
            } elseif (in_array($newStatus, ['Completed', 'Cancelled'])) {
                $hasOtherActive = DB::table('maintenance_tickets')
                    ->where('room_id', $ticket->room_id)
                    ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
                    ->whereIn('status', ['Open', 'In Progress'])
                    ->where('id', '!=', $id)
                    ->exists();

                if (!$hasOtherActive) {
                    if ($room->status === 'Maintenance') {
                        $room->update(['status' => 'Available']);
                    }
                    Housekeeping::updateOrCreate(
                        ['room_id' => $ticket->room_id],
                        [
                            'status'     => 'Inspecting',
                            'updated_by' => Auth::id(),
                            'hotel_id'   => $room->hotel_id ?? $hotelId,
                            'notes'      => "Maintenance Ticket #{$id} resolved. Pending inspection."
                        ]
                    );
                }
            }
        }

        $this->dispatch('toast', message: "Ticket #{$id} status updated to {$newStatus}.", type: 'success');
    }

    public function delete(int $id): void
    {
        $hotelId = Auth::user()?->hotel_id;
        $deleteQuery = DB::table('maintenance_tickets')->where('id', $id);
        if ($hotelId) {
            $deleteQuery->where('hotel_id', $hotelId);
        }
        $deleteQuery->delete();
        $this->dispatch('toast', message: 'Ticket deleted.', type: 'success');
    }

    private function resetFields(): void
    {
        $this->ticketId   = null;
        $this->room_id    = '';
        $this->issue      = '';
        $this->priority   = 'Medium';
        $this->assigned_to = '';
        $this->status     = 'Open';
        $this->notes      = '';
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function filterByStatus(string $status): void
    {
        $this->priorityFilter = '';
        $this->statusFilter = $this->statusFilter === $status ? '' : $status;
        $this->resetPage();
    }

    public function filterByCritical(): void
    {
        $this->statusFilter = '';
        $this->priorityFilter = $this->priorityFilter === 'Critical' ? '' : 'Critical';
        $this->resetPage();
    }

    public function render(): mixed
    {
        $hotelId = Auth::user()?->hotel_id;
        $userId  = Auth::id();

        $query = DB::table('maintenance_tickets')
            ->join('rooms', 'rooms.id', '=', 'maintenance_tickets.room_id')
            ->leftJoin('users', 'users.id', '=', 'maintenance_tickets.assigned_to')
            ->select('maintenance_tickets.*', 'rooms.room_number', 'users.name as assignee_name')
            ->when($hotelId, fn ($q) => $q->where('maintenance_tickets.hotel_id', $hotelId));

        if ($this->activeTab === 'my_tasks') {
            $query->where('maintenance_tickets.assigned_to', $userId);
        } elseif ($this->activeTab === 'history') {
            $query->whereIn('maintenance_tickets.status', ['Completed', 'Cancelled']);
        } elseif ($this->activeTab === 'dashboard') {
            // Dashboard shows active tickets (Open / In Progress)
            $query->whereIn('maintenance_tickets.status', ['Open', 'In Progress']);
        }

        if ($this->search) {
            $query->where(function ($qq) {
                $qq->where('rooms.room_number', 'like', "%{$this->search}%")
                   ->orWhere('maintenance_tickets.issue', 'like', "%{$this->search}%");
            });
        }

        if ($this->priorityFilter) {
            $query->where('maintenance_tickets.priority', $this->priorityFilter);
        }

        if ($this->statusFilter) {
            $query->where('maintenance_tickets.status', $this->statusFilter);
        }

        $query->orderByRaw("CASE priority WHEN 'Critical' THEN 1 WHEN 'High' THEN 2 WHEN 'Medium' THEN 3 ELSE 4 END")
              ->orderBy('maintenance_tickets.created_at', 'desc');

        $tickets = $query->paginate(15);

        // Group status count optimization scoped by hotel_id
        $statusCounts = DB::table('maintenance_tickets')
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalTickets = DB::table('maintenance_tickets')
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->count();

        $assignedCount = DB::table('maintenance_tickets')
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->whereNotNull('assigned_to')
            ->where('status', '!=', 'Completed')
            ->count();

        $urgentCount = DB::table('maintenance_tickets')
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->whereIn('priority', ['High', 'Critical'])
            ->where('status', '!=', 'Completed')
            ->count();

        $myTasksCount = DB::table('maintenance_tickets')
            ->when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->where('assigned_to', $userId)
            ->where('status', '!=', 'Completed')
            ->count();

        $counts = [
            'total'          => $totalTickets,
            'open'           => $statusCounts['Open'] ?? 0,
            'assigned'       => $assignedCount,
            'inprogress'     => $statusCounts['In Progress'] ?? 0,
            'in_progress'    => $statusCounts['In Progress'] ?? 0,
            'urgent'         => $urgentCount,
            'critical'       => $urgentCount,
            'completed'      => $statusCounts['Completed'] ?? 0,
            'my_tasks'       => $myTasksCount,
            'preventive_due' => 1,
        ];

        $rooms = Room::when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->orderBy('room_number')
            ->get();

        $users = User::when($hotelId, fn ($q) => $q->where('hotel_id', $hotelId))
            ->orderBy('name')
            ->get();

        return $this->view([
            'tickets' => $tickets,
            'counts'  => $counts,
            'rooms'   => $rooms,
            'users'   => $users,
        ]);
    }
};
