<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - Lodgiko</title>
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
            <div class="w-20 h-20 rounded-full bg-rose-500/10 border border-rose-500/30 text-rose-400 mx-auto flex items-center justify-center text-3xl font-bold mb-6">
                ❌
            </div>

            <h1 class="text-2xl font-bold text-white mb-2">Payment Cancelled</h1>
            <p class="text-slate-300 text-sm mb-6">
                Your payment process for invoice <strong>#{{ $invoice->invoice_number }}</strong> was cancelled or interrupted.
            </p>

            <p class="text-xs text-slate-400 leading-relaxed mb-8">
                Your hotel status remains <strong>Awaiting Payment</strong>. You can try paying again at any time using the link below.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('billing.pay', $invoice->invoice_number) }}" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm transition">Try Paying Again</a>
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-medium text-sm transition">Return to Login</a>
            </div>
        </div>
    </main>

    <footer class="border-t border-slate-800 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Lodgiko.com. All rights reserved.
    </footer>
</body>
</html>
