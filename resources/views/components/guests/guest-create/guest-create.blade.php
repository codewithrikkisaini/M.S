<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('guests.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Add Guest</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create a new guest record in the system directory</p>
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Form Panel --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-6">
                <div class="flex items-center gap-2 mb-5 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-user-plus text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Guest Profile Details</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Guest ID <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="guest_id" class="pms-input text-xs" placeholder="G-00001">
                        @error('guest_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" class="pms-input text-xs" placeholder="John Doe">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Email Address</label>
                        <input type="email" wire:model="email" class="pms-input text-xs" placeholder="john@example.com">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Phone Number</label>
                        <input type="text" wire:model="phone" class="pms-input text-xs" placeholder="+1 234 567 8900">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Nationality</label>
                        <input type="text" wire:model="nationality" class="pms-input text-xs" placeholder="e.g. Indian">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Passport / Govt ID No.</label>
                        <input type="text" wire:model="passport_number" class="pms-input text-xs" placeholder="A1234567">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Address</label>
                        <textarea wire:model="address" rows="2" class="pms-input text-xs resize-none rounded-lg border border-slate-200" placeholder="Full residential address..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 mt-6">
                    <a href="{{ route('guests.index') }}" class="btn-secondary text-xs rounded-lg py-2 font-bold px-4">Cancel</a>
                    <button wire:click="save" wire:loading.attr="disabled" class="btn-primary text-xs rounded-lg py-2 font-bold px-4 cursor-pointer shadow-sm">
                        <span wire:loading wire:target="save" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                        Create Guest
                    </button>
                </div>
            </div>
        </div>

        {{-- Right Info Panel --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-info-circle text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Compliance & Privacy</h3>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Identification Policy</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Always verify the government-issued photo identification (Passport, Driving License, National ID) at check-in and verify document details match the guest record.</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">GDPR & Data Privacy</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Guest contact details and passport files are encrypted and processed in compliance with local privacy laws. Never share guest directories externally.</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Contact Details</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Having a valid email address and phone number is highly recommended for sending digital invoices, receipts, and automated booking notifications.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
