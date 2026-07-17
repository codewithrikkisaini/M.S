<div>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Billing & Subscriptions</h1>
        <p class="text-sm text-slate-500 mt-0.5">Manage your hotel's subscription plan, view invoice details, and inspect system limits</p>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        {{-- Left: Current Active Plan Details & Usage (2 cols wide on large screens) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Active Subscription Info Card --}}
            <div class="bg-gradient-to-br from-slate-900 to-slate-950 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden border border-slate-800/80">
                <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none transform translate-x-12 translate-y-12">
                    <i class="fas fa-hotel text-[180px]"></i>
                </div>
                
                <div class="flex justify-between items-start">
                    <div>
                        <span class="bg-indigo-500/10 text-indigo-400 text-[10px] font-black tracking-widest uppercase px-3 py-1 rounded-full border border-indigo-500/20">
                            Current Package
                        </span>
                        <h2 class="text-2xl font-black tracking-tight mt-3">
                            {{ $activeSub ? $activeSub->plan->name : 'No Active Plan' }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">
                            @if($activeSub)
                                Status: <span class="text-emerald-400 font-extrabold capitalize">{{ $activeSub->status }}</span>
                            @else
                                Please select a plan to activate system features.
                            @endif
                        </p>
                    </div>

                    @if($activeSub)
                    <div class="text-right">
                        <div class="text-2xl font-black">${{ number_format($activeSub->plan->price, 2) }}</div>
                        <div class="text-[10px] text-slate-450 uppercase tracking-widest mt-0.5">
                            billed {{ $activeSub->plan->billing_cycle }}
                        </div>
                    </div>
                    @endif
                </div>

                @if($activeSub)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 border-t border-slate-800/60 pt-6">
                    <div>
                        <span class="text-[10px] text-slate-450 uppercase font-bold tracking-wider">Subscription Started</span>
                        <p class="text-sm font-bold text-slate-205 mt-0.5">
                            {{ $activeSub->starts_at->format('d F Y') }}
                        </p>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-450 uppercase font-bold tracking-wider">Subscription Renews/Expires</span>
                        <p class="text-sm font-bold text-slate-205 mt-0.5">
                            @if($activeSub->ends_at)
                                {{ $activeSub->ends_at->format('d F Y') }}
                            @else
                                <span class="text-indigo-400 font-extrabold">Lifetime Access</span>
                            @endif
                        </p>
                    </div>
                </div>
                @endif
            </div>

            {{-- System Usage Limits --}}
            <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
                <h3 class="text-base font-extrabold text-slate-850 mb-5 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-indigo-500"></i> Plan Usage & Limits
                </h3>

                <div class="space-y-6">
                    {{-- Rooms Limit --}}
                    <div>
                        <div class="flex justify-between items-baseline mb-2">
                            <div>
                                <span class="text-sm font-bold text-slate-700">Property Rooms Managed</span>
                                <p class="text-[10px] text-slate-400 mt-0.5">Total count of rooms defined in the system</p>
                            </div>
                            <span class="text-xs font-black text-slate-800">
                                {{ $roomsCount }} / {{ ($activeSub && $activeSub->plan->max_rooms) ? $activeSub->plan->max_rooms : 'Unlimited' }}
                            </span>
                        </div>
                        @php
                            $roomsPercent = 0;
                            if ($activeSub && $activeSub->plan->max_rooms) {
                                $roomsPercent = min(100, ($roomsCount / $activeSub->plan->max_rooms) * 100);
                            }
                        @endphp
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ $roomsPercent }}%"></div>
                        </div>
                    </div>

                    {{-- Users Limit --}}
                    <div>
                        <div class="flex justify-between items-baseline mb-2">
                            <div>
                                <span class="text-sm font-bold text-slate-700">Team Users & Accounts</span>
                                <p class="text-[10px] text-slate-400 mt-0.5">Total receptionist and admin staff accounts</p>
                            </div>
                            <span class="text-xs font-black text-slate-800">
                                {{ $usersCount }} / {{ ($activeSub && $activeSub->plan->max_users) ? $activeSub->plan->max_users : 'Unlimited' }}
                            </span>
                        </div>
                        @php
                            $usersPercent = 0;
                            if ($activeSub && $activeSub->plan->max_users) {
                                $usersPercent = min(100, ($usersCount / $activeSub->plan->max_users) * 100);
                            }
                        @endphp
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ $usersPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right: Quick Invoice History Summary --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-850 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-indigo-500"></i> Past Billing Invoices
                </h3>

                <div class="space-y-3 max-h-[300px] overflow-y-auto no-scrollbar">
                    @forelse($invoices as $inv)
                    <div class="p-3 bg-slate-50/70 border border-slate-100 rounded-xl flex items-center justify-between">
                        <div>
                            <div class="text-xs font-extrabold text-slate-800">Invoice #{{ $inv->invoice_number }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Date: {{ $inv->billing_date->format('d M Y') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-black text-slate-800">${{ number_format($inv->amount, 2) }}</div>
                            <button wire:click="viewInvoiceDetails({{ $inv->id }})" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-bold transition-colors cursor-pointer mt-1 block">
                                View / Print
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 py-6 text-center">No billing history found.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- Upgrade Plans Selection Grid --}}
    <div class="mb-8">
        <h2 class="text-lg font-black text-slate-900 mb-5">Change or Upgrade Subscription Plan</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($plans as $plan)
            @php
                $isCurrent = $activeSub && $activeSub->subscription_plan_id === $plan->id;
            @endphp
            <div class="bg-white rounded-2xl border {{ $isCurrent ? 'border-indigo-600 ring-2 ring-indigo-500/10' : 'border-slate-100' }} p-6 shadow-sm flex flex-col justify-between relative overflow-hidden">
                @if($isCurrent)
                <div class="absolute top-0 right-0 bg-indigo-600 text-white text-[9px] font-black px-3 py-1 rounded-bl-xl uppercase tracking-wider">
                    Current
                </div>
                @endif

                <div>
                    <h3 class="text-base font-extrabold text-slate-800">{{ $plan->name }}</h3>
                    <p class="text-xs text-slate-400 mt-1 capitalize">{{ $plan->billing_cycle }} billing</p>
                    
                    <div class="mt-4 flex items-baseline">
                        <span class="text-2xl font-black text-slate-900">${{ number_format($plan->price, 2) }}</span>
                        <span class="text-xs text-slate-400 ml-1">
                            @if($plan->billing_cycle === 'monthly') /mo @elseif($plan->billing_cycle === 'yearly') /yr @elseif($plan->billing_cycle === 'trial') /{{ $plan->trial_days }} days @else /one-time @endif
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 mt-4 min-h-[48px] line-clamp-3">{{ $plan->description }}</p>

                    <div class="border-t border-slate-50 pt-4 mt-4 space-y-2">
                        <div class="flex justify-between text-xs text-slate-650">
                            <span>Max Rooms:</span>
                            <span class="font-bold text-slate-800">{{ $plan->max_rooms ?: 'Unlimited' }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-slate-655">
                            <span>Max Users:</span>
                            <span class="font-bold text-slate-800">{{ $plan->max_users ?: 'Unlimited' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    @if($isCurrent)
                    <button class="w-full text-center py-2 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-lg border border-indigo-100 cursor-not-allowed" disabled>
                        Your Current Plan
                    </button>
                    @else
                    <button wire:click="selectPlan({{ $plan->id }})" class="w-full text-center py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg cursor-pointer transition-colors shadow-sm shadow-indigo-600/5">
                        Upgrade / Choose Plan
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Upgrade Payment Simulation Modal --}}
    @if($showUpgradeModal && $selectedPlan)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-slate-100 animate-fadeIn duration-200">
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <h3 class="text-lg font-black text-slate-850">Simulate Payment Integration</h3>
                <button wire:click="closeUpgradeModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition-colors cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form wire:submit.prevent="processUpgrade" class="p-6 space-y-4">
                <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl">
                    <span class="text-[10px] font-bold text-indigo-650 uppercase tracking-widest">Plan Summary</span>
                    <div class="flex justify-between items-baseline mt-1.5">
                        <span class="text-sm font-bold text-slate-800">{{ $selectedPlan->name }}</span>
                        <span class="text-base font-black text-indigo-600">${{ number_format($selectedPlan->price, 2) }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Cardholder Name</label>
                    <input type="text" wire:model="cardName" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="John Doe">
                    @error('cardName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Card Number (16-19 digits)</label>
                    <input type="text" wire:model="cardNumber" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="4111 2222 3333 4444">
                    @error('cardNumber') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Expiry Date (MM/YY)</label>
                        <input type="text" wire:model="cardExpiry" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="12/28">
                        @error('cardExpiry') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">CVC / CVV</label>
                        <input type="text" wire:model="cardCvc" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="123">
                        @error('cardCvc') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6 mt-6">
                    <button type="button" wire:click="closeUpgradeModal" class="btn-secondary rounded-lg px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-650 font-semibold text-sm cursor-pointer transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm cursor-pointer shadow-md transition-colors">
                        Simulate Payment & Upgrade
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Printable Subscription Invoice Detail Modal --}}
    @if($showInvoiceModal && $selectedInvoice)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-slate-100 animate-fadeIn duration-200">
            <div class="flex items-center justify-between p-6 border-b border-slate-100 print:hidden">
                <h3 class="text-sm font-bold text-slate-800">Subscription Invoice Details</h3>
                <div class="flex items-center gap-2">
                    <button onclick="window.print()" class="btn-secondary rounded-lg px-3 py-1.5 border border-slate-200 hover:bg-slate-50 text-xs font-bold cursor-pointer transition-colors">
                        <i class="fas fa-print mr-1"></i> Print Invoice
                    </button>
                    <button wire:click="closeInvoiceModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition-colors cursor-pointer">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Printable Invoice Area --}}
            <div class="p-8 space-y-6" id="printable-invoice">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-lg font-black text-indigo-650 tracking-tight uppercase">SplitEase SaaS</span>
                        <p class="text-xs text-slate-500 mt-1">123 Cloud Avenue, Tech Suite 500<br>San Francisco, CA 94107<br>billing@splitease.com</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-black text-slate-805 uppercase">Invoice</span>
                        <p class="text-xs text-slate-600 font-extrabold mt-1">#{{ $selectedInvoice->invoice_number }}</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Date: {{ $selectedInvoice->billing_date->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-b border-slate-100 py-4 my-4">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Billed To:</span>
                        <p class="text-xs font-bold text-slate-800 mt-1">{{ $selectedInvoice->hotel->name }}</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $selectedInvoice->hotel->address ?? 'No Address Listed' }}</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">Email: {{ $selectedInvoice->hotel->email }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Payment Details:</span>
                        <p class="text-xs font-semibold text-slate-700 mt-1">Status: 
                            <span class="text-emerald-600 font-bold">{{ ucfirst($selectedInvoice->status) }}</span>
                        </p>
                        @if($selectedInvoice->paid_at)
                            <p class="text-[10px] text-slate-500 mt-0.5">Paid On: {{ $selectedInvoice->paid_at->format('d M Y H:i') }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">Method: {{ $selectedInvoice->payment_method }}</p>
                        @endif
                    </div>
                </div>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-[10px] font-bold text-slate-400 uppercase">
                            <th class="pb-2">Description</th>
                            <th class="pb-2 text-center">Billing Period</th>
                            <th class="pb-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-100 text-xs">
                            <td class="py-3">
                                <div class="font-bold text-slate-800">Subscription Plan - {{ $selectedInvoice->plan->name }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Full access to PMS platform features including room bookings, invoices, and analytics.</div>
                            </td>
                            <td class="py-3 text-center text-slate-600 capitalize">
                                {{ $selectedInvoice->plan->billing_cycle }}
                            </td>
                            <td class="py-3 text-right font-bold text-slate-800">
                                ${{ number_format($selectedInvoice->amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex justify-end pt-4">
                    <div class="w-1/2 text-right space-y-1.5">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Subtotal:</span>
                            <span class="font-bold text-slate-750">${{ number_format($selectedInvoice->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-500">Tax / VAT (0%):</span>
                            <span class="font-bold text-slate-750">$0.00</span>
                        </div>
                        <div class="flex justify-between text-sm border-t border-slate-150 pt-1.5">
                            <span class="font-black text-slate-900">Total Paid:</span>
                            <span class="font-black text-indigo-600">${{ number_format($selectedInvoice->amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-6 text-center">
                    <p class="text-[10px] text-slate-400">If you have any questions about this invoice, please contact billing@splitease.com</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
