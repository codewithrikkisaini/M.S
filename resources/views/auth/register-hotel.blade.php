<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Hotel - Lodgiko Partner Network</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-blue-pattern {
            background-color: #1E62EC;
            background-image: radial-gradient(rgba(255, 255, 255, 0.15) 1.5px, transparent 1.5px);
            background-size: 24px 24px;
        }
        .step-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased selection:bg-blue-600 selection:text-white">

    <div class="min-h-screen flex flex-col lg:flex-row w-full flex-grow">
        
        <!-- Left Column: Branding & Value Proposition -->
        <div class="lg:w-[42%] bg-blue-pattern text-white p-8 lg:p-14 flex flex-col justify-between relative overflow-hidden">
            <!-- Subtle background glows -->
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <!-- Header / Brand Logo -->
                <a href="/" class="inline-flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-white text-blue-600 flex items-center justify-center font-extrabold text-xl shadow-lg shadow-blue-900/20 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-black tracking-tight text-white leading-none">LODGIKO</span>
                        <span class="text-[10px] font-extrabold tracking-widest text-blue-200 uppercase mt-1">PARTNER NETWORK</span>
                    </div>
                </a>

                <!-- Hero Copy -->
                <div class="mt-14 lg:mt-20">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-bold uppercase tracking-wider mb-6">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        Join Our Network
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-black leading-[1.15] text-white tracking-tight">
                        List Your Property & Grow Your Bookings.
                    </h1>

                    <p class="text-blue-100 text-sm sm:text-base mt-4 leading-relaxed font-normal max-w-md">
                        Reach millions of travelers globally. Manage your inventory, get direct bookings, and increase your revenue.
                    </p>
                </div>

                <!-- Feature Highlights -->
                <div class="mt-10 lg:mt-14 space-y-4">
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15">
                        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white shrink-0 font-bold text-sm">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-white">ZERO SETUP FEES</h4>
                            <p class="text-xs text-blue-100/90 mt-0.5 leading-normal">Start listing for free and only pay a small commission on confirmed bookings.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15">
                        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white shrink-0 font-bold text-sm">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-white">POWERFUL DASHBOARD</h4>
                            <p class="text-xs text-blue-100/90 mt-0.5 leading-normal">Manage rooms, rates, and guest communication from a single interface.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer copyright -->
            <div class="relative z-10 pt-10 mt-8 border-t border-white/15 text-xs text-blue-200 font-medium">
                &copy; {{ date('Y') }} Merahkie Bookings.
            </div>
        </div>

        <!-- Right Column: Multi-step Onboarding Form -->
        <div class="lg:w-[58%] bg-white p-6 sm:p-10 lg:p-14 flex flex-col justify-between">
            <div class="max-w-2xl mx-auto w-full">
                
                <!-- Subtitle & Main Title -->
                <div class="mb-8">
                    <span class="text-xs font-black uppercase tracking-widest text-blue-600 block mb-1">PARTNER ONBOARDING</span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">Register Your Hotel</h2>
                    <p class="text-slate-500 text-sm mt-1">Complete this application to list your property on our network.</p>
                </div>

                <!-- 5-Step Stepper Navigation -->
                <div class="relative mb-10">
                    <div class="absolute top-4 left-0 w-full h-0.5 bg-slate-200 -z-0"></div>
                    <div id="stepperProgress" class="absolute top-4 left-0 h-0.5 bg-blue-600 transition-all duration-300 -z-0" style="width: 0%;"></div>

                    <div class="flex justify-between relative z-10">
                        <!-- Step 1 -->
                        <div class="step-indicator flex flex-col items-center cursor-pointer" onclick="goToStep(1)" data-step="1">
                            <div class="step-circle w-9 h-9 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center ring-4 ring-white shadow-sm transition-all">
                                1
                            </div>
                            <span class="step-label text-[10px] font-bold tracking-wider uppercase mt-2 text-blue-600">BUSINESS</span>
                        </div>

                        <!-- Step 2 -->
                        <div class="step-indicator flex flex-col items-center cursor-pointer" onclick="goToStep(2)" data-step="2">
                            <div class="step-circle w-9 h-9 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center ring-4 ring-white transition-all border border-slate-200">
                                2
                            </div>
                            <span class="step-label text-[10px] font-bold tracking-wider uppercase mt-2 text-slate-400">CONTACT</span>
                        </div>

                        <!-- Step 3 -->
                        <div class="step-indicator flex flex-col items-center cursor-pointer" onclick="goToStep(3)" data-step="3">
                            <div class="step-circle w-9 h-9 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center ring-4 ring-white transition-all border border-slate-200">
                                3
                            </div>
                            <span class="step-label text-[10px] font-bold tracking-wider uppercase mt-2 text-slate-400">LOCATION</span>
                        </div>

                        <!-- Step 4 -->
                        <div class="step-indicator flex flex-col items-center cursor-pointer" onclick="goToStep(4)" data-step="4">
                            <div class="step-circle w-9 h-9 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center ring-4 ring-white transition-all border border-slate-200">
                                4
                            </div>
                            <span class="step-label text-[10px] font-bold tracking-wider uppercase mt-2 text-slate-400">PROFILE</span>
                        </div>

                        <!-- Step 5 -->
                        <div class="step-indicator flex flex-col items-center cursor-pointer" onclick="goToStep(5)" data-step="5">
                            <div class="step-circle w-9 h-9 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center ring-4 ring-white transition-all border border-slate-200">
                                5
                            </div>
                            <span class="step-label text-[10px] font-bold tracking-wider uppercase mt-2 text-slate-400">ACCOUNT</span>
                        </div>
                    </div>
                </div>

                <!-- Server Side Errors (if any) -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-rose-800">
                            <i class="fa-solid fa-triangle-exclamation"></i> Submission Errors:
                        </div>
                        <ul class="list-disc list-inside pl-1 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Multi-Step Form -->
                <form action="{{ route('register-hotel.post') }}" method="POST" id="registerHotelForm">
                    @csrf

                    <!-- STEP 1: BUSINESS & TAX DETAILS -->
                    <div id="step-1" class="step-panel space-y-6">
                        <h3 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Business & Tax Details</h3>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Hotel Trade / Display Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="hotel_name" id="hotel_name" value="{{ old('hotel_name') }}" required placeholder="e.g. Grand Plaza Resort" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                            <span class="error-msg text-xs text-rose-600 mt-1 hidden">Please enter hotel display name.</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Legal / Company Name</label>
                                <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}" placeholder="e.g. Grand Plaza Hospitality LLC" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Owner Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name') }}" required placeholder="e.g. John Doe" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                                <span class="error-msg text-xs text-rose-600 mt-1 hidden">Please enter owner name.</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tax ID / GSTIN</label>
                                <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}" placeholder="e.g. TAX-98765432" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Company Reg Number</label>
                                <input type="text" name="company_reg_number" id="company_reg_number" value="{{ old('company_reg_number') }}" placeholder="e.g. REG-123456" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: CONTACT INFORMATION -->
                    <div id="step-2" class="step-panel space-y-6 hidden">
                        <h3 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Contact Information</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Work Email Address <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="owner@hotel.com" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                                <span class="error-msg text-xs text-rose-600 mt-1 hidden">Please enter a valid email address.</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phone Number <span class="text-rose-500">*</span></label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="+1 (234) 567-890" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                                <span class="error-msg text-xs text-rose-600 mt-1 hidden">Please enter phone number.</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">WhatsApp Number (Optional)</label>
                                <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}" placeholder="+1 (234) 567-890" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Property Website URL (Optional)</label>
                                <input type="text" name="website" id="website" value="{{ old('website') }}" placeholder="https://www.grandplaza.com" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: LOCATION & ADDRESS -->
                    <div id="step-3" class="step-panel space-y-6 hidden">
                        <h3 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Location & Address</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Country <span class="text-rose-500">*</span></label>
                                <input type="text" name="country" id="country" value="{{ old('country', 'United States') }}" required placeholder="e.g. United States" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                                <span class="error-msg text-xs text-rose-600 mt-1 hidden">Please enter country.</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">State / Province</label>
                                <input type="text" name="state" id="state" value="{{ old('state') }}" placeholder="e.g. New York" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">City <span class="text-rose-500">*</span></label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" required placeholder="e.g. New York City" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                                <span class="error-msg text-xs text-rose-600 mt-1 hidden">Please enter city.</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">ZIP / Postal Code</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" placeholder="e.g. 10001" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Street Address</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" placeholder="e.g. 123 Luxury Avenue, Suite 400" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                        </div>
                    </div>

                    <!-- STEP 4: PROPERTY PROFILE -->
                    <div id="step-4" class="step-panel space-y-6 hidden">
                        <h3 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Property Profile & Capacity</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Property Type</label>
                                <select name="property_type" id="property_type" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                                    <option value="Hotel" {{ old('property_type') == 'Hotel' ? 'selected' : '' }}>Hotel</option>
                                    <option value="Resort" {{ old('property_type') == 'Resort' ? 'selected' : '' }}>Resort</option>
                                    <option value="Boutique Hotel" {{ old('property_type') == 'Boutique Hotel' ? 'selected' : '' }}>Boutique Hotel</option>
                                    <option value="Hostel" {{ old('property_type') == 'Hostel' ? 'selected' : '' }}>Hostel</option>
                                    <option value="Apartment / Villa" {{ old('property_type') == 'Apartment / Villa' ? 'selected' : '' }}>Apartment / Villa</option>
                                    <option value="Guest House" {{ old('property_type') == 'Guest House' ? 'selected' : '' }}>Guest House</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Total Rooms / Units (Optional)</label>
                                <input type="number" name="rooms_count" id="rooms_count" value="{{ old('rooms_count', 15) }}" min="1" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Star Rating / Category</label>
                                <select name="category" id="category" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                                    <option value="3 Star">3 Star Hotel</option>
                                    <option value="4 Star">4 Star Hotel</option>
                                    <option value="5 Star">5 Star Luxury Resort</option>
                                    <option value="Boutique">Boutique Property</option>
                                    <option value="Budget">Budget / Standard</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Current PMS System (Optional)</label>
                                <input type="text" name="current_pms" id="current_pms" value="{{ old('current_pms') }}" placeholder="e.g. Opera, Cloudbeds, None" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 5: ACCOUNT CREDENTIALS -->
                    <div id="step-5" class="step-panel space-y-6 hidden">
                        <h3 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Admin Account Credentials</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="password" id="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                                <span class="error-msg text-xs text-rose-600 mt-1 hidden">Password must be at least 6 characters.</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Confirm Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-slate-900 text-sm font-medium focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 transition">
                                <span class="error-msg text-xs text-rose-600 mt-1 hidden">Passwords do not match.</span>
                            </div>
                        </div>

                        <!-- Summary Review Box -->
                        <div class="p-5 rounded-2xl bg-blue-50/70 border border-blue-100 text-slate-700 text-xs space-y-2.5">
                            <div class="font-bold text-slate-900 text-sm flex items-center justify-between">
                                <span><i class="fa-solid fa-clipboard-check text-blue-600 mr-1.5"></i> Registration Summary</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700 text-[10px] uppercase font-bold">Ready to Submit</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-slate-600 pt-1 border-t border-blue-100/80">
                                <div><span class="font-semibold text-slate-900">Hotel:</span> <span id="summaryHotelName" class="text-slate-700 font-medium">-</span></div>
                                <div><span class="font-semibold text-slate-900">Owner:</span> <span id="summaryOwnerName" class="text-slate-700 font-medium">-</span></div>
                                <div><span class="font-semibold text-slate-900">Email:</span> <span id="summaryEmail" class="text-slate-700 font-medium">-</span></div>
                                <div><span class="font-semibold text-slate-900">Location:</span> <span id="summaryLocation" class="text-slate-700 font-medium">-</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Action Buttons -->
                    <div class="pt-8 flex items-center justify-between gap-4 border-t border-slate-100 mt-8">
                        <button type="button" id="prevBtn" onclick="nextPrev(-1)" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition hidden">
                            <i class="fa-solid fa-arrow-left mr-2 text-xs"></i> Back
                        </button>

                        <div class="ml-auto">
                            <button type="button" id="nextBtn" onclick="nextPrev(1)" class="px-8 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                                Next Step <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>

                            <button type="submit" id="submitBtn" class="px-8 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition hidden flex items-center gap-2">
                                <i class="fa-solid fa-paper-plane text-xs"></i> Submit Registration
                            </button>
                        </div>
                    </div>
                </form>

            </div>

            <!-- Login Redirect Link -->
            <div class="mt-10 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
                Already have an approved property? 
                <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Sign In Here</a>
            </div>
        </div>

    </div>

    <!-- JavaScript Multi-Step Navigation & Client Validation -->
    <script>
        let currentTab = 1;
        showTab(currentTab);

        function showTab(n) {
            const tabs = document.getElementsByClassName("step-panel");
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.add("hidden");
            }

            const targetTab = document.getElementById("step-" + n);
            if (targetTab) {
                targetTab.classList.remove("hidden");
            }

            // Button visibility
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            const submitBtn = document.getElementById("submitBtn");

            if (n === 1) {
                prevBtn.classList.add("hidden");
            } else {
                prevBtn.classList.remove("hidden");
            }

            if (n === 5) {
                nextBtn.classList.add("hidden");
                submitBtn.classList.remove("hidden");
                updateSummary();
            } else {
                nextBtn.classList.remove("hidden");
                submitBtn.classList.add("hidden");
            }

            updateStepperUI(n);
        }

        function nextPrev(n) {
            if (n === 1 && !validateForm(currentTab)) return false;

            currentTab += n;
            if (currentTab > 5) {
                document.getElementById("registerHotelForm").submit();
                return false;
            }
            showTab(currentTab);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function goToStep(step) {
            if (step < currentTab) {
                currentTab = step;
                showTab(currentTab);
            } else if (step > currentTab) {
                for (let i = currentTab; i < step; i++) {
                    if (!validateForm(i)) return false;
                }
                currentTab = step;
                showTab(currentTab);
            }
        }

        function validateForm(tabNum) {
            let valid = true;
            const currentPanel = document.getElementById("step-" + tabNum);
            if (!currentPanel) return true;

            const requiredInputs = currentPanel.querySelectorAll("[required]");
            
            requiredInputs.forEach(input => {
                const errorMsg = input.nextElementSibling;
                if (!input.value.trim()) {
                    input.classList.add("border-rose-500", "focus:ring-rose-500/20");
                    input.classList.remove("border-slate-200");
                    if (errorMsg && errorMsg.classList.contains("error-msg")) {
                        errorMsg.classList.remove("hidden");
                    }
                    valid = false;
                } else {
                    input.classList.remove("border-rose-500", "focus:ring-rose-500/20");
                    input.classList.add("border-slate-200");
                    if (errorMsg && errorMsg.classList.contains("error-msg")) {
                        errorMsg.classList.add("hidden");
                    }
                }
            });

            // Specific validations
            if (tabNum === 2) {
                const emailInput = document.getElementById("email");
                if (emailInput && emailInput.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailInput.value.trim())) {
                        emailInput.classList.add("border-rose-500");
                        const errorMsg = emailInput.nextElementSibling;
                        if (errorMsg) errorMsg.classList.remove("hidden");
                        valid = false;
                    }
                }
            }

            if (tabNum === 5) {
                const pwd = document.getElementById("password");
                const pwdConfirm = document.getElementById("password_confirmation");

                if (pwd && pwd.value.length < 6) {
                    pwd.classList.add("border-rose-500");
                    const errorMsg = pwd.nextElementSibling;
                    if (errorMsg) errorMsg.classList.remove("hidden");
                    valid = false;
                }

                if (pwd && pwdConfirm && pwd.value !== pwdConfirm.value) {
                    pwdConfirm.classList.add("border-rose-500");
                    const errorMsg = pwdConfirm.nextElementSibling;
                    if (errorMsg) errorMsg.classList.remove("hidden");
                    valid = false;
                }
            }

            return valid;
        }

        function updateStepperUI(activeStep) {
            const indicators = document.querySelectorAll(".step-indicator");
            const progressBar = document.getElementById("stepperProgress");

            const progressWidth = ((activeStep - 1) / 4) * 100;
            if (progressBar) progressBar.style.width = progressWidth + "%";

            indicators.forEach(ind => {
                const stepNum = parseInt(ind.getAttribute("data-step"));
                const circle = ind.querySelector(".step-circle");
                const label = ind.querySelector(".step-label");

                if (stepNum === activeStep) {
                    circle.className = "step-circle w-9 h-9 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center ring-4 ring-white shadow-sm transition-all";
                    circle.innerHTML = stepNum;
                    label.className = "step-label text-[10px] font-bold tracking-wider uppercase mt-2 text-blue-600";
                } else if (stepNum < activeStep) {
                    circle.className = "step-circle w-9 h-9 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center ring-4 ring-white shadow-sm transition-all";
                    circle.innerHTML = '<i class="fa-solid fa-check text-xs"></i>';
                    label.className = "step-label text-[10px] font-bold tracking-wider uppercase mt-2 text-slate-700";
                } else {
                    circle.className = "step-circle w-9 h-9 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center ring-4 ring-white transition-all border border-slate-200";
                    circle.innerHTML = stepNum;
                    label.className = "step-label text-[10px] font-bold tracking-wider uppercase mt-2 text-slate-400";
                }
            });
        }

        function updateSummary() {
            document.getElementById("summaryHotelName").innerText = document.getElementById("hotel_name").value || "-";
            document.getElementById("summaryOwnerName").innerText = document.getElementById("owner_name").value || "-";
            document.getElementById("summaryEmail").innerText = document.getElementById("email").value || "-";
            const city = document.getElementById("city").value || "";
            const country = document.getElementById("country").value || "";
            document.getElementById("summaryLocation").innerText = (city && country) ? `${city}, ${country}` : (city || country || "-");
        }
    </script>
</body>
</html>
