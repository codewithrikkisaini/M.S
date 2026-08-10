<x-app-layout>
    <x-slot name="title">Hotel Registration Approval & Subscriptions</x-slot>

    <div class="space-y-6" x-data="{ 
        paidModal: false, 
        extendModal: false, 
        viewModal: false, 
        selectedHotelId: null, 
        selectedHotelName: '', 
        selectedHotelCode: '',
        activeHotel: {}
    }">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Hotel Registrations & Subscriptions</h1>
                <p class="text-sm text-slate-500">Manage partner onboarding applications, review records, trial periods, paid subscriptions, and audit logs.</p>
            </div>
            
            @if($pendingCount > 0)
                <div class="flex items-center space-x-2 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-2 rounded-xl text-sm font-medium shadow-sm">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    <span>{{ $pendingCount }} Hotel(s) Pending Approval</span>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Filters & Search -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <form action="{{ route('superadmin.hotels.index') }}" method="GET" class="w-full md:w-auto flex flex-col md:flex-row items-center gap-3">
                <div class="relative w-full md:w-80">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID, Name, Tax ID, Email, City..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <select name="status" onchange="this.form.submit()" class="w-full md:w-48 px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="">All Account Statuses</option>
                    <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <button type="submit" class="w-full md:w-auto px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium transition shadow-sm">
                    Filter
                </button>
            </form>
        </div>

        <!-- Hotels Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-xs font-semibold">
                        <tr>
                            <th class="py-3.5 px-4">Hotel ID & Name</th>
                            <th class="py-3.5 px-4">Owner / Email</th>
                            <th class="py-3.5 px-4">Location</th>
                            <th class="py-3.5 px-4">Reg. Date</th>
                            <th class="py-3.5 px-4">Account Status</th>
                            <th class="py-3.5 px-4">Subscription</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($hotels as $hotel)
                            @php
                                $sub = $hotel->subscriptions->last();
                                $hotelJson = json_encode([
                                    'id' => $hotel->id,
                                    'name' => $hotel->name,
                                    'hotel_code' => $hotel->hotel_code ?: ('LDG-' . str_pad($hotel->id, 6, '0', STR_PAD_LEFT)),
                                    'business_name' => $hotel->business_name ?: 'N/A',
                                    'tax_id' => $hotel->tax_id ?: 'N/A',
                                    'company_reg_number' => $hotel->company_reg_number ?: 'N/A',
                                    'owner_name' => $hotel->owner_name ?: 'N/A',
                                    'email' => $hotel->email,
                                    'phone' => $hotel->phone ?: 'N/A',
                                    'whatsapp' => $hotel->whatsapp ?: 'N/A',
                                    'website' => $hotel->website ?: 'N/A',
                                    'city' => $hotel->city ?: 'N/A',
                                    'state' => $hotel->state ?: '',
                                    'country' => $hotel->country ?: 'N/A',
                                    'postal_code' => $hotel->postal_code ?: '',
                                    'address' => $hotel->address ?: 'N/A',
                                    'property_type' => $hotel->property_type ?: 'Hotel',
                                    'category' => $hotel->category ?: 'Standard',
                                    'rooms_count' => $hotel->rooms_count ?: 10,
                                    'current_pms' => $hotel->current_pms ?: 'None',
                                    'account_status' => $hotel->account_status ?: 'pending_approval',
                                    'created_at' => $hotel->created_at->format('M d, Y H:i')
                                ]);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-4">
                                    <button type="button" @click="activeHotel = {{ $hotelJson }}; viewModal = true;" class="text-left group">
                                        <div class="font-bold text-slate-900 group-hover:text-indigo-600 transition">{{ $hotel->name }}</div>
                                        <div class="font-mono text-xs text-indigo-600 font-semibold">{{ $hotel->hotel_code ?: ('LDG-' . str_pad($hotel->id, 6, '0', STR_PAD_LEFT)) }}</div>
                                    </button>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-medium text-slate-800">{{ $hotel->owner_name ?: 'N/A' }}</div>
                                    <div class="text-xs text-slate-500">{{ $hotel->email }}</div>
                                </td>
                                <td class="py-4 px-4 text-xs text-slate-600">
                                    {{ $hotel->city ?: 'N/A' }}, {{ $hotel->country ?: 'N/A' }}
                                </td>
                                <td class="py-4 px-4 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $hotel->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($hotel->account_status === 'pending_approval' || $hotel->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 shadow-sm whitespace-nowrap">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> ⏳ Pending Approval
                                        </span>
                                    @elseif($hotel->account_status === 'active' || $hotel->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm whitespace-nowrap">
                                            ✅ Active
                                        </span>
                                    @elseif($hotel->account_status === 'suspended')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 shadow-sm whitespace-nowrap">
                                            🚫 Suspended
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 shadow-sm whitespace-nowrap">
                                            {{ ucfirst($hotel->account_status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if($sub)
                                        <div class="font-medium text-xs text-slate-900">{{ $sub->status }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            @if($sub->trial_ends_at)
                                                Expires: {{ $sub->trial_ends_at->format('M d, Y') }}
                                            @elseif($sub->ends_at)
                                                Expires: {{ $sub->ends_at->format('M d, Y') }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">No Active Plan</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                    <button type="button" @click="activeHotel = {{ $hotelJson }}; viewModal = true;" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 text-xs font-bold transition shadow-sm" title="Quick View Full Record">
                                        <i class="fa-solid fa-eye mr-1.5 text-indigo-500"></i> Review
                                    </button>

                                    @if($hotel->account_status === 'pending_approval' || $hotel->status === 'pending')
                                        <form action="{{ route('superadmin.hotels.approve-7day', $hotel->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Approve 7-Day Trial for {{ addslashes($hotel->name) }}?')" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition shadow-sm">
                                                7D Trial
                                            </button>
                                        </form>

                                        <form action="{{ route('superadmin.hotels.approve-15day', $hotel->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Approve 15-Day Trial for {{ addslashes($hotel->name) }}?')" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition shadow-sm">
                                                15D Trial
                                            </button>
                                        </form>

                                        <button type="button" @click="paidModal = true; selectedHotelId = '{{ $hotel->id }}'; selectedHotelName = '{{ addslashes($hotel->name) }}'; selectedHotelCode = '{{ $hotel->hotel_code }}'" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition shadow-sm">
                                            Paid Sub
                                        </button>
                                    @else
                                        <button type="button" @click="extendModal = true; selectedHotelId = '{{ $hotel->id }}'; selectedHotelName = '{{ addslashes($hotel->name) }}'; selectedHotelCode = '{{ $hotel->hotel_code }}'" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold transition shadow-sm">
                                            Extend
                                        </button>

                                        @if($hotel->account_status === 'suspended')
                                            <form action="{{ route('superadmin.hotels.activate', $hotel->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition shadow-sm">
                                                    Activate
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('superadmin.hotels.suspend', $hotel->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Suspend hotel {{ addslashes($hotel->name) }}?')" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition shadow-sm">
                                                    Suspend
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    <form action="{{ route('superadmin.hotels.destroy', $hotel->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('⚠️ PERMANENT DELETE: Are you sure you want to delete hotel {{ addslashes($hotel->name) }}? This cannot be undone!')" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 text-xs font-semibold transition shadow-sm ml-1" title="Delete Hotel">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    No hotel records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $hotels->links() }}
            </div>
        </div>

        <!-- Full Hotel Record Inspection Modal -->
        <div x-show="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display: none;">
            <div class="bg-white rounded-3xl border border-slate-200 max-w-2xl w-full p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                
                <div class="flex justify-between items-center border-b border-slate-200 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-xs text-indigo-600 bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 rounded-full" x-text="activeHotel.hotel_code"></span>
                            <span class="text-xs text-slate-400" x-text="activeHotel.created_at"></span>
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-1" x-text="activeHotel.name"></h3>
                    </div>
                    <button @click="viewModal = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-base">&times;</button>
                </div>

                <!-- Business & Tax Details -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs space-y-4">
                    <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] border-b border-slate-200 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-building text-indigo-600"></i> Business & Tax Record
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-700">
                        <div>
                            <span class="text-slate-400 block font-medium">Hotel Trade Name:</span>
                            <strong class="text-slate-900 text-sm" x-text="activeHotel.name"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Legal Company Name:</span>
                            <strong class="text-slate-900" x-text="activeHotel.business_name"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Tax ID / GSTIN:</span>
                            <strong class="text-slate-900" x-text="activeHotel.tax_id"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Company Reg Number:</span>
                            <strong class="text-slate-900" x-text="activeHotel.company_reg_number"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Owner / Contact Person:</span>
                            <strong class="text-slate-900" x-text="activeHotel.owner_name"></strong>
                        </div>
                    </div>
                </div>

                <!-- Contact & Location Details -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs space-y-4">
                    <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] border-b border-slate-200 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-indigo-600"></i> Contact & Location
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-700">
                        <div>
                            <span class="text-slate-400 block font-medium">Email Address:</span>
                            <strong class="text-slate-900" x-text="activeHotel.email"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Phone Number:</span>
                            <strong class="text-slate-900" x-text="activeHotel.phone"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">WhatsApp:</span>
                            <strong class="text-slate-900" x-text="activeHotel.whatsapp"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Website:</span>
                            <strong class="text-slate-900" x-text="activeHotel.website"></strong>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 block font-medium">Full Address:</span>
                            <strong class="text-slate-900" x-text="[activeHotel.address, activeHotel.city, activeHotel.state, activeHotel.country, activeHotel.postal_code].filter(Boolean).join(', ')"></strong>
                        </div>
                    </div>
                </div>

                <!-- Property Specifications -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs space-y-4">
                    <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] border-b border-slate-200 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-hotel text-indigo-600"></i> Capacity & PMS Specs
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-slate-700">
                        <div>
                            <span class="text-slate-400 block font-medium">Property Type:</span>
                            <strong class="text-slate-900" x-text="activeHotel.property_type"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Star Category:</span>
                            <strong class="text-slate-900" x-text="activeHotel.category"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Total Rooms:</span>
                            <strong class="text-indigo-600 font-bold" x-text="activeHotel.rooms_count"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Current PMS:</span>
                            <strong class="text-slate-900" x-text="activeHotel.current_pms"></strong>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-200">
                    <a :href="'/superadmin/hotels/' + activeHotel.id" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-md flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-up-right-from-square text-indigo-400"></i> Open Full Audit Page
                    </a>
                    <button type="button" @click="viewModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Close Record
                    </button>
                </div>

            </div>
        </div>

        <!-- Paid Subscription Modal -->
        <div x-show="paidModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display: none;">
            <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-bold text-slate-800">Create Paid Subscription Invoice</h3>
                    <button @click="paidModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                
                <form :action="'/superadmin/hotels/' + selectedHotelId + '/approve-paid'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <p class="text-xs text-slate-500 mb-2">Hotel: <strong class="text-slate-800" x-text="selectedHotelName"></strong> (<span x-text="selectedHotelCode"></span>)</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Subscription Amount ($)</label>
                        <input type="number" step="0.01" name="amount" required value="89.00" class="w-full px-3 py-2 border rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Currency</label>
                            <select name="currency" class="w-full px-3 py-2 border rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="GBP">GBP (£)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Billing Cycle</label>
                            <select name="billing_cycle" class="w-full px-3 py-2 border rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annual">Annual</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" placeholder="Optional invoice notes..." class="w-full px-3 py-2 border rounded-xl text-sm focus:outline-none focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="paidModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Generate Invoice & Send Email</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Extend Trial Modal -->
        <div x-show="extendModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" style="display: none;">
            <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-bold text-slate-800">Extend Trial Duration</h3>
                    <button @click="extendModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>

                <form :action="'/superadmin/hotels/' + selectedHotelId + '/extend-trial'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <p class="text-xs text-slate-500 mb-2">Hotel: <strong class="text-slate-800" x-text="selectedHotelName"></strong></p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Extend By (Days)</label>
                        <input type="number" name="additional_days" required min="1" max="180" value="7" class="w-full px-3 py-2 border rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Audit Notes / Reason</label>
                        <input type="text" name="notes" placeholder="Reason for extension..." class="w-full px-3 py-2 border rounded-xl text-sm focus:outline-none focus:border-indigo-500">
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="extendModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-medium">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">Confirm Extension</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
