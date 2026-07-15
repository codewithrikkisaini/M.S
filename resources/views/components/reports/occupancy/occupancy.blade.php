<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Occupancy Reports</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track and analyze occupancy rates and room type utilization over time</p>
        </div>
        <input type="month" wire:model.live="month" class="pms-input py-1.5 text-xs w-44 rounded-lg border border-slate-200">
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Currently Occupied</p>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-md hover:shadow-xl transition-all duration-200 border-none">
            <div class="stat-icon bg-white/20 text-white border-none"><i class="fas fa-percent text-lg"></i></div>
            <div>
                <p class="text-2xl font-black tracking-tight">{{ $occupancyRate }}%</p>
                <p class="text-[10px] font-bold text-indigo-100 uppercase tracking-wider mt-0.5">Average Occupancy Rate</p>
            </div>
        </div>
    </div>

    {{-- Daily occupancy chart (visual bar) --}}
    <div class="pms-card shadow-sm border border-slate-100/80 p-5 mb-6">
        <div class="flex items-center justify-between border-b border-slate-50 pb-3.5 mb-5">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-chart-column text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Daily Occupancy Chart</h3>
                    <p class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</p>
                </div>
            </div>
        </div>
        
        <div class="flex items-end gap-1.5 h-36 border-b border-slate-100 pb-3">
            @foreach($dailyData as $d)
            <div class="flex flex-col items-center flex-1 gap-1">
                <div class="w-full rounded-t-lg bg-gradient-to-t from-indigo-500 to-purple-600 transition-all duration-300 min-h-[4px] hover:from-indigo-600 hover:to-purple-700 hover:scale-105 shadow-sm"
                     style="height: {{ max(4, $d['rate']) }}%"
                     title="Day {{ $d['date'] }}: {{ $d['count'] }} rooms ({{ $d['rate'] }}%)"></div>
                @if(count($dailyData) <= 31)
                <span class="text-slate-400 text-[8px] font-bold mt-1">{{ $d['date'] }}</span>
                @endif
            </div>
            @endforeach
        </div>
        <div class="flex items-center gap-2 mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
            <span class="w-2.5 h-2.5 rounded-sm bg-indigo-600 shadow-sm"></span> Occupied Rooms Share (%)
        </div>
    </div>

    {{-- Room type breakdown --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-th-list text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Status Distribution by Room Type</h3>
                    <p class="text-[10px] text-slate-400">Current availability logs by layout categories</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Room Type</th>
                        <th class="font-bold">Available</th>
                        <th class="font-bold">Occupied</th>
                        <th class="font-bold">Reserved</th>
                        <th class="font-bold">Maintenance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roomTypeStats as $typeId => $group)
                    @php
                        $typeName = optional($group->first()->roomType)->name ?? 'Unknown';
                        $byStatus = $group->pluck('count','status');
                    @endphp
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <span class="font-bold text-slate-800 text-sm block">{{ $typeName }}</span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">{{ $byStatus['Available'] ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100 shadow-sm">{{ $byStatus['Occupied'] ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 shadow-sm">{{ $byStatus['Reserved'] ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-sm">{{ $byStatus['Maintenance'] ?? 0 }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-12 text-center text-slate-400 text-sm font-semibold">No data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>