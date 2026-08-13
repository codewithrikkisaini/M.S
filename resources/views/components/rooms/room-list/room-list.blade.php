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
                        <th class="font-bold">Photo</th>
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
                            <div class="flex items-center gap-1.5">
                                <img src="{{ $room->image_url }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=400&q=80';" alt="Room {{ $room->room_number }}" class="w-12 h-9 object-cover rounded-lg border border-slate-200 shadow-sm shrink-0">
                                @php $imgs = $room->images; @endphp
                                @if(count($imgs) > 1)
                                <span class="px-1.5 py-0.5 text-[10px] font-black text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-md shrink-0" title="{{ count($imgs) }} total photos">
                                    +{{ count($imgs) - 1 }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="font-black text-slate-800 text-base tracking-tight bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">{{ $room->room_number }}</span>
                        </td>
                        <td>
                            <span class="font-bold text-slate-800 text-sm block">{{ $room->roomType->name ?? '—' }}</span>
                            @if(!empty($room->bed_type) || !empty($room->room_option))
                            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                @if(!empty($room->bed_type))
                                    <span class="text-[10px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">{{ $room->bed_type }}</span>
                                @endif
                                @if(!empty($room->room_option))
                                    @foreach(explode(',', $room->room_option) as $opt)
                                        @if(trim($opt))
                                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100/80">
                                                <i class="fas fa-check-circle text-[9px] mr-1 text-indigo-500"></i>{{ trim($opt) }}
                                            </span>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            @endif
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
                                    'Clean' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'Dirty' => 'bg-orange-50 text-orange-700 border-orange-200',
                                    'Inspecting' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <select wire:change="updateHousekeepingStatus({{ $room->id }}, $event.target.value)"
                                    class="inline-flex items-center text-xs font-bold rounded-full border px-2.5 py-1 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all shadow-xs {{ $hkClass }}">
                                <option value="Clean" {{ $hk === 'Clean' ? 'selected' : '' }} class="bg-white text-emerald-700 font-bold">🟢 Clean</option>
                                <option value="Dirty" {{ $hk === 'Dirty' ? 'selected' : '' }} class="bg-white text-orange-700 font-bold">🟠 Dirty</option>
                                <option value="Inspecting" {{ $hk === 'Inspecting' ? 'selected' : '' }} class="bg-white text-amber-700 font-bold">🟡 Inspecting</option>
                            </select>
                        </td>
                        <td>
                            @php
                                $ticketsCount = $room->activeMaintenanceTickets->count();
                            @endphp
                            <div class="flex items-center gap-1.5">
                                @if($ticketsCount > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        {{ $ticketsCount }} Ticket{{ $ticketsCount > 1 ? 's' : '' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-50 text-slate-400 border border-slate-150">
                                        None
                                    </span>
                                @endif
                                <button wire:click="openMaintenanceModal({{ $room->id }})"
                                        class="px-2 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg transition-all cursor-pointer flex items-center gap-1"
                                        title="Report Maintenance Issue">
                                    <i class="fas fa-wrench text-[9px] text-slate-500"></i> + Ticket
                                </button>
                            </div>
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
                        <td colspan="9" class="py-12 text-center text-slate-400">
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

    {{-- Quick Maintenance Ticket Modal --}}
    @if($showTicketModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 transition-all">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm border border-amber-100">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">New Maintenance Ticket</h3>
                        <p class="text-xs text-slate-500">Room <span class="font-bold text-indigo-600">#{{ $selectedRoomNumber }}</span></p>
                    </div>
                </div>
                <button wire:click="closeMaintenanceModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveMaintenanceTicket" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Issue Description <span class="text-rose-500">*</span></label>
                    <textarea wire:model="ticketIssue" required rows="3" placeholder="Describe the maintenance or repair issue..." class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20"></textarea>
                    @error('ticketIssue') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Priority</label>
                        <select wire:model="ticketPriority" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-bold focus:bg-white focus:outline-none focus:border-indigo-600">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Initial Status</label>
                        <select wire:model="ticketStatus" class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs font-bold focus:bg-white focus:outline-none focus:border-indigo-600">
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Notes (Optional)</label>
                    <input type="text" wire:model="ticketNotes" placeholder="Additional notes or technician instructions..." class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 text-xs focus:bg-white focus:outline-none focus:border-indigo-600">
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 mt-6">
                    <button type="button" wire:click="closeMaintenanceModal" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-500/20">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
