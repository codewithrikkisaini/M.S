<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Maintenance Tickets</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage room repairs, facility issues, and technical maintenance logs</p>
        </div>
        <button wire:click="openCreate" class="btn-primary btn-sm rounded-lg shadow-sm cursor-pointer">
            <i class="fas fa-plus text-xs"></i> New Ticket
        </button>
    </div>

    {{-- Status Filters Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <button wire:click="filterByStatus('Open')"
                class="pms-card p-5 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $statusFilter === 'Open' ? 'ring-2 ring-indigo-600 border-indigo-100 shadow-md bg-indigo-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-folder-open text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ $counts['open'] }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Open Tickets</p>
                </div>
            </div>
        </button>

        <button wire:click="filterByStatus('In Progress')"
                class="pms-card p-5 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $statusFilter === 'In Progress' ? 'ring-2 ring-indigo-600 border-indigo-100 shadow-md bg-indigo-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-spinner text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ $counts['inprogress'] }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">In Progress</p>
                </div>
            </div>
        </button>

        <button wire:click="filterByStatus('Completed')"
                class="pms-card p-5 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $statusFilter === 'Completed' ? 'ring-2 ring-indigo-600 border-indigo-100 shadow-md bg-indigo-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ $counts['completed'] }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Completed</p>
                </div>
            </div>
        </button>

        <button wire:click="filterByCritical"
                class="pms-card p-5 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $priorityFilter === 'Critical' ? 'ring-2 ring-indigo-600 border-indigo-100 shadow-md bg-indigo-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-circle text-base animate-pulse"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ $counts['critical'] }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Critical Active</p>
                </div>
            </div>
        </button>
    </div>

    {{-- Ticket Table Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-tools text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Maintenance Registry</h3>
                    <p class="text-[10px] text-slate-400">Search and manage room repair records</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search tickets..." class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                </div>
                <select wire:model.live="priorityFilter" class="pms-select text-xs py-1.5 rounded-lg border border-slate-200 w-32">
                    <option value="">Priority</option>
                    <option>Low</option><option>Medium</option><option>High</option><option>Critical</option>
                </select>
                <select wire:model.live="statusFilter" class="pms-select text-xs py-1.5 rounded-lg border border-slate-200 w-36">
                    <option value="">Status</option>
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
                        <th class="font-bold"># ID</th>
                        <th class="font-bold">Room</th>
                        <th class="font-bold">Issue Description</th>
                        <th class="font-bold">Priority</th>
                        <th class="font-bold">Assigned To</th>
                        <th class="font-bold">Status</th>
                        <th class="font-bold">Created Date</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $t)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="text-slate-400 text-xs font-semibold">#{{ $t->id }}</td>
                        <td>
                            <span class="font-black text-slate-800 text-base tracking-tight bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">{{ $t->room_number }}</span>
                        </td>
                        <td class="max-w-[200px]">
                            <p class="text-slate-800 font-bold text-sm truncate" title="{{ $t->issue }}">{{ $t->issue }}</p>
                            @if($t->notes) <p class="text-slate-400 text-xs truncate mt-0.5" title="{{ $t->notes }}">{{ $t->notes }}</p> @endif
                        </td>
                        <td>
                            @php
                                $p = $t->priority;
                                $pClass = match($p) {
                                    'Critical' => 'bg-red-50 text-red-700 border-red-100',
                                    'High' => 'bg-orange-50 text-orange-700 border-orange-100',
                                    'Medium' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    default => 'bg-slate-50 text-slate-600 border-slate-150',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $pClass }}">
                                @if($p=='Critical')<i class="fas fa-fire text-[9px] animate-pulse"></i>@endif
                                {{ $p }}
                            </span>
                        </td>
                        <td>
                            <span class="text-xs font-semibold text-slate-700">{{ $t->assignee_name ?? '—' }}</span>
                        </td>
                        <td>
                            @php
                                $s = $t->status;
                                $statusClass = match($s) {
                                    'Open' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    default => 'bg-slate-50 text-slate-500 border-slate-150',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusClass }}">
                                {{ $s }}
                            </span>
                        </td>
                        <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y') }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button wire:click="edit({{ $t->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm cursor-pointer" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button wire:click="delete({{ $t->id }})" wire:confirm="Delete this ticket?" class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <i class="fas fa-tools text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium">No maintenance tickets found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())<div class="px-5 py-4 border-t border-slate-100">{{ $tickets->links() }}</div>@endif
    </div>

    {{-- Slide-over Drawer --}}
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
                    @foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach
                </select>
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