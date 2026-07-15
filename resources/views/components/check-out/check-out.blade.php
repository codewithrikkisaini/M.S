<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Check-Out Interface</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track today's departures and process guest check-outs</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-orange-50 text-orange-600 border border-orange-100"><i class="fas fa-calendar-day text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $checkoutsToday }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Departures Scheduled Today</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-rose-50 text-rose-600 border border-rose-100"><i class="fas fa-exclamation-triangle text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $overdueCount }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Overdue Departures</p>
            </div>
        </div>
    </div>

    {{-- Checked-In Guests Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center border border-emerald-100"><i class="fas fa-hotel text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">In-House Guests</h3>
                    <p class="text-[10px] text-slate-400">Guests currently checked in and residing at the hotel</p>
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
                        <th class="font-bold">Room</th>
                        <th class="font-bold">Check-In</th>
                        <th class="font-bold">Check-Out Due</th>
                        <th class="font-bold">Nights</th>
                        <th class="font-bold">Outstanding Balance</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($checkedIn as $res)
                    @php
                        $nights = \Carbon\Carbon::parse($res->check_in_date)->diffInDays(\Carbon\Carbon::parse($res->check_out_date));
                        $isOverdue = \Carbon\Carbon::parse($res->check_out_date)->isPast();
                        $isDueToday = \Carbon\Carbon::parse($res->check_out_date)->isToday();
                        
                        $rowBg = $isOverdue ? 'bg-red-50/30' : ($isDueToday ? 'bg-amber-50/30' : 'hover:bg-slate-50/40');
                    @endphp
                    <tr class="{{ $rowBg }} transition-colors">
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
                            <span class="font-black text-slate-800 text-sm bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">{{ $res->rooms->pluck('room_number')->implode(', ') ?: 'N/A' }}</span>
                        </td>
                        <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}</td>
                        <td>
                            <span class="text-xs font-bold @if($isOverdue) text-rose-600 @elseif($isDueToday) text-amber-600 @else text-slate-600 @endif">
                                {{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}
                            </span>
                            @if($isOverdue)
                                <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[9px] font-black bg-rose-50 text-rose-700 border border-rose-100 uppercase ml-1 animate-pulse">Overdue</span>
                            @elseif($isDueToday)
                                <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[9px] font-black bg-amber-50 text-amber-700 border border-amber-100 uppercase ml-1">Due Today</span>
                            @endif
                        </td>
                        <td class="text-slate-600 text-xs font-semibold">
                            <span class="flex items-center gap-1"><i class="fas fa-moon text-slate-400 text-[10px]"></i> {{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</span>
                        </td>
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
                                Paid
                            </span>
                            @endif
                        </td>
                        <td class="text-right">
                            <button wire:click="checkOut({{ $res->id }})"
                                    wire:confirm="Check out {{ $res->guest->name ?? 'guest' }}? This will generate an invoice."
                                    wire:loading.attr="disabled"
                                    class="btn-warning btn-sm rounded-lg py-1 px-2.5 text-[11px] font-bold shadow-sm cursor-pointer">
                                <i class="fas fa-sign-out-alt text-[10px]"></i> Check Out
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-slate-400">
                            <i class="fas fa-hotel text-5xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-semibold text-slate-500">No guests currently checked in</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($checkedIn->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $checkedIn->links() }}</div>
        @endif
    </div>
</div>