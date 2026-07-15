<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Reservations</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage bookings, check-ins, check-outs, and guest stays</p>
        </div>
        <a href="{{ route('reservations.create') }}" class="btn-primary btn-sm rounded-lg shadow-sm">
            <i class="fas fa-plus text-xs"></i> New Reservation
        </a>
    </div>

    {{-- Filter/Search Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-calendar-check text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Booking Inventory</h3>
                    <p class="text-[10px] text-slate-400">Search and filter active hotel reservations</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search by guest or room..."
                           class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                </div>
                <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                    {{ $reservations->total() }} total
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold"># ID</th>
                        <th class="font-bold">Guest</th>
                        <th class="font-bold">Room(s)</th>
                        <th class="font-bold">Check In</th>
                        <th class="font-bold">Check Out</th>
                        <th class="font-bold">Payment Status</th>
                        <th class="font-bold">Booking Status</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reservations as $res)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="text-slate-400 text-xs font-semibold">#{{ $res->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                @php
                                    $initials = strtoupper(substr($res->guest->name ?? 'G', 0, 1));
                                    $gradients = [
                                        'A' => 'from-indigo-400 to-indigo-600', 'B' => 'from-emerald-400 to-emerald-600',
                                        'C' => 'from-blue-400 to-blue-600', 'D' => 'from-rose-400 to-rose-600',
                                        'E' => 'from-amber-400 to-amber-600', 'F' => 'from-orange-400 to-orange-600',
                                        'G' => 'from-teal-400 to-teal-600', 'H' => 'from-purple-400 to-purple-600',
                                        'I' => 'from-pink-400 to-pink-600', 'J' => 'from-cyan-400 to-cyan-600',
                                    ];
                                    $gradient = $gradients[$initials] ?? 'from-slate-400 to-slate-600';
                                @endphp
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center shrink-0 shadow-sm border border-white">
                                    <span class="text-xs font-black text-white">{{ $initials }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 text-sm block leading-none mb-1">{{ $res->guest->name ?? 'N/A' }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $res->guest->email ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="font-semibold text-slate-700 text-sm">
                            {{ $res->rooms->pluck('room_number')->implode(', ') ?: 'N/A' }}
                        </td>
                        <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}</td>
                        <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}</td>
                        <td>
                            @php $balance = $res->balance_due; @endphp
                            @if($balance > 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                ${{ number_format($balance, 2) }} due
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Fully Paid
                            </span>
                            @endif
                        </td>
                        <td>
                            @php 
                                $s = $res->status; 
                                $badgeClass = match($s) {
                                    'Confirmed' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                    'Checked-In' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Checked-Out' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'Cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    default => 'bg-blue-50 text-blue-700 border-blue-100',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                {{ $s }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($res->status == 'Confirmed' || $res->status == 'Reserved')
                                <button wire:click="checkIn({{ $res->id }})" wire:confirm="Check-In this guest?"
                                        class="btn-success btn-sm rounded-lg py-1 px-2.5 text-[11px] font-bold shadow-sm cursor-pointer">
                                    <i class="fas fa-sign-in-alt text-[10px]"></i> Check-In
                                </button>
                                @elseif($res->status == 'Checked-In')
                                <button wire:click="checkOut({{ $res->id }})" wire:confirm="Check-Out this guest?"
                                        class="btn-warning btn-sm rounded-lg py-1 px-2.5 text-[11px] font-bold shadow-sm cursor-pointer">
                                    <i class="fas fa-sign-out-alt text-[10px]"></i> Check-Out
                                </button>
                                @elseif($res->status == 'Checked-Out' && optional(optional($res->checkOut)->invoice)->id)
                                <a href="{{ route('invoice.download', $res->checkOut->invoice->id) }}"
                                   target="_blank" class="btn-secondary btn-sm rounded-lg py-1 px-2.5 text-[11px] font-bold shadow-sm">
                                    <i class="fas fa-file-pdf text-[10px] text-red-500"></i> Invoice
                                </a>
                                @endif
                                <a href="{{ route('reservations.edit', $res->id) }}" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <button wire:click="delete({{ $res->id }})" wire:confirm="Delete this reservation?"
                                        class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <i class="fas fa-calendar-times text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">No reservations found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reservations->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $reservations->links() }}</div>
        @endif
    </div>
</div>
