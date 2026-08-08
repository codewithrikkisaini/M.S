<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Subscription Invoice #{{ $invoice->invoice_number }} - Lodgiko</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id={{ $clientId !== 'test' ? $clientId : 'sb' }}&currency={{ $currency }}"></script>
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
            <span class="text-xs font-semibold uppercase text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-full border border-indigo-500/20">Secure Checkout</span>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center py-12 px-4">
        <div class="max-w-lg w-full bg-slate-800/90 border border-slate-700 rounded-2xl p-8 shadow-2xl backdrop-blur space-y-6">
            
            <div class="text-center border-b border-slate-700/80 pb-6">
                <p class="text-xs uppercase tracking-wider font-semibold text-indigo-400">Lodgiko Subscription Payment</p>
                <h1 class="text-2xl font-bold text-white mt-1">{{ $invoice->hotel->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">Hotel ID: <code>{{ $invoice->hotel->hotel_code }}</code></p>
            </div>

            <!-- Invoice Summary Box -->
            <div class="bg-slate-900/90 border border-slate-700/80 rounded-xl p-5 space-y-3">
                <div class="flex justify-between items-center text-sm border-b border-slate-800 pb-2">
                    <span class="text-slate-400">Invoice Number:</span>
                    <span class="font-mono font-bold text-indigo-400">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex justify-between items-center text-sm border-b border-slate-800 pb-2">
                    <span class="text-slate-400">Billing Date:</span>
                    <span class="text-slate-200">{{ $invoice->billing_date->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm border-b border-slate-800 pb-2">
                    <span class="text-slate-400">Due Date:</span>
                    <span class="text-slate-200">{{ $invoice->due_date->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-center text-base pt-1">
                    <span class="font-semibold text-slate-200">Total Amount Due:</span>
                    <span class="font-bold text-emerald-400 text-xl">{{ $currency }} {{ number_format($invoice->amount, 2) }}</span>
                </div>
            </div>

            <!-- PayPal Container -->
            <div class="space-y-4">
                <div id="paypal-button-container" class="w-full"></div>

                <!-- Simulation Sandbox Fallback Button -->
                <form action="{{ route('billing.paypal.capture.api', $invoice->invoice_number) }}" method="POST" id="sandbox-form" class="pt-2">
                    @csrf
                    <input type="hidden" name="orderID" value="SANDBOX-ORDER-{{ time() }}">
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition flex items-center justify-center space-x-2">
                        <span>💳 Instant PayPal Sandbox Payment (Test Demo)</span>
                    </button>
                </form>
            </div>

            <p class="text-xs text-center text-slate-500">
                🔒 Transactions are encrypted and processed securely. Server-side verification enabled.
            </p>
        </div>
    </main>

    <footer class="border-t border-slate-800 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Lodgiko.com. All rights reserved.
    </footer>

    <script>
        if (typeof paypal !== 'undefined') {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return fetch('/billing/paypal/' + @json($invoice->invoice_number) + '/create-order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(function(res) {
                        return res.json();
                    }).then(function(orderData) {
                        return orderData.id;
                    });
                },
                onApprove: function(data, actions) {
                    return fetch('/billing/paypal/' + @json($invoice->invoice_number) + '/capture-order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ orderID: data.orderID })
                    }).then(function(res) {
                        return res.json();
                    }).then(function(details) {
                        if (details.redirect) {
                            window.location.href = details.redirect;
                        } else {
                            alert('Payment completed successfully!');
                            window.location.reload();
                        }
                    });
                }
            }).render('#paypal-button-container');
        }
    </script>
</body>
</html>
