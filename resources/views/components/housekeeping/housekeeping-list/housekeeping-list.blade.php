<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Housekeeping Board</h1>
            <p class="text-sm text-gray-500 mt-0.5">Monitor, update, and manage room cleanliness levels and assignments</p>
        </div>
        @if(Auth::user()->hasRole('admin'))
        <button wire:click="openCreate" class="btn-primary btn-sm rounded-lg shadow-sm cursor-pointer">
            <i class="fas fa-plus text-xs"></i> Add Housekeeping Record
        </button>
        @endif
    </div>

    @php
        $statusConfig = [
            'Clean' => ['key' => 'clean', 'bg' => 'bg-emerald-50 border-emerald-100/50', 'text' => 'text-emerald-600', 'icon' => 'fa-check-circle'],
            'Dirty' => ['key' => 'dirty', 'bg' => 'bg-rose-50 border-rose-100/50', 'text' => 'text-rose-600', 'icon' => 'fa-exclamation-circle'],
            'Inspecting' => ['key' => 'inspecting', 'bg' => 'bg-amber-50 border-amber-100/50', 'text' => 'text-amber-600', 'icon' => 'fa-search'],
            'Maintenance' => ['key' => 'maintenance', 'bg' => 'bg-slate-100 border-slate-200/50', 'text' => 'text-slate-600', 'icon' => 'fa-tools'],
        ];
    @endphp

    {{-- Status Overview Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach($statusConfig as $label => $config)
        <button wire:click="$set('statusFilter', '{{ $statusFilter === $label ? '' : $label }}')"
                class="pms-card p-5 text-left hover:shadow-md transition-all duration-200 cursor-pointer border {{ $statusFilter === $label ? 'ring-2 ring-indigo-600 border-indigo-100 shadow-md bg-indigo-50/10' : 'border-slate-100/80 hover:border-slate-200' }}">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl {{ $config['bg'] }} border flex items-center justify-center shrink-0">
                    <i class="fas {{ $config['icon'] }} {{ $config['text'] }} text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ $counts[$config['key']] }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ $label }}</p>
                </div>
            </div>
        </button>
        @endforeach
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
                    <option>Maintenance</option>
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
                        <th class="font-bold">Updated By</th>
                        <th class="font-bold">Notes</th>
                        <th class="font-bold">Last Updated</th>
                        @if(Auth::user()->hasRole('admin'))<th class="font-bold text-right">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $rec)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <span class="font-black text-slate-800 text-base tracking-tight bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">{{ $rec->room->room_number ?? 'N/A' }}</span>
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
                            <span class="font-semibold text-slate-800 text-sm">{{ $rec->updater->name ?? '—' }}</span>
                        </td>
                        <td class="text-slate-500 text-xs max-w-[200px] truncate" title="{{ $rec->notes }}">{{ $rec->notes ?? '—' }}</td>
                        <td class="text-slate-500 text-xs font-medium">{{ $rec->updated_at->format('d M Y, h:i A') }}</td>
                        @if(Auth::user()->hasRole('admin'))
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
    @if(Auth::user()->hasRole('admin'))
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
                    <option value="Maintenance">Maintenance</option>
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
