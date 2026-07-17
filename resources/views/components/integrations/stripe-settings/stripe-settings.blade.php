<div class="space-y-6">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Stripe Integration</h1>
        <p class="text-sm text-slate-500 mt-0.5">Collect secure credit card payments for reservations and invoices via Stripe</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Configuration Form --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">Stripe API Credentials</h2>
            
            <form wire:submit.prevent="saveSettings" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Stripe Publishable Key</label>
                    <input type="text" wire:model="stripe_publishable_key" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="pk_test_...">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Stripe Secret Key</label>
                    <input type="password" wire:model="stripe_secret_key" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="sk_test_...">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Webhook Secret (for receiving instant payment events)</label>
                    <input type="text" wire:model="stripe_webhook_secret" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="whsec_...">
                </div>

                <div class="border-t border-slate-100 pt-4 mt-6 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="stripe_enabled" wire:model="stripe_enabled" class="rounded border-slate-350 text-indigo-600 focus:ring-indigo-500">
                        <label for="stripe_enabled" class="text-xs font-bold text-slate-700 cursor-pointer">Enable Stripe Checkout</label>
                    </div>

                    <button type="submit" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-755 text-white font-semibold text-sm cursor-pointer shadow-md">
                        Save Configurations
                    </button>
                </div>
            </form>
        </div>

        {{-- Test Charge Simulator Box --}}
        <div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Stripe Simulator</h3>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">Testing Tools</span>
                </div>

                <div class="space-y-4 text-xs">
                    <p class="text-slate-455 leading-relaxed">Simulate webhooks and card payments by targeting an existing outstanding (unpaid) invoice in the system.</p>

                    @if(count($invoicesList) > 0)
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Select Unpaid Invoice</label>
                        <select wire:model.live="test_invoice_id" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-1.5 focus:outline-none focus:border-indigo-500">
                            @foreach($invoicesList as $inv)
                            <option value="{{ $inv->id }}">{{ $inv->invoice_number }} (${{ number_format($inv->amount, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3">
                        <div class="flex justify-between">
                            <span class="text-slate-450">Simulated Charge Amount:</span>
                            <span class="font-extrabold text-slate-800">${{ number_format($test_amount, 2) }}</span>
                        </div>
                    </div>

                    <button wire:click="runTestCharge" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 rounded-lg shadow-md transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                        <i class="fab fa-stripe"></i> Execute Test Charge
                    </button>
                    @else
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-center">
                        <i class="fas fa-file-invoice text-2xl text-slate-300 mb-1"></i>
                        <p class="text-[10px] text-slate-450">No unpaid invoices found in the system to run a simulation.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
