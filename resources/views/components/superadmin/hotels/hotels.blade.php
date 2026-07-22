<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manage Hotels</h1>
            <p class="text-sm text-slate-500 mt-0.5">SaaS multi-tenant onboarding, subscriptions and tenant administration</p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="btn-primary rounded-lg shadow-md px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm flex items-center gap-1.5 cursor-pointer transition-all duration-200">
                <i class="fas fa-plus text-xs"></i> New Hotel Wizard
            </button>
        </div>
    </div>

    {{-- Hotel List Table --}}
    <div class="pms-card shadow-sm border border-slate-100/80 bg-white rounded-xl">
        <div class="pms-card-header flex-wrap gap-4 p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-list text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">All Registered Tenants</h3>
                    <p class="text-[10px] text-slate-400">Total list of active, pending, and suspended hotel systems</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-450 text-[10px] font-extrabold uppercase tracking-wider">
                        <th class="p-4 pl-6">Hotel ID</th>
                        <th class="p-4">Hotel Details</th>
                        <th class="p-4">Location</th>
                        <th class="p-4">Contact Info</th>
                        <th class="p-4">Rooms</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 pr-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($hotelsList as $h)
                    <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                        <td class="p-4 pl-6 font-extrabold text-slate-700">#{{ $h->id }}</td>
                        <td class="p-4">
                            <div class="font-bold text-slate-800 text-sm">{{ $h->name }}</div>
                            @if($h->business_name)
                                <div class="text-[10px] text-slate-500">Biz: {{ $h->business_name }}</div>
                            @endif
                            <div class="text-[9px] text-slate-400 mt-0.5">Created: {{ $h->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="p-4 text-slate-600 font-medium">
                            <div>{{ $h->city ?? 'N/A' }}, {{ $h->country ?? 'N/A' }}</div>
                            <div class="text-[9px] text-slate-400">{{ $h->timezone }} | {{ $h->currency }}</div>
                        </td>
                        <td class="p-4">
                            <div class="text-slate-650 font-semibold flex items-center gap-1.5"><i class="far fa-envelope text-slate-400"></i>{{ $h->email }}</div>
                            <div class="text-[10px] text-slate-450 mt-1 flex items-center gap-1.5"><i class="fas fa-phone text-slate-400"></i>{{ $h->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="p-4 text-slate-600 font-bold text-sm">{{ $h->rooms_count ?? 'N/A' }} Rooms</td>
                        <td class="p-4">
                            @php
                                $statusColor = match($h->status) {
                                    'approved'  => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'pending'   => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'rejected'  => 'bg-rose-50 text-rose-700 border-rose-100',
                                    'suspended' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    default     => 'bg-slate-50 text-slate-700 border-slate-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusColor }}">
                                {{ ucfirst($h->status) }}
                            </span>
                        </td>
                        <td class="p-4 pr-6 text-right whitespace-nowrap">
                            <button wire:click="openViewModal({{ $h->id }})" 
                                    class="text-sky-600 hover:text-sky-800 font-bold mr-3 cursor-pointer">
                                <i class="far fa-eye mr-0.5"></i> View
                            </button>

                            @if($h->status === 'pending')
                                <button onclick="confirm('Approve this registration request?') || event.stopImmediatePropagation()"
                                        wire:click="approveHotel({{ $h->id }})" 
                                        class="text-emerald-600 hover:text-emerald-800 font-bold mr-3 cursor-pointer">
                                    <i class="fas fa-check mr-0.5"></i> Approve
                                </button>
                                <button onclick="confirm('Reject this registration request?') || event.stopImmediatePropagation()"
                                        wire:click="rejectHotel({{ $h->id }})" 
                                        class="text-rose-500 hover:text-rose-700 font-bold mr-3 cursor-pointer">
                                    <i class="fas fa-times mr-0.5"></i> Reject
                                </button>
                            @endif

                            @if($h->status === 'approved')
                                <button wire:click="loginAsHotelAdmin({{ $h->id }})" 
                                        class="text-indigo-600 hover:text-indigo-800 font-bold mr-3 cursor-pointer">
                                    <i class="fas fa-sign-in-alt mr-0.5"></i> Impersonate
                                </button>
                                <button onclick="confirm('Suspend this hotel tenant? This will disable logins.') || event.stopImmediatePropagation()"
                                        wire:click="suspendHotel({{ $h->id }})" 
                                        class="text-amber-600 hover:text-amber-800 font-bold mr-3 cursor-pointer">
                                    <i class="fas fa-ban mr-0.5"></i> Suspend
                                </button>
                            @elseif($h->status === 'suspended')
                                <button onclick="confirm('Unsuspend and activate this hotel tenant?') || event.stopImmediatePropagation()"
                                        wire:click="unsuspendHotel({{ $h->id }})" 
                                        class="text-emerald-600 hover:text-emerald-800 font-bold mr-3 cursor-pointer">
                                    <i class="fas fa-play mr-0.5"></i> Activate
                                </button>
                            @endif

                            <button onclick="confirm('Are you sure you want to completely delete this hotel and all associated data?') || event.stopImmediatePropagation()" 
                                    wire:click="deleteHotel({{ $h->id }})" 
                                    class="text-rose-600 hover:text-rose-800 font-bold cursor-pointer">
                                <i class="far fa-trash-alt mr-0.5"></i> Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-slate-400 py-10">No hotels found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create Hotel Modal (6-Step Wizard) --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full border border-slate-100 animate-fadeIn duration-200">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Add New Hotel Tenant</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Step-by-step setup and provisioning wizard</p>
                </div>
                <button wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-650 p-1.5 rounded-lg transition-colors cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Wizard Step Indicator --}}
            <div class="px-8 pt-6 pb-2 border-b border-slate-100 bg-white">
                <div class="flex items-center justify-between relative">
                    <!-- Connecting Lines -->
                    <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-0.5 bg-slate-100 z-0"></div>
                    <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-0.5 bg-indigo-600 transition-all duration-300 z-0" style="width: {{ (($currentStep - 1) / 5) * 100 }}%"></div>
                    
                    <!-- Step Nodes -->
                    @for($i = 1; $i <= 6; $i++)
                        @php
                            $stepLabel = match($i) {
                                1 => 'Business',
                                2 => 'Contact',
                                3 => 'Location',
                                4 => 'Property',
                                5 => 'Admin',
                                6 => 'Plan'
                            };
                            $stepIcon = match($i) {
                                1 => 'fa-briefcase',
                                2 => 'fa-address-book',
                                3 => 'fa-map-marker-alt',
                                4 => 'fa-hotel',
                                5 => 'fa-user-shield',
                                6 => 'fa-receipt'
                            };
                            $isCompleted = $currentStep > $i;
                            $isActive = $currentStep === $i;
                        @endphp
                        <div class="flex flex-col items-center z-10">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center border-2 transition-all duration-300 {{ $isCompleted ? 'bg-indigo-650 border-indigo-650 text-white' : ($isActive ? 'bg-white border-indigo-600 text-indigo-600 shadow-md shadow-indigo-600/10' : 'bg-white border-slate-200 text-slate-400') }}">
                                @if($isCompleted)
                                    <i class="fas fa-check text-xs"></i>
                                @else
                                    <i class="fas {{ $stepIcon }} text-xs"></i>
                                @endif
                            </div>
                            <span class="text-[9px] font-bold mt-1.5 uppercase tracking-wider {{ $isActive ? 'text-indigo-600' : ($isCompleted ? 'text-slate-700' : 'text-slate-400') }}">{{ $stepLabel }}</span>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Wizard Forms --}}
            <div class="p-6">
                <!-- STEP 1: Business Information -->
                @if($currentStep === 1)
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider mb-2">Step 1: Business Info</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Hotel Name *</label>
                            <input type="text" wire:model="name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Grand Plaza Resort">
                            @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Business/Company Name</label>
                            <input type="text" wire:model="business_name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Grand Plaza LLC">
                            @error('business_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Owner Name</label>
                            <input type="text" wire:model="owner_name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Richard Hendricks">
                            @error('owner_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Tax ID / GSTIN</label>
                            <input type="text" wire:model="tax_id" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. TX-98765432">
                            @error('tax_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Company Registration Number</label>
                            <input type="text" wire:model="company_reg_number" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. CRN-12345">
                            @error('company_reg_number') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Business License Number</label>
                            <input type="text" wire:model="business_license_number" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. LIC-456789">
                            @error('business_license_number') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- STEP 2: Contact Information -->
                @if($currentStep === 2)
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider mb-2">Step 2: Contact Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Hotel Email Address *</label>
                            <input type="email" wire:model="email" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. info@grandplaza.com">
                            @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Hotel Phone Number</label>
                            <input type="text" wire:model="phone" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. +1 (555) 019-2834">
                            @error('phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">WhatsApp Business Number</label>
                            <input type="text" wire:model="whatsapp" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. +1 (555) 019-2835">
                            @error('whatsapp') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Website URL</label>
                            <input type="text" wire:model="website" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. https://www.grandplazahotel.com">
                            @error('website') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- STEP 3: Location -->
                @if($currentStep === 3)
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider mb-2">Step 3: Location Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Full Street Address *</label>
                            <input type="text" wire:model="address" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. 789 Ocean Drive, Suite 100">
                            @error('address') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Postal / ZIP Code</label>
                            <input type="text" wire:model="postal_code" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. 33139">
                            @error('postal_code') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">City</label>
                            <input type="text" wire:model="city" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Miami">
                            @error('city') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">State / Province</label>
                            <input type="text" wire:model="state" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Florida">
                            @error('state') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Country *</label>
                            <input type="text" wire:model="country" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. United States">
                            @error('country') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Timezone *</label>
                            <select wire:model="timezone" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                                <option value="UTC">UTC</option>
                                <option value="GMT">GMT</option>
                                <option value="Asia/Kolkata">IST (UTC+5:30)</option>
                                <option value="America/New_York">EST (UTC-5)</option>
                                <option value="Europe/London">GMT/BST</option>
                            </select>
                            @error('timezone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Base Currency *</label>
                            <select wire:model="currency" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                                <option value="INR">INR (₹)</option>
                                <option value="GBP">GBP (£)</option>
                                <option value="AED">AED</option>
                            </select>
                            @error('currency') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- STEP 4: Hotel details and property information -->
                @if($currentStep === 4)
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider mb-2">Step 4: Property Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Total Rooms *</label>
                            <input type="number" wire:model="rooms_count" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" min="1" max="500">
                            @error('rooms_count') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Hotel Category</label>
                            <select wire:model="category" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                                <option value="">Select Category</option>
                                <option value="5-star">5 Star Luxury</option>
                                <option value="4-star">4 Star Deluxe</option>
                                <option value="3-star">3 Star Standard</option>
                                <option value="boutique">Boutique Hotel</option>
                                <option value="budget">Budget / Hostels</option>
                            </select>
                            @error('category') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Property Type</label>
                            <input type="text" wire:model="property_type" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Resort / Villa / Hotel">
                            @error('property_type') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Current PMS (if any)</label>
                            <input type="text" wire:model="current_pms" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Cloudbeds / Opera">
                            @error('current_pms') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Current Channel Manager</label>
                            <input type="text" wire:model="current_channel_manager" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. SiteMinder">
                            @error('current_channel_manager') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Current Marketing Site</label>
                            <input type="text" wire:model="current_website" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. https://www.originalsite.com">
                            @error('current_website') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- STEP 5: Administrator -->
                @if($currentStep === 5)
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider mb-2">Step 5: Hotel Administrator User</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Administrator Full Name *</label>
                            <input type="text" wire:model="admin_name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. John Doe">
                            @error('admin_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Admin Email Username *</label>
                            <input type="email" wire:model="admin_email" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. admin@emerald.com">
                            @error('admin_email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Secure Password *</label>
                            <input type="password" wire:model="admin_password" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="Minimum 6 characters">
                            @error('admin_password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- STEP 6: Subscription Plan -->
                @if($currentStep === 6)
                <div class="space-y-4">
                    <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider mb-2">Step 6: Subscription Management</h4>
                    <p class="text-xs text-slate-400">Select the initial SaaS subscription plan for this tenant hotel.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <label class="border-2 border-slate-100 rounded-xl p-4 flex flex-col items-center justify-between text-center cursor-pointer transition-all duration-200 {{ $subscription_plan === 'trial' ? 'border-indigo-500 bg-indigo-50/20 shadow-md shadow-indigo-600/5' : 'hover:border-slate-200 hover:bg-slate-50' }}">
                            <input type="radio" wire:model="subscription_plan" value="trial" class="sr-only">
                            <i class="fas fa-hourglass-start text-indigo-500 text-lg mb-2"></i>
                            <span class="font-extrabold text-sm text-slate-800">Trial Plan</span>
                            <span class="text-[10px] text-slate-400 mt-0.5">14-Day Free Trial</span>
                            <span class="text-xs font-black text-indigo-600 mt-2">Free</span>
                        </label>

                        <label class="border-2 border-slate-100 rounded-xl p-4 flex flex-col items-center justify-between text-center cursor-pointer transition-all duration-200 {{ $subscription_plan === 'monthly' ? 'border-indigo-500 bg-indigo-50/20 shadow-md shadow-indigo-600/5' : 'hover:border-slate-200 hover:bg-slate-50' }}">
                            <input type="radio" wire:model="subscription_plan" value="monthly" class="sr-only">
                            <i class="fas fa-calendar-alt text-indigo-500 text-lg mb-2"></i>
                            <span class="font-extrabold text-sm text-slate-800">Monthly Pro</span>
                            <span class="text-[10px] text-slate-400 mt-0.5">Recurring Monthly</span>
                            <span class="text-xs font-black text-indigo-600 mt-2">$29 / mo</span>
                        </label>

                        <label class="border-2 border-slate-100 rounded-xl p-4 flex flex-col items-center justify-between text-center cursor-pointer transition-all duration-200 {{ $subscription_plan === 'yearly' ? 'border-indigo-500 bg-indigo-50/20 shadow-md shadow-indigo-600/5' : 'hover:border-slate-200 hover:bg-slate-50' }}">
                            <input type="radio" wire:model="subscription_plan" value="yearly" class="sr-only">
                            <i class="fas fa-gift text-indigo-500 text-lg mb-2"></i>
                            <span class="font-extrabold text-sm text-slate-800">Yearly Premium</span>
                            <span class="text-[10px] text-slate-400 mt-0.5">Recurring Yearly</span>
                            <span class="text-xs font-black text-indigo-600 mt-2">$249 / yr</span>
                        </label>

                        <label class="border-2 border-slate-100 rounded-xl p-4 flex flex-col items-center justify-between text-center cursor-pointer transition-all duration-200 {{ $subscription_plan === 'lifetime' ? 'border-indigo-500 bg-indigo-50/20 shadow-md shadow-indigo-600/5' : 'hover:border-slate-200 hover:bg-slate-50' }}">
                            <input type="radio" wire:model="subscription_plan" value="lifetime" class="sr-only">
                            <i class="fas fa-infinity text-indigo-500 text-lg mb-2"></i>
                            <span class="font-extrabold text-sm text-slate-800">Enterprise</span>
                            <span class="text-[10px] text-slate-400 mt-0.5">Lifetime Access</span>
                            <span class="text-xs font-black text-indigo-600 mt-2">$999 Once</span>
                        </label>
                    </div>
                    @error('subscription_plan') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between p-6 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl">
                <div>
                    @if($currentStep > 1)
                        <button type="button" wire:click="prevStep" class="btn-secondary rounded-lg px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-650 font-semibold text-sm cursor-pointer transition-all">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </button>
                    @else
                        <div></div>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" wire:click="closeCreateModal" class="btn-secondary rounded-lg px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-650 font-semibold text-sm cursor-pointer transition-all">
                        Cancel
                    </button>
                    
                    @if($currentStep < 6)
                        <button type="button" wire:click="nextStep" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm cursor-pointer shadow-md transition-all">
                            Next <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    @else
                        <button type="button" wire:click="saveHotel" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm cursor-pointer shadow-md transition-all">
                            Create Hotel & Provision <i class="fas fa-magic ml-1"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- View Hotel Details Modal --}}
    @if($showViewModal && $viewHotel)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-5xl w-full border border-slate-100 animate-fadeIn duration-200 overflow-hidden">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-slate-900 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-black text-lg shadow-lg">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h3 class="text-2xl font-black text-white tracking-tight">{{ $viewHotel->name }}</h3>
                            <span class="text-xs font-extrabold px-3 py-0.5 rounded-full uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                Tenant ID #{{ $viewHotel->id }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Complete registration application & multi-tenant onboarding record</p>
                    </div>
                </div>
                <button wire:click="closeViewModal" class="text-slate-400 hover:text-white p-2 rounded-lg transition-colors cursor-pointer">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-6 max-h-[78vh] overflow-y-auto bg-slate-50/60">
                @php
                    $viewHotelImage = $viewHotel->primary_image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80';
                @endphp
                <div class="rounded-3xl overflow-hidden border border-slate-200 shadow-sm">
                    <img src="{{ $viewHotelImage }}" alt="{{ $viewHotel->name }}" class="w-full h-60 object-cover">
                </div>
                {{-- Registration Summary Bar --}}
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Registration Status</span>
                        @php
                            $vStatusColor = match($viewHotel->status) {
                                'approved'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'pending'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                'rejected'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                'suspended' => 'bg-slate-100 text-slate-600 border-slate-200',
                                default     => 'bg-slate-50 text-slate-700 border-slate-200'
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black border {{ $vStatusColor }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                            {{ ucfirst($viewHotel->status) }}
                        </span>
                    </div>

                    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Registered Date</span>
                        <div class="text-xs font-black text-slate-800">
                            {{ $viewHotel->created_at ? $viewHotel->created_at->format('d M Y') : 'N/A' }}
                        </div>
                        <div class="text-[9px] text-slate-400">{{ $viewHotel->created_at ? $viewHotel->created_at->format('h:i A') : '' }}</div>
                    </div>

                    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Provisioned Rooms</span>
                        <div class="text-sm font-black text-slate-800 flex items-center gap-1.5">
                            <i class="fas fa-door-open text-indigo-600"></i>
                            {{ $viewHotel->rooms_count ?: $viewHotel->rooms->count() }} Rooms
                        </div>
                    </div>

                    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Staff Accounts</span>
                        <div class="text-sm font-black text-slate-800 flex items-center gap-1.5">
                            <i class="fas fa-users text-indigo-600"></i>
                            {{ $viewHotel->users ? $viewHotel->users->count() : 0 }} Users
                        </div>
                    </div>

                    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-sm col-span-2 sm:col-span-1">
                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Subscription Plan</span>
                        <div class="text-xs font-black text-indigo-600">
                            {{ $viewHotel->subscription && $viewHotel->subscription->plan ? $viewHotel->subscription->plan->name : 'Trial Plan' }}
                        </div>
                    </div>
                </div>

                {{-- Full 6-Step Registration Breakdown --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Step 1: Business Profile -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-briefcase"></i> 1. Business Information
                            </h4>
                            <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Step 1</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Hotel Trade Name</span>
                                <span class="font-extrabold text-slate-800">{{ $viewHotel->name }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Legal / Business Name</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->business_name ?: 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Proprietor / Owner Name</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->owner_name ?: 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Tax Identification / GSTIN</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->tax_id ?: 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Company Reg. Number</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->company_reg_number ?: 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-400 font-medium">Business License No.</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->business_license_number ?: 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Contact Details -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-address-book"></i> 2. Contact & Communication
                            </h4>
                            <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Step 2</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Registered Hotel Email</span>
                                <span class="font-extrabold text-indigo-600">{{ $viewHotel->email }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Primary Phone</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->phone ?: 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">WhatsApp Business</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->whatsapp ?: 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-400 font-medium">Official Website</span>
                                @if($viewHotel->website)
                                    <a href="{{ $viewHotel->website }}" target="_blank" class="font-bold text-indigo-600 hover:underline truncate max-w-[200px]">
                                        {{ $viewHotel->website }} <i class="fas fa-external-link-alt text-[9px] ml-0.5"></i>
                                    </a>
                                @else
                                    <span class="font-bold text-slate-400">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Location Details -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-map-marker-alt"></i> 3. Location & System Locale
                            </h4>
                            <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Step 3</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Full Street Address</span>
                                <span class="font-bold text-slate-800 text-right max-w-[220px]">{{ $viewHotel->address ?: 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">City / State</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->city ?? 'N/A' }}, {{ $viewHotel->state ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Country / ZIP</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->country ?: 'N/A' }} {{ $viewHotel->postal_code ? "({$viewHotel->postal_code})" : '' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Timezone</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->timezone }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-400 font-medium">Base Currency</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->currency }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Property Details -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-sliders"></i> 4. Property & Migration Info
                            </h4>
                            <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Step 4</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Property Type</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->property_type ?: 'Boutique Hotel' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Category / Star Rating</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->category ?: 'Standard' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Previous PMS System</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->current_pms ?: 'None' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Current Channel Manager</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->current_channel_manager ?: 'None' }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-400 font-medium">Existing Marketing Site</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->current_website ?: 'None' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Administrator & Users Section -->
                <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-user-shield"></i> 5. Administrator & Registered Users
                        </h4>
                        <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Step 5</span>
                    </div>

                    @if($viewHotel->users && $viewHotel->users->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-400 text-[10px] font-extrabold uppercase tracking-wider">
                                    <th class="p-2.5 pl-3">User ID</th>
                                    <th class="p-2.5">User Name</th>
                                    <th class="p-2.5">Email Username</th>
                                    <th class="p-2.5">Assigned Role</th>
                                    <th class="p-2.5 pr-3 text-right">Account Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($viewHotel->users as $u)
                                <tr>
                                    <td class="p-2.5 pl-3 font-bold text-slate-500">#{{ $u->id }}</td>
                                    <td class="p-2.5 font-extrabold text-slate-800">{{ $u->name }}</td>
                                    <td class="p-2.5 font-bold text-indigo-600">{{ $u->email }}</td>
                                    <td class="p-2.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $u->role ? $u->role->name : 'Staff' }}
                                        </span>
                                    </td>
                                    <td class="p-2.5 pr-3 text-right">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $u->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                            {{ ucfirst($u->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="text-xs text-slate-400 py-3 text-center">No registered user accounts found for this hotel.</div>
                    @endif
                </div>

                <!-- Step 6: SaaS Subscription & Inventory -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-receipt"></i> 6. SaaS Subscription Details
                            </h4>
                            <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Step 6</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Plan Name</span>
                                <span class="font-extrabold text-indigo-600">
                                    {{ $viewHotel->subscription && $viewHotel->subscription->plan ? $viewHotel->subscription->plan->name : 'Trial Plan' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Subscription Status</span>
                                <span class="font-bold text-slate-700 uppercase">
                                    {{ $viewHotel->subscription ? $viewHotel->subscription->status : 'trialing' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Starts At</span>
                                <span class="font-bold text-slate-700">
                                    {{ $viewHotel->subscription && $viewHotel->subscription->starts_at ? $viewHotel->subscription->starts_at->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-400 font-medium">Expires / Renews</span>
                                <span class="font-bold text-slate-700">
                                    {{ $viewHotel->subscription && $viewHotel->subscription->ends_at ? $viewHotel->subscription->ends_at->format('d M Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h4 class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-door-closed"></i> Initial Inventory Summary
                            </h4>
                            <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Configured</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Target Rooms Count</span>
                                <span class="font-extrabold text-slate-800">{{ $viewHotel->rooms_count ?: 10 }} Rooms</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Active Database Rooms</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->rooms ? $viewHotel->rooms->count() : 0 }} Units</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-400 font-medium">Default Starting Floor</span>
                                <span class="font-bold text-slate-700">Floor 1</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-slate-400 font-medium">Default Starting Price</span>
                                <span class="font-bold text-slate-700">{{ $viewHotel->currency ?: 'USD' }} $100.00 / night</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between p-5 border-t border-slate-100 bg-white">
                <div class="flex items-center gap-3">
                    @if($viewHotel->status === 'approved')
                        <button wire:click="loginAsHotelAdmin({{ $viewHotel->id }})" 
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-sign-in-alt"></i> Impersonate Hotel Admin
                        </button>
                    @endif
                </div>

                <button wire:click="closeViewModal" class="px-6 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition-colors cursor-pointer">
                    Close Total View
                </button>
            </div>
        </div>
    </div>
    @endif
</div>


