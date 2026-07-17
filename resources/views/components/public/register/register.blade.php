<div class="min-h-screen flex">
    {{-- ===== LEFT HERO PANEL ===== --}}
    <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 overflow-hidden">
        {{-- Background decorative elements --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-20 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-32 right-16 w-56 h-56 bg-indigo-300 rounded-full blur-3xl"></div>
        </div>

        {{-- Grid pattern --}}
        <div class="absolute inset-0"
             style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.05) 1px, transparent 0); background-size: 32px 32px;"></div>

        <div class="relative z-10 flex flex-col justify-between p-12 w-full">
            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/10 shadow-lg backdrop-blur-md">
                    <i class="fas fa-hotel text-white text-base"></i>
                </div>
                <span class="text-white font-black text-lg tracking-tight">Merahkie PMS Lite</span>
            </div>

            {{-- Text/Features --}}
            <div class="max-w-md my-auto space-y-6">
                <h1 class="text-4xl font-extrabold text-white tracking-tight leading-tight">
                    Onboard Your Hotel and Start Managing Smarter.
                </h1>
                <p class="text-indigo-200/80 text-sm leading-relaxed">
                    Register your property in under 2 minutes. Gain access to the industry-leading PMS software designed for seamless check-ins, interactive calendars, invoicing, and real-time room operations.
                </p>
                <div class="space-y-4 pt-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-400/20"><i class="fas fa-check text-xs"></i></div>
                        <div>
                            <h4 class="text-white text-xs font-bold uppercase tracking-wider">Multi-Tenant Isolation</h4>
                            <p class="text-indigo-200/60 text-xs mt-0.5">Strict database scoping ensures your property data remains completely secure and isolated.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-400/20"><i class="fas fa-check text-xs"></i></div>
                        <div>
                            <h4 class="text-white text-xs font-bold uppercase tracking-wider">Complete Guest Ledger</h4>
                            <p class="text-indigo-200/60 text-xs mt-0.5">Automated invoices, guest profiling, and reservation calendars ready right away.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer info --}}
            <p class="text-indigo-300/40 text-xs">
                &copy; {{ date('Y') }} Merahkie Inc. &mdash; Enterprise Hotel management simplified.
            </p>
        </div>
    </div>

    {{-- ===== RIGHT FORM PANEL ===== --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-white">
        <div class="max-w-md w-full">
            @if($successMessage)
                {{-- Success State --}}
                <div class="text-center space-y-5 animate-fadeIn">
                    <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100 flex items-center justify-center mx-auto shadow-sm">
                        <i class="fas fa-check-circle text-4xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Application Submitted!</h2>
                        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                            Thank you for registering <strong>{{ $name }}</strong>. Your application is now pending approval by the Super Administrator.
                        </p>
                        <p class="text-xs text-slate-455 mt-1">
                            An email notification will be sent to <strong>{{ $admin_email }}</strong> once your PMS portal is ready.
                        </p>
                    </div>
                    <div class="pt-4">
                        <a href="{{ route('login') }}" class="btn-primary w-full block text-center rounded-xl py-3 text-sm font-semibold shadow-md bg-indigo-600 hover:bg-indigo-755 text-white">
                            Back to Sign In
                        </a>
                    </div>
                </div>
            @else
                {{-- Registration Form --}}
                <div class="mb-8">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Register Your Property</h2>
                    <p class="text-sm text-slate-550 mt-1">Enter your hotel and admin credentials to apply for a tenant portal.</p>
                </div>

                <form wire:submit.prevent="registerHotel" class="space-y-5">
                    {{-- Section: Hotel Info --}}
                    <div class="space-y-3.5">
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-1">1. Property Details</h3>
                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Hotel Name *</label>
                            <input type="text" wire:model="name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Grand Plaza Hotel">
                            @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Hotel Email *</label>
                                <input type="email" wire:model="email" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. hotel@mail.com">
                                @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Hotel Phone</label>
                                <input type="text" wire:model="phone" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. +123456789">
                                @error('phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Address</label>
                            <input type="text" wire:model="address" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. 123 Resort Blvd, Hawaii">
                            @error('address') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Section: Admin account info --}}
                    <div class="space-y-3.5 pt-2">
                        <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-1">2. Administrator Account</h3>
                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Admin Full Name *</label>
                            <input type="text" wire:model="admin_name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Jane Smith">
                            @error('admin_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Admin Login Email *</label>
                            <input type="email" wire:model="admin_email" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. janesmith@mail.com">
                            @error('admin_email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Password *</label>
                                <input type="password" wire:model="admin_password" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="Min 6 chars">
                                @error('admin_password') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Confirm Password *</label>
                                <input type="password" wire:model="admin_password_confirmation" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full flex items-center justify-center rounded-xl bg-indigo-600 hover:bg-indigo-755 text-white py-3 font-semibold text-sm shadow-md transition-all cursor-pointer">
                            Submit Application
                        </button>
                    </div>

                    <p class="text-center text-xs text-slate-455 mt-4">
                        Already have an approved hotel? <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline">Sign In</a>
                    </p>
                </form>
            @endif
        </div>
    </div>
</div>
