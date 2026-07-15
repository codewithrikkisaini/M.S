<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Check-In Interface</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track today's arrivals and process guest check-ins</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-indigo-50 text-indigo-600 border border-indigo-100"><i class="fas fa-calendar-day text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $todayCount }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Arrivals Scheduled Today</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-amber-50 text-amber-600 border border-amber-100"><i class="fas fa-clock text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $pendingTotal }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Pending Check-Ins</p>
            </div>
        </div>
    </div>

    {{-- Pending Table Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center border border-amber-100"><i class="fas fa-door-open text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Pending Arrivals</h3>
                    <p class="text-[10px] text-slate-400">Guests scheduled to check in today or overdue</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search guest..."
                           class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Guest</th>
                        <th class="font-bold">Assigned Room</th>
                        <th class="font-bold">Check-In Date</th>
                        <th class="font-bold">Check-Out Date</th>
                        <th class="font-bold">Occupancy</th>
                        <th class="font-bold">Notes</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($arrivals as $res)
                    <tr class="hover:bg-slate-50/40 transition-colors">
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
                                    <span class="text-[10px] text-slate-400 block">{{ $res->guest->phone ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-black text-slate-800 text-sm bg-slate-50 px-2 py-0.5 rounded border border-slate-150 shadow-sm">{{ $res->rooms->pluck('room_number')->implode(', ') ?: 'N/A' }}</span>
                            <span class="text-[10px] text-slate-400 font-medium block mt-1">{{ $res->rooms->map(fn($r) => optional($r->roomType)->name)->filter()->implode(', ') }}</span>
                        </td>
                        <td>
                            @php
                                $isToday = \Carbon\Carbon::parse($res->check_in_date)->isToday();
                                $isPast = \Carbon\Carbon::parse($res->check_in_date)->isPast();
                            @endphp
                            <span class="text-xs font-bold @if($isToday) text-emerald-600 @elseif($isPast) text-rose-600 @else text-slate-600 @endif">
                                {{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}
                            </span>
                            @if($isToday)
                                <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[9px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase ml-1">Today</span>
                            @elseif($isPast)
                                <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[9px] font-black bg-rose-50 text-rose-700 border border-rose-100 uppercase ml-1">Overdue</span>
                            @endif
                        </td>
                        <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}</td>
                        <td class="text-slate-600 text-xs font-semibold">
                            <span class="flex items-center gap-1"><i class="fas fa-users text-slate-400 text-[10px]"></i> {{ $res->adults }} Ad, {{ $res->children }} Ch</span>
                        </td>
                        <td class="text-slate-500 text-[11px] max-w-[120px] truncate" title="{{ $res->special_notes }}">{{ $res->special_notes ?? '—' }}</td>
                        <td class="text-right">
                            <button wire:click="checkIn({{ $res->id }})"
                                    wire:confirm="Check in {{ $res->guest->name ?? 'guest' }}?"
                                    wire:loading.attr="disabled"
                                    class="btn-success btn-sm rounded-lg py-1 px-2.5 text-[11px] font-bold shadow-sm cursor-pointer">
                                <i class="fas fa-sign-in-alt text-[10px]"></i> Check In
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-slate-400">
                            <i class="fas fa-check-circle text-5xl text-emerald-100 mb-3 block"></i>
                            <p class="text-sm font-semibold text-slate-500">No pending check-ins for today.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($arrivals->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $arrivals->links() }}</div>
        @endif
    </div>

    {{-- Upcoming (informational, not yet actionable) --}}
    <div class="pms-card mt-6 shadow-sm border border-slate-100/80">
        <div class="pms-card-header">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-slate-50 text-slate-500 rounded-lg flex items-center justify-center border border-slate-150"><i class="fas fa-calendar-alt text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Upcoming Arrivals</h3>
                    <p class="text-[10px] text-slate-400">Confirmed future arrivals logs</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Guest</th>
                        <th class="font-bold">Room</th>
                        <th class="font-bold">Check-In Date</th>
                        <th class="font-bold">Check-Out Date</th>
                        <th class="font-bold">Occupancy</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($upcoming as $res)
                    <tr class="opacity-80 hover:bg-slate-50/40 transition-colors">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200">
                                    <span class="text-xs font-bold text-slate-500">{{ strtoupper(substr($res->guest->name ?? 'G', 0, 1)) }}</span>
                                </div>
                                <span class="font-bold text-slate-700 text-sm">{{ $res->guest->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="font-semibold text-slate-700 text-sm">{{ $res->rooms->pluck('room_number')->implode(', ') ?: 'N/A' }}</span>
                        </td>
                        <td class="text-slate-600 text-xs font-medium">
                            <span>{{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}</span>
                            <span class="text-[10px] text-slate-400 font-semibold ml-1">({{ \Carbon\Carbon::parse($res->check_in_date)->diffForHumans() }})</span>
                        </td>
                        <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}</td>
                        <td class="text-slate-600 text-xs font-semibold">{{ $res->adults }}+{{ $res->children }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400">
                            <p class="text-sm font-medium">No upcoming confirmed bookings.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>