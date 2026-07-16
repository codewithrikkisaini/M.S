<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $hotelName }} | Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans antialiased">

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
                <img src="{{ asset('images/logo.png') }}" alt="{{ $hotelName }}" class="h-16 w-auto object-contain bg-white p-1.5 rounded-2xl shadow-sm">
            </div>

            {{-- Center Content --}}
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-full">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                    <span class="text-white/80 text-sm font-medium">Property Management System</span>
                </div>
                <h1 class="text-5xl font-bold text-white leading-tight">
                    Manage your<br>hotel smarter.
                </h1>
                <p class="text-lg text-indigo-200 leading-relaxed max-w-sm">
                    Streamline reservations, housekeeping, and guest experiences from a single dashboard.
                </p>

                {{-- Feature pills --}}
                <div class="flex flex-wrap gap-2 pt-2">
                    @foreach(['Reservations', 'Booking Calendar', 'Housekeeping', 'Revenue Reports'] as $feat)
                    <span class="bg-white/10 backdrop-blur-sm text-white/80 text-xs font-medium px-3 py-1.5 rounded-full border border-white/10">
                        {{ $feat }}
                    </span>
                    @endforeach
                </div>
            </div>

            {{-- Bottom stats --}}
            <div class="grid grid-cols-3 gap-4">
                @foreach([['100+', 'Rooms Managed'], ['24/7', 'Operations'], ['99.9%', 'Uptime']] as $stat)
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/10">
                    <p class="text-2xl font-bold text-white">{{ $stat[0] }}</p>
                    <p class="text-xs text-indigo-300 mt-0.5">{{ $stat[1] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== RIGHT LOGIN FORM ===== --}}
    <div class="flex flex-1 items-center justify-center p-8 bg-white">
        <div class="w-full max-w-sm">

            {{-- Mobile logo --}}
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-hotel text-white"></i>
                </div>
                <span class="text-gray-900 font-bold text-xl">{{ $hotelName }}</span>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
                <p class="mt-1.5 text-sm text-gray-500">Sign in to your admin panel</p>
            </div>

            @if($errors->any())
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 p-3.5">
                <div class="flex gap-2.5">
                    <i class="fas fa-exclamation-circle text-red-400 mt-0.5 shrink-0"></i>
                    <div class="text-sm text-red-700">{{ $errors->first() }}</div>
                </div>
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="post" x-data="{ showPassword: false }" class="space-y-5" id="login-form">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="pms-label">Email address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               autocomplete="email"
                               class="pms-input !pl-9 @error('email') border-red-400 @enderror"
                               placeholder="you@example.com">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="pms-label mb-0">Password</label>
                        <a href="#" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input id="password"
                               :type="showPassword ? 'text' : 'password'"
                               name="password"
                               value=""
                               autocomplete="current-password"
                               class="pms-input !pl-9 pr-10"
                               placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center gap-2.5">
                    <input id="remember" type="checkbox" name="remember"
                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    <label for="remember" class="text-sm text-gray-600 cursor-pointer select-none">Remember me</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full btn-primary justify-center py-2.5">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In to Dashboard
                </button>

                {{-- Quick Login Buttons --}}
                <div class="pt-4 border-t border-slate-100 mt-6">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest mb-3 text-center">Quick Login (Demo)</p>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="document.getElementById('email').value = 'superadmin@merahkie.com'; document.getElementById('password').value = '123456'; document.getElementById('login-form').submit();"
                                class="flex flex-col items-center justify-center gap-1.5 p-2 border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50 rounded-lg text-[10px] font-bold text-slate-700 hover:text-indigo-600 transition-all cursor-pointer">
                            <i class="fas fa-crown text-indigo-600 text-sm"></i> Super Admin
                        </button>
                        <button type="button" onclick="document.getElementById('email').value = 'admin@merahkie.com'; document.getElementById('password').value = '123456'; document.getElementById('login-form').submit();"
                                class="flex flex-col items-center justify-center gap-1.5 p-2 border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50 rounded-lg text-[10px] font-bold text-slate-700 hover:text-indigo-600 transition-all cursor-pointer">
                            <i class="fas fa-user-shield text-indigo-500 text-sm"></i> Hotel Admin
                        </button>
                        <button type="button" onclick="document.getElementById('email').value = 'receptionist@merahkie.com'; document.getElementById('password').value = '123456'; document.getElementById('login-form').submit();"
                                class="flex flex-col items-center justify-center gap-1.5 p-2 border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50 rounded-lg text-[10px] font-bold text-slate-700 hover:text-indigo-600 transition-all cursor-pointer">
                            <i class="fas fa-user text-indigo-500 text-sm"></i> Receptionist
                        </button>
                    </div>
                </div>

                {{-- Public Hotel Registration Link --}}
                <div class="mt-5 text-center">
                    <p class="text-xs text-slate-500">
                        Want to list your property? 
                        <a href="/register-hotel" class="text-indigo-600 hover:text-indigo-700 font-semibold hover:underline">Register your Hotel here</a>
                    </p>
                </div>
            </form>

            <p class="mt-8 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ $hotelName }}. All rights reserved.
            </p>
        </div>
    </div>
</div>

</body>
</html>
