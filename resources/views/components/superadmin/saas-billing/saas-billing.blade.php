<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">SaaS Hotel Subscriptions</h1>
            <p class="text-sm text-slate-500 mt-0.5">Monitor and manage tenant subscriptions and system access levels</p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="btn-primary rounded-lg shadow-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm flex items-center gap-1.5 cursor-pointer transition-colors">
                <i class="fas fa-plus text-xs"></i> Assign New Subscription
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Subscriptions</span>
                    <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $subscriptions->count() }}</h3>
                </div>
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100">
                    <i class="fas fa-users-cog text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Active Subscribers</span>
                    <h3 class="text-2xl font-black text-emerald-600 mt-1">{{ $subscriptions->where('status', 'active')->count() }}</h3>
                </div>
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center border border-emerald-100">
                    <i class="fas fa-toggle-on text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Trialing Users</span>
                    <h3 class="text-2xl font-black text-amber-500 mt-1">{{ $subscriptions->where('status', 'trialing')->count() }}</h3>
                </div>
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center border border-amber-100">
                    <i class="fas fa-hourglass-half text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Expired / Cancelled</span>
                    <h3 class="text-2xl font-black text-rose-500 mt-1">
                        {{ $subscriptions->whereIn('status', ['expired', 'cancelled'])->count() }}
                    </h3>
                </div>
                <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center border border-rose-100">
                    <i class="fas fa-ban text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Subscriptions Table --}}
    <div class="pms-card shadow-sm border border-slate-100/80 bg-white">
        <div class="pms-card-header flex justify-between items-center flex-wrap gap-4 border-b border-slate-50 p-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                    <i class="fas fa-receipt text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Hotel Subscription Assignments</h3>
                    <p class="text-[10px] text-slate-400">Detailed list of active subscription periods for all properties</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase border-b border-slate-100">
                        <th class="p-3">Hotel / Property</th>
                        <th class="p-3">Assigned Plan</th>
                        <th class="p-3">Start Date</th>
                        <th class="p-3">Expiry Date</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $sub)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                        <td class="p-3">
                            <div class="font-bold text-slate-800 text-sm">{{ $sub->hotel?->name ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-400">Email: {{ $sub->hotel?->email ?? 'N/A' }}</div>
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-slate-800 text-sm">{{ $sub->plan?->name ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-400 capitalize">{{ $sub->plan?->billing_cycle ?? 'N/A' }} billing</div>
                        </td>
                        <td class="p-3 text-slate-600 text-xs font-semibold">
                            {{ $sub->starts_at ? $sub->starts_at->format('d M Y') : 'N/A' }}
                        </td>
                        <td class="p-3 text-slate-600 text-xs font-semibold">
                            @if($sub->ends_at)
                                {{ $sub->ends_at->format('d M Y') }}
                            @else
                                <span class="text-indigo-600 font-bold">Lifetime</span>
                            @endif
                        </td>
                        <td class="p-3">
                            @php
                                $statusColor = match($sub->status) {
                                    'active'    => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'trialing'  => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'expired'   => 'bg-rose-50 text-rose-700 border-rose-100',
                                    'cancelled' => 'bg-slate-55 text-slate-700 border-slate-200',
                                    default     => 'bg-slate-50 text-slate-700 border-slate-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $statusColor }}">
                                {{ ucfirst($sub->status) }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="openEditModal({{ $sub->id }})" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold transition-colors cursor-pointer">
                                    <i class="far fa-edit mr-1"></i> Edit
                                </button>
                                <button onclick="confirm('Are you sure you want to delete this subscription?') || event.stopImmediatePropagation()" 
                                        wire:click="deleteSubscription({{ $sub->id }})" 
                                        class="text-rose-650 hover:text-rose-850 text-xs font-bold transition-colors cursor-pointer">
                                    <i class="far fa-trash-alt mr-1"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-slate-400 py-10">No subscriptions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create / Edit Subscription Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-xl w-full border border-slate-100 animate-fadeIn duration-200">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-800">{{ $isEdit ? 'Edit Subscription Assignment' : 'Assign New Subscription' }}</h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition-colors cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveSubscription" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Select Hotel / Property *</label>
                    <select wire:model="hotel_id" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" @if($isEdit) disabled @endif>
                        <option value="">-- Choose Hotel --</option>
                        @foreach($hotels as $h)
                        <option value="{{ $h->id }}">{{ $h->name }} ({{ $h->email }})</option>
                        @endforeach
                    </select>
                    @error('hotel_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Select Subscription Plan *</label>
                    <select wire:model="subscription_plan_id" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="">-- Choose Plan --</option>
                        @foreach($plans as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (${{ number_format($p->price, 2) }} / {{ $p->billing_cycle }})</option>
                        @endforeach
                    </select>
                    @error('subscription_plan_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Subscription Status *</label>
                    <select wire:model="status" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="trialing">Trialing</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    @error('status') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Starts At *</label>
                    <input type="datetime-local" wire:model="starts_at" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    @error('starts_at') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Ends At (Leave blank for Lifetime)</label>
                    <input type="datetime-local" wire:model="ends_at" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    @error('ends_at') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Trial Ends At</label>
                    <input type="datetime-local" wire:model="trial_ends_at" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    @error('trial_ends_at') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6 mt-6">
                    <button type="button" wire:click="closeModal" class="btn-secondary rounded-lg px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-650 font-semibold text-sm cursor-pointer transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm cursor-pointer shadow-md transition-colors">
                        {{ $isEdit ? 'Save Changes' : 'Assign Plan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
