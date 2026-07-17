<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">API Key Management</h1>
            <p class="text-sm text-slate-500 mt-0.5">Generate tokens to connect keyless locks, housekeeping apps, or custom guest apps</p>
        </div>
        <div>
            <button wire:click="openCreateModal" class="btn-primary rounded-lg shadow-sm px-4 py-2 bg-indigo-600 hover:bg-indigo-755 text-white font-semibold text-sm flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-key text-xs"></i> Generate New API Key
            </button>
        </div>
    </div>

    {{-- API Keys Table --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-650 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-plug text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Active Integrations</h3>
                    <p class="text-[10px] text-slate-400">Manage developer API credentials authorized to access your hotel dataset</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr>
                        <th>Key Name</th>
                        <th>API Token</th>
                        <th>Created At</th>
                        <th>Last Used</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keysList as $k)
                    <tr>
                        <td class="font-bold text-slate-800 text-sm">{{ $k->name }}</td>
                        <td class="font-mono text-xs text-slate-500">
                            <code>pms_live_••••••••••••{{ substr($k->key, -6) }}</code>
                        </td>
                        <td class="text-slate-500 text-xs font-semibold">{{ $k->created_at->format('d M Y') }}</td>
                        <td class="text-slate-550 text-xs font-medium">
                            {{ $k->last_used_at ? $k->last_used_at->diffForHumans() : 'Never used' }}
                        </td>
                        <td>
                            @if($k->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">Inactive</span>
                            @endif
                        </td>
                        <td class="text-right space-x-3">
                            <button wire:click="toggleStatus({{ $k->id }})" class="text-indigo-600 hover:text-indigo-805 text-xs font-bold transition-colors cursor-pointer">
                                {{ $k->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                            <button onclick="confirm('Are you sure you want to revoke this API key permanently?') || event.stopImmediatePropagation()" 
                                    wire:click="deleteKey({{ $k->id }})" 
                                    class="text-rose-600 hover:text-rose-800 text-xs font-bold transition-colors cursor-pointer">
                                Revoke
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-slate-455 py-10">No integration keys generated yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create Key Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full border border-slate-100 animate-fadeIn duration-200">
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">Generate API Token</h3>
                <button wire:click="$set('showCreateModal', false)" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition-colors cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                @if(!$newlyCreatedKey)
                <form wire:submit.prevent="createKey" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Integration Application Name *</label>
                        <input type="text" wire:model="name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Assa Abloy Keyless Locks">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="rounded-lg px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-655 font-semibold text-xs cursor-pointer">Cancel</button>
                        <button type="submit" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-755 text-white font-semibold text-xs cursor-pointer shadow-md">Generate Token</button>
                    </div>
                </form>
                @else
                <div class="space-y-4">
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-left">
                        <div class="flex gap-2 text-amber-800 font-bold text-xs mb-1">
                            <i class="fas fa-exclamation-triangle mt-0.5"></i> Warning! Security Notice
                        </div>
                        <p class="text-[10px] text-amber-755 leading-relaxed">Please copy your API token now. For security purposes, it will not be displayed again after you close this modal.</p>
                    </div>

                    <div class="relative bg-slate-50 border border-slate-200 rounded-xl p-3.5 font-mono text-xs select-all text-slate-800 break-all pr-12">
                        <code>{{ $newlyCreatedKey }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ $newlyCreatedKey }}'); alert('API Key copied to clipboard!')" 
                                class="absolute right-3.5 top-3.5 text-indigo-600 hover:text-indigo-805 cursor-pointer">
                            <i class="far fa-copy text-sm"></i>
                        </button>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="bg-indigo-600 hover:bg-indigo-755 text-white font-bold text-xs py-2 px-5 rounded-lg cursor-pointer shadow-md">
                            I've Saved the Key
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
