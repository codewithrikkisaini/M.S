<div>
    {{-- Page Title Header --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shadow-xs">
                    <i class="fas fa-broom"></i>
                </div>
                <span>Housekeeping Dashboard</span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                {{ Auth::user()?->hotel?->name ?? 'Hotel Management System' }} • Real-time room status tracking & audit controls
            </p>
        </div>
    </div>

    {{-- Dedicated Housekeeping Dashboard Welcome Banner --}}
    <div class="mb-6 bg-gradient-to-r from-teal-600 via-emerald-600 to-teal-800 rounded-2xl p-5 text-white shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-emerald-500/30">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/20 shadow-inner">
                <i class="fas fa-sparkles text-2xl text-white"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-extrabold text-white tracking-tight">Housekeeping Dashboard</h2>
                    <span class="bg-white/20 text-white text-[10px] font-black px-2.5 py-0.5 rounded-full uppercase tracking-wider border border-white/20">{{ Auth::user()?->hotel?->name ?? 'Hotel' }}</span>
                </div>
                <p class="text-emerald-100 text-xs mt-1">
                    Manage room cleanliness, staff audit logs, and inspection workflows for <strong>{{ Auth::user()?->hotel?->name ?? 'your hotel' }}</strong>.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2.5 shrink-0 w-full md:w-auto justify-end">
            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('receptionist') || Auth::user()->hasRole('housekeeping'))
            <button wire:click="openCreate" class="bg-white text-emerald-800 hover:bg-emerald-50 text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <i class="fas fa-plus"></i> Add Entry
            </button>
            @endif
            <a href="{{ route('dashboard') }}" class="bg-emerald-900/60 hover:bg-emerald-900 text-white text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 border border-white/20">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- Status Overview Cards (5 Executive Cards) --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 mb-6">
        {{-- Total Rooms --}}
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

        {{-- Clean & Ready --}}
        <button wire:click="$set('statusFilter', '{{ $statusFilter === 'Clean' ? '' : 'Clean' }}')"
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

        {{-- Dirty / Needs Cleaning --}}
        <button wire:click="$set('statusFilter', '{{ $statusFilter === 'Dirty' ? '' : 'Dirty' }}')"
                class="pms-card p-4 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $statusFilter === 'Dirty' ? 'ring-2 ring-rose-600 border-rose-100 shadow-md bg-rose-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
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

        {{-- Under Inspection --}}
        <button wire:click="$set('statusFilter', '{{ $statusFilter === 'Inspecting' ? '' : 'Inspecting' }}')"
                class="pms-card p-4 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $statusFilter === 'Inspecting' ? 'ring-2 ring-amber-600 border-amber-100 shadow-md bg-amber-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
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

        {{-- Pending Turnaround --}}
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

    {{-- Table Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-broom text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Cleanliness Register</h3>
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
                        <td colspan="6" class="py-12 text-center text-slate-400">
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
    @endif
</div>
