<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ now()->format('l, d F Y') }} &mdash; Welcome back, admin</p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('reservations.index') }}" class="btn-primary btn-sm rounded-lg shadow-sm">
                <i class="fas fa-plus text-xs"></i> New Booking
            </a>
            <a href="{{ route('housekeeping.index') }}" class="btn-secondary btn-sm rounded-lg shadow-sm">
                <i class="fas fa-broom text-xs"></i> Housekeeping
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
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
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Arrivals</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-orange-50 text-orange-600 border border-orange-100"><i class="fas fa-sign-out-alt text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $checkOutsToday }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Departures</p>
            </div>
        </div>
        <div class="stat-card bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-md hover:shadow-xl transition-all duration-200 border-none">
            <div class="stat-icon bg-white/20 text-white border-none"><i class="fas fa-dollar-sign text-lg"></i></div>
            <div>
                <p class="text-2xl font-black tracking-tight">${{ number_format($revenueToday, 0) }}</p>
                <p class="text-[10px] font-bold text-indigo-100 uppercase tracking-wider mt-0.5">Revenue Today</p>
            </div>
        </div>
    </div>

    {{-- Main Body Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Side Columns (Floor Map & Recent Bookings) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Tabbed Live Room Floor Map --}}
            <div class="pms-card shadow-sm border border-slate-100/80" x-data="{ activeFloor: 'All' }">
                <div class="pms-card-header flex-wrap gap-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-map text-sm"></i></div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Live Room Floor Map</h3>
                            <p class="text-[10px] text-slate-400">Click room tiles for quick actions and status details</p>
                        </div>
                    </div>
                    
                    {{-- Status Legends --}}
                    <div class="flex flex-wrap gap-2 text-[10px] font-medium bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Available</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Occupied</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Reserved</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-400"></span> Dirty</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span> Maintenance</span>
                    </div>
                </div>

                <div class="p-5">
                    {{-- Floor Selector Tabs --}}
                    <div class="flex flex-wrap gap-1.5 bg-slate-50 p-1 rounded-xl border border-slate-100 mb-5 max-w-max">
                        <button @click="activeFloor = 'All'" 
                                :class="activeFloor === 'All' ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 font-semibold'" 
                                class="px-3.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                            All Floors
                        </button>
                        @foreach($rooms->groupBy('floor')->keys()->sort() as $floor)
                            @if($floor)
                            <button @click="activeFloor = '{{ $floor }}'" 
                                    :class="activeFloor === '{{ $floor }}' ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800 font-semibold'" 
                                    class="px-3.5 py-1.5 rounded-lg text-xs transition-all cursor-pointer">
                                {{ $floor === 'Unassigned' ? 'Unassigned' : 'Floor ' . $floor }}
                            </button>
                            @endif
                        @endforeach
                    </div>

                    {{-- Rooms Layout --}}
                    <div class="space-y-6">
                        @foreach($rooms->groupBy('floor')->sortKeys() as $floor => $floorRooms)
                            @if($floor)
                            <div x-show="activeFloor === 'All' || activeFloor === '{{ $floor }}'" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="border-b border-dashed border-slate-100 last:border-b-0 pb-5 last:pb-0">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">{{ $floor === 'Unassigned' ? 'Unassigned Rooms' : 'Floor ' . $floor }}</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3.5">
                                    @foreach($floorRooms as $room)
                                        @php
                                            $hk = optional($room->latestHousekeeping)->status ?? 'Clean';
                                            $status = $room->status;
                                            
                                            // Determine visual styling classes
                                            if ($status === 'Maintenance') {
                                                $classes = 'bg-slate-50 border border-slate-200 text-slate-500 hover:bg-slate-100 hover:border-slate-300';
                                                $indicatorBg = 'bg-slate-400';
                                            } elseif ($status === 'Occupied') {
                                                $classes = 'bg-red-50/70 border border-red-200/80 text-red-700 hover:bg-red-100 hover:border-red-300';
                                                $indicatorBg = 'bg-red-500';
                                            } elseif ($status === 'Reserved') {
                                                $classes = 'bg-blue-50/70 border border-blue-200/80 text-blue-700 hover:bg-blue-100 hover:border-blue-300';
                                                $indicatorBg = 'bg-blue-500';
                                            } elseif ($hk === 'Dirty') {
                                                $classes = 'bg-orange-50/70 border border-orange-200/80 text-orange-700 hover:bg-orange-100 hover:border-orange-300';
                                                $indicatorBg = 'bg-orange-400';
                                            } else {
                                                $classes = 'bg-emerald-50/70 border border-emerald-200/80 text-emerald-700 hover:bg-emerald-100 hover:border-emerald-300';
                                                $indicatorBg = 'bg-emerald-500';
                                            }
                                        @endphp
                                        <div x-data="{ open: false }" class="relative">
                                            <button @click.stop="open = !open" type="button" 
                                                    class="w-full p-4 rounded-xl flex flex-col items-center justify-center transition-all duration-200 cursor-pointer shadow-sm text-center relative {{ $classes }} hover:-translate-y-0.5">
                                                <span class="font-extrabold text-base tracking-tight">{{ $room->room_number }}</span>
                                                <span class="text-[9px] font-bold opacity-75 mt-0.5 uppercase tracking-wide">{{ optional($room->roomType)->name ?? 'No Type' }}</span>
                                                
                                                {{-- Live Indicator dots --}}
                                                <div class="flex items-center gap-1 mt-1.5">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $indicatorBg }}"></span>
                                                    @if($room->activeMaintenanceTickets->count() > 0)
                                                        <i class="fas fa-tools text-[9px] text-red-600 animate-pulse" title="Open Maintenance Ticket"></i>
                                                    @endif
                                                </div>
                                            </button>
                                            
                                            {{-- Click Popover Modal --}}
                                            <div x-show="open" @click.outside="open = false" 
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                                 class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2.5 z-30 bg-white border border-slate-150 rounded-xl shadow-2xl p-4 w-56 text-left"
                                                 style="display:none;">
                                                 
                                                 <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                                                     <span class="font-bold text-slate-800 text-sm">Room {{ $room->room_number }}</span>
                                                     <span class="text-[9px] font-extrabold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full uppercase tracking-wider">{{ optional($room->roomType)->name ?? 'No Type' }}</span>
                                                 </div>
                                                 
                                                 <div class="space-y-1.5 text-xs text-slate-600">
                                                     <div class="flex justify-between"><span class="text-slate-400">Price:</span> <span class="font-bold text-slate-800">${{ number_format($room->price, 2) }}</span></div>
                                                     <div class="flex justify-between">
                                                         <span class="text-slate-400">Status:</span> 
                                                         <span class="font-bold text-slate-800">{{ $status }}</span>
                                                     </div>
                                                     <div class="flex justify-between">
                                                         <span class="text-slate-400">Housekeeping:</span>
                                                         <span class="font-bold @if($hk=='Clean') text-emerald-600 @elseif($hk=='Dirty') text-orange-500 @else text-amber-500 @endif">{{ $hk }}</span>
                                                     </div>
                                                     @if($room->activeMaintenanceTickets->count() > 0)
                                                     <div class="flex justify-between text-rose-600 border-t border-dashed border-slate-100 pt-1.5 mt-1.5">
                                                         <span class="font-semibold">Tickets:</span>
                                                         <span class="font-black flex items-center gap-1"><i class="fas fa-tools text-[10px]"></i> {{ $room->activeMaintenanceTickets->count() }} Open</span>
                                                     </div>
                                                     @endif
                                                 </div>
                                                 
                                                 {{-- Quick Action link inside popover --}}
                                                 <div class="mt-3.5 border-t border-slate-100 pt-2 flex justify-end gap-1.5">
                                                     <a href="{{ route('rooms.edit', $room->id) }}" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1.5 rounded-lg transition-colors flex items-center gap-1 w-full justify-center">
                                                         <i class="fas fa-edit text-[8px]"></i> Edit Room Details
                                                     </a>
                                                 </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Recent Reservations Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80">
                <div class="pms-card-header">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-calendar-alt text-sm"></i></div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Recent Bookings</h3>
                            <p class="text-[10px] text-slate-400">Real-time occupancy booking tracker logs</p>
                        </div>
                    </div>
                    <a href="{{ route('reservations.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="pms-table">
                        <thead>
                            <tr>
                                <th>Guest</th>
                                <th>Room(s)</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentReservations as $res)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        @php
                                            $initials = strtoupper(substr($res->guest->name ?? 'G', 0, 1));
                                            // Dynamic soft gradients for profile avatars
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
                                            <span class="font-bold text-slate-800 text-sm block">{{ $res->guest->name ?? 'N/A' }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $res->guest->email ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-semibold text-slate-700 text-sm">
                                    {{ $res->rooms->pluck('room_number')->implode(', ') ?: 'N/A' }}
                                </td>
                                <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}</td>
                                <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}</td>
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                        {{ $s }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-slate-400 py-10">No reservations found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Side Column (Analytics & Operations Cards) --}}
        <div class="space-y-6">

            {{-- 7-Day Revenue Trend Chart --}}
            <div class="pms-card shadow-sm border border-slate-100/80">
                <div class="pms-card-header">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-chart-line text-sm"></i></div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Weekly Revenue Trend</h3>
                            <p class="text-[10px] text-slate-400">Checkout values logs over last 7 days</p>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div id="revenue-chart" x-init="
                        const chart = new ApexCharts($el, {
                            chart: {
                                type: 'area',
                                height: 180,
                                sparkline: { enabled: false },
                                toolbar: { show: false },
                                animations: { enabled: true }
                            },
                            series: [{
                                name: 'Revenue',
                                data: {{ json_encode(collect($revenueTrend)->pluck('revenue')) }}
                            }],
                            xaxis: {
                                categories: {{ json_encode(collect($revenueTrend)->pluck('day')) }},
                                labels: { style: { fontSize: '10px', colors: '#94a3b8', fontFamily: 'Inter, sans-serif', fontWeight: 500 } },
                                axisBorder: { show: false },
                                axisTicks: { show: false }
                            },
                            yaxis: {
                                labels: {
                                    show: true,
                                    formatter: (val) => '$' + val,
                                    style: { fontSize: '10px', colors: '#94a3b8', fontFamily: 'Inter, sans-serif' }
                                }
                            },
                            grid: { 
                                borderColor: '#f8fafc',
                                strokeDashArray: 4
                            },
                            stroke: { curve: 'smooth', width: 2.5, colors: ['#6366f1'] },
                            colors: ['#6366f1'],
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.35,
                                    opacityTo: 0.02,
                                    stops: [0, 100]
                                }
                            },
                            markers: {
                                size: 4,
                                colors: ['#ffffff'],
                                strokeColors: '#6366f1',
                                strokeWidth: 2,
                                hover: { size: 6 }
                            },
                            tooltip: {
                                theme: 'light',
                                x: { show: true },
                                y: { formatter: (val) => '$' + val.toFixed(2) }
                            }
                        });
                        chart.render();
                        $watch('sidebarOpen', () => {
                            setTimeout(() => {
                                chart.windowResizeHandler();
                            }, 200);
                        });
                    "></div>
                </div>
            </div>

            {{-- Sleek Occupancy Progress --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Live Occupancy</h3>
                        <p class="text-[10px] text-slate-400">Total bookings capacity share</p>
                    </div>
                    <span class="text-2xl font-black text-indigo-600 tracking-tight">{{ $occupancyPercent }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 mb-5 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full transition-all duration-1000 ease-out"
                         style="width: {{ $occupancyPercent }}%"></div>
                </div>
                <div class="grid grid-cols-3 gap-2.5 text-center">
                    <div class="bg-red-50/50 rounded-xl py-2.5 border border-red-100/30">
                        <p class="text-lg font-bold text-red-600 leading-none">{{ $occupiedRooms }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mt-1.5">Occupied</p>
                    </div>
                    <div class="bg-emerald-50/50 rounded-xl py-2.5 border border-emerald-100/30">
                        <p class="text-lg font-bold text-emerald-600 leading-none">{{ $availableRooms }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mt-1.5">Available</p>
                    </div>
                    <div class="bg-blue-50/50 rounded-xl py-2.5 border border-blue-100/30">
                        <p class="text-lg font-bold text-blue-600 leading-none">{{ $reservedRooms }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide mt-1.5">Reserved</p>
                    </div>
                </div>
            </div>

            {{-- Operational Alerts banner (Housekeeping) --}}
            @if($housekeepingPending > 0)
                <div class="rounded-xl p-4 bg-amber-50 border border-amber-200 text-amber-800 space-y-2.5 shadow-sm">
                    <p class="font-bold flex items-center gap-2 text-xs">
                        <i class="fas fa-exclamation-triangle text-amber-500 animate-bounce"></i> 
                        Housekeeping Attention Required:
                    </p>
                    <p class="text-[11px] text-amber-700 font-medium">
                        Currently, there are <strong>{{ $housekeepingPending }}</strong> rooms flagged as pending cleaning, inspecting, or under maintenance. Check logs immediately.
                    </p>
                    <a href="{{ route('housekeeping.index') }}" class="text-[11px] font-bold text-amber-800 hover:text-amber-950 flex items-center gap-1 w-max">
                        Go to Housekeeping panel &rarr;
                    </a>
                </div>
            @endif

            {{-- Operations Panel (Quick Actions) --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-5">
                <h3 class="text-sm font-bold text-slate-800 mb-4">Operations & Actions</h3>
                <div class="grid grid-cols-2 gap-2.5">
                    <a href="{{ route('checkin.index') }}" class="flex flex-col items-center gap-2 p-3 bg-indigo-50/20 hover:bg-indigo-50 border border-indigo-100/30 rounded-xl transition-all group">
                        <div class="w-9 h-9 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform"><i class="fas fa-sign-in-alt"></i></div>
                        <span class="text-[11px] font-bold text-slate-700">Check-In</span>
                    </a>
                    <a href="{{ route('checkout.index') }}" class="flex flex-col items-center gap-2 p-3 bg-orange-50/20 hover:bg-orange-50 border border-orange-100/30 rounded-xl transition-all group">
                        <div class="w-9 h-9 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform"><i class="fas fa-sign-out-alt"></i></div>
                        <span class="text-[11px] font-bold text-slate-700">Check-Out</span>
                    </a>
                    <a href="{{ route('reports.daily') }}" class="flex flex-col items-center gap-2 p-3 bg-blue-50/20 hover:bg-blue-50 border border-blue-100/30 rounded-xl transition-all group">
                        <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform"><i class="fas fa-chart-bar"></i></div>
                        <span class="text-[11px] font-bold text-slate-700">Daily Report</span>
                    </a>
                    <a href="{{ route('calendar') }}" class="flex flex-col items-center gap-2 p-3 bg-emerald-50/20 hover:bg-emerald-50 border border-emerald-100/30 rounded-xl transition-all group">
                        <div class="w-9 h-9 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform"><i class="fas fa-calendar-alt"></i></div>
                        <span class="text-[11px] font-bold text-slate-700">Calendar Overview</span>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
