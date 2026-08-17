<div>
    {{-- Top Page Title Header with + New Ticket Button --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-sm shadow-xs">
                    <i class="fas {{ $activeTab === 'dashboard' ? 'fa-th-large' : ($activeTab === 'tickets' ? 'fa-ticket-alt' : ($activeTab === 'my_tasks' ? 'fa-tasks' : ($activeTab === 'preventive' ? 'fa-shield-alt' : ($activeTab === 'equipment' ? 'fa-toolbox' : 'fa-history')))) }}"></i>
                </div>
                <span>
                    @if($activeTab === 'dashboard') Maintenance Operations
                    @elseif($activeTab === 'tickets') Maintenance Tickets Registry
                    @elseif($activeTab === 'my_tasks') My Work Orders & Assigned Tasks
                    @elseif($activeTab === 'preventive') Preventive Maintenance Schedules
                    @elseif($activeTab === 'equipment') Hotel Equipment & Asset Inventory
                    @elseif($activeTab === 'history') Maintenance Resolution Audit History
                    @endif
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                {{ Auth::user()?->hotel?->name ?? 'Hotel Management System' }} • Real-time repair ticketing & equipment management
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                <i class="fas fa-plus"></i> New Ticket
            </button>
        </div>
    </div>

    {{-- Executive Metric Summary Cards (Shown ONLY for staff, DELETED for Hotel Admin) --}}
    @if(!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('superadmin'))
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 mb-6">
        <div class="pms-card p-4 text-left border border-slate-100/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-ticket-alt text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ sprintf('%02d', $counts['total'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Total Tickets</p>
                </div>
            </div>
        </div>

        <button wire:click="setTab('tickets'); $set('statusFilter', 'Open')"
                class="pms-card p-4 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $statusFilter === 'Open' ? 'ring-2 ring-blue-600 border-blue-100 shadow-md bg-blue-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-folder-open text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-blue-600 tracking-tight">{{ sprintf('%02d', $counts['open'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Open Tickets</p>
                </div>
            </div>
        </button>

        <button wire:click="setTab('tickets'); $set('statusFilter', 'In Progress')"
                class="pms-card p-4 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $statusFilter === 'In Progress' ? 'ring-2 ring-amber-600 border-amber-100 shadow-md bg-amber-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-spinner text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-amber-600 tracking-tight">{{ sprintf('%02d', $counts['in_progress'] ?? $counts['inprogress'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">In Progress</p>
                </div>
            </div>
        </button>

        <button wire:click="setTab('tickets'); $set('priorityFilter', 'Critical')"
                class="pms-card p-4 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $priorityFilter === 'Critical' ? 'ring-2 ring-red-600 border-red-100 shadow-md bg-red-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-fire text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-red-600 tracking-tight">{{ sprintf('%02d', $counts['critical'] ?? $counts['urgent'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Critical Urgent</p>
                </div>
            </div>
        </button>

        <button wire:click="setTab('history')"
                class="pms-card p-4 text-left hover:shadow-md transition-all duration-200 cursor-pointer border border-slate-100/80 hover:border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-check-double text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-emerald-600 tracking-tight">{{ sprintf('%02d', $counts['completed'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Resolved</p>
                </div>
            </div>
        </button>

        <button wire:click="setTab('preventive')"
                class="pms-card p-4 text-left hover:shadow-md transition-all duration-200 cursor-pointer border border-slate-100/80 hover:border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-shield-alt text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-purple-600 tracking-tight">{{ sprintf('%02d', $counts['preventive_due'] ?? 0) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Preventive Due</p>
                </div>
            </div>
        </button>
    </div>
    @endif

    {{-- TAB 1: DASHBOARD VIEW --}}
    @if($activeTab === 'dashboard')
        <div class="pms-card shadow-sm border border-slate-100/80">
            <div class="pms-card-header flex-wrap gap-4 justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center border border-blue-100">
                        <i class="fas fa-clock text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Active Work Orders & Open Issues</h3>
                        <p class="text-[10px] text-slate-400">Tickets requiring attention or currently under repair</p>
                    </div>
                </div>
                <button wire:click="setTab('tickets')" class="text-xs font-extrabold text-blue-600 hover:text-blue-800 flex items-center gap-1 cursor-pointer">
                    View All Tickets <i class="fas fa-arrow-right text-[10px]"></i>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="pms-table w-full">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                            <th class="font-bold">Ticket No.</th>
                            <th class="font-bold">Room</th>
                            <th class="font-bold">Issue</th>
                            <th class="font-bold">Priority</th>
                            <th class="font-bold">Assigned Technician</th>
                            <th class="font-bold">Status</th>
                            @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('superadmin'))<th class="font-bold text-right">Actions</th>@endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tickets as $t)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="text-slate-600 text-xs font-bold font-mono">#MT-{{ sprintf('%03d', $t->id) }}</td>
                            <td><span class="font-black text-slate-800 text-base tracking-tight bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">{{ $t->room_number }}</span></td>
                            <td>
                                <p class="text-slate-800 font-bold text-xs truncate max-w-[220px]" title="{{ $t->issue }}">{{ $t->issue }}</p>
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ match($t->priority){ 'Critical'=>'bg-red-50 text-red-700 border-red-100', 'High'=>'bg-orange-50 text-orange-700 border-orange-100', 'Medium'=>'bg-amber-50 text-amber-700 border-amber-100', default=>'bg-slate-50 text-slate-600 border-slate-100' } }}">
                                    {{ $t->priority }}
                                </span>
                            </td>
                            <td>
                                @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('superadmin'))
                                    <select wire:change="assignStaff({{ $t->id }}, $event.target.value)"
                                            class="text-xs font-semibold rounded-lg border border-slate-200 bg-white px-2 py-1.5 cursor-pointer min-w-36">
                                        <option value="">Unassigned</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (int) $t->assigned_to === $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="text-xs font-semibold text-slate-700">{{ $t->assignee_name ?? 'Unassigned' }}</span>
                                @endif
                            </td>
                            <td>
                                <select wire:change="updateTicketStatus({{ $t->id }}, $event.target.value)" class="text-xs font-bold rounded-lg border px-2 py-1 cursor-pointer {{ $t->status === 'Open' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($t->status === 'In Progress' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                                    <option value="Open" {{ $t->status === 'Open' ? 'selected' : '' }}>🔵 Open</option>
                                    <option value="In Progress" {{ $t->status === 'In Progress' ? 'selected' : '' }}>⏳ In Progress</option>
                                    <option value="Completed" {{ $t->status === 'Completed' ? 'selected' : '' }}>✅ Completed</option>
                                </select>
                            </td>
                            @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('superadmin'))
                            <td class="text-right">
                                <button wire:click="edit({{ $t->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 shadow-sm cursor-pointer" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fas fa-check-circle text-4xl text-emerald-300 mb-3 block"></i>
                                <p class="text-sm font-medium">All clear! No pending work orders.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    {{-- TAB 2: MAINTENANCE TICKETS REGISTRY VIEW --}}
    @elseif($activeTab === 'tickets')
        <div class="pms-card shadow-sm border border-slate-100/80">
            <div class="pms-card-header flex-wrap gap-4 justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Maintenance Tickets Registry</span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative max-w-xs w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search room or issue..." class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                    </div>
                    <select wire:model.live="priorityFilter" class="pms-select text-xs py-1.5 rounded-lg border border-slate-200 w-32">
                        <option value="">All Priorities</option>
                        <option>Low</option><option>Medium</option><option>High</option><option>Critical</option>
                    </select>
                    <select wire:model.live="statusFilter" class="pms-select text-xs py-1.5 rounded-lg border border-slate-200 w-36">
                        <option value="">All Statuses</option>
                        <option>Open</option><option>In Progress</option><option>Completed</option><option>Cancelled</option>
                    </select>
                    @if($statusFilter || $priorityFilter)
                    <button wire:click="$set('statusFilter', ''); $set('priorityFilter', '')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer shrink-0">
                        Clear
                    </button>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="pms-table">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                            <th class="font-bold">Ticket No.</th>
                            <th class="font-bold">Room No.</th>
                            <th class="font-bold">Issue</th>
                            <th class="font-bold">Priority</th>
                            <th class="font-bold">Assigned To</th>
                            <th class="font-bold">Status</th>
                            <th class="font-bold">Reported On</th>
                            @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('superadmin'))<th class="font-bold text-right">Actions</th>@endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tickets as $t)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="text-slate-600 text-xs font-bold font-mono">#MT-{{ sprintf('%03d', $t->id) }}</td>
                            <td><span class="font-black text-slate-800 text-base tracking-tight bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">{{ $t->room_number }}</span></td>
                            <td class="max-w-[200px]">
                                <p class="text-slate-800 font-bold text-sm truncate" title="{{ $t->issue }}">{{ $t->issue }}</p>
                                @if($t->notes) <p class="text-slate-400 text-xs truncate mt-0.5" title="{{ $t->notes }}">{{ $t->notes }}</p> @endif
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ match($t->priority){ 'Critical'=>'bg-red-50 text-red-700 border-red-100', 'High'=>'bg-orange-50 text-orange-700 border-orange-100', 'Medium'=>'bg-amber-50 text-amber-700 border-amber-100', default=>'bg-slate-50 text-slate-600 border-slate-150' } }}">
                                    @if($t->priority=='Critical')<i class="fas fa-fire text-[9px] animate-pulse"></i>@endif
                                    {{ $t->priority }}
                                </span>
                            </td>
                            <td>
                                @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('superadmin'))
                                    <select wire:change="assignStaff({{ $t->id }}, $event.target.value)"
                                            class="text-xs font-semibold rounded-lg border border-slate-200 bg-white px-2 py-1.5 cursor-pointer min-w-36">
                                        <option value="">Unassigned</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (int) $t->assigned_to === $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <span class="text-xs font-semibold text-slate-700">{{ $t->assignee_name ?? '—' }}</span>
                                @endif
                            </td>
                            <td>
                                <select wire:change="updateTicketStatus({{ $t->id }}, $event.target.value)" class="text-xs font-bold rounded-lg border px-2 py-1 cursor-pointer transition-all focus:outline-none {{ $t->status === 'Open' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($t->status === 'In Progress' ? 'bg-amber-50 text-amber-700 border-amber-200' : ($t->status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200')) }}">
                                    <option value="Open" {{ $t->status === 'Open' ? 'selected' : '' }}>🔵 Open</option>
                                    <option value="In Progress" {{ $t->status === 'In Progress' ? 'selected' : '' }}>⏳ In Progress</option>
                                    <option value="Completed" {{ $t->status === 'Completed' ? 'selected' : '' }}>✅ Completed</option>
                                    <option value="Cancelled" {{ $t->status === 'Cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                </select>
                            </td>
                            <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y') }}</td>
                            @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('superadmin'))
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button wire:click="edit({{ $t->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 shadow-sm cursor-pointer" title="Edit"><i class="fas fa-edit text-xs"></i></button>
                                    <button wire:click="delete({{ $t->id }})" wire:confirm="Delete this ticket?" class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 shadow-sm cursor-pointer" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fas fa-tools text-4xl text-slate-200 mb-3 block"></i>
                                <p class="text-sm font-medium">No tickets matching selected filters.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())<div class="px-5 py-4 border-t border-slate-100">{{ $tickets->links() }}</div>@endif
        </div>

    {{-- TAB 3: MY TASKS VIEW --}}
    @elseif($activeTab === 'my_tasks')
        <div class="pms-card shadow-sm border border-slate-100/80">
            <div class="pms-card-header flex-wrap gap-4 justify-between">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-user-check text-emerald-600"></i> My Personal Task Checklist
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="pms-table">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                            <th class="font-bold">Ticket No.</th>
                            <th class="font-bold">Room</th>
                            <th class="font-bold">Issue Description</th>
                            <th class="font-bold">Priority</th>
                            <th class="font-bold">Status</th>
                            <th class="font-bold text-right">Quick Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tickets as $t)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="text-slate-600 text-xs font-bold font-mono">#MT-{{ sprintf('%03d', $t->id) }}</td>
                            <td><span class="font-black text-slate-800 text-base bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">{{ $t->room_number }}</span></td>
                            <td class="max-w-[250px]"><p class="text-slate-800 font-bold text-xs truncate">{{ $t->issue }}</p></td>
                            <td>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ match($t->priority){ 'Critical'=>'bg-red-50 text-red-700 border-red-100', 'High'=>'bg-orange-50 text-orange-700 border-orange-100', 'Medium'=>'bg-amber-50 text-amber-700 border-amber-100', default=>'bg-slate-50 text-slate-600 border-slate-150' } }}">
                                    {{ $t->priority }}
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $t->status === 'In Progress' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($t->status !== 'In Progress')
                                    <button wire:click="updateTicketStatus({{ $t->id }}, 'In Progress')" class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-lg border border-blue-200 cursor-pointer">
                                        <i class="fas fa-play text-[10px]"></i> Start
                                    </button>
                                    @endif
                                    <button wire:click="updateTicketStatus({{ $t->id }}, 'Completed')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xs cursor-pointer">
                                        <i class="fas fa-check text-[10px]"></i> Mark Done
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fas fa-smile text-4xl text-slate-200 mb-3 block"></i>
                                <p class="text-sm font-medium">You have no pending tasks assigned at the moment.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    {{-- TAB 4: PREVENTIVE MAINTENANCE VIEW --}}
    @elseif($activeTab === 'preventive')
        <div class="pms-card shadow-sm border border-slate-100/80">
            <div class="pms-card-header">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-amber-600"></i> Routine Service Registry
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="pms-table w-full">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                            <th class="font-bold">Schedule Task</th>
                            <th class="font-bold">Equipment / Asset</th>
                            <th class="font-bold">Frequency</th>
                            <th class="font-bold">Last Done</th>
                            <th class="font-bold">Next Due</th>
                            <th class="font-bold">Assigned Technician</th>
                            <th class="font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($preventiveSchedules as $ps)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="font-bold text-slate-800 text-xs">{{ $ps['title'] }}</td>
                            <td class="text-xs font-semibold text-slate-700">{{ $ps['equipment'] }}</td>
                            <td><span class="bg-slate-100 text-slate-700 font-extrabold px-2.5 py-0.5 rounded-full text-[10px] border border-slate-200">{{ $ps['frequency'] }}</span></td>
                            <td class="text-xs text-slate-500 font-medium">{{ $ps['last_completed'] }}</td>
                            <td class="text-xs font-bold text-indigo-600">{{ $ps['next_due'] }}</td>
                            <td class="text-xs text-slate-600 font-medium">{{ $ps['assigned_to'] }}</td>
                            <td>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold border {{ $ps['status'] === 'Operational' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($ps['status'] === 'Due Soon' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-blue-50 text-blue-700 border-blue-100') }}">
                                    <i class="fas fa-circle text-[8px]"></i> {{ $ps['status'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    {{-- TAB 5: EQUIPMENT INVENTORY VIEW --}}
    @elseif($activeTab === 'equipment')
        <div class="pms-card shadow-sm border border-slate-100/80">
            <div class="pms-card-header">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-microchip text-cyan-600"></i> Asset Master List
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="pms-table w-full">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                            <th class="font-bold">Asset Tag</th>
                            <th class="font-bold">Equipment Name</th>
                            <th class="font-bold">Category</th>
                            <th class="font-bold">Location</th>
                            <th class="font-bold">Condition Status</th>
                            <th class="font-bold">Installation Date</th>
                            <th class="font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($equipmentList as $eq)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="font-mono font-bold text-xs text-indigo-600">{{ $eq['tag_number'] }}</td>
                            <td class="font-bold text-slate-800 text-xs">{{ $eq['name'] }}</td>
                            <td><span class="bg-indigo-50 text-indigo-700 font-extrabold px-2.5 py-0.5 rounded-md text-[10px]">{{ $eq['category'] }}</span></td>
                            <td class="text-xs text-slate-600 font-medium">{{ $eq['location'] }}</td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ str_contains($eq['status'], 'Good') || str_contains($eq['status'], 'Operational') ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                                    <i class="fas fa-check-circle text-[10px]"></i> {{ $eq['status'] }}
                                </span>
                            </td>
                            <td class="text-xs text-slate-500 font-medium">{{ $eq['installed_date'] }}</td>
                            <td class="text-right">
                                <button wire:click="openCreateForAsset('{{ $eq['name'] }}')" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-lg border border-indigo-200 cursor-pointer">
                                    <i class="fas fa-plus-circle"></i> Log Issue
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    {{-- TAB 6: RESOLUTION HISTORY VIEW --}}
    @elseif($activeTab === 'history')
        <div class="pms-card shadow-sm border border-slate-100/80">
            <div class="pms-card-header">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-archive text-purple-600"></i> Closed Ticket Audit Log
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="pms-table">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                            <th class="font-bold">Ticket No.</th>
                            <th class="font-bold">Room</th>
                            <th class="font-bold">Resolved Issue</th>
                            <th class="font-bold">Technician</th>
                            <th class="font-bold">Status</th>
                            <th class="font-bold">Completion Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tickets as $t)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="text-slate-600 text-xs font-bold font-mono">#MT-{{ sprintf('%03d', $t->id) }}</td>
                            <td><span class="font-black text-slate-800 text-base bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">{{ $t->room_number }}</span></td>
                            <td class="max-w-[250px]"><p class="text-slate-800 font-bold text-xs truncate">{{ $t->issue }}</p></td>
                            <td class="text-xs font-semibold text-slate-700">{{ $t->assignee_name ?? 'Staff' }}</td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $t->status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                    <i class="fas {{ $t->status === 'Completed' ? 'fa-check-circle' : 'fa-times-circle' }} text-[10px]"></i> {{ $t->status }}
                                </span>
                            </td>
                            <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($t->updated_at ?? $t->created_at)->format('d M Y, h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fas fa-history text-4xl text-slate-200 mb-3 block"></i>
                                <p class="text-sm font-medium">No completed history tickets found yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())<div class="px-5 py-4 border-t border-slate-100">{{ $tickets->links() }}</div>@endif
        </div>
    @endif

    {{-- Slide-over Drawer for Create & Edit Ticket --}}
    <div x-show="$wire.showDrawer" class="drawer-overlay" @click="$wire.showDrawer = false"
         x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display:none"></div>
    <div x-show="$wire.showDrawer" class="drawer-panel"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         style="display:none">
        <div class="drawer-header border-b border-slate-100 px-6 py-4 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">{{ $isEditMode ? 'Edit Maintenance Ticket' : 'New Maintenance Ticket' }}</h3>
            <button @click="$wire.showDrawer = false" class="btn-icon text-slate-400 hover:text-slate-600 cursor-pointer"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body space-y-5 px-6 py-5">
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room <span class="text-red-500">*</span></label>
                <select wire:model="room_id" class="pms-select text-xs">
                    <option value="">Select room...</option>
                    @foreach($rooms as $room)<option value="{{ $room->id }}">{{ $room->room_number }}</option>@endforeach
                </select>
                @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Issue Description <span class="text-red-500">*</span></label>
                <textarea wire:model="issue" rows="3" class="pms-input text-xs resize-none rounded-lg border border-slate-200" placeholder="Describe the maintenance or repair issue..."></textarea>
                @error('issue') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Priority</label>
                    <select wire:model="priority" class="pms-select text-xs">
                        <option>Low</option><option>Medium</option><option>High</option><option>Critical</option>
                    </select>
                </div>
                <div>
                    <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</label>
                    <select wire:model="status" class="pms-select text-xs">
                        <option>Open</option><option>In Progress</option><option>Completed</option><option>Cancelled</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Assign To Staff</label>
                <select wire:model="assigned_to" class="pms-select text-xs">
                    <option value="">Unassigned</option>
                    @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? 'Staff' }})</option>@endforeach
                </select>
                @error('assigned_to') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Internal Notes</label>
                <textarea wire:model="notes" rows="2" class="pms-input text-xs resize-none rounded-lg border border-slate-200" placeholder="Additional details or updates..."></textarea>
            </div>
        </div>
        <div class="drawer-footer border-t border-slate-100 px-6 py-4 flex items-center justify-end gap-3">
            <button @click="$wire.showDrawer = false" class="btn-secondary text-xs font-bold rounded-lg py-2">Cancel</button>
            <button wire:click="store" wire:loading.attr="disabled" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer">
                <span wire:loading wire:target="store" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                {{ $isEditMode ? 'Update Ticket' : 'Create Ticket' }}
            </button>
        </div>
    </div>
</div>
