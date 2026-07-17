<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">SaaS Subscription Plans</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage subscription packages, pricing, and system limits</p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="btn-primary rounded-lg shadow-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm flex items-center gap-1.5 cursor-pointer transition-colors">
                <i class="fas fa-plus text-xs"></i> Create New Plan
            </button>
        </div>
    </div>

    {{-- Plans List Grid/Table --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @foreach($plans as $p)
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm relative overflow-hidden flex flex-col justify-between">
            @if($p->status === 'inactive')
            <div class="absolute top-3 right-3">
                <span class="bg-slate-100 text-slate-650 text-[10px] font-bold px-2 py-0.5 rounded-full border border-slate-200">Inactive</span>
            </div>
            @else
            <div class="absolute top-3 right-3">
                <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-100">Active</span>
            </div>
            @endif

            <div>
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100 mb-4">
                    <i class="fas @if($p->billing_cycle === 'trial') fa-hourglass-start @elseif($p->billing_cycle === 'monthly') fa-calendar-alt @elseif($p->billing_cycle === 'yearly') fa-calendar-check @else fa-infinity @endif text-sm"></i>
                </div>
                
                <h3 class="text-base font-extrabold text-slate-800">{{ $p->name }}</h3>
                <p class="text-xs text-slate-400 mt-1 capitalize">{{ $p->billing_cycle }} Plan</p>
                
                <div class="mt-4 flex items-baseline">
                    <span class="text-2xl font-black text-slate-900">${{ number_format($p->price, 2) }}</span>
                    <span class="text-xs text-slate-450 ml-1">
                        @if($p->billing_cycle === 'monthly') /mo @elseif($p->billing_cycle === 'yearly') /yr @elseif($p->billing_cycle === 'trial') /{{ $p->trial_days }} days @else /one-time @endif
                    </span>
                </div>

                <p class="text-xs text-slate-500 mt-3 min-h-[32px] line-clamp-2">{{ $p->description ?: 'No description provided.' }}</p>

                <div class="border-t border-slate-50 pt-4 mt-4 space-y-2">
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Max Rooms:</span>
                        <span class="font-bold text-slate-850">{{ $p->max_rooms ?: 'Unlimited' }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Max Users:</span>
                        <span class="font-bold text-slate-850">{{ $p->max_users ?: 'Unlimited' }}</span>
                    </div>
                    @if($p->billing_cycle === 'trial')
                    <div class="flex justify-between text-xs text-slate-600">
                        <span>Trial Duration:</span>
                        <span class="font-bold text-slate-850">{{ $p->trial_days }} Days</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-50 pt-4 mt-5">
                <button wire:click="openEditModal({{ $p->id }})" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold transition-colors cursor-pointer">
                    <i class="far fa-edit mr-1"></i> Edit
                </button>
                <button onclick="confirm('Are you sure you want to delete this plan?') || event.stopImmediatePropagation()"
                        wire:click="deletePlan({{ $p->id }})"
                        class="text-rose-650 hover:text-rose-850 text-xs font-bold transition-colors ml-4 cursor-pointer">
                    <i class="far fa-trash-alt mr-1"></i> Delete
                </button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Create / Edit Plan Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-slate-100 animate-fadeIn duration-200">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-800">{{ $isEdit ? 'Edit Subscription Plan' : 'Create Subscription Plan' }}</h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition-colors cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form wire:submit.prevent="savePlan" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Plan Name *</label>
                        <input type="text" wire:model="name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Monthly Pro">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Plan Slug (Unique URL Identifier) *</label>
                        <input type="text" wire:model="slug" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. monthly-pro">
                        @error('slug') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Price ($) *</label>
                        <input type="number" step="0.01" wire:model="price" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="0.00">
                        @error('price') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Billing Cycle *</label>
                        <select wire:model="billing_cycle" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                            <option value="trial">Trial</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="lifetime">Lifetime</option>
                        </select>
                        @error('billing_cycle') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Trial Period (Days)</label>
                        <input type="number" wire:model="trial_days" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="0">
                        @error('trial_days') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Status *</label>
                        <select wire:model="status" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Max Rooms (Leave blank for Unlimited)</label>
                        <input type="number" wire:model="max_rooms" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="Unlimited">
                        @error('max_rooms') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Max Users (Leave blank for Unlimited)</label>
                        <input type="number" wire:model="max_users" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="Unlimited">
                        @error('max_users') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Description</label>
                    <textarea wire:model="description" rows="3" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="Describe the plan inclusions..."></textarea>
                    @error('description') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                    <button type="button" wire:click="closeModal" class="btn-secondary rounded-lg px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-650 font-semibold text-sm cursor-pointer transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm cursor-pointer shadow-md transition-colors">
                        {{ $isEdit ? 'Save Changes' : 'Create Plan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
