<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Daily Operations Report</h1>
            <p class="text-sm text-gray-500 mt-0.5">Overview of occupancy, departures, check-ins, and financial summaries</p>
        </div>
        <div class="flex items-center gap-2.5">
            <input type="date" wire:model.live="date" class="pms-input py-1.5 text-xs w-44 rounded-lg border border-slate-200">
            <button wire:click="$set('date', '{{ now()->toDateString() }}')" class="btn-secondary btn-sm rounded-lg py-1.5 font-bold cursor-pointer">
                <i class="fas fa-calendar-day text-[10px]"></i> Today
            </button>
        </div>
    </div>

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-slate-50 text-slate-500 border border-slate-100"><i class="fas fa-bed text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $totalRooms }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Total Rooms</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-red-50 text-red-600 border border-red-100"><i class="fas fa-door-closed text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $occupiedRooms }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Occupied</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-emerald-50 text-emerald-600 border border-emerald-100"><i class="fas fa-door-open text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $availableRooms }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Available</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-blue-50 text-blue-600 border border-blue-100"><i class="fas fa-sign-in-alt text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $checkInsToday }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Check-Ins</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-orange-50 text-orange-600 border border-orange-100"><i class="fas fa-sign-out-alt text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $checkOutsToday }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Check-Outs</p>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-md hover:shadow-xl transition-all duration-200 border-none">
            <div class="stat-icon bg-white/20 text-white border-none"><i class="fas fa-dollar-sign text-lg"></i></div>
            <div>
                <p class="text-2xl font-black tracking-tight">${{ number_format($revenueToday, 0) }}</p>
                <p class="text-[10px] font-bold text-indigo-100 uppercase tracking-wider mt-0.5">Daily Revenue</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Occupancy rate --}}
        <div class="pms-card shadow-sm border border-slate-100/80 p-5">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-indigo-500"></i> Occupancy Share
            </h3>
            <div class="flex items-end gap-3 mb-3">
                <span class="text-3xl font-black text-indigo-600 tracking-tight">{{ $occupancyRate }}%</span>
                <span class="text-xs text-slate-400 mb-1 font-semibold">of {{ $totalRooms }} total rooms</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-3 mb-4 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full transition-all duration-500" style="width: {{ $occupancyRate }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-slate-400 font-semibold">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span> {{ $occupiedRooms }} occupied</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-slate-300"></span> {{ $availableRooms }} available</span>
            </div>
        </div>

        {{-- Housekeeping --}}
        <div class="pms-card shadow-sm border border-slate-100/80 p-5">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fas fa-broom text-indigo-500"></i> Housekeeping Status
            </h3>
            @if($housekeepingPending > 0)
            <div class="flex items-center gap-3.5 p-3.5 bg-amber-50 rounded-xl border border-amber-200 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-amber-100 border border-amber-200 flex items-center justify-center shrink-0">
                    <i class="fas fa-broom text-amber-600 text-lg animate-pulse"></i>
                </div>
                <div>
                    <p class="font-bold text-amber-800 text-sm">{{ $housekeepingPending }} rooms pending</p>
                    <p class="text-[11px] text-amber-600 font-semibold">Need cleaning or inspection</p>
                </div>
            </div>
            @else
            <div class="flex items-center gap-3.5 p-3.5 bg-emerald-50 rounded-xl border border-emerald-200 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 border border-emerald-200 flex items-center justify-center shrink-0">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                </div>
                <div>
                    <p class="font-bold text-emerald-800 text-sm">All rooms clean</p>
                    <p class="text-[11px] text-emerald-600 font-semibold">No pending tasks</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Revenue summary --}}
        <div class="pms-card shadow-sm border border-slate-100/80 p-5">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="fas fa-wallet text-indigo-500"></i> Revenue Summary
            </h3>
            <div class="text-center bg-slate-50 border border-slate-100 py-3 rounded-xl">
                <p class="text-3xl font-black text-emerald-600 tracking-tight">${{ number_format($revenueToday, 2) }}</p>
                <p class="text-xs text-slate-400 mt-1 font-bold">Total for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">collected from {{ $checkOutsToday }} departure check-out(s)</p>
            </div>
        </div>
    </div>

    {{-- Recent Check-Outs table --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center border border-orange-100"><i class="fas fa-sign-out-alt text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Check-Outs on {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>
                    <p class="text-[10px] text-slate-400">List of departures and generated bills</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Guest</th>
                        <th class="font-bold">Room</th>
                        <th class="font-bold">Stay Duration</th>
                        <th class="font-bold">Paid Amount</th>
                        <th class="font-bold text-right">Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentCheckOuts as $co)
                    @php $res = optional($co->reservation); @endphp
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <span class="font-bold text-slate-800 text-sm">{{ optional($res->guest)->name ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="font-semibold text-slate-700 text-sm">{{ $res->rooms ? ($res->rooms->pluck('room_number')->implode(', ') ?: '—') : '—' }}</span>
                        </td>
                        <td>
                            <span class="text-slate-600 text-xs font-semibold">
                                {{ $res->check_in_date && $res->check_out_date
                                    ? \Carbon\Carbon::parse($res->check_in_date)->diffInDays(\Carbon\Carbon::parse($res->check_out_date)) . ' night(s)'
                                    : '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="font-bold text-slate-900 text-sm">${{ number_format($co->total_amount, 2) }}</span>
                        </td>
                        <td class="text-right">
                            @if($co->invoice)
                            <a href="{{ route('invoice.download', $co->invoice->id) }}" target="_blank"
                               class="btn-secondary btn-sm rounded-lg py-1 font-bold shadow-sm inline-flex items-center gap-1">
                                <i class="fas fa-file-pdf text-red-500 text-[10px]"></i> PDF
                            </a>
                            @else 
                            <span class="text-slate-400 text-xs font-semibold">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-10 text-center text-slate-400 text-sm font-semibold">No check-outs for this date.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daily Cash Sheet --}}
    <div class="pms-card mt-6 shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-receipt text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Daily Cash Sheet</h3>
                    <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($date)->format('d M Y, l') }}</p>
                </div>
            </div>
            <a href="{{ route('reports.daily-cash-sheet.download', ['date' => $date]) }}" target="_blank" class="btn-secondary btn-sm rounded-lg py-1.5 font-bold shadow-sm">
                <i class="fas fa-file-pdf text-red-500"></i> Download Cash Sheet
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Room No.</th>
                        <th class="font-bold">Guest Name</th>
                        <th class="font-bold">Room Rent</th>
                        <th class="font-bold">Tax</th>
                        <th class="font-bold">Misc Charges</th>
                        <th class="font-bold">Arrv. Date</th>
                        <th class="font-bold">Dept. Date</th>
                        <th class="font-bold">Balance Due</th>
                        <th class="font-bold">Paid</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($cashSheet['rows'] as $row)
                    <tr class="{{ $row['name'] ? 'hover:bg-slate-50/40' : 'opacity-40' }} transition-colors">
                        <td class="font-black text-slate-800 text-xs">
                            <span class="bg-slate-50 px-2.5 py-0.5 rounded border border-slate-150 shadow-sm">{{ $row['room_number'] }}</span>
                        </td>
                        <td class="font-bold text-slate-800 text-xs">{{ $row['name'] ?? '—' }}</td>
                        <td class="text-slate-600 text-xs font-semibold">{{ $row['rent'] !== null ? '$' . number_format($row['rent'], 2) : '—' }}</td>
                        <td class="text-slate-600 text-xs font-medium">{{ $row['tax'] !== null ? '$' . number_format($row['tax'], 2) : '—' }}</td>
                        <td class="text-slate-400 text-xs">—</td>
                        <td class="text-slate-500 text-xs font-medium">{{ $row['arrival_date'] ? \Carbon\Carbon::parse($row['arrival_date'])->format('d M Y') : '—' }}</td>
                        <td class="text-slate-500 text-xs font-medium">{{ $row['departure_date'] ? \Carbon\Carbon::parse($row['departure_date'])->format('d M Y') : '—' }}</td>
                        <td class="text-xs font-bold {{ ($row['balance_due'] ?? 0) > 0 ? 'text-red-600' : 'text-slate-600' }}">{{ $row['balance_due'] !== null ? '$' . number_format($row['balance_due'], 2) : '—' }}</td>
                        <td class="text-emerald-600 text-xs font-bold">{{ $row['paid'] !== null ? '$' . number_format($row['paid'], 2) : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-150 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs font-bold text-slate-600 bg-slate-50/40">
            <div>Total Cash: <span class="font-black text-slate-800 ml-1">${{ number_format($cashSheet['totals']['cash'], 2) }}</span></div>
            <div>Total Card: <span class="font-black text-slate-800 ml-1">${{ number_format($cashSheet['totals']['card'], 2) }}</span></div>
            <div>Total UPI: <span class="font-black text-slate-800 ml-1">${{ number_format($cashSheet['totals']['upi'], 2) }}</span></div>
            <div>Grand Total: <span class="font-black text-emerald-600 ml-1">${{ number_format($cashSheet['totals']['grand_total'], 2) }}</span></div>
        </div>
    </div>

    {{-- Range export --}}
    <div class="pms-card mt-6 shadow-sm border border-slate-100/80 p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-2.5 flex items-center gap-2">
            <i class="fas fa-file-export text-indigo-500"></i> Bulk Export Date Range
        </h3>
        <p class="text-xs text-slate-400 mb-4 font-semibold">Generates one Daily Cash Sheet page per day in the selected range as a combined PDF.</p>
        <div class="flex flex-wrap items-end gap-3.5">
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">From Date</label>
                <input type="date" wire:model="rangeFrom" class="pms-input text-xs py-1.5 rounded-lg border border-slate-200">
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">To Date</label>
                <input type="date" wire:model="rangeTo" class="pms-input text-xs py-1.5 rounded-lg border border-slate-200">
            </div>
            <a href="{{ route('reports.daily-cash-sheet.download-range', ['from' => $rangeFrom, 'to' => $rangeTo]) }}"
               target="_blank" class="btn-primary btn-sm rounded-lg py-2 font-bold shadow-sm">
                <i class="fas fa-file-pdf"></i> Download Range PDF
            </a>
        </div>
    </div>

    {{-- Auto-email schedule --}}
    <div class="pms-card mt-6 shadow-sm border border-slate-100/80 p-5">
        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i class="fas fa-envelope-open-text text-indigo-500"></i> Operational Email Automation
        </h3>
        <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-150 rounded-xl mb-5">
            <div>
                <p class="font-bold text-slate-800 text-sm">Auto-send Daily Cash Sheet by Email</p>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Today's report will be emailed automatically to management at the set time</p>
            </div>
            <button wire:click="$toggle('daily_report_auto_send')"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer {{ $daily_report_auto_send ? 'bg-indigo-600' : 'bg-slate-300' }}">
                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $daily_report_auto_send ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Recipient Emails <span class="text-slate-400 font-normal lowercase">(comma-separated)</span></label>
                <input type="text" wire:model="daily_report_email" class="pms-input text-xs" placeholder="manager@hotel.com, accounts@hotel.com">
                @error('daily_report_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Trigger Time</label>
                <input type="time" wire:model="daily_report_time" class="pms-input text-xs">
                @error('daily_report_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex justify-end pt-5 border-t border-slate-100 mt-5">
            <button wire:click="saveEmailSchedule" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer shadow-sm"><i class="fas fa-save text-[10px]"></i> Save Automation Setup</button>
        </div>
    </div>
</div>