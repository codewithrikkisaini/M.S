<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manage Hotels</h1>
            <p class="text-sm text-slate-500 mt-0.5">CRUD operations and tenant onboarding management</p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="btn-primary rounded-lg shadow-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-755 text-white font-semibold text-sm flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-plus text-xs"></i> Add New Hotel
            </button>
        </div>
    </div>

    {{-- Hotel List Table --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-list text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">All Registered Tenants</h3>
                    <p class="text-[10px] text-slate-400">Total list of active, pending, and suspended hotel systems</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>Hotel ID</th>
                        <th>Hotel Details</th>
                        <th>Contact info</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hotelsList as $h)
                    <tr>
                        <td class="font-extrabold text-slate-700 text-sm">#{{ $h->id }}</td>
                        <td>
                            <div class="font-bold text-slate-800 text-sm">{{ $h->name }}</div>
                            <div class="text-[10px] text-slate-400">Created: {{ $h->created_at->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div class="text-slate-650 text-xs font-semibold"><i class="far fa-envelope mr-1.5 text-slate-400"></i>{{ $h->email }}</div>
                            <div class="text-[10px] text-slate-450 mt-0.5"><i class="fas fa-phone mr-1.5 text-slate-400"></i>{{ $h->phone ?? 'N/A' }}</div>
                        </td>
                        <td class="text-slate-600 text-xs font-medium max-w-xs truncate">{{ $h->address ?? 'N/A' }}</td>
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
                            @if($h->status === 'pending')
                            <button onclick="confirm('Are you sure you want to approve this hotel?') || event.stopImmediatePropagation()"
                                    wire:click="approveHotel({{ $h->id }})" 
                                    class="text-emerald-600 hover:text-emerald-800 text-xs font-bold transition-colors cursor-pointer mr-2">
                                <i class="fas fa-check mr-1"></i> Approve
                            </button>
                            <button onclick="confirm('Are you sure you want to reject this hotel?') || event.stopImmediatePropagation()"
                                    wire:click="rejectHotel({{ $h->id }})" 
                                    class="text-rose-500 hover:text-rose-700 text-xs font-bold transition-colors cursor-pointer mr-2">
                                <i class="fas fa-times mr-1"></i> Reject
                            </button>
                            @endif
                            <button onclick="confirm('Are you sure you want to delete this hotel and all associated users/data?') || event.stopImmediatePropagation()" 
                                    wire:click="deleteHotel({{ $h->id }})" 
                                    class="text-rose-600 hover:text-rose-800 text-xs font-bold transition-colors cursor-pointer">
                                <i class="far fa-trash-alt mr-1"></i> Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-slate-455 py-10">No hotels found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create Hotel Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-slate-100 animate-fadeIn duration-200">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-800">Add New Hotel Tenant</h3>
                <button wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition-colors cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveHotel" class="p-6 space-y-6">
                {{-- Hotel Info Section --}}
                <div>
                    <h4 class="text-xs font-extrabold text-indigo-600 uppercase tracking-widest mb-4">1. Hotel Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Hotel Name *</label>
                            <input type="text" wire:model="name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Emerald Beach Resort">
                            @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Hotel Email *</label>
                            <input type="email" wire:model="email" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. contact@emeraldbeach.com">
                            @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Hotel Phone</label>
                            <input type="text" wire:model="phone" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. +1234567890">
                            @error('phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Address</label>
                            <input type="text" wire:model="address" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. 456 Palm Drive, Miami, FL">
                            @error('address') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Admin Account Section --}}
                <div class="border-t border-slate-100 pt-6">
                    <h4 class="text-xs font-extrabold text-indigo-600 uppercase tracking-widest mb-4">2. Hotel Administrator User Account</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Admin Full Name *</label>
                            <input type="text" wire:model="admin_name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. John Doe">
                            @error('admin_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Admin Email *</label>
                            <input type="email" wire:model="admin_email" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. johndoe@hotel.com">
                            @error('admin_email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Login Password *</label>
                            <input type="password" wire:model="admin_password" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="Minimum 6 characters">
                            @error('admin_password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                    <button type="button" wire:click="closeCreateModal" class="btn-secondary rounded-lg px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-650 font-semibold text-sm cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-755 text-white font-semibold text-sm cursor-pointer shadow-md">
                        Save Tenant & Seed
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
