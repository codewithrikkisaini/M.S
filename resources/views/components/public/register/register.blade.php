<div class="min-h-screen flex bg-slate-50">
    {{-- ===== LEFT HERO PANEL ===== --}}
    <div class="hidden lg:flex lg:w-5/12 relative overflow-hidden min-h-screen bg-blue-700">
        {{-- Background decorative elements --}}
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-20 left-20 w-80 h-80 bg-blue-400 rounded-full blur-3xl"></div>
            <div class="absolute bottom-32 right-16 w-64 h-64 bg-blue-300 rounded-full blur-3xl"></div>
        </div>

        {{-- Grid pattern --}}
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.1) 1px, transparent 0); background-size: 32px 32px;"></div>

        <div class="relative z-10 flex flex-col justify-between p-12 w-full h-full">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3">
                <div class="w-11 h-11 bg-white rounded-2xl flex items-center justify-center border border-white/20 shadow-xl">
                    <i class="fas fa-paper-plane text-blue-600 text-lg"></i>
                </div>
                <div>
                    <span class="text-white font-black text-xl tracking-tight block">MERAHKIE</span>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-blue-200">Partner Network</span>
                </div>
            </a>

            {{-- Text/Features --}}
            <div class="max-w-md my-auto space-y-6 py-8">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/30 border border-blue-400/40 text-blue-100 text-xs font-bold uppercase tracking-wider shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Join Our Network
                </span>
                <h1 class="text-4xl font-black text-white tracking-tight leading-tight">
                    List Your Property & Grow Your Bookings.
                </h1>
                <p class="text-blue-100 text-sm leading-relaxed font-medium">
                    Reach millions of travelers globally. Manage your inventory, get direct bookings, and increase your revenue.
                </p>
                
                <div class="space-y-4 pt-4 border-t border-blue-600/50">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-400/30"><i class="fas fa-check text-xs"></i></div>
                        <div>
                            <h4 class="text-white text-xs font-bold uppercase tracking-wider">Zero Setup Fees</h4>
                            <p class="text-blue-100/70 text-xs mt-0.5">Start listing for free and only pay a small commission on confirmed bookings.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-400/30"><i class="fas fa-check text-xs"></i></div>
                        <div>
                            <h4 class="text-white text-xs font-bold uppercase tracking-wider">Powerful Dashboard</h4>
                            <p class="text-blue-100/70 text-xs mt-0.5">Manage rooms, rates, and guest communication from a single interface.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer info --}}
            <p class="text-blue-200/50 text-xs">
                &copy; {{ date('Y') }} Merahkie Bookings.
            </p>
        </div>
    </div>

    {{-- ===== RIGHT FORM PANEL ===== --}}
    <div class="w-full lg:w-7/12 flex items-center justify-center p-6 sm:p-12 bg-white relative overflow-y-auto">
        <div class="max-w-2xl w-full relative z-10 py-6">
            @if($successMessage)
                {{-- Success State --}}
                <div class="text-center space-y-6 animate-fadeIn py-12">
                    <div class="w-24 h-24 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100 flex items-center justify-center mx-auto shadow-md">
                        <i class="fas fa-check-circle text-5xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">✅ Property Registered Successfully!</h2>
                        <p class="text-sm text-slate-600 mt-2 leading-relaxed max-w-md mx-auto">
                            Your hotel is under review by our moderation team. You will be redirected to the login page shortly.
                        </p>
                    </div>
                    <div class="pt-4 max-w-xs mx-auto">
                        <a href="{{ route('login') }}" class="w-full block text-center rounded-xl py-3.5 text-sm font-bold shadow-md bg-blue-600 hover:bg-blue-700 text-white transition-all">
                            Go to Login
                        </a>
                    </div>
                </div>
                <script>
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}";
                    }, 3000);
                </script>
            @else
                {{-- Registration Form Header --}}
                <div class="mb-8 border-b border-slate-100 pb-5">
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-1">Partner Onboarding</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Register Your Hotel</h2>
                    <p class="text-sm text-slate-500 mt-1">Complete this application to list your property on our network.</p>
                </div>

                {{-- Progress Bar --}}
                <div class="mb-8">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-100 rounded-full z-0"></div>
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-blue-600 rounded-full z-0 transition-all duration-300" style="width: {{ (($currentStep - 1) / ($totalSteps - 1)) * 100 }}%"></div>
                        
                        @for($i = 1; $i <= $totalSteps; $i++)
                            <div class="relative z-10 flex flex-col items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300 {{ $currentStep >= $i ? 'bg-blue-600 text-white border-2 border-blue-600' : 'bg-white text-slate-400 border-2 border-slate-200' }}">
                                    @if($currentStep > $i)
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $i }}
                                    @endif
                                </div>
                                <span class="hidden sm:block text-[10px] font-bold uppercase tracking-wider {{ $currentStep >= $i ? 'text-blue-600' : 'text-slate-400' }}">
                                    @switch($i)
                                        @case(1) Business @break
                                        @case(2) Contact @break
                                        @case(3) Location @break
                                        @case(4) Profile @break
                                        @case(5) Account @break
                                    @endswitch
                                </span>
                            </div>
                        @endfor
                    </div>
                </div>

                <form wire:submit.prevent="registerHotel" class="space-y-6">
                    
                    {{-- STEP 1: Business Details --}}
                    @if($currentStep == 1)
                        <div class="animate-fadeIn">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">Business & Tax Details</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Hotel Trade / Display Name *</label>
                                    <input type="text" wire:model.defer="name" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white" placeholder="e.g. Grand Plaza Resort">
                                    @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Legal / Company Name</label>
                                    <input type="text" wire:model.defer="business_name" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Owner Name</label>
                                    <input type="text" wire:model.defer="owner_name" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Tax ID / GSTIN</label>
                                    <input type="text" wire:model.defer="tax_id" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Company Reg Number</label>
                                    <input type="text" wire:model.defer="company_reg_number" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- STEP 2: Contact Details --}}
                    @if($currentStep == 2)
                        <div class="animate-fadeIn">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">Contact & Communication</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Official Hotel Email *</label>
                                    <input type="email" wire:model.defer="email" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-blue-500 bg-white" placeholder="e.g. info@grandplaza.com">
                                    @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Primary Phone Number</label>
                                    <input type="text" wire:model.defer="phone" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">WhatsApp Business</label>
                                    <input type="text" wire:model.defer="whatsapp" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Website URL</label>
                                    <input type="text" wire:model.defer="website" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white" placeholder="https://www.grandplaza.com">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- STEP 3: Location Details --}}
                    @if($currentStep == 3)
                        <div class="animate-fadeIn">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">Location & Region</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Full Address *</label>
                                    <input type="text" wire:model.defer="address" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-blue-500 bg-white" placeholder="123 Street Name">
                                    @error('address') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">City</label>
                                    <input type="text" wire:model.defer="city" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">State / Province</label>
                                    <input type="text" wire:model.defer="state" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Country *</label>
                                    <input type="text" wire:model.defer="country" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                    @error('country') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Postal Code</label>
                                    <input type="text" wire:model.defer="postal_code" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Timezone *</label>
                                    <select wire:model.defer="timezone" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                        <option value="UTC">UTC</option>
                                        <option value="Asia/Kolkata">IST (UTC+5:30)</option>
                                        <option value="America/New_York">EST (UTC-5)</option>
                                        <option value="Europe/London">GMT/BST</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Currency *</label>
                                    <select wire:model.defer="currency" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                        <option value="USD">USD ($)</option>
                                        <option value="EUR">EUR (€)</option>
                                        <option value="INR">INR (₹)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- STEP 4: Property Specifications --}}
                    @if($currentStep == 4)
                        <div class="animate-fadeIn">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">Property Specifications</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Category / Rating</label>
                                    <select wire:model.defer="category" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                        <option value="5-star">5 Star</option>
                                        <option value="4-star">4 Star</option>
                                        <option value="3-star">3 Star</option>
                                        <option value="budget">Budget</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Property Type</label>
                                    <select wire:model.defer="property_type" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                        <option value="Hotel">Hotel</option>
                                        <option value="Resort">Resort</option>
                                        <option value="Villa">Villa</option>
                                        <option value="Homestay">Homestay</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Current PMS (if any)</label>
                                    <input type="text" wire:model.defer="current_pms" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- STEP 5: Administrator Credentials --}}
                    @if($currentStep == 5)
                        <div class="animate-fadeIn">
                            <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100 mb-4">
                                <h3 class="text-lg font-bold text-blue-900 mb-4">Administrator Account</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Admin Full Name *</label>
                                        <input type="text" wire:model.defer="admin_name" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                        @error('admin_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">Admin Login Email *</label>
                                        <input type="email" wire:model.defer="admin_email" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                        @error('admin_email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Password *</label>
                                            <input type="password" wire:model.defer="admin_password" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                            @error('admin_password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Confirm Password *</label>
                                            <input type="password" wire:model.defer="admin_password_confirmation" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-blue-500 bg-white">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Form Controls --}}
                    <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                        @if($currentStep > 1)
                            <button type="button" wire:click="prevStep" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-all">
                                Back
                            </button>
                        @else
                            <div></div>
                        @endif

                        @if($currentStep < $totalSteps)
                            <button type="button" wire:click="nextStep" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-md transition-all">
                                Next Step
                            </button>
                        @else
                            <button type="submit" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-md transition-all flex items-center gap-2">
                                Submit Application <i class="fas fa-check"></i>
                            </button>
                        @endif
                    </div>

                    <p class="text-center text-xs text-slate-500 mt-6">
                        Already have an approved property? <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-700 hover:underline">Sign In Here</a>
                    </p>
                </form>
            @endif
        </div>
    </div>
</div>
