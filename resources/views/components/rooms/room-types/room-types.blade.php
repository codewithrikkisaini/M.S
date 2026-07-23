<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('rooms.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Room Types & Physical Room Management</h1>
                <p class="text-sm text-gray-500 mt-0.5">Configure room categories, daily/weekly/monthly rate tariffs, and assign physical rooms</p>
            </div>
        </div>
    </div>

    {{-- Grid 1: Add Room Type Tariff & Add Physical Room Form Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        {{-- Card 1: Add or Preset Room Type Tariff --}}
        <div class="pms-card shadow-sm border border-slate-100/80 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                            <i class="fas fa-file-invoice-dollar text-xs"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">1. Add Room Type Tariff</h3>
                    </div>
                    <span class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">
                        Preset Tariff
                    </span>
                </div>

                <div class="space-y-3">
                    {{-- Dropdown Preset --}}
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room Type Preset *</label>
                        <select wire:model.live="selected_preset" class="pms-input text-xs font-bold text-slate-800 bg-slate-50 border-slate-200">
                            <option value="">-- Select Preset or Custom Type --</option>
                            <option value="Single">Single (Daily: 59.95 | Weekly: 249.90 | Monthly: 990.00 | Tax: 15%)</option>
                            <option value="Double">Double (Daily: 79.95 | Weekly: 349.90 | Monthly: 1190.00 | Tax: 15%)</option>
                            <option value="Apartment">Apartment (Daily: 79.90 | Weekly: 349.90 | Monthly: 1349.00 | Tax: 15%)</option>
                            <option value="custom">Custom Room Type</option>
                        </select>
                    </div>

                    {{-- Room Type Name --}}
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room Type Name *</label>
                        <input type="text" wire:model="name" class="pms-input text-xs" placeholder="e.g. Single, Double, Apartment">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Daily / Weekly / Monthly / Tax Rates --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <div>
                            <label class="pms-label text-[10px] font-semibold text-slate-600 uppercase">Daily *</label>
                            <input type="number" step="0.01" wire:model="daily_rate" class="pms-input text-xs" placeholder="59.95">
                            @error('daily_rate') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-[10px] font-semibold text-slate-600 uppercase">Weekly *</label>
                            <input type="number" step="0.01" wire:model="weekly_rate" class="pms-input text-xs" placeholder="249.90">
                            @error('weekly_rate') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-[10px] font-semibold text-slate-600 uppercase">Monthly *</label>
                            <input type="number" step="0.01" wire:model="monthly_rate" class="pms-input text-xs" placeholder="990.00">
                            @error('monthly_rate') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-[10px] font-semibold text-slate-600 uppercase">Tax (%) *</label>
                            <input type="number" step="0.01" wire:model="tax_percent" class="pms-input text-xs" placeholder="15">
                            @error('tax_percent') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Status *</label>
                        <select wire:model="status" class="pms-input text-xs">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex justify-end">
                <button wire:click="addType" wire:loading.attr="disabled" class="btn-primary text-xs font-bold rounded-lg py-2 px-5 cursor-pointer shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-save text-xs"></i> Save Room Type Tariff
                </button>
            </div>
        </div>

        {{-- Card 2: Add Physical Room under Room Type --}}
        <div class="pms-card shadow-sm border border-slate-100/80 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center border border-emerald-100">
                            <i class="fas fa-bed text-xs"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">2. Add Room to Room Type</h3>
                    </div>
                    <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                        Physical Room
                    </span>
                </div>

                <div class="space-y-3">
                    {{-- Select Room Type --}}
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Select Room Type *</label>
                        <select wire:model.live="room_type_id_for_room" class="pms-input text-xs font-bold text-slate-800">
                            <option value="">-- Choose Room Type --</option>
                            @foreach($roomTypes as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} (Daily: ${{ number_format($t->daily_rate ?: 59.95, 2) }} | Tax: {{ $t->tax_percent ?: 15 }}%)</option>
                            @endforeach
                        </select>
                        @error('room_type_id_for_room') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        {{-- Room Number --}}
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room No. *</label>
                            <input type="text" wire:model.live="room_number" class="pms-input text-xs font-bold" placeholder="e.g. 101, 102">
                            @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Floor --}}
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Floor *</label>
                            <input type="text" wire:model="room_floor" class="pms-input text-xs" placeholder="e.g. 1">
                            @error('room_floor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        {{-- Price --}}
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Price / Night ($) *</label>
                            <input type="number" step="0.01" wire:model="room_price" class="pms-input text-xs font-semibold" placeholder="59.95">
                            @error('room_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Room Status --}}
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Status *</label>
                            <select wire:model="room_status" class="pms-input text-xs">
                                <option value="Available">Available</option>
                                <option value="Occupied">Occupied</option>
                                <option value="Reserved">Reserved</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                            @error('room_status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex justify-end">
                <button wire:click="addRoom" wire:loading.attr="disabled" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg py-2 px-5 cursor-pointer shadow-sm flex items-center gap-1.5 transition-all">
                    <i class="fas fa-plus text-xs"></i> Add Room to Inventory
                </button>
            </div>
        </div>

    </div>

    {{-- Tariff Master Registry Table Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80 mb-6">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                    <i class="fas fa-list-alt text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Tariff Master Registry</h3>
                    <p class="text-[10px] text-slate-400">Registered room types, rate plans, and tax rules</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                {{ $roomTypes->count() }} total types
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Room Type</th>
                        <th class="font-bold">Daily Rate</th>
                        <th class="font-bold">Weekly Rate</th>
                        <th class="font-bold">Monthly Rate</th>
                        <th class="font-bold">Tax Rate (%)</th>
                        <th class="font-bold">Associated Rooms</th>
                        <th class="font-bold">Status</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roomTypes as $type)
                    <tr wire:key="type-{{ $type->id }}" class="hover:bg-slate-50/40 transition-colors">
                        @if($editingId === $type->id)
                            <td colspan="7" class="py-3 px-2">
                                <div class="grid grid-cols-6 gap-2 items-center">
                                    <input type="text" wire:model="editingName" class="pms-input text-xs py-1" placeholder="Name">
                                    <input type="number" step="0.01" wire:model="editingDailyRate" class="pms-input text-xs py-1" placeholder="Daily">
                                    <input type="number" step="0.01" wire:model="editingWeeklyRate" class="pms-input text-xs py-1" placeholder="Weekly">
                                    <input type="number" step="0.01" wire:model="editingMonthlyRate" class="pms-input text-xs py-1" placeholder="Monthly">
                                    <input type="number" step="0.01" wire:model="editingTaxPercent" class="pms-input text-xs py-1" placeholder="Tax %">
                                    <select wire:model="editingStatus" class="pms-input text-xs py-1">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </td>
                            <td class="text-right py-3 px-2">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button wire:click="updateType" class="btn-icon text-emerald-600 hover:bg-emerald-50 border border-slate-100 hover:border-emerald-100 shadow-sm cursor-pointer" title="Save">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                    <button wire:click="cancelEdit" class="btn-icon text-slate-500 hover:bg-slate-100 border border-slate-100 hover:border-slate-200 shadow-sm cursor-pointer" title="Cancel">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        @else
                            <td class="font-bold text-slate-800 text-sm">
                                {{ $type->name }}
                            </td>
                            <td class="font-semibold text-slate-700">
                                {{ number_format($type->daily_rate, 2) }}
                            </td>
                            <td class="font-semibold text-slate-700">
                                {{ number_format($type->weekly_rate, 2) }}
                            </td>
                            <td class="font-semibold text-slate-700">
                                {{ number_format($type->monthly_rate, 2) }}
                            </td>
                            <td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ number_format($type->tax_percent, 1) }}%
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm">
                                    {{ $type->rooms_count }} room{{ $type->rooms_count !== 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $type->status === 'inactive' ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $type->status === 'inactive' ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                    {{ ucfirst($type->status ?: 'active') }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button wire:click="editType({{ $type->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm cursor-pointer" title="Edit Tariff">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button wire:click="deleteType({{ $type->id }})"
                                            wire:confirm="Delete room type \"{{ $type->name }}\"?"
                                            class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <i class="fas fa-file-invoice-dollar text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">No room type tariffs registered. Add one above.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Physical Rooms Directory Table Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center border border-emerald-100">
                    <i class="fas fa-door-open text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Physical Rooms Inventory</h3>
                    <p class="text-[10px] text-slate-400">List of physical room units assigned to room types</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                {{ $rooms->count() }} physical rooms
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Room No.</th>
                        <th class="font-bold">Room Type</th>
                        <th class="font-bold">Floor</th>
                        <th class="font-bold">Price / Night</th>
                        <th class="font-bold">Status</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rooms as $r)
                    <tr wire:key="room-item-{{ $r->id }}" class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <span class="font-black text-slate-800 text-sm bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">Room {{ $r->room_number }}</span>
                        </td>
                        <td>
                            <span class="font-bold text-slate-800 text-xs">{{ $r->roomType->name ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-150 px-2 py-0.5 rounded">{{ $r->floor ?? '1' }}</span>
                        </td>
                        <td>
                            <span class="font-bold text-slate-800 text-xs">${{ number_format($r->price, 2) }}</span>
                        </td>
                        <td>
                            @php 
                                $st = $r->status; 
                                $bClass = match($st) {
                                    'Available' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Occupied' => 'bg-red-50 text-red-700 border-red-100',
                                    'Reserved' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'Maintenance' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-100',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $bClass }}">
                                {{ $st }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('rooms.edit', $r->id) }}" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 shadow-sm" title="Edit Room">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <button wire:click="deleteRoom({{ $r->id }})" wire:confirm="Delete room {{ $r->room_number }}?" class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 shadow-sm cursor-pointer" title="Delete Room">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            <i class="fas fa-door-closed text-3xl text-slate-200 mb-2 block"></i>
                            <p class="text-xs font-medium text-slate-400">No physical rooms added yet. Use form above to add room units.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
