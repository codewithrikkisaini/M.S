<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Hotel - Lodgiko PMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white">

    <!-- Navbar -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/30">
                    L
                </div>
                <span class="text-xl font-bold tracking-tight text-white">Lodgiko<span class="text-indigo-400">.com</span></span>
            </a>
            <div class="flex items-center space-x-4 text-sm">
                <span class="text-slate-400 hidden sm:inline">Already registered?</span>
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium transition">Login</a>
            </div>
        </div>
    </header>

    <!-- Main Registration Section -->
    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full bg-slate-800/80 border border-slate-700/60 rounded-2xl p-8 sm:p-10 shadow-2xl backdrop-blur">
            <div class="text-center mb-8">
                <span class="px-3 py-1 text-xs font-semibold uppercase tracking-wider text-indigo-400 bg-indigo-500/10 rounded-full border border-indigo-500/20">Hotel Onboarding</span>
                <h1 class="text-3xl font-bold text-white mt-3">Register Your Hotel</h1>
                <p class="text-slate-400 mt-2 text-sm">Fill in your hotel details below. Your account will be submitted for Super Admin review.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register-hotel.post') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Hotel Name & Owner Name -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Hotel Name <span class="text-rose-400">*</span></label>
                        <input type="text" name="hotel_name" value="{{ old('hotel_name') }}" required placeholder="e.g. Grand Samarkand Hotel" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Contact Person / Owner <span class="text-rose-400">*</span></label>
                        <input type="text" name="owner_name" value="{{ old('owner_name') }}" required placeholder="e.g. John Doe" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <!-- Email & Phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Work Email <span class="text-rose-400">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="owner@hotel.com" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Phone Number <span class="text-rose-400">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="+1 234 567 890" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <!-- Location: Country & City -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Country <span class="text-rose-400">*</span></label>
                        <input type="text" name="country" value="{{ old('country', 'United States') }}" required placeholder="e.g. United States" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">City <span class="text-rose-400">*</span></label>
                        <input type="text" name="city" value="{{ old('city') }}" required placeholder="e.g. New York" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <!-- Property Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Property Type</label>
                        <select name="property_type" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                            <option value="Hotel">Hotel</option>
                            <option value="Resort">Resort</option>
                            <option value="Boutique Hotel">Boutique Hotel</option>
                            <option value="Hostel">Hostel</option>
                            <option value="Apartment / Villa">Apartment / Villa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Total Rooms</label>
                        <input type="number" name="rooms_count" value="{{ old('rooms_count', 15) }}" min="1" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <!-- Password & Confirmation -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Password <span class="text-rose-400">*</span></label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Confirm Password <span class="text-rose-400">*</span></label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 px-6 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold text-base shadow-lg shadow-indigo-500/25 transition duration-200">
                        Submit Hotel For Approval
                    </button>
                </div>

                <p class="text-xs text-center text-slate-400 mt-4">
                    By submitting, your registration will be reviewed by the Lodgiko Admin team. Initial status will be <span class="text-amber-400 font-semibold">Pending Approval</span>.
                </p>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Lodgiko.com. All rights reserved.
    </footer>
</body>
</html>
