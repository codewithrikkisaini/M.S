<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Revenue Reports</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track annual and monthly cash flow metrics and comparative analytics</p>
        </div>
        <select wire:model.live="year" class="pms-select text-xs py-1.5 rounded-lg border border-slate-200 w-28">
            @for($y = date('Y'); $y >= date('Y') - 4; $y--)
            <option value="{{ $y }}">{{ $y }}</option>
            @endfor
        </select>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-indigo-50 text-indigo-600 border border-indigo-100"><i class="fas fa-chart-line text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">${{ number_format($totalAnnual, 2) }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Total Revenue ({{ $year }})</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-blue-50 text-blue-600 border border-blue-100"><i class="fas fa-calendar-alt text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">${{ number_format($currentMonth, 2) }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">This Month's Earnings</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            @php
                $isGrowth = $growth >= 0;
                $growthIcon = $isGrowth ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
                $growthColorBg = $isGrowth ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-red-50 text-red-600 border-red-100';
                $growthText = $isGrowth ? 'text-emerald-600' : 'text-red-600';
            @endphp
            <div class="stat-icon {{ $growthColorBg }} border"><i class="fas {{ $growthIcon }} text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold {{ $growthText }} tracking-tight">
                    {{ $isGrowth ? '+' : '' }}{{ $growth }}%
                </p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Compared to Last Month</p>
            </div>
        </div>
    </div>

    {{-- Monthly bar chart --}}
    <div class="pms-card shadow-sm border border-slate-100/80 p-5 mb-6">
        <div class="flex items-center justify-between border-b border-slate-50 pb-3.5 mb-5">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-chart-column text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Monthly Revenue Comparison</h3>
                    <p class="text-[10px] text-slate-400">Total checkouts values for {{ $year }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-end gap-2 h-44 border-b border-slate-100 pb-3">
            @php $maxRev = collect($monthlyRevenue)->max('revenue') ?: 1; @endphp
            @foreach($monthlyRevenue as $m)
            <div class="flex flex-col items-center flex-1 gap-1.5">
                <span class="text-[9px] text-slate-400 font-bold">${{ $m['revenue'] > 0 ? number_format($m['revenue']/1000, 1).'k' : '0' }}</span>
                <div class="w-full rounded-t-lg bg-gradient-to-t from-indigo-500 to-purple-600 transition-all duration-300 min-h-[4px] hover:from-indigo-600 hover:to-purple-700 hover:scale-105 shadow-sm"
                     style="height: {{ max(4, ($m['revenue'] / $maxRev) * 120) }}px"
                     title="{{ $m['month'] }}: ${{ number_format($m['revenue'], 2) }} ({{ $m['count'] }} check-outs)"></div>
                <span class="text-slate-500 text-[10px] font-bold mt-1">{{ $m['month'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Monthly table --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-table text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Monthly Financial Registry</h3>
                    <p class="text-[10px] text-slate-400">Chronological checkout and revenue values share</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Month</th>
                        <th class="font-bold">Total Check-Outs</th>
                        <th class="font-bold">Collected Revenue</th>
                        <th class="font-bold">Annual Share (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($monthlyRevenue as $m)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="font-bold text-slate-800 text-sm">{{ $m['month'] }} {{ $year }}</td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 border border-slate-150">{{ $m['count'] }} checkout{{ $m['count'] !== 1 ? 's' : '' }}</span>
                        </td>
                        <td>
                            <span class="font-black text-slate-800 text-sm">${{ number_format($m['revenue'], 2) }}</span>
                        </td>
                        <td>
                            @php $share = $totalAnnual > 0 ? round(($m['revenue'] / $totalAnnual) * 100, 1) : 0; @endphp
                            <div class="flex items-center gap-2.5">
                                <div class="flex-1 bg-slate-100 rounded-full h-2 max-w-[100px] overflow-hidden">
                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full" style="width:{{ $share }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-400">{{ $share }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-indigo-50/20 font-black border-t-2 border-indigo-100 text-slate-800">
                        <td class="font-black text-slate-800">Total ({{ $year }})</td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-100">{{ collect($monthlyRevenue)->sum('count') }} checkouts</span>
                        </td>
                        <td class="text-indigo-600 text-sm">${{ number_format($totalAnnual, 2) }}</td>
                        <td class="text-slate-400 text-xs">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>