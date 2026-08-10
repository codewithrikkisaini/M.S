<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Hotel System Settings</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Manage configuration, policies, taxes, timings, and email setup for <strong class="text-indigo-600 font-extrabold">{{ $hotel_name }}</strong></p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 border border-indigo-100 rounded-xl text-xs font-bold text-indigo-700">
            <i class="fas fa-building text-indigo-500"></i>
            <span>Scope: Hotel Property Level</span>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Tab sidebar --}}
        <div class="lg:w-64 shrink-0">
            <div class="pms-card shadow-sm border border-slate-100/80 p-2 space-y-1">
                @foreach([
                    ['hotel', 'fas fa-hotel', 'Hotel Profile', 'Name, address, contact'],
                    ['preferences', 'fas fa-clock', 'Timings & Currency', 'Check-in/out, timezone'],
                    ['tax_invoice', 'fas fa-receipt', 'Tax & Invoice', 'GSTIN, rate, prefix'],
                    ['policies', 'fas fa-shield-alt', 'Booking & Policies', 'Cancellation, rules'],
                    ['email', 'fas fa-paper-plane', 'Hotel Email (SMTP)', 'Custom mail server'],
                    ['notifications', 'fas fa-bell', 'Alerts & Notifications', 'Email & SMS alerts']
                ] as [$tab, $icon, $label, $subtext])
                <button wire:click="setTab('{{ $tab }}')"
                        class="w-full flex items-center justify-between px-3.5 py-3 rounded-xl text-xs font-bold transition-all cursor-pointer text-left {{ $activeTab === $tab ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <div class="flex items-center gap-3">
                        <i class="{{ $icon }} w-4 text-center text-sm {{ $activeTab === $tab ? 'text-white' : 'text-slate-400' }}"></i>
                        <div>
                            <p class="leading-tight">{{ $label }}</p>
                            <p class="text-[10px] font-medium leading-tight mt-0.5 {{ $activeTab === $tab ? 'text-indigo-100' : 'text-slate-400' }}">{{ $subtext }}</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-[10px] {{ $activeTab === $tab ? 'text-indigo-200' : 'text-slate-300' }}"></i>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Tab content --}}
        <div class="flex-1">
            
            {{-- 1. HOTEL PROFILE TAB --}}
            @if($activeTab === 'hotel')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100"><i class="fas fa-hotel text-xs"></i></div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Hotel Profile & Details</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">Primary property details displayed on public pages & customer receipts</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Hotel Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="hotel_name" class="pms-input text-xs" placeholder="e.g. Grand Plaza Hotel">
                        @error('hotel_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Phone / Frontdesk Contact</label>
                        <input type="text" wire:model="hotel_phone" class="pms-input text-xs" placeholder="+91 9876543210">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Email Address</label>
                        <input type="email" wire:model="hotel_email" class="pms-input text-xs" placeholder="info@hotel.com">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Website URL</label>
                        <input type="url" wire:model="hotel_website" class="pms-input text-xs" placeholder="https://www.hotel.com">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Property Street Address</label>
                        <textarea wire:model="hotel_address" rows="2" class="pms-input text-xs resize-none rounded-xl border border-slate-200" placeholder="123 Beach Road, Resort Area..."></textarea>
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">City</label>
                        <input type="text" wire:model="hotel_city" class="pms-input text-xs" placeholder="e.g. New Delhi">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">State / Province</label>
                        <input type="text" wire:model="hotel_state" class="pms-input text-xs" placeholder="e.g. Delhi">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Country</label>
                        <input type="text" wire:model="hotel_country" class="pms-input text-xs" placeholder="India">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Postal / PIN Code</label>
                        <input type="text" wire:model="hotel_pincode" class="pms-input text-xs" placeholder="110001">
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="saveHotel" class="btn-primary text-xs font-bold rounded-xl py-2.5 px-5 cursor-pointer shadow-md flex items-center gap-2">
                        <i class="fas fa-save text-[10px]"></i> Save Hotel Profile
                    </button>
                </div>
            </div>

            {{-- 2. TIMINGS & PREFERENCES TAB --}}
            @elseif($activeTab === 'preferences')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100"><i class="fas fa-clock text-xs"></i></div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Check-In / Out Timings & Currency</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">Define operational schedules and monetary display rules for this hotel</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Standard Check-In Time</label>
                        <input type="time" wire:model="checkin_time" class="pms-input text-xs">
                        <p class="text-[10px] text-slate-400 mt-1">Default: 14:00 (02:00 PM)</p>
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Standard Check-Out Time</label>
                        <input type="time" wire:model="checkout_time" class="pms-input text-xs">
                        <p class="text-[10px] text-slate-400 mt-1">Default: 12:00 (12:00 PM)</p>
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Hotel Currency</label>
                        <select wire:model="currency" class="pms-select text-xs">
                            <option value="INR">INR (₹)</option>
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="GBP">GBP (£)</option>
                            <option value="AED">AED (AED)</option>
                        </select>
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Date Format</label>
                        <select wire:model="date_format" class="pms-select text-xs">
                            <option value="d M Y">25 Jan 2026</option>
                            <option value="Y-m-d">2026-01-25</option>
                            <option value="d/m/Y">25/01/2026</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Timezone</label>
                        <select wire:model="hotel_timezone" class="pms-select text-xs">
                            <option value="Asia/Kolkata">Asia/Kolkata (IST)</option>
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">America/New_York (EST)</option>
                            <option value="Europe/London">Europe/London (GMT)</option>
                            <option value="Asia/Dubai">Asia/Dubai (GST)</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="savePreferences" class="btn-primary text-xs font-bold rounded-xl py-2.5 px-5 cursor-pointer shadow-md flex items-center gap-2">
                        <i class="fas fa-save text-[10px]"></i> Save Timings & Currency
                    </button>
                </div>
            </div>

            {{-- 3. TAX & INVOICE TAB --}}
            @elseif($activeTab === 'tax_invoice')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100"><i class="fas fa-receipt text-xs"></i></div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Tax & Billing Configuration</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">Configure GST/Tax identification numbers, invoice numbering format & receipts</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">GSTIN / Tax ID Number</label>
                        <input type="text" wire:model="tax_id" class="pms-input text-xs" placeholder="e.g. 07AAAAA0000A1Z5">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Default Tax Rate (%)</label>
                        <input type="number" step="0.01" wire:model="tax_rate" class="pms-input text-xs" placeholder="18">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Invoice Prefix</label>
                        <input type="text" wire:model="invoice_prefix" class="pms-input text-xs" placeholder="INV-">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Invoice Footer / Legal Terms</label>
                        <textarea wire:model="invoice_footer" rows="3" class="pms-input text-xs resize-none rounded-xl border border-slate-200" placeholder="Thank you for staying with us. All disputes are subject to local jurisdiction..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="saveTaxInvoice" class="btn-primary text-xs font-bold rounded-xl py-2.5 px-5 cursor-pointer shadow-md flex items-center gap-2">
                        <i class="fas fa-save text-[10px]"></i> Save Tax & Invoice Setup
                    </button>
                </div>
            </div>

            {{-- 4. BOOKING & POLICIES TAB --}}
            @elseif($activeTab === 'policies')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100"><i class="fas fa-shield-alt text-xs"></i></div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Booking Engine & Guest Policies</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">Rules displayed to online guests during checkout & voucher generation</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div>
                            <p class="font-bold text-slate-800 text-sm">Auto-Confirm Direct Bookings</p>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Automatically mark incoming website bookings as confirmed</p>
                        </div>
                        <button wire:click="$toggle('auto_confirm_booking')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer {{ $auto_confirm_booking ? 'bg-indigo-600' : 'bg-slate-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $auto_confirm_booking ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>

                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Cancellation Policy</label>
                        <textarea wire:model="cancellation_policy" rows="3" class="pms-input text-xs resize-none rounded-xl border border-slate-200" placeholder="Free cancellation up to 24 hours prior to arrival..."></textarea>
                    </div>

                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Guest Check-In Rules / House Policies</label>
                        <textarea wire:model="booking_policy" rows="3" class="pms-input text-xs resize-none rounded-xl border border-slate-200" placeholder="Government ID card mandatory for all adult guests..."></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="savePolicies" class="btn-primary text-xs font-bold rounded-xl py-2.5 px-5 cursor-pointer shadow-md flex items-center gap-2">
                        <i class="fas fa-save text-[10px]"></i> Save Hotel Policies
                    </button>
                </div>
            </div>

            {{-- 5. HOTEL SMTP EMAIL TAB --}}
            @elseif($activeTab === 'email')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl mb-4">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">Enable Custom Hotel SMTP</p>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Send guest vouchers & email receipts directly from your hotel domain email</p>
                    </div>
                    <button wire:click="$toggle('smtp_enabled')"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer {{ $smtp_enabled ? 'bg-indigo-600' : 'bg-slate-300' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $smtp_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>

                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100"><i class="fas fa-paper-plane text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">SMTP Mail Server Credentials</h3>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">SMTP Server Host</label>
                        <input type="text" wire:model="smtp_host" class="pms-input text-xs" placeholder="smtp.gmail.com or mail.yourhotel.com">
                        @error('smtp_host') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Port</label>
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
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Username / Email</label>
                        <input type="text" wire:model="smtp_username" class="pms-input text-xs" placeholder="reservations@yourhotel.com" autocomplete="off">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Password / App Key</label>
                        <input type="password" wire:model="smtp_password" class="pms-input text-xs" placeholder="••••••••" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">From Sender Email</label>
                        <input type="email" wire:model="smtp_from_address" class="pms-input text-xs" placeholder="bookings@yourhotel.com">
                        @error('smtp_from_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">From Sender Name</label>
                        <input type="text" wire:model="smtp_from_name" class="pms-input text-xs" placeholder="e.g. Grand Plaza Reservations">
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="saveEmail" class="btn-primary text-xs font-bold rounded-xl py-2.5 px-5 cursor-pointer shadow-md flex items-center gap-2">
                        <i class="fas fa-save text-[10px]"></i> Save SMTP Mail Setup
                    </button>
                </div>

                <div class="border-t border-slate-100 pt-5 mt-5">
                    <h3 class="text-sm font-bold text-slate-800 mb-1 flex items-center gap-2"><i class="fas fa-vial text-indigo-500 text-xs"></i> Test Email Sending</h3>
                    <p class="text-[11px] text-slate-400 font-semibold mb-3">Sends a test email to verify credentials before saving.</p>
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <input type="email" wire:model="test_email" class="pms-input text-xs rounded-xl border border-slate-200" placeholder="your-email@example.com">
                            @error('test_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button wire:click="sendTestEmail" wire:loading.attr="disabled" class="btn-secondary text-xs font-bold rounded-xl px-4 py-2 flex items-center gap-2 cursor-pointer shadow-sm">
                            <span wire:loading wire:target="sendTestEmail" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                            <i class="fas fa-paper-plane text-[10px]" wire:loading.remove wire:target="sendTestEmail"></i> Send Test
                        </button>
                    </div>
                </div>
            </div>

            {{-- 6. ALERTS & NOTIFICATIONS TAB --}}
            @elseif($activeTab === 'notifications')
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100"><i class="fas fa-bell text-xs"></i></div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">Hotel Staff Notifications & Alerts</h3>
                        <p class="text-[11px] text-slate-400 font-semibold">Enable or disable real-time email and SMS alerts for new bookings</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div>
                            <p class="font-bold text-slate-800 text-sm">Email Booking Alerts</p>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Send instant email notifications to hotel staff when a new reservation is created</p>
                        </div>
                        <button wire:click="$toggle('email_notifications')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer {{ $email_notifications ? 'bg-indigo-600' : 'bg-slate-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $email_notifications ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                        <div>
                            <p class="font-bold text-slate-800 text-sm">SMS Alerts (Gateway)</p>
                            <p class="text-xs text-slate-400 font-semibold mt-0.5">Send SMS booking confirmation messages to guests</p>
                        </div>
                        <button wire:click="$toggle('sms_notifications')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors cursor-pointer {{ $sms_notifications ? 'bg-indigo-600' : 'bg-slate-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $sms_notifications ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4 border-t border-slate-100 mt-4">
                    <button wire:click="saveNotifications" class="btn-primary text-xs font-bold rounded-xl py-2.5 px-5 cursor-pointer shadow-md flex items-center gap-2">
                        <i class="fas fa-save text-[10px]"></i> Save Alert Preferences
                    </button>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>