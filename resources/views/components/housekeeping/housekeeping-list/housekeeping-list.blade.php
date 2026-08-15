<div>
    {{-- Dynamic Page Title Header with + Add Entry Button --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shadow-xs">
                    <i class="fas {{ $activeTab === 'room_status' ? 'fa-bed' : ($activeTab === 'cleaning_tasks' ? 'fa-tasks' : ($activeTab === 'inspections' ? 'fa-clipboard-check' : ($activeTab === 'lost_found' ? 'fa-box-open' : ($activeTab === 'task_history' ? 'fa-history' : 'fa-broom')))) }}"></i>
                </div>
                <span>
                    @if($activeTab === 'room_status') Room Status Overview
                    @elseif($activeTab === 'cleaning_tasks') Cleaning Tasks Pending
                    @elseif($activeTab === 'inspections') Quality Control & Inspections
                    @elseif($activeTab === 'lost_found') Lost & Found Items Registry
                    @elseif($activeTab === 'task_history') Housekeeping Task History Log
                    @else Housekeeping Dashboard
                    @endif
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                {{ Auth::user()?->hotel?->name ?? 'Hotel Management System' }} • Real-time room cleanliness tracking & staff audit controls
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('receptionist') || Auth::user()->hasRole('housekeeping'))
                @if($activeTab === 'lost_found')
                <button wire:click="$set('showLostFoundModal', true)" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-plus"></i> Register Found Item
                </button>
                @else
                <button wire:click="openCreate" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-plus"></i> Add Entry
                </button>
                @endif
            @endif
        </div>
    </div>

    {{-- Executive Summary Cards (Shown ONLY for non-admin staff, DELETED for Hotel Admin) --}}
    @if(!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('superadmin'))
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 mb-6">
        <div class="pms-card p-4 text-left border border-slate-100/80 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-hotel text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ sprintf('%02d', $counts['total']) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Total Rooms</p>
                </div>
            </div>
        </div>

        <button wire:click="setTab('room_status'); $set('statusFilter', '{{ $statusFilter === 'Clean' ? '' : 'Clean' }}')"
                class="pms-card p-4 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $statusFilter === 'Clean' ? 'ring-2 ring-emerald-600 border-emerald-100 shadow-md bg-emerald-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-emerald-600 tracking-tight">{{ sprintf('%02d', $counts['clean']) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Clean & Ready</p>
                </div>
            </div>
        </button>

        <button wire:click="setTab('cleaning_tasks')"
                class="pms-card p-4 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $activeTab === 'cleaning_tasks' ? 'ring-2 ring-rose-600 border-rose-100 shadow-md bg-rose-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-circle text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-rose-600 tracking-tight">{{ sprintf('%02d', $counts['dirty']) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Needs Cleaning</p>
                </div>
            </div>
        </button>

        <button wire:click="setTab('inspections')"
                class="pms-card p-4 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $activeTab === 'inspections' ? 'ring-2 ring-amber-600 border-amber-100 shadow-md bg-amber-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-search text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-amber-600 tracking-tight">{{ sprintf('%02d', $counts['inspecting']) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Under Inspection</p>
                </div>
            </div>
        </button>

        <div class="pms-card p-4 text-left border border-slate-100/80 hover:shadow-sm transition-all">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-clock text-base"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-orange-600 tracking-tight">{{ sprintf('%02d', $counts['pending']) }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Pending Action</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Content Views --}}
    @if($activeTab === 'lost_found')
    {{-- Lost & Found View --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4 justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                    <i class="fas fa-box-open text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Lost & Found Registry</h3>
                    <p class="text-[10px] text-slate-400">Track and manage items left behind by guests in hotel rooms</p>
                </div>
            </div>

            <button wire:click="$set('showLostFoundModal', true)" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-4 py-2 rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                <i class="fas fa-plus"></i> Register Found Item
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table w-full">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Item Name</th>
                        <th class="font-bold">Room</th>
                        <th class="font-bold">Found By</th>
                        <th class="font-bold">Status</th>
                        <th class="font-bold">Date Found</th>
                        <th class="font-bold">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lostFoundItems as $lf)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                                    <i class="fas fa-box text-xs"></i>
                                </div>
                                <span class="font-black text-slate-800 text-sm tracking-tight">{{ $lf['item_name'] }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="bg-slate-100 text-slate-800 font-extrabold px-2.5 py-1 rounded-lg text-xs border border-slate-200">
                                Room {{ $lf['room_number'] }}
                            </span>
                        </td>
                        <td class="text-xs font-semibold text-slate-700">{{ $lf['found_by'] }}</td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-extrabold border {{ $lf['status'] === 'Returned to Guest' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                                <i class="fas {{ $lf['status'] === 'Returned to Guest' ? 'fa-check-circle' : 'fa-vault' }} text-[10px]"></i>
                                {{ $lf['status'] }}
                            </span>
                        </td>
                        <td class="text-xs text-slate-500 font-medium">{{ $lf['found_date'] }}</td>
                        <td class="text-xs text-slate-500 max-w-[220px] truncate" title="{{ $lf['notes'] }}">{{ $lf['notes'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="fas fa-box-open text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium">No lost & found items recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @else

    {{-- Cleanliness / Room Status / Task History Register View --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-broom text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">
                        @if($activeTab === 'cleaning_tasks') Cleaning Tasks Pending
                        @elseif($activeTab === 'inspections') Inspections Pending
                        @elseif($activeTab === 'task_history') Complete Audit History Log
                        @elseif($activeTab === 'room_status') Room Status Overview
                        @else Cleanliness Register
                        @endif
                    </h3>
                    <p class="text-[10px] text-slate-400">Manage logs and cleaning audit trials</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search by room..."
                           class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                </div>
                <select wire:model.live="statusFilter" class="pms-select text-xs py-1.5 rounded-lg border border-slate-200 w-40">
                    <option value="">All Statuses</option>
                    <option>Clean</option>
                    <option>Dirty</option>
                    <option>Inspecting</option>
                </select>
                @if($statusFilter)
                <button wire:click="$set('statusFilter', '')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer shrink-0">
                    Clear
                </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Room</th>
                        <th class="font-bold">Status</th>
                        <th class="font-bold">Quick Change</th>
                        <th class="font-bold">Updated By</th>
                        <th class="font-bold">Notes</th>
                        <th class="font-bold">Last Updated</th>
                        @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('receptionist') || Auth::user()->hasRole('housekeeping'))<th class="font-bold text-right">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-800 text-base tracking-tight bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">{{ $rec->room->room_number ?? 'N/A' }}</span>
                                @if($rec->room && $rec->room->roomType)
                                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">{{ $rec->room->roomType->name }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php 
                                $s = $rec->status; 
                                $badgeClass = match($s) {
                                    'Clean' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Dirty' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    'Inspecting' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'Maintenance' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-100',
                                };
                                $badgeIcon = match($s) {
                                    'Clean' => 'fa-check-circle',
                                    'Dirty' => 'fa-exclamation-circle',
                                    'Inspecting' => 'fa-hourglass-half',
                                    'Maintenance' => 'fa-tools',
                                    default => 'fa-info-circle',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                <i class="fas {{ $badgeIcon }} text-[10px]"></i>
                                {{ $s }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <button wire:click="updateRoomStatus({{ $rec->id }}, 'Clean')" 
                                        title="Mark Clean"
                                        class="px-2 py-1 text-[10px] font-extrabold rounded-md border transition-all cursor-pointer {{ $s === 'Clean' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
                                    <i class="fas fa-check"></i> Clean
                                </button>
                                <button wire:click="updateRoomStatus({{ $rec->id }}, 'Dirty')" 
                                        title="Mark Dirty"
                                        class="px-2 py-1 text-[10px] font-extrabold rounded-md border transition-all cursor-pointer {{ $s === 'Dirty' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}">
                                    <i class="fas fa-exclamation"></i> Dirty
                                </button>
                                <button wire:click="updateRoomStatus({{ $rec->id }}, 'Inspecting')" 
                                        title="Mark Inspecting"
                                        class="px-2 py-1 text-[10px] font-extrabold rounded-md border transition-all cursor-pointer {{ $s === 'Inspecting' ? 'bg-amber-500 text-white border-amber-500 shadow-xs' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' }}">
                                    <i class="fas fa-search"></i> Inspect
                                </button>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-extrabold">
                                    {{ strtoupper(substr($rec->updater->name ?? 'S', 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-800 text-xs">{{ $rec->updater->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="text-slate-500 text-xs max-w-[200px] truncate" title="{{ $rec->notes }}">{{ $rec->notes ?? '—' }}</td>
                        <td class="text-slate-500 text-xs font-medium">{{ $rec->updated_at->format('d M Y, h:i A') }}</td>
                        @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('receptionist') || Auth::user()->hasRole('housekeeping'))
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button wire:click="edit({{ $rec->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm cursor-pointer" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button wire:click="delete({{ $rec->id }})" wire:confirm="Delete this housekeeping record?"
                                        class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fas fa-broom text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium">No records found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $records->links() }}</div>
        @endif
    </div>
    @endif

    {{-- Slide-over Drawer --}}
    @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('receptionist') || Auth::user()->hasRole('housekeeping'))
    <div x-show="$wire.showDrawer" class="drawer-overlay" @click="$wire.showDrawer = false"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display:none"></div>
    <div x-show="$wire.showDrawer" class="drawer-panel"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         style="display:none">
        <div class="drawer-header border-b border-slate-100 px-6 py-4 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">{{ $isEditMode ? 'Edit Cleanliness Status' : 'New Housekeeping Entry' }}</h3>
            <button @click="$wire.showDrawer = false" class="btn-icon text-slate-400 hover:text-slate-600 cursor-pointer"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body space-y-5 px-6 py-5">
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room <span class="text-red-500">*</span></label>
                <select wire:model="room_id" class="pms-select text-xs">
                    <option value="">Select room...</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">{{ $room->room_number }}</option>
                    @endforeach
                </select>
                @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Housekeeping Status <span class="text-red-500">*</span></label>
                <select wire:model="status" class="pms-select text-xs">
                    <option value="Clean">Clean</option>
                    <option value="Dirty">Dirty</option>
                    <option value="Inspecting">Inspecting</option>
                </select>
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Internal Notes</label>
                <textarea wire:model="notes" rows="3" class="pms-input text-xs resize-none rounded-lg border border-slate-200" placeholder="Any internal notes or staff descriptions..."></textarea>
            </div>
        </div>
        <div class="drawer-footer border-t border-slate-100 px-6 py-4 flex items-center justify-end gap-3">
            <button @click="$wire.showDrawer = false" class="btn-secondary text-xs font-bold rounded-lg py-2">Cancel</button>
            <button wire:click="store" wire:loading.attr="disabled" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer">
                <span wire:loading wire:target="store" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                {{ $isEditMode ? 'Update' : 'Save Entry' }}
            </button>
        </div>
    </div>

    {{-- Lost & Found Item Registration Modal --}}
    <div x-show="$wire.showLostFoundModal" class="drawer-overlay" @click="$wire.showLostFoundModal = false"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display:none"></div>
    <div x-show="$wire.showLostFoundModal" class="drawer-panel"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         style="display:none">
        <div class="drawer-header border-b border-slate-100 px-6 py-4 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Register Lost & Found Item</h3>
            <button @click="$wire.showLostFoundModal = false" class="btn-icon text-slate-400 hover:text-slate-600 cursor-pointer"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body space-y-5 px-6 py-5">
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Item Name / Description <span class="text-red-500">*</span></label>
                <input type="text" wire:model="lf_item_name" class="pms-input text-xs" placeholder="e.g. Leather Wallet, iPhone Charger, Watch...">
                @error('lf_item_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room Found In <span class="text-red-500">*</span></label>
                <select wire:model="lf_room_id" class="pms-select text-xs">
                    <option value="">Select room...</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">Room {{ $room->room_number }}</option>
                    @endforeach
                </select>
                @error('lf_room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Found By (Staff Name) <span class="text-red-500">*</span></label>
                <input type="text" wire:model="lf_founder" class="pms-input text-xs" placeholder="Staff member name...">
                @error('lf_founder') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Current Location / Status</label>
                <select wire:model="lf_status" class="pms-select text-xs">
                    <option value="Stored in Safe">Stored in Safe</option>
                    <option value="Front Desk Counter">Front Desk Counter</option>
                    <option value="Housekeeping Office">Housekeeping Office</option>
                    <option value="Returned to Guest">Returned to Guest</option>
                </select>
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Additional Remarks</label>
                <textarea wire:model="lf_notes" rows="3" class="pms-input text-xs resize-none rounded-lg border border-slate-200" placeholder="Notes about item condition, guest details..."></textarea>
            </div>
        </div>
        <div class="drawer-footer border-t border-slate-100 px-6 py-4 flex items-center justify-end gap-3">
            <button @click="$wire.showLostFoundModal = false" class="btn-secondary text-xs font-bold rounded-lg py-2">Cancel</button>
            <button wire:click="addLostFound" wire:loading.attr="disabled" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer bg-emerald-600 hover:bg-emerald-700">
                <span wire:loading wire:target="addLostFound" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                Save Item
            </button>
        </div>
    </div>
    @endif
</div>
