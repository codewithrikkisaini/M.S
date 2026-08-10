<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Submitted - Lodgiko Partner Network</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between antialiased selection:bg-blue-600 selection:text-white">

    <!-- Header Navigation -->
    <header class="border-b border-slate-200 bg-white sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-lg shadow-md group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-black tracking-tight text-slate-900 leading-none">LODGIKO</span>
                    <span class="text-[9px] font-extrabold tracking-widest text-blue-600 uppercase mt-0.5">PARTNER NETWORK</span>
                </div>
            </a>
            
            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-right-to-bracket"></i> Partner Sign In
            </a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-200/60 relative overflow-hidden">
            
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-500 mx-auto flex items-center justify-center text-2xl mb-4 shadow-sm">
                    <i class="fa-solid fa-hourglass-half animate-pulse"></i>
                </div>

                <span class="px-3.5 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold uppercase tracking-wider mb-2 inline-block">
                    Application Submitted
                </span>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Hotel Registration Record</h1>
                <p class="text-slate-500 text-sm mt-1">Thank you for submitting your hotel details. Here is your full registration summary.</p>
            </div>

            <!-- Complete Registered Hotel Record Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 mb-8 text-xs space-y-6">
                
                <!-- Status Banner -->
                <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
                    <div>
                        <span class="text-slate-400 uppercase font-extrabold text-[10px] tracking-wider block">HOTEL CODE</span>
                        <span class="font-mono font-black text-blue-600 text-base">{{ $code ?? ($hotel->hotel_code ?? 'LDG-000000') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 uppercase font-extrabold text-[10px] tracking-wider block">ACCOUNT STATUS</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200 inline-flex items-center gap-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> Pending SuperAdmin Approval
                        </span>
                    </div>
                </div>

                <!-- Section 1: Business & Legal Details -->
                <div>
                    <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] border-b border-slate-200 pb-2 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-building text-blue-600"></i> Business & Tax Details
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-700">
                        <div>
                            <span class="text-slate-400 font-medium block">Hotel Display Name:</span>
                            <strong class="text-slate-900 text-sm">{{ $hotel->name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Legal Company Name:</span>
                            <strong class="text-slate-900">{{ $hotel->business_name ?? 'N/A' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Owner / Representative:</span>
                            <strong class="text-slate-900">{{ $hotel->owner_name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Tax ID / GSTIN:</span>
                            <strong class="text-slate-900">{{ $hotel->tax_id ?? 'N/A' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Company Reg Number:</span>
                            <strong class="text-slate-900">{{ $hotel->company_reg_number ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Contact & Location -->
                <div>
                    <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] border-b border-slate-200 pb-2 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-blue-600"></i> Contact & Location
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-700">
                        <div>
                            <span class="text-slate-400 font-medium block">Work Email:</span>
                            <strong class="text-slate-900">{{ $hotel->email ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Phone Number:</span>
                            <strong class="text-slate-900">{{ $hotel->phone ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">WhatsApp:</span>
                            <strong class="text-slate-900">{{ $hotel->whatsapp ?? 'N/A' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Website:</span>
                            <strong class="text-slate-900">{{ $hotel->website ?? 'N/A' }}</strong>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 font-medium block">Property Address:</span>
                            <strong class="text-slate-900">
                                {{ implode(', ', array_filter([$hotel->address ?? null, $hotel->city ?? null, $hotel->state ?? null, $hotel->country ?? null])) ?: '-' }}
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Property Specs -->
                <div>
                    <h4 class="font-bold text-slate-900 uppercase tracking-wider text-[11px] border-b border-slate-200 pb-2 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-hotel text-blue-600"></i> Property Specifications
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-slate-700">
                        <div>
                            <span class="text-slate-400 font-medium block">Property Type:</span>
                            <strong class="text-slate-900">{{ $hotel->property_type ?? 'Hotel' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Total Rooms / Units:</span>
                            <strong class="text-slate-900">{{ $hotel->rooms_count ?? 10 }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Category / Rating:</span>
                            <strong class="text-slate-900">{{ $hotel->category ?? 'Standard' }}</strong>
                        </div>
                    </div>
                </div>

            </div>

            <p class="text-xs text-slate-500 leading-relaxed mb-8 bg-blue-50/60 p-4 rounded-xl border border-blue-100 text-left">
                <i class="fa-solid fa-circle-info text-blue-600 mr-1"></i> Our SuperAdmin team has been notified. Once approved by our administrator, your login credentials will be activated for full hotel PMS access.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('login') }}" class="px-6 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-md flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-blue-400"></i> Go to Sign In
                </a>
                <a href="/" class="px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-md shadow-blue-500/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-house"></i> Return Home
                </a>
            </div>
        </div>
    </main>

    <footer class="border-t border-slate-200 py-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} Merahkie Bookings. All rights reserved. Professional Hotel Onboarding Platform.
    </footer>

</body>
</html>
