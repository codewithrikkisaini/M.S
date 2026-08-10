<x-app-layout>
    <x-slot name="title">Review Hotel: {{ $hotel->name }}</x-slot>

    <div class="space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('superadmin.hotels.index') }}" class="text-slate-400 hover:text-slate-600 text-sm">
                        <i class="fa-solid fa-arrow-left"></i> Back to Hotels
                    </a>
                    <span class="text-slate-300">|</span>
                    <span class="font-mono text-xs text-indigo-600 bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 rounded-full font-bold">
                        {{ $hotel->hotel_code ?: ('LDG-' . str_pad($hotel->id, 6, '0', STR_PAD_LEFT)) }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">{{ $hotel->name }}</h1>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                @if($hotel->account_status === 'pending_approval' || $hotel->status === 'pending')
                    <form action="{{ route('superadmin.hotels.approve-7day', $hotel->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Approve 7-Day Trial?')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                            Approve 7-Day Trial
                        </button>
                    </form>
                    <form action="{{ route('superadmin.hotels.approve-15day', $hotel->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Approve 15-Day Trial?')" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                            Approve 15-Day Trial
                        </button>
                    </form>
                @endif

                @if($hotel->account_status === 'suspended')
                    <form action="{{ route('superadmin.hotels.activate', $hotel->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                            Activate Hotel
                        </button>
                    </form>
                @else
                    <form action="{{ route('superadmin.hotels.suspend', $hotel->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Suspend hotel?')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                            Suspend Hotel
                        </button>
                    </form>
                @endif

                <form action="{{ route('superadmin.hotels.destroy', $hotel->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('⚠️ PERMANENT DELETE: Are you sure you want to delete {{ addslashes($hotel->name) }}? This action cannot be undone!')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                        <i class="fa-solid fa-trash-can"></i> Delete Hotel
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Column 1 & 2: Details & Invoices -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Hotel & Owner Profile Card -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 border-b pb-3 text-base">Hotel & Owner Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-slate-400 block text-xs">Hotel Name</span>
                            <span class="font-medium text-slate-800">{{ $hotel->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-xs">Hotel ID</span>
                            <span class="font-mono font-bold text-indigo-600">{{ $hotel->hotel_code }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-xs">Owner / Contact</span>
                            <span class="font-medium text-slate-800">{{ $hotel->owner_name ?: 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-xs">Email</span>
                            <span class="font-medium text-slate-800">{{ $hotel->email }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-xs">Phone</span>
                            <span class="font-medium text-slate-800">{{ $hotel->phone }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-xs">Property Type & Rooms</span>
                            <span class="font-medium text-slate-800">{{ $hotel->property_type ?: 'Hotel' }} ({{ $hotel->rooms_count ?: 0 }} Rooms)</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-xs">City & Country</span>
                            <span class="font-medium text-slate-800">{{ $hotel->city }}, {{ $hotel->country }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-xs">Registration Date</span>
                            <span class="font-medium text-slate-800">{{ $hotel->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Subscription Invoices History -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 border-b pb-3 text-base">Subscription Invoices & Payment History</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-50 text-slate-500 uppercase font-semibold">
                                <tr>
                                    <th class="py-2.5 px-3">Invoice #</th>
                                    <th class="py-2.5 px-3">Amount</th>
                                    <th class="py-2.5 px-3">Status</th>
                                    <th class="py-2.5 px-3">PayPal Order / Txn ID</th>
                                    <th class="py-2.5 px-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($hotel->subscriptionInvoices as $inv)
                                    <tr>
                                        <td class="py-3 px-3 font-mono font-bold text-indigo-600">{{ $inv->invoice_number }}</td>
                                        <td class="py-3 px-3 font-semibold">{{ $inv->currency }} {{ number_format($inv->amount, 2) }}</td>
                                        <td class="py-3 px-3">
                                            @if($inv->status === 'paid' || $inv->payment_status === 'paid')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">PAID</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">PENDING</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 font-mono text-[11px] text-slate-500">
                                            {{ $inv->paypal_transaction_id ?: ($inv->paypal_order_id ?: 'N/A') }}
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            @if($inv->status !== 'paid')
                                                <form action="{{ route('superadmin.hotels.resend-invoice', ['hotel' => $hotel->id, 'invoice' => $inv->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-medium">
                                                        Resend Link
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-slate-400">No subscription invoices generated yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Column 3: Audit Trail -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-slate-800 border-b pb-3 text-base">Audit Trail Log</h3>

                    <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                        @forelse($auditLogs as $log)
                            <div class="border-l-2 border-indigo-500 pl-3 py-1 space-y-1">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-bold text-slate-800">{{ $log->action }}</span>
                                    <span class="text-slate-400 text-[10px]">{{ $log->created_at->format('M d, H:i') }}</span>
                                </div>
                                <p class="text-xs text-slate-600">{{ $log->description }}</p>
                                @if($log->notes)
                                    <p class="text-[11px] text-slate-500 italic">Notes: {{ $log->notes }}</p>
                                @endif
                                <div class="text-[10px] text-slate-400">By: {{ $log->admin_name ?: 'System' }} ({{ $log->ip_address }})</div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">No audit logs recorded for this hotel yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
