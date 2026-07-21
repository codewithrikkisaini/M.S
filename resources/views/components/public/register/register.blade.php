<div class="min-h-screen flex bg-slate-50">
    {{-- ===== LEFT HERO PANEL ===== --}}
    <div class="hidden lg:flex lg:w-5/12 relative overflow-hidden min-h-screen" style="background: linear-gradient(135deg, #090d16 0%, #0f172a 50%, #1e1b4b 100%) !important; color: #ffffff !important;">
        {{-- Background decorative elements --}}
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-20 left-20 w-80 h-80 bg-indigo-600 rounded-full blur-3xl"></div>
            <div class="absolute bottom-32 right-16 w-64 h-64 bg-indigo-500 rounded-full blur-3xl"></div>
        </div>

        {{-- Grid pattern --}}
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.08) 1px, transparent 0); background-size: 32px 32px;"></div>

        <div class="relative z-10 flex flex-col justify-between p-12 w-full h-full">
            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 shadow-xl backdrop-blur-md">
                    <i class="fas fa-hotel text-indigo-400 text-lg"></i>
                </div>
                <div>
                    <span class="text-white font-black text-xl tracking-tight block">Merahkie Group</span>
                    <span class="text-[10px] uppercase font-bold tracking-widest text-indigo-300">SaaS PMS Enterprise</span>
                </div>
            </div>

            {{-- Text/Features --}}
            <div class="max-w-md my-auto space-y-6 py-8">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-indigo-500/20 border border-indigo-400/40 text-indigo-200 text-xs font-bold uppercase tracking-wider shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Complete Tenant Portal Onboarding
                </span>
                <h1 class="text-4xl font-black text-white tracking-tight leading-tight">
                    Apply for Your Enterprise Hotel Portal.
                </h1>
                <p class="text-slate-300 text-sm leading-relaxed font-medium">
                    Provide your complete business profile, property specifications, and primary admin details to register your tenant account.
                </p>
                
                <div class="space-y-4 pt-4 border-t border-white/15">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-400/30"><i class="fas fa-check text-xs"></i></div>
                        <div>
                            <h4 class="text-white text-xs font-bold uppercase tracking-wider">Full Data Isolation</h4>
                            <p class="text-slate-300 text-xs mt-0.5">Automated multi-tenant database scoping for complete data privacy.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-400/30"><i class="fas fa-check text-xs"></i></div>
                        <div>
                            <h4 class="text-white text-xs font-bold uppercase tracking-wider">Instant Provisioning</h4>
                            <p class="text-slate-300 text-xs mt-0.5">Rooms, floor layouts, and billing defaults configured upon approval.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer info --}}
            <p class="text-indigo-200/50 text-xs">
                &copy; {{ date('Y') }} Merahkie Silk Road Group &mdash; All Rights Reserved.
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
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Full Application Submitted!</h2>
                        <p class="text-sm text-slate-600 mt-2 leading-relaxed max-w-md mx-auto">
                            Thank you for registering <strong>{{ $name }}</strong> with complete business details. Your application is now queued for Super Admin review.
                        </p>
                        <p class="text-xs text-slate-400 mt-2">
                            An activation email will be sent to <strong>{{ $admin_email }}</strong> once approved.
                        </p>
                    </div>
                    <div class="pt-4 max-w-xs mx-auto">
                        <a href="{{ route('login') }}" class="w-full block text-center rounded-xl py-3.5 text-sm font-bold shadow-md bg-indigo-600 hover:bg-indigo-700 text-white transition-all">
                            Back to Sign In
                        </a>
                    </div>
                </div>
            @else
                {{-- Registration Form Header --}}
                <div class="mb-8 border-b border-slate-100 pb-5">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block mb-1">Application Form</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Register Your Hotel Property</h2>
                    <p class="text-sm text-slate-500 mt-1">Enter total property specifications, business credentials, and admin user details below.</p>
                </div>

                <form wire:submit.prevent="registerHotel" class="space-y-8">
                    {{-- 1. Business Profile --}}
                    <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80 space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                            <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">1</div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Business & Tax Details</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-600 mb-1">Hotel Trade / Display Name *</label>
                                <input type="text" wire:model="name" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500 bg-white shadow-sm" placeholder="e.g. Grand Plaza Resort & Spa">
                                @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Legal / Company Name</label>
                                <input type="text" wire:model="business_name" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. Grand Plaza Hospitality LLC">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Proprietor / Owner Name</label>
                                <input type="text" wire:model="owner_name" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. Richard Hendricks">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Tax ID / GSTIN</label>
                                <input type="text" wire:model="tax_id" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. TX-98765432">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Company Reg Number</label>
                                <input type="text" wire:model="company_reg_number" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. CRN-12345">
                            </div>
                        </div>
                    </div>

                    {{-- 2. Contact Details --}}
                    <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80 space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                            <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">2</div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Contact & Communication</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Hotel Email Address *</label>
                                <input type="email" wire:model="email" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500 bg-white shadow-sm" placeholder="e.g. info@grandplaza.com">
                                @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Primary Phone Number</label>
                                <input type="text" wire:model="phone" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. +1 (555) 019-2834">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">WhatsApp Business</label>
                                <input type="text" wire:model="whatsapp" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. +1 (555) 019-2835">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Official Website URL</label>
                                <input type="text" wire:model="website" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. https://www.grandplazahotel.com">
                            </div>
                        </div>
                    </div>

                    {{-- 3. Location Details --}}
                    <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80 space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                            <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">3</div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Location & Region</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-600 mb-1">Full Street Address *</label>
                                <input type="text" wire:model="address" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500 bg-white shadow-sm" placeholder="e.g. 789 Ocean Drive, Suite 100">
                                @error('address') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">City</label>
                                <input type="text" wire:model="city" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. Miami">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">State / Province</label>
                                <input type="text" wire:model="state" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. Florida">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Country *</label>
                                <input type="text" wire:model="country" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500 bg-white shadow-sm" placeholder="e.g. United States">
                                @error('country') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Postal / ZIP Code</label>
                                <input type="text" wire:model="postal_code" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. 33139">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Timezone *</label>
                                <select wire:model="timezone" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white">
                                    <option value="UTC">UTC</option>
                                    <option value="GMT">GMT</option>
                                    <option value="Asia/Kolkata">IST (UTC+5:30)</option>
                                    <option value="America/New_York">EST (UTC-5)</option>
                                    <option value="Europe/London">GMT/BST</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Base Currency *</label>
                                <select wire:model="currency" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white">
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="INR">INR (₹)</option>
                                    <option value="GBP">GBP (£)</option>
                                    <option value="AED">AED</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Property Specifications --}}
                    <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/80 space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                            <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">4</div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Property Profile & Inventory</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Total Rooms *</label>
                                <input type="number" wire:model="rooms_count" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500 bg-white shadow-sm" min="1" max="500">
                                @error('rooms_count') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Category Rating</label>
                                <select wire:model="category" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white">
                                    <option value="5-star">5 Star Luxury</option>
                                    <option value="4-star">4 Star Deluxe</option>
                                    <option value="3-star">3 Star Standard</option>
                                    <option value="boutique">Boutique Hotel</option>
                                    <option value="budget">Budget / Hostel</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Property Type</label>
                                <input type="text" wire:model="property_type" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. Resort / Villa">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Current PMS (if any)</label>
                                <input type="text" wire:model="current_pms" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. Cloudbeds / Opera">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Current Channel Manager</label>
                                <input type="text" wire:model="current_channel_manager" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 bg-white" placeholder="e.g. SiteMinder">
                            </div>
                        </div>
                    </div>

                    {{-- 5. Admin Account Credentials --}}
                    <div class="bg-indigo-50/40 p-5 rounded-2xl border border-indigo-100 space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-indigo-100">
                            <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">5</div>
                            <h3 class="text-xs font-black text-indigo-900 uppercase tracking-wider">Hotel Administrator Credentials</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Admin Full Name *</label>
                                <input type="text" wire:model="admin_name" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500 bg-white shadow-sm" placeholder="e.g. Jane Smith">
                                @error('admin_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Admin Login Email *</label>
                                <input type="email" wire:model="admin_email" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500 bg-white shadow-sm" placeholder="e.g. admin@grandplaza.com">
                                @error('admin_email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Password *</label>
                                <input type="password" wire:model="admin_password" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500 bg-white shadow-sm" placeholder="Minimum 6 characters">
                                @error('admin_password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Confirm Password *</label>
                                <input type="password" wire:model="admin_password_confirmation" class="w-full border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500 bg-white shadow-sm" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button type="submit" class="w-full flex items-center justify-center rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white py-4 font-black text-base shadow-xl hover:shadow-2xl transition-all cursor-pointer">
                            Submit Complete Application <i class="fas fa-paper-plane ml-2 text-sm"></i>
                        </button>
                    </div>

                    <p class="text-center text-xs text-slate-500 mt-4">
                        Already have an approved portal? <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-700 hover:underline">Sign In Here</a>
                    </p>
                </form>
            @endif
        </div>
    </div>
</div>
