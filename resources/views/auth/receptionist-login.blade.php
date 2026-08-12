<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reception Staff Login | {{ $hotelName ?? 'Lodgiko PMS' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif !important;
            background-color: #f8fafc !important;
        }
        .pms-label {
            color: #475569 !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }
        .pms-input {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
            border-radius: 0.75rem !important;
            padding-top: 0.625rem !important;
            padding-bottom: 0.625rem !important;
        }
        .pms-input::placeholder {
            color: #94a3b8 !important;
        }
        .pms-input:focus {
            border-color: #059669 !important;
            box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.2) !important;
        }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 font-sans antialiased overflow-x-hidden">

<div class="min-h-screen flex">

    {{-- ===== LEFT HERO PANEL ===== --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden" style="background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #0f172a 100%) !important; color: #ffffff !important;">

        {{-- Background decorative elements --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-20 w-72 h-72 bg-emerald-400 rounded-full blur-3xl"></div>
            <div class="absolute bottom-32 right-16 w-56 h-56 bg-teal-300 rounded-full blur-3xl"></div>
        </div>

        {{-- Grid pattern --}}
        <div class="absolute inset-0"
             style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.08) 1px, transparent 0); background-size: 32px 32px;"></div>

        <div class="relative z-10 flex flex-col justify-between p-12 w-full">
            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white text-emerald-700 rounded-2xl flex items-center justify-center shadow-lg font-black text-xl border border-white/20">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h3 class="text-white font-black text-xl tracking-tight">Reception Staff Portal</h3>
                    <p class="text-emerald-200 text-xs font-semibold">Front Desk & Hotel Operations</p>
                </div>
            </div>

            {{-- Center Content --}}
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full border border-white/10">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span class="text-white/90 text-sm font-medium">Hotel-Wise Reception Dashboard</span>
                </div>
                <h1 class="text-5xl font-bold text-white leading-tight">
                    Reception Desk<br>Control Center.
                </h1>
                <p class="text-lg text-emerald-100 leading-relaxed max-w-sm">
                    Manage guest check-ins, check-outs, room statuses, and reservations for your assigned hotel.
                </p>

                {{-- Feature pills --}}
                <div class="flex flex-wrap gap-2 pt-2">
                    @foreach(['Guest Check-In', 'Check-Out', 'Live Floor Map', 'Daily Cash Sheet'] as $feat)
                    <span class="bg-white/10 backdrop-blur-sm text-white/90 text-xs font-medium px-3 py-1.5 rounded-full border border-white/10">
                        {{ $feat }}
                    </span>
                    @endforeach
                </div>
            </div>

            {{-- Bottom info --}}
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10 flex items-center gap-4">
                <div class="w-10 h-10 bg-emerald-500/30 text-white rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas fa-hotel text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-white">Multi-Tenant Hotel Isolation</p>
                    <p class="text-xs text-emerald-200">Automatically loads your hotel dashboard based on your staff login.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== RIGHT LOGIN FORM ===== --}}
    <div class="flex flex-1 items-center justify-center p-8 bg-white relative overflow-hidden">
        <div class="w-full max-w-sm relative z-10">

            {{-- Mobile logo --}}
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <div class="w-9 h-9 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-tie text-white"></i>
                </div>
                <span class="text-slate-800 font-bold text-xl">Reception Staff Login</span>
            </div>

            <div class="mb-8">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 mb-3">
                    <i class="fas fa-id-badge text-[10px]"></i> Front Desk Staff
                </div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Reception Staff Login</h2>
                <p class="mt-1.5 text-sm text-slate-550">Enter your hotel staff credentials to open your hotel dashboard</p>
            </div>

            @if(session('status'))
            <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-100 p-3.5 shadow-sm">
                <div class="flex gap-2.5">
                    <i class="fas fa-check-circle text-emerald-600 mt-0.5 shrink-0"></i>
                    <div class="text-sm text-emerald-700 font-medium">{{ session('status') }}</div>
                </div>
            </div>
            @endif

            @if(isset($errors) && $errors->any())
            <div class="mb-5 rounded-xl bg-red-50 border border-red-100 p-3.5 shadow-sm">
                <div class="flex gap-2.5">
                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5 shrink-0"></i>
                    <div class="text-sm text-red-700 font-medium">{{ $errors->first() }}</div>
                </div>
            </div>
            @endif

            <form action="{{ route('receptionist.login.post') }}" method="post" x-data="{ showPassword: false }" class="space-y-5" id="receptionist-login-form">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="pms-label">Receptionist Email</label>
                    <div class="relative mt-1">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               autocomplete="email"
                               required
                               class="pms-input !pl-9 @error('email') border-red-500 @enderror w-full"
                               placeholder="you@hotel.com">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="pms-label mb-0">Password</label>
                    </div>
                    <div class="relative mt-1">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                        <input id="password"
                               :type="showPassword ? 'text' : 'password'"
                               name="password"
                               value=""
                               autocomplete="current-password"
                               required
                               class="pms-input !pl-9 pr-10 w-full"
                               placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                            <i class="fas text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt text-sm"></i>
                    Open Reception Staff Dashboard
                </button>

                {{-- Demo Multi-Hotel Receptionists --}}
                <div class="mt-4 p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl text-left">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <i class="fas fa-hotel text-emerald-600"></i> Select Hotel Receptionist Demo:
                    </p>
                    <div class="space-y-1.5">
                        <button type="button" onclick="fillReceptionist('receptionist@merahkie.com', '123456')" class="w-full text-left px-2.5 py-1.5 bg-white hover:bg-emerald-50 rounded-lg border border-slate-200 hover:border-emerald-300 transition-all text-xs flex justify-between items-center group">
                            <span class="font-semibold text-slate-700 group-hover:text-emerald-800">🏢 Grand Plaza Hotel</span>
                            <span class="text-[10px] text-slate-400 font-mono">receptionist@merahkie.com</span>
                        </button>
                        <button type="button" onclick="fillReceptionist('receptionist2@sunsetbeach.com', '123456')" class="w-full text-left px-2.5 py-1.5 bg-white hover:bg-emerald-50 rounded-lg border border-slate-200 hover:border-emerald-300 transition-all text-xs flex justify-between items-center group">
                            <span class="font-semibold text-slate-700 group-hover:text-emerald-800">🏖️ Sunset Beach Resort</span>
                            <span class="text-[10px] text-slate-400 font-mono">receptionist2@sunsetbeach.com</span>
                        </button>
                        <button type="button" onclick="fillReceptionist('receptionist3@royalcrown.com', '123456')" class="w-full text-left px-2.5 py-1.5 bg-white hover:bg-emerald-50 rounded-lg border border-slate-200 hover:border-emerald-300 transition-all text-xs flex justify-between items-center group">
                            <span class="font-semibold text-slate-700 group-hover:text-emerald-800">👑 Royal Crown Hotel</span>
                            <span class="text-[10px] text-slate-400 font-mono">receptionist3@royalcrown.com</span>
                        </button>
                    </div>
                </div>

                <script>
                    function fillReceptionist(email, pass) {
                        document.getElementById('email').value = email;
                        document.getElementById('password').value = pass;
                        document.getElementById('receptionist-login-form').submit();
                    }
                </script>

                {{-- Link to Admin Login --}}
                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium inline-flex items-center gap-1 transition-colors">
                        <i class="fas fa-arrow-left text-[10px]"></i> Back to Main Admin Login
                    </a>
                </div>
            </form>

            <p class="mt-8 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} {{ $hotelName ?? 'Lodgiko PMS' }}. All rights reserved.
            </p>
        </div>
    </div>
</div>

</body>
</html>
