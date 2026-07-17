<div class="space-y-6">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Global System Settings</h1>
        <p class="text-sm text-slate-500 mt-0.5">Manage global platform configurations, outgoing mail servers, and database backups</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Configuration Forms --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">SMTP Mail server configuration</h2>
                <form wire:submit.prevent="saveSettings" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">SMTP Host</label>
                            <input type="text" wire:model="smtp_host" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. smtp.mailtrap.io">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Port</label>
                            <input type="text" wire:model="smtp_port" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. 2525">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">SMTP Username</label>
                            <input type="text" wire:model="smtp_username" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">SMTP Password</label>
                            <input type="password" wire:model="smtp_password" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">Platform Stripe API Credentials (for SaaS Subscriptions)</h2>
                <form wire:submit.prevent="saveSettings" class="space-y-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Platform Publishable Key</label>
                            <input type="text" wire:model="global_stripe_publishable" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="pk_live_...">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Platform Secret Key</label>
                            <input type="password" wire:model="global_stripe_secret" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="sk_live_...">
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 mt-6 flex justify-end">
                        <button type="submit" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-755 text-white font-semibold text-sm cursor-pointer shadow-md">
                            Save Global Configurations
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- System Backup Simulator Box --}}
        <div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-6">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">Database Backup</h3>
                
                <div class="space-y-4 text-xs">
                    <p class="text-slate-455 leading-relaxed">Schedule automated platform snapshots or trigger a compressed file download of database tables.</p>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Backup Frequency</label>
                        <select wire:model="backup_frequency" class="w-full text-slate-805 border border-slate-200 rounded-lg px-3 py-1.5 focus:outline-none focus:border-indigo-500">
                            <option value="hourly">Every Hour</option>
                            <option value="daily">Daily (At Midnight)</option>
                            <option value="weekly">Weekly (Sundays)</option>
                        </select>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 space-y-1.5 text-[10px]">
                        <div class="flex justify-between">
                            <span class="text-slate-450 font-medium">Last Successful Backup:</span>
                            <span class="font-bold text-slate-700">Today, 04:00 AM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-450 font-medium">Backup Storage Location:</span>
                            <span class="font-bold text-slate-700">AWS S3 Bucket</span>
                        </div>
                    </div>

                    <button wire:click="triggerBackup" class="w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-755 border border-indigo-100 font-bold text-xs py-2.5 rounded-lg transition-colors cursor-pointer text-center">
                        <i class="fas fa-download mr-1"></i> Trigger System Backup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
