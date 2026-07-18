<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Super Admin Dashboard</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ now()->format('l, d F Y') }} &mdash; Multi-tenant SaaS Management Panel</p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('superadmin.hotels.index') }}" class="btn-primary rounded-lg shadow-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm flex items-center gap-1.5 cursor-pointer transition-colors duration-200">
                <i class="fas fa-hotel text-xs"></i> Manage Hotels
            </a>
        </div>
    </div>

    {{-- SECTION 1: Hotels Status Summary --}}
    <div class="mb-8">
        <h3 class="text-xs font-extrabold text-indigo-650 uppercase tracking-widest mb-4 flex items-center gap-2">
            <i class="fas fa-building text-xs"></i> Hotel Tenant Statuses
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl hover:shadow-md transition-all duration-200">
                <div class="stat-icon bg-slate-50 text-slate-500 border border-slate-100 p-2.5 rounded-lg w-10 h-10 flex items-center justify-center mb-3"><i class="fas fa-hotel text-sm"></i></div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $totalHotels }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Total Hotels</p>
                </div>
            </div>
            <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl hover:shadow-md transition-all duration-200">
                <div class="stat-icon bg-emerald-50 text-emerald-600 border border-emerald-100 p-2.5 rounded-lg w-10 h-10 flex items-center justify-center mb-3"><i class="fas fa-check-circle text-sm"></i></div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $approvedHotels }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Active Hotels</p>
                </div>
            </div>
            <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl hover:shadow-md transition-all duration-200">
                <div class="stat-icon bg-amber-50 text-amber-600 border border-amber-100 p-2.5 rounded-lg w-10 h-10 flex items-center justify-center mb-3"><i class="fas fa-clock text-sm"></i></div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $pendingHotels }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Pending Approval</p>
                </div>
            </div>
            <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl hover:shadow-md transition-all duration-200">
                <div class="stat-icon bg-slate-100 text-slate-650 border border-slate-200 p-2.5 rounded-lg w-10 h-10 flex items-center justify-center mb-3"><i class="fas fa-ban text-sm"></i></div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $suspendedHotels }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Suspended</p>
                </div>
            </div>
            <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl hover:shadow-md transition-all duration-200">
                <div class="stat-icon bg-rose-50 text-rose-600 border border-rose-100 p-2.5 rounded-lg w-10 h-10 flex items-center justify-center mb-3"><i class="fas fa-times-circle text-sm"></i></div>
                <div>
                    <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $rejectedHotels }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Rejected</p>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: SaaS Revenue & Hotel statistics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Revenue Panel --}}
        <div>
            <h3 class="text-xs font-extrabold text-indigo-650 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-wallet text-xs"></i> SaaS Platform Revenue
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="stat-icon bg-indigo-50 text-indigo-600 border border-indigo-100 p-2.5 rounded-lg w-10 h-10 flex items-center justify-center mb-3"><i class="fas fa-chart-line text-sm"></i></div>
                    <div>
                        <p class="text-xl font-extrabold text-slate-800 tracking-tight">${{ number_format($totalRevenue, 2) }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Total Revenue</p>
                    </div>
                </div>
                <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="stat-icon bg-indigo-50 text-indigo-600 border border-indigo-100 p-2.5 rounded-lg w-10 h-10 flex items-center justify-center mb-3"><i class="fas fa-calendar-check text-sm"></i></div>
                    <div>
                        <p class="text-xl font-extrabold text-slate-800 tracking-tight">${{ number_format($monthlyRevenue, 2) }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Monthly Revenue</p>
                    </div>
                </div>
                <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="stat-icon bg-indigo-50 text-indigo-600 border border-indigo-100 p-2.5 rounded-lg w-10 h-10 flex items-center justify-center mb-3"><i class="fas fa-receipt text-sm"></i></div>
                    <div>
                        <p class="text-xl font-extrabold text-slate-800 tracking-tight">${{ number_format($subscriptionRevenue, 2) }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Sub Revenue</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hotel Capacity Stats --}}
        <div>
            <h3 class="text-xs font-extrabold text-indigo-650 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-xs"></i> Global Hotel Capacity Statistics
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="stat-icon bg-blue-50 text-blue-600 border border-blue-100 p-2 rounded-lg w-8 h-8 flex items-center justify-center mb-3"><i class="fas fa-bed text-xs"></i></div>
                    <div>
                        <p class="text-lg font-extrabold text-slate-800 tracking-tight">{{ $totalRooms }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Total Rooms</p>
                    </div>
                </div>
                <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="stat-icon bg-red-50 text-red-600 border border-red-100 p-2 rounded-lg w-8 h-8 flex items-center justify-center mb-3"><i class="fas fa-door-closed text-xs"></i></div>
                    <div>
                        <p class="text-lg font-extrabold text-slate-800 tracking-tight">{{ $occupiedRooms }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Occupied Rooms</p>
                    </div>
                </div>
                <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="stat-icon bg-emerald-50 text-emerald-600 border border-emerald-100 p-2 rounded-lg w-8 h-8 flex items-center justify-center mb-3"><i class="fas fa-door-open text-xs"></i></div>
                    <div>
                        <p class="text-lg font-extrabold text-slate-800 tracking-tight">{{ $vacantRooms }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Vacant Rooms</p>
                    </div>
                </div>
                <div class="stat-card border border-slate-100 bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="stat-icon bg-purple-50 text-purple-650 border border-purple-100 p-2 rounded-lg w-8 h-8 flex items-center justify-center mb-3"><i class="fas fa-calendar-check text-xs"></i></div>
                    <div>
                        <p class="text-lg font-extrabold text-slate-800 tracking-tight">{{ $totalReservations }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Reservations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: Recent Activity Lists --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        {{-- Activity 1: New Hotel Registrations --}}
        <div class="pms-card shadow-sm border border-slate-100 bg-white rounded-xl">
            <div class="pms-card-header p-4 border-b border-slate-100 flex items-center gap-2">
                <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center"><i class="fas fa-user-plus text-xs"></i></div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800">Onboarding Queue</h3>
                    <p class="text-[9px] text-slate-400">Latest tenant hotel signups</p>
                </div>
            </div>
            <div class="p-4 space-y-3.5">
                @forelse($recentHotels as $h)
                <div class="flex items-center justify-between border-b border-slate-50 pb-3 last:border-b-0 last:pb-0">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ $h->name }}</p>
                        <p class="text-[9px] text-slate-450 truncate mt-0.5"><i class="far fa-envelope mr-1 text-slate-400"></i>{{ $h->email }}</p>
                    </div>
                    <div>
                        @if($h->status === 'pending')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                        @elseif($h->status === 'approved')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Approved</span>
                        @elseif($h->status === 'suspended')
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-650 border border-slate-200">Suspended</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-100">Rejected</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-center text-slate-400 text-xs py-6">No registrations found.</p>
                @endforelse
            </div>
        </div>

        {{-- Activity 2: Recent Tenant Bookings --}}
        <div class="pms-card shadow-sm border border-slate-100 bg-white rounded-xl">
            <div class="pms-card-header p-4 border-b border-slate-100 flex items-center gap-2">
                <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center"><i class="fas fa-calendar-check text-xs"></i></div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800">Recent Bookings</h3>
                    <p class="text-[9px] text-slate-400">Latest reservation logs across hotels</p>
                </div>
            </div>
            <div class="p-4 space-y-3.5">
                @forelse($recentBookings as $b)
                <div class="flex items-start justify-between border-b border-slate-50 pb-3 last:border-b-0 last:pb-0">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ $b->guest_name ?? 'Walk-In Guest' }}</p>
                        <p class="text-[9px] text-slate-450 truncate mt-0.5"><i class="fas fa-hotel mr-1 text-slate-400"></i>{{ $b->hotel?->name ?? 'N/A' }}</p>
                        <p class="text-[9px] text-slate-400 mt-0.5">Stay: {{ $b->check_in_date }} &rarr; {{ $b->check_out_date }}</p>
                    </div>
                    <div class="text-right whitespace-nowrap">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $b->status }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-center text-slate-400 text-xs py-6">No bookings recorded yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Activity 3: Recent Payments --}}
        <div class="pms-card shadow-sm border border-slate-100 bg-white rounded-xl">
            <div class="pms-card-header p-4 border-b border-slate-100 flex items-center gap-2">
                <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center"><i class="fas fa-credit-card text-xs"></i></div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800">Recent Payments</h3>
                    <p class="text-[9px] text-slate-400">Payment checkouts across hotels</p>
                </div>
            </div>
            <div class="p-4 space-y-3.5">
                @forelse($recentPayments as $p)
                <div class="flex items-center justify-between border-b border-slate-50 pb-3 last:border-b-0 last:pb-0">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate">${{ number_format($p->amount, 2) }}</p>
                        <p class="text-[9px] text-slate-450 truncate mt-0.5"><i class="fas fa-hotel mr-1 text-slate-400"></i>{{ $p->hotel?->name ?? 'N/A' }}</p>
                        <p class="text-[9px] text-slate-400 mt-0.5">Method: {{ $p->payment_type }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-500">{{ $p->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-slate-400 text-xs py-6">No payments recorded yet.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
