<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Submitted - Lodgiko PMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col justify-between">

    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/30">
                    L
                </div>
                <span class="text-xl font-bold tracking-tight text-white">Lodgiko<span class="text-indigo-400">.com</span></span>
            </a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center py-16 px-4">
        <div class="max-w-lg w-full bg-slate-800/90 border border-slate-700 rounded-2xl p-8 text-center shadow-2xl backdrop-blur">
            <div class="w-20 h-20 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 mx-auto flex items-center justify-center text-3xl font-bold mb-6">
                ⏳
            </div>

            <h1 class="text-2xl font-bold text-white mb-2">Registration Received!</h1>
            <p class="text-slate-300 text-sm mb-6">
                Thank you for registering <strong>{{ $hotel->name ?? 'your hotel' }}</strong> with Lodgiko.
            </p>

            <div class="bg-slate-900/80 border border-slate-700/80 rounded-xl p-5 mb-6 text-left text-sm space-y-3">
                <div class="flex justify-between border-b border-slate-800 pb-2">
                    <span class="text-slate-400">Generated Hotel ID:</span>
                    <span class="font-mono font-bold text-indigo-400">{{ $code ?? ($hotel->hotel_code ?? 'LDG-000000') }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-800 pb-2">
                    <span class="text-slate-400">Account Status:</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">Pending Approval</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Owner Email:</span>
                    <span class="font-medium text-slate-200">{{ $hotel->email ?? 'Registered Email' }}</span>
                </div>
            </div>

            <p class="text-xs text-slate-400 leading-relaxed mb-8">
                Our Lodgiko Super Admin team has been notified. You will receive an email confirmation once your account is reviewed and approved (with trial or subscription details).
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-medium text-sm transition">Return to Login</a>
                <a href="/" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm transition">Back to Home</a>
            </div>
        </div>
    </main>

    <footer class="border-t border-slate-800 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Lodgiko.com. All rights reserved.
    </footer>
</body>
</html>
