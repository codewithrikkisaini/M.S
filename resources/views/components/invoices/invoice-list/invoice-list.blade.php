<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Invoices</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage and track guest billing statements, tax receipts, and payment logs</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-indigo-50 text-indigo-600 border border-indigo-100"><i class="fas fa-dollar-sign text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">${{ number_format($totalAmount, 2) }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Total Billing Value</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-emerald-50 text-emerald-600 border border-emerald-100"><i class="fas fa-check-circle text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $paidCount }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Cleared Invoices</p>
            </div>
        </div>
    </div>

    {{-- Invoices Table Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-file-invoice-dollar text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Billing Log</h3>
                    <p class="text-[10px] text-slate-400">Search and filter guest checkout invoice logs</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search by guest..."
                           class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                </div>
                <select wire:model.live="statusFilter" class="pms-select text-xs py-1.5 rounded-lg border border-slate-200 w-40">
                    <option value="">All Status</option>
                    <option>Paid</option>
                    <option>Pending</option>
                    <option>Cancelled</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Invoice #</th>
                        <th class="font-bold">Guest</th>
                        <th class="font-bold">Room</th>
                        <th class="font-bold">Billing Total</th>
                        <th class="font-bold">Status</th>
                        <th class="font-bold">Date Issued</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                    @php $res = optional(optional($inv->checkout)->reservation); @endphp
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 shadow-sm">#{{ $inv->id }}</span>
                        </td>
                        <td>
                            <span class="font-bold text-slate-800 text-sm block leading-none">{{ optional($res->guest)->name ?? '—' }}</span>
                            <span class="text-[10px] text-slate-400 block mt-1">{{ optional($res->guest)->email ?? '' }}</span>
                        </td>
                        <td>
                            <span class="font-black text-slate-800 text-sm bg-slate-50 px-2 py-0.5 rounded border border-slate-150 shadow-sm">{{ $res->rooms ? ($res->rooms->pluck('room_number')->implode(', ') ?: '—') : '—' }}</span>
                        </td>
                        <td>
                            <span class="font-black text-slate-800 text-sm">${{ number_format(optional($inv->checkout)->total_amount, 2) }}</span>
                        </td>
                        <td>
                            @php
                                $status = $inv->status ?? 'Paid';
                                $badgeClass = match($status) {
                                    'Paid' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Cancelled' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    default => 'bg-amber-50 text-amber-700 border-amber-100',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="text-slate-500 text-xs font-medium">{{ $inv->created_at->format('d M Y') }}</td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('invoice.view', $inv->id) }}" target="_blank"
                                   class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm" title="View">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('invoice.download', $inv->id) }}" target="_blank"
                                   class="btn-icon text-emerald-500 hover:bg-emerald-50 border border-slate-100 hover:border-emerald-100 shadow-sm" title="Download PDF">
                                    <i class="fas fa-file-pdf text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fas fa-file-invoice-dollar text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium">No invoices found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>