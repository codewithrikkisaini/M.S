<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Rooms</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage hotel room inventory, rates, housekeeping, and maintenance statuses</p>
        </div>
        <a href="{{ route('rooms.create') }}" class="btn-primary btn-sm rounded-lg shadow-sm">
            <i class="fas fa-plus text-xs"></i> Add Room
        </a>
    </div>

    {{-- Table Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-bed text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Room Directory</h3>
                    <p class="text-[10px] text-slate-400">Manage rates, active tickets, and clean statuses</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search rooms..."
                           class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                </div>
                <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                    {{ $rooms->total() }} total
                </span>
            </div>
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
                        <th class="font-bold">Housekeeping</th>
                        <th class="font-bold">Active Tickets</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rooms as $room)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <span class="font-black text-slate-800 text-base tracking-tight bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">{{ $room->room_number }}</span>
                        </td>
                        <td>
                            <span class="font-bold text-slate-800 text-sm block">{{ $room->roomType->name ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-150 px-2 py-1 rounded-md">{{ $room->floor ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="font-bold text-slate-800 text-sm">${{ number_format($room->price, 2) }}</span>
                        </td>
                        <td>
                            @php 
                                $s = $room->status; 
                                $badgeClass = match($s) {
                                    'Available' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Occupied' => 'bg-red-50 text-red-700 border-red-100',
                                    'Reserved' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'Maintenance' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-100',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                {{ $s }}
                            </span>
                        </td>
                        <td>
                            @php
                                $hk = optional($room->latestHousekeeping)->status ?? 'Clean';
                                $hkClass = match($hk) {
                                    'Clean' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Dirty' => 'bg-orange-50 text-orange-700 border-orange-100',
                                    'Inspecting' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    default => 'bg-slate-50 text-slate-600 border-slate-100',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $hkClass }}">
                                {{ $hk }}
                            </span>
                        </td>
                        <td>
                            @php
                                $ticketsCount = $room->activeMaintenanceTickets->count();
                            @endphp
                            @if($ticketsCount > 0)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    {{ $ticketsCount }} Ticket{{ $ticketsCount > 1 ? 's' : '' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-50 text-slate-400 border border-slate-150">
                                    None
                                </span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('rooms.edit', $room->id) }}"
                                   class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <button wire:click="delete({{ $room->id }})"
                                        wire:confirm="Delete room {{ $room->room_number }}?"
                                        class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <i class="fas fa-bed text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">No rooms found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rooms->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $rooms->links() }}
        </div>
        @endif
    </div>
</div>
