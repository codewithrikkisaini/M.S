<div class="min-h-screen bg-slate-950 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    {{-- Glow Accents --}}
    <div class="absolute top-1/4 left-1/4 w-80 h-80 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-purple-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full bg-slate-900/60 rounded-3xl border border-slate-800/80 backdrop-blur-md shadow-2xl p-8 relative z-10">
        {{-- Header --}}
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-950/80 text-indigo-400 border border-indigo-900/50 mb-3 shadow-sm">
                <i class="fas fa-magic text-[10px]"></i> Setup Wizard
            </span>
            <h2 class="text-2xl font-black text-white tracking-tight">Setup Your Hotel</h2>
            <p class="text-xs text-slate-400 mt-1">Configure your workspace defaults to get started immediately</p>
        </div>

        {{-- Progress Dots --}}
        <div class="flex justify-center gap-1.5 mb-8">
            <span class="w-10 h-1.5 rounded-full {{ $step >= 1 ? 'bg-gradient-to-r from-indigo-500 to-purple-500' : 'bg-slate-800' }}"></span>
            <span class="w-10 h-1.5 rounded-full {{ $step >= 2 ? 'bg-gradient-to-r from-indigo-500 to-purple-500' : 'bg-slate-800' }}"></span>
            <span class="w-10 h-1.5 rounded-full {{ $step >= 3 ? 'bg-gradient-to-r from-indigo-500 to-purple-500' : 'bg-slate-800' }}"></span>
        </div>

        {{-- Step Forms --}}
        @if($step == 1)
        <div class="space-y-4">
            <h3 class="text-xs font-black text-indigo-400 uppercase tracking-wider mb-3">1. Hotel Information</h3>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Hotel Brand Name *</label>
                <input type="text" wire:model="hotel_name" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 placeholder-slate-600">
                @error('hotel_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Preferred Currency *</label>
                    <select wire:model="currency" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30">
                        <option value="USD" class="bg-slate-900 text-white">USD ($)</option>
                        <option value="EUR" class="bg-slate-900 text-white">EUR (€)</option>
                        <option value="INR" class="bg-slate-900 text-white">INR (₹)</option>
                        <option value="GBP" class="bg-slate-900 text-white">GBP (£)</option>
                    </select>
                    @error('currency') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Timezone *</label>
                    <select wire:model="timezone" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30">
                        <option value="UTC" class="bg-slate-900 text-white">UTC</option>
                        <option value="GMT" class="bg-slate-900 text-white">GMT</option>
                        <option value="Asia/Kolkata" class="bg-slate-900 text-white">IST (+5:30)</option>
                        <option value="America/New_York" class="bg-slate-900 text-white">EST (-5:00)</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800/60 flex justify-end">
                <button wire:click="nextStep" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 px-5 rounded-xl shadow-lg shadow-indigo-500/20 transition-all cursor-pointer">
                    Next Section <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                </button>
            </div>
        </div>
        @endif

        @if($step == 2)
        <div class="space-y-4">
            <h3 class="text-xs font-black text-indigo-400 uppercase tracking-wider mb-3">2. Initial Room Category</h3>
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Category Name *</label>
                <input type="text" wire:model="room_type_name" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 placeholder-slate-600" placeholder="e.g. Executive Suite">
                @error('room_type_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Base Price Per Night *</label>
                    <input type="number" step="0.01" wire:model="base_price" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30">
                    @error('base_price') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Max Occupancy *</label>
                    <input type="number" wire:model="base_occupancy" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30">
                    @error('base_occupancy') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800/60 flex justify-between items-center">
                <button wire:click="prevStep" class="rounded-xl px-4 py-2 border border-slate-700 bg-slate-850 hover:bg-slate-800 text-slate-350 font-bold text-xs cursor-pointer transition-all">
                    Back
                </button>
                <button wire:click="nextStep" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 px-5 rounded-xl shadow-lg shadow-indigo-500/20 transition-all cursor-pointer">
                    Next Section <i class="fas fa-arrow-right ml-1 text-[10px]"></i>
                </button>
            </div>
        </div>
        @endif

        @if($step == 3)
        <div class="space-y-4">
            <h3 class="text-xs font-black text-indigo-400 uppercase tracking-wider mb-3">3. Add Your First Room</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Room Number *</label>
                    <input type="text" wire:model="room_number" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 placeholder-slate-600" placeholder="e.g. 101">
                    @error('room_number') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Floor Level *</label>
                    <input type="text" wire:model="floor" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 placeholder-slate-600" placeholder="e.g. 1">
                    @error('floor') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800/60 flex justify-between items-center">
                <button wire:click="prevStep" class="rounded-xl px-4 py-2 border border-slate-700 bg-slate-850 hover:bg-slate-800 text-slate-355 font-bold text-xs cursor-pointer transition-all">
                    Back
                </button>
                <button wire:click="completeOnboarding" class="bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs py-2.5 px-5 rounded-xl shadow-lg shadow-emerald-500/20 transition-all cursor-pointer">
                    <i class="fas fa-check mr-1 text-[10px]"></i> Complete Onboarding
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
