<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">SaaS Subscription Invoices</h1>
            <p class="text-sm text-slate-500 mt-0.5">View and manage subscription billing invoices and payments</p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="btn-primary rounded-lg shadow-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm flex items-center gap-1.5 cursor-pointer transition-colors">
                <i class="fas fa-plus text-xs"></i> Create Manual Invoice
            </button>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="pms-card shadow-sm border border-slate-100/80 bg-white">
        <div class="pms-card-header flex justify-between items-center flex-wrap gap-4 border-b border-slate-50 p-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                    <i class="fas fa-file-invoice-dollar text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">SaaS Subscription Invoice Records</h3>
                    <p class="text-[10px] text-slate-400">Total list of payments, dues, and transaction histories</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase border-b border-slate-100">
                        <th class="p-3">Invoice Number</th>
                        <th class="p-3">Hotel / Property</th>
                        <th class="p-3">Plan / Description</th>
                        <th class="p-3">Billing & Due Dates</th>
                        <th class="p-3">Amount</th>
                        <th class="p-3">Payment Info</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                        <td class="p-3 font-extrabold text-slate-700 text-sm">
                            #{{ $inv->invoice_number }}
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-slate-800 text-sm">{{ $inv->hotel?->name ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-400">Email: {{ $inv->hotel?->email ?? 'N/A' }}</div>
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-slate-800 text-sm">{{ $inv->plan?->name ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-405 capitalize">{{ $inv->plan?->billing_cycle ?? 'N/A' }} package</div>
                        </td>
                        <td class="p-3">
                            <div class="text-slate-600 text-xs font-semibold">Bill: {{ $inv->billing_date ? $inv->billing_date->format('d M Y') : 'N/A' }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Due: {{ $inv->due_date ? $inv->due_date->format('d M Y') : 'N/A' }}</div>
                        </td>
                        <td class="p-3 font-black text-slate-800 text-sm">
                            ${{ number_format($inv->amount, 2) }}
                        </td>
                        <td class="p-3">
                            @if($inv->status === 'paid')
                                <div class="text-slate-600 text-xs font-semibold">Method: {{ $inv->payment_method }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Paid: {{ $inv->paid_at ? $inv->paid_at->format('d M Y H:i') : 'N/A' }}</div>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-3">
                            @php
                                $statusColor = match($inv->status) {
                                    'paid'     => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'unpaid'   => 'bg-rose-50 text-rose-700 border-rose-100',
                                    'pending'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'refunded' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default    => 'bg-slate-50 text-slate-700 border-slate-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusColor }}">
                                {{ ucfirst($inv->status) }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex items-center justify-end gap-2.5">
                                @if($inv->status !== 'paid')
                                <button wire:click="markAsPaid({{ $inv->id }})" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold transition-colors cursor-pointer">
                                    <i class="fas fa-check-circle mr-1"></i> Mark Paid
                                </button>
                                @endif
                                <button onclick="confirm('Are you sure you want to delete this invoice record?') || event.stopImmediatePropagation()" 
                                        wire:click="deleteInvoice({{ $inv->id }})" 
                                        class="text-rose-650 hover:text-rose-850 text-xs font-bold transition-colors cursor-pointer">
                                    <i class="far fa-trash-alt mr-1"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-slate-400 py-10">No invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create Invoice Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-xl w-full border border-slate-100 animate-fadeIn duration-200">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-800">Create Subscription Invoice</h3>
                <button wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition-colors cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveInvoice" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Select Hotel / Property *</label>
                    <select wire:model="hotel_id" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="">-- Choose Hotel --</option>
                        @foreach($hotels as $h)
                        <option value="{{ $h->id }}">{{ $h->name }} ({{ $h->email }})</option>
                        @endforeach
                    </select>
                    @error('hotel_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Subscription Plan *</label>
                    <select wire:model.live="subscription_plan_id" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="">-- Choose Plan --</option>
                        @foreach($plans as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (${{ number_format($p->price, 2) }} / {{ $p->billing_cycle }})</option>
                        @endforeach
                    </select>
                    @error('subscription_plan_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Invoice Number *</label>
                    <input type="text" wire:model="invoice_number" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    @error('invoice_number') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Amount ($) *</label>
                    <input type="number" step="0.01" wire:model="amount" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    @error('amount') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Billing Date *</label>
                    <input type="date" wire:model="billing_date" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    @error('billing_date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Due Date *</label>
                    <input type="date" wire:model="due_date" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    @error('due_date') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Invoice Status *</label>
                    <select wire:model="status" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="unpaid">Unpaid</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="refunded">Refunded</option>
                    </select>
                    @error('status') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Payment Method *</label>
                    <select wire:model="payment_method" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="Manual">Manual / Bank Transfer</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Stripe">Stripe</option>
                        <option value="Free">Free</option>
                    </select>
                    @error('payment_method') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6 mt-6">
                    <button type="button" wire:click="closeCreateModal" class="btn-secondary rounded-lg px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-650 font-semibold text-sm cursor-pointer transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm cursor-pointer shadow-md transition-colors">
                        Create Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
