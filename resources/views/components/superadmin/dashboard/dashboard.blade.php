<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Super Admin Dashboard</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ now()->format('l, d F Y') }} &mdash; Multi-tenant management panel</p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('superadmin.hotels.index') }}" class="btn-primary btn-sm rounded-lg shadow-sm">
                <i class="fas fa-hotel text-xs mr-1"></i> Manage Hotels
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-slate-50 text-slate-500 border border-slate-100"><i class="fas fa-building text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $totalHotels }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Total Hotels</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-emerald-50 text-emerald-600 border border-emerald-100"><i class="fas fa-check-circle text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $approvedHotels }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Approved</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-amber-50 text-amber-600 border border-amber-100"><i class="fas fa-clock text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $pendingHotels }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Pending Approval</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-rose-50 text-rose-600 border border-rose-100"><i class="fas fa-times-circle text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $rejectedHotels }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Rejected</p>
            </div>
        </div>
    </div>

    {{-- Main Body Grid --}}
    <div class="grid grid-cols-1 gap-6">
        {{-- Recent Hotel Registrations --}}
        <div class="pms-card shadow-sm border border-slate-100/80">
            <div class="pms-card-header flex-wrap gap-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-history text-sm"></i></div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Recent Registrations & Approval Queue</h3>
                        <p class="text-[10px] text-slate-400">Onboarding pipeline for new tenant hotels</p>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="pms-table">
                    <thead>
                        <tr>
                            <th>Hotel Name</th>
                            <th>Contact Info</th>
                            <th>Address</th>
                            <th>Registered At</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentHotels as $h)
                        <tr>
                            <td>
                                <div class="font-bold text-slate-850 text-sm">{{ $h->name }}</div>
                                <div class="text-[10px] text-slate-400">ID: {{ $h->id }}</div>
                            </td>
                            <td>
                                <div class="text-slate-650 text-xs font-semibold"><i class="far fa-envelope mr-1 text-slate-400"></i>{{ $h->email }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5"><i class="fas fa-phone mr-1 text-slate-400"></i>{{ $h->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="text-slate-600 text-xs font-medium">{{ $h->address ?? 'N/A' }}</td>
                            <td class="text-slate-500 text-xs font-medium">{{ $h->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                @php
                                    $statusColor = match($h->status) {
                                        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'pending'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                                        default    => 'bg-slate-50 text-slate-700 border-slate-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusColor }}">
                                    {{ ucfirst($h->status) }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($h->status === 'pending')
                                        <button wire:click="approveHotel({{ $h->id }})" class="btn-success btn-xs px-2.5 py-1 rounded-md text-[10px] font-bold text-white shadow-sm bg-emerald-600 hover:bg-emerald-700 transition-colors">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                        <button wire:click="rejectHotel({{ $h->id }})" class="btn-danger btn-xs px-2.5 py-1 rounded-md text-[10px] font-bold text-white shadow-sm bg-rose-600 hover:bg-rose-700 transition-colors">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </button>
                                    @elseif($h->status === 'approved')
                                        <button wire:click="rejectHotel({{ $h->id }})" class="text-rose-600 hover:text-rose-800 text-[11px] font-semibold transition-colors">
                                            <i class="fas fa-ban mr-1"></i> Suspend
                                        </button>
                                    @elseif($h->status === 'rejected')
                                        <button wire:click="approveHotel({{ $h->id }})" class="text-emerald-600 hover:text-emerald-800 text-[11px] font-semibold transition-colors">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate-400 py-10">No hotels registered yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
