<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Guests Directory</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage guest profiles, contact details, and nationalities</p>
        </div>
        <a href="{{ route('guests.create') }}" class="btn-primary btn-sm rounded-lg shadow-sm">
            <i class="fas fa-user-plus text-xs"></i> Add Guest
        </a>
    </div>

    {{-- Table Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-users text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Guest Profiles</h3>
                    <p class="text-[10px] text-slate-400">Search registered guest accounts and contact files</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search guests..."
                           class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                </div>
                <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                    {{ $guests->total() }} total
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Guest ID</th>
                        <th class="font-bold">Guest Name</th>
                        <th class="font-bold">Email Address</th>
                        <th class="font-bold">Phone Number</th>
                        <th class="font-bold">Nationality</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($guests as $guest)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 shadow-sm">{{ $guest->guest_id }}</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                @php
                                    $initials = strtoupper(substr($guest->name, 0, 1));
                                    $gradients = [
                                        'A' => 'from-indigo-400 to-indigo-600', 'B' => 'from-emerald-400 to-emerald-600',
                                        'C' => 'from-blue-400 to-blue-600', 'D' => 'from-rose-400 to-rose-600',
                                        'E' => 'from-amber-400 to-amber-600', 'F' => 'from-orange-400 to-orange-600',
                                        'G' => 'from-teal-400 to-teal-600', 'H' => 'from-purple-400 to-purple-600',
                                        'I' => 'from-pink-400 to-pink-600', 'J' => 'from-cyan-400 to-cyan-600',
                                    ];
                                    $gradient = $gradients[$initials] ?? 'from-slate-400 to-slate-600';
                                @endphp
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center shrink-0 shadow-sm border border-white">
                                    <span class="text-xs font-black text-white">{{ $initials }}</span>
                                </div>
                                <span class="font-bold text-slate-800 text-sm leading-none">{{ $guest->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-slate-600 text-xs font-medium">{{ $guest->email ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="text-slate-600 text-xs font-semibold">{{ $guest->phone ?? '—' }}</span>
                        </td>
                        <td>
                            @if($guest->nationality)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-150 shadow-sm">
                                {{ $guest->nationality }}
                            </span>
                            @else
                            <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('guests.edit', $guest->id) }}" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <button wire:click="delete({{ $guest->id }})" wire:confirm="Delete guest {{ $guest->name }}?"
                                        class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="fas fa-users text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium">No guests found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($guests->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $guests->links() }}</div>
        @endif
    </div>
</div>
