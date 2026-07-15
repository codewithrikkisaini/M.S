<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">System Settings</h1>
        <p class="text-sm text-gray-500 mt-0.5">Configure hotel system preferences, branding, notifications, and SMTP integrations</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Tab sidebar --}}
        <div class="lg:w-60 shrink-0">
            <div class="pms-card shadow-sm border border-slate-100/80 p-2">
                @foreach([
                    ['hotel', 'fas fa-hotel', 'Hotel Info'],
                    ['preferences', 'fas fa-sliders-h', 'Preferences'],
                    ['notifications', 'fas fa-bell', 'Notifications'],
                    ['invoice', 'fas fa-file-invoice', 'Invoice Prefix'],
                    ['email', 'fas fa-envelope', 'Email (SMTP)']
                ] as [$tab, $icon, $label])
                <button wire:click="setTab('{{ $tab }}')"
                        class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-xs font-bold transition-all cursor-pointer mb-0.5 last:mb-0 {{ $activeTab === $tab ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                    <i class="{{ $icon }} w-4 text-center text-sm"></i>
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Tab content --}}
        <div class="flex-1">
            @if($activeTab === 'hotel')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-hotel text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Hotel Information</h3>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Hotel Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="hotel_name" class="pms-input text-xs" placeholder="Grand Hotel">
                        @error('hotel_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Phone Number</label>
                        <input type="text" wire:model="hotel_phone" class="pms-input text-xs" placeholder="+1 234 567 8900">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Email Address</label>
                        <input type="email" wire:model="hotel_email" class="pms-input text-xs" placeholder="info@hotel.com">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Website URL</label>
                        <input type="url" wire:model="hotel_website" class="pms-input text-xs" placeholder="https://hotel.com">
                    </div>
                    <div class="col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Address</label>
                        <textarea wire:model="hotel_address" rows="2" class="pms-input text-xs resize-none rounded-lg border border-slate-200" placeholder="Full residential address..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="saveHotel" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer shadow-sm">
                        <i class="fas fa-save text-[10px]"></i> Save Hotel Info
                    </button>
                </div>
            </div>

            @elseif($activeTab === 'preferences')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-sliders-h text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Preferences</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">System Currency</label>
                        <select wire:model="currency" class="pms-select text-xs">
                            <option>USD</option><option>EUR</option><option>GBP</option><option>INR</option>
                        </select>
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Date Format</label>
                        <select wire:model="date_format" class="pms-select text-xs">
                            <option value="d M Y">25 Jan 2026</option>
                            <option value="Y-m-d">2026-01-25</option>
                            <option value="m/d/Y">01/25/2026</option>
                        </select>
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Default Check-In Time</label>
                        <input type="time" wire:model="checkin_time" class="pms-input text-xs">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Default Check-Out Time</label>
                        <input type="time" wire:model="checkout_time" class="pms-input text-xs">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Timezone</label>
                        <select wire:model="hotel_timezone" class="pms-select text-xs">
                            <option>UTC</option><option>Asia/Kolkata</option><option>America/New_York</option><option>Europe/London</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="savePreferences" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer shadow-sm"><i class="fas fa-save text-[10px]"></i> Save Preferences</button>
                </div>
            </div>

            @elseif($activeTab === 'notifications')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-bell text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Notifications</h3>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-150 rounded-xl">
                        <div>
                            <p class="font-bold text-slate-800 text-sm">Email Notifications</p>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Receive reservation alerts via email</p>
                        </div>
                        <button wire:click="$toggle('email_notifications')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer {{ $email_notifications ? 'bg-indigo-600' : 'bg-slate-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $email_notifications ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-150 rounded-xl">
                        <div>
                            <p class="font-bold text-slate-800 text-sm">SMS Notifications</p>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Receive alerts via SMS</p>
                        </div>
                        <button wire:click="$toggle('sms_notifications')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer {{ $sms_notifications ? 'bg-indigo-600' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $sms_notifications ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="saveNotifications" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer shadow-sm"><i class="fas fa-save text-[10px]"></i> Save Setup</button>
                </div>
            </div>

            @elseif($activeTab === 'invoice')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-file-invoice text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Invoice Settings</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Invoice Number Prefix</label>
                        <input type="text" wire:model="invoice_prefix" class="pms-input text-xs" placeholder="INV-">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Invoice Footer Text</label>
                        <textarea wire:model="invoice_footer" rows="3" class="pms-input text-xs resize-none rounded-lg border border-slate-200" placeholder="Thank you for staying with us..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="saveInvoice" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer shadow-sm"><i class="fas fa-save text-[10px]"></i> Save Prefix Settings</button>
                </div>
            </div>

            @elseif($activeTab === 'email')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-150 rounded-xl mb-4">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">Enable SMTP Email</p>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Use custom SMTP settings to route automated system emails</p>
                    </div>
                    <button wire:click="$toggle('smtp_enabled')"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer {{ $smtp_enabled ? 'bg-indigo-600' : 'bg-slate-300' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $smtp_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>

                <div class="flex items-center gap-2 mb-4 border-b border-slate-55 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-envelope-open text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">SMTP Configuration</h3>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">SMTP Host <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="smtp_host" class="pms-input text-xs" placeholder="smtp.gmail.com">
                        @error('smtp_host') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Port <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="smtp_port" class="pms-input text-xs" placeholder="587">
                        @error('smtp_port') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Encryption Protocol</label>
                        <select wire:model="smtp_encryption" class="pms-select text-xs">
                            <option value="tls">TLS (STARTTLS)</option>
                            <option value="ssl">SSL (Implicit TLS)</option>
                            <option value="none">None</option>
                        </select>
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Username</label>
                        <input type="text" wire:model="smtp_username" class="pms-input text-xs" placeholder="you@example.com" autocomplete="off">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Password</label>
                        <input type="password" wire:model="smtp_password" class="pms-input text-xs" placeholder="••••••••" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">From Email Address</label>
                        <input type="email" wire:model="smtp_from_address" class="pms-input text-xs" placeholder="noreply@hotel.com">
                        @error('smtp_from_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">From Sender Name</label>
                        <input type="text" wire:model="smtp_from_name" class="pms-input text-xs" placeholder="Merahkie Hotel">
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="saveEmail" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer shadow-sm"><i class="fas fa-save text-[10px]"></i> Save Email Settings</button>
                </div>

                <div class="border-t border-slate-150 pt-5 mt-5">
                    <h3 class="text-sm font-bold text-slate-800 mb-2 flex items-center gap-1.5"><i class="fas fa-paper-plane text-indigo-500 text-xs"></i> Send a Test Email</h3>
                    <p class="text-[11px] text-slate-400 font-semibold mb-4">Verifies system delivery using settings above before committing changes.</p>
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <input type="email" wire:model="test_email" class="pms-input text-xs rounded-lg border border-slate-200" placeholder="test@example.com">
                            @error('test_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button wire:click="sendTestEmail" wire:loading.attr="disabled" class="btn-secondary text-xs font-bold rounded-lg px-4 py-2 flex items-center gap-2 cursor-pointer shadow-sm">
                            <span wire:loading wire:target="sendTestEmail" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                            <i class="fas fa-paper-plane text-[10px]" wire:loading.remove wire:target="sendTestEmail"></i> Send Test
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>