<div>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Audit & Activity Logs</h1>
        <p class="text-sm text-slate-500 mt-0.5">Chronological record of system changes, user operations, and API synchronizations</p>
    </div>

    {{-- Filters Bar --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex-1 flex gap-3">
            <div class="relative flex-1 max-w-md">
                <input type="text" wire:model.live="search" class="w-full text-slate-800 border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="Search logs by description, IP address...">
                <div class="absolute left-3 top-2.5 text-slate-400"><i class="fas fa-search text-xs"></i></div>
            </div>

            <select wire:model.live="actionFilter" class="text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                <option value="">All Actions</option>
                @foreach($actions as $act)
                <option value="{{ $act }}">{{ $act }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                <i class="fas fa-shield-alt"></i> Tamper-proof
            </span>
        </div>
    </div>

    {{-- Audit Logs Table --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th class="w-48">Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Log Description</th>
                        <th class="w-32">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logsList as $l)
                    <tr>
                        <td class="text-slate-500 text-xs font-medium">{{ $l->created_at->format('d M Y, H:i:s') }}</td>
                        <td>
                            @if($l->user)
                            <div class="font-bold text-slate-850 text-xs">{{ $l->user->name }}</div>
                            <div class="text-[9px] text-slate-400">ID: #{{ $l->user->id }}</div>
                            @else
                            <div class="font-bold text-slate-450 text-xs">System Engine</div>
                            <div class="text-[9px] text-slate-400">Webhook/Public</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeColor = match($l->action) {
                                    'Check-In', 'Check-Out' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Stripe Checkout', 'Stripe Gateway' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                    'Channel Sync', 'Channel Status' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeColor }}">
                                {{ $l->action }}
                            </span>
                        </td>
                        <td class="text-slate-700 text-xs font-medium">{{ $l->description }}</td>
                        <td class="font-mono text-[10px] text-slate-500">{{ $l->ip_address ?? '127.0.0.1' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-slate-455 py-10">No audit logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
