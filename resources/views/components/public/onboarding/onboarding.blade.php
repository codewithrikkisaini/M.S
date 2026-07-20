<div class="min-h-screen bg-slate-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <div class="max-w-md w-full bg-white rounded-2xl border border-slate-100 shadow-sm p-8 relative z-10">
        {{-- Header --}}
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-650 border border-indigo-100 mb-3">
                <i class="fas fa-magic text-[10px]"></i> Setup Wizard
            </span>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Setup Your Hotel</h2>
            <p class="text-xs text-slate-500 mt-1">Configure your workspace defaults to get started immediately</p>
        </div>

        {{-- Progress Dots --}}
        <div class="flex justify-center gap-1.5 mb-8">
            <span class="w-10 h-1.5 rounded-full {{ $step >= 1 ? 'bg-indigo-600' : 'bg-slate-200' }}"></span>
            <span class="w-10 h-1.5 rounded-full {{ $step >= 2 ? 'bg-indigo-600' : 'bg-slate-200' }}"></span>
            <span class="w-10 h-1.5 rounded-full {{ $step >= 3 ? 'bg-indigo-600' : 'bg-slate-200' }}"></span>
        </div>

        {{-- Step Forms --}}
        @if($step == 1)
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-indigo-650 uppercase tracking-wider mb-3">1. Hotel Information</h3>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Hotel Brand Name *</label>
                <input type="text" wire:model="hotel_name" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. Grand Horizon Hotel">
                @error('hotel_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Preferred Currency *</label>
                    <select wire:model="currency" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white">
                        <option value="USD">USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="INR">INR (₹)</option>
                        <option value="GBP">GBP (£)</option>
                    </select>
                    @error('currency') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Timezone *</label>
                    <select wire:model="timezone" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white">
                        <option value="UTC">UTC</option>
                        <option value="GMT">GMT</option>
                        <option value="Asia/Kolkata">IST (+5:30)</option>
                        <option value="America/New_York">EST (-5:00)</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button wire:click="nextStep" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 px-5 rounded-lg shadow-sm transition-all cursor-pointer">
                    Next Section <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                </button>
            </div>
        </div>
        @endif

        @if($step == 2)
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-indigo-650 uppercase tracking-wider mb-3">2. Initial Room Category</h3>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1.5">Category Name *</label>
                <input type="text" wire:model="room_type_name" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. Executive Suite">
                @error('room_type_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Base Price Per Night *</label>
                    <input type="number" step="0.01" wire:model="base_price" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white">
                    @error('base_price') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Max Occupancy *</label>
                    <input type="number" wire:model="base_occupancy" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white">
                    @error('base_occupancy') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                <button wire:click="prevStep" class="rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-655 font-semibold text-xs py-2 px-4 transition-all cursor-pointer">
                    Back
                </button>
                <button wire:click="nextStep" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 px-5 rounded-lg shadow-sm transition-all cursor-pointer">
                    Next Section <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                </button>
            </div>
        </div>
        @endif

        @if($step == 3)
        <div class="space-y-4">
            <h3 class="text-xs font-bold text-indigo-655 uppercase tracking-wider mb-3">3. Add Your First Room</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Room Number *</label>
                    <input type="text" wire:model="room_number" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. 101">
                    @error('room_number') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Floor Level *</label>
                    <input type="text" wire:model="floor" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. 1">
                    @error('floor') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-between items-center">
                <button wire:click="prevStep" class="rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-655 font-semibold text-xs py-2 px-4 transition-all cursor-pointer">
                    Back
                </button>
                <button wire:click="completeOnboarding" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2 px-5 rounded-lg shadow-sm transition-all cursor-pointer">
                    <i class="fas fa-check mr-1 text-[10px]"></i> Complete Onboarding
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
