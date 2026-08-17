<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Blacklisted Guests</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage blocked guests and identity restrictions</p>
        </div>
        @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('superadmin'))
        <a href="{{ route('guests.blacklist.create') }}" class="btn-primary btn-sm rounded-lg shadow-sm">
            <i class="fas fa-ban text-xs"></i> Add Blacklisted Guest
        </a>
        @endif
    </div>

    {{-- Table Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center border border-red-100"><i class="fas fa-ban text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Blacklist Records</h3>
                    <p class="text-[10px] text-slate-400">Guests restricted from making new bookings</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select wire:model.live="status" class="pms-select text-xs py-1.5 rounded-lg border border-slate-200">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="removed">Removed</option>
                </select>
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search guest / passport / license..."
                           class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                </div>
                <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                    {{ $blacklists->total() }} total
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">#</th>
                        <th class="font-bold">First Name</th>
                        <th class="font-bold">Last Name</th>
                        <th class="font-bold">ID / Passport No.</th>
                        <th class="font-bold">Date of Birth</th>
                        <th class="font-bold">Reason</th>
                        <th class="font-bold">Status</th>
                        <th class="font-bold">Blacklisted At</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($blacklists as $index => $blacklist)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <span class="text-xs font-mono text-slate-500">{{ $blacklists->firstItem() + $index }}</span>
                        </td>
                        <td>
                            <span class="font-bold text-slate-800 text-sm">{{ $blacklist->first_name }}</span>
                        </td>
                        <td>
                            <span class="font-bold text-slate-800 text-sm">{{ $blacklist->last_name }}</span>
                        </td>
                        <td>
                            @if($blacklist->id_number)
                                <span class="text-xs font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                    {{ strtoupper($blacklist->id_type ?? 'ID') }}: {{ $blacklist->id_number }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td>
                            @if($blacklist->date_of_birth)
                                <span class="text-xs text-slate-600">{{ $blacklist->date_of_birth->format('d M Y') }}</span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-xs text-slate-600 max-w-[200px] truncate block" title="{{ $blacklist->reason }}">
                                {{ Str::limit($blacklist->reason, 40) }}
                            </span>
                        </td>
                        <td>
                            @if($blacklist->status === 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-50 text-red-700 border border-red-100 shadow-2xs">
                                    <i class="fas fa-circle text-[6px] mr-1"></i> Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-50 text-green-700 border border-green-100 shadow-2xs">
                                    <i class="fas fa-circle text-[6px] mr-1"></i> Removed
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-xs text-slate-500">{{ $blacklist->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('superadmin'))
                                    @if($blacklist->status === 'active')
                                        <a href="{{ route('guests.blacklist.edit', $blacklist->id) }}" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm" title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <button wire:click="remove({{ $blacklist->id }})" wire:confirm="Remove blacklist for {{ $blacklist->first_name }} {{ $blacklist->last_name }}? This guest will be allowed to book again."
                                                class="btn-icon text-green-500 hover:bg-green-50 border border-slate-100 hover:border-green-100 shadow-sm cursor-pointer" title="Remove Blacklist">
                                            <i class="fas fa-undo text-xs"></i>
                                        </button>
                                    @else
                                        <button wire:click="restore({{ $blacklist->id }})" wire:confirm="Restore blacklist for {{ $blacklist->first_name }} {{ $blacklist->last_name }}?"
                                                class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Restore Blacklist">
                                            <i class="fas fa-redo text-xs"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <i class="fas fa-ban text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium">No blacklisted guests found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($blacklists->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $blacklists->links() }}</div>
        @endif
    </div>
</div>
