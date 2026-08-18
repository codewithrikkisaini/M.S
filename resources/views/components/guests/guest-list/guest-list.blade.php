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
                        <th class="font-bold">Photo / ID</th>
                        <th class="font-bold">Email Address</th>
                        <th class="font-bold">Phone Number</th>
                        <th class="font-bold">ID Type</th>
                        <th class="font-bold">ID Number</th>
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
                            <div>
                                <span class="font-bold text-slate-800 text-sm leading-none block mb-0.5">{{ $guest->name }}</span>
                                @if($guest->id_card_front || $guest->id_card_back || $guest->guest_photo)
                                    <button type="button" wire:click="openIdModal({{ $guest->id }})"
                                            class="text-[9px] bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200/80 px-1.5 py-0.2 rounded font-bold transition-all cursor-pointer inline-flex items-center gap-1 mt-0.5"
                                            title="Click to view Guest Photo & ID Scans">
                                        <i class="fas fa-camera text-indigo-500"></i> Docs
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                                $selectedImage = $guest->id_card_front ?: ($guest->guest_photo ?: $guest->id_card_back);
                            @endphp
                            @if($selectedImage)
                                <button type="button" wire:click="openIdModal({{ $guest->id }})" title="View Guest Verification Photo / ID Document"
                                        class="block cursor-pointer group">
                                    <div class="relative inline-block">
                                        <img src="{{ asset('storage/' . $selectedImage) }}" alt="Guest Verification Image"
                                             class="w-10 h-10 rounded-lg object-cover border border-slate-200 shadow-sm group-hover:scale-105 group-hover:border-indigo-400 transition-all">
                                        <span class="absolute -bottom-1 -right-1 bg-emerald-500 text-white rounded-full w-3.5 h-3.5 text-[7px] flex items-center justify-center shadow-xs border border-white">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    </div>
                                </button>
                            @else
                                <span class="text-slate-400 text-xs italic">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-slate-600 text-xs font-medium">{{ $guest->email ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="text-slate-600 text-xs font-semibold">{{ $guest->phone ?? '—' }}</span>
                        </td>
                        <td>
                            @if($guest->id_type)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-2xs">
                                    {{ $guest->id_type }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $gidNum = $guest->id_number ?: $guest->passport_number;
                            @endphp
                            @if($gidNum)
                                <span class="text-xs font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 shadow-2xs">
                                    {{ $gidNum }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
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
                                @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('superadmin'))
                                <a href="{{ route('guests.edit', $guest->id) }}" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <button wire:click="delete({{ $guest->id }})" wire:confirm="Delete guest {{ $guest->name }}?"
                                        class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
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

    {{-- Guest ID & Photo Verification Modal Popup --}}
    @if($showIdModal && $selectedGuest)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 transition-all">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    @if($selectedGuest->guest_photo)
                        <img src="{{ asset('storage/' . $selectedGuest->guest_photo) }}" class="w-11 h-11 rounded-xl object-cover border border-indigo-200 shadow-sm">
                    @elseif($selectedGuest->id_card_front)
                        <img src="{{ asset('storage/' . $selectedGuest->id_card_front) }}" class="w-11 h-11 rounded-xl object-cover border border-indigo-200 shadow-sm">
                    @else
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-base border border-indigo-100">
                            <i class="fas fa-user font-black"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">{{ $selectedGuest->name }}</h3>
                        <p class="text-xs text-slate-500">{{ $selectedGuest->phone ?? 'No phone' }} • {{ $selectedGuest->email ?? 'No email' }}</p>
                    </div>
                </div>
                <button wire:click="closeIdModal" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-slate-100 cursor-pointer">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-150 gap-2">
                    <div>
                        <span class="text-xs font-bold text-slate-700 block">ID Document Details:</span>
                        <span class="text-xs font-mono font-bold text-indigo-700">
                            {{ $selectedGuest->id_type ?: 'ID Card' }}: {{ $selectedGuest->id_number ?: ($selectedGuest->passport_number ?: 'Not Provided') }}
                        </span>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200 shrink-0">
                        <i class="fas fa-shield-alt text-xs"></i> Verified Guest Profile
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Guest Live Photo --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">Live Photo</label>
                        @if($selectedGuest->guest_photo)
                            <a href="{{ asset('storage/' . $selectedGuest->guest_photo) }}" target="_blank" class="block group relative">
                                <img src="{{ asset('storage/' . $selectedGuest->guest_photo) }}" class="w-full h-36 object-cover rounded-xl border border-slate-200 shadow-sm group-hover:opacity-90">
                                <span class="absolute inset-0 bg-black/40 text-white text-xs font-bold flex items-center justify-center opacity-0 group-hover:opacity-100 rounded-xl transition-all"><i class="fas fa-external-link-alt mr-1"></i> Open Full</span>
                            </a>
                        @else
                            <div class="w-full h-36 rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 p-3 text-center">
                                <i class="fas fa-camera text-2xl mb-1 text-slate-300"></i>
                                <span class="text-xs font-semibold">No Live Photo</span>
                            </div>
                        @endif
                    </div>

                    {{-- ID Card Front --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">ID Front Scan</label>
                        @if($selectedGuest->id_card_front)
                            <a href="{{ asset('storage/' . $selectedGuest->id_card_front) }}" target="_blank" class="block group relative">
                                <img src="{{ asset('storage/' . $selectedGuest->id_card_front) }}" class="w-full h-36 object-cover rounded-xl border border-slate-200 shadow-sm group-hover:opacity-90">
                                <span class="absolute inset-0 bg-black/40 text-white text-xs font-bold flex items-center justify-center opacity-0 group-hover:opacity-100 rounded-xl transition-all"><i class="fas fa-external-link-alt mr-1"></i> Open Full</span>
                            </a>
                        @else
                            <div class="w-full h-36 rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 p-3 text-center">
                                <i class="fas fa-id-card text-2xl mb-1 text-slate-300"></i>
                                <span class="text-xs font-semibold">No Front Scan</span>
                            </div>
                        @endif
                    </div>

                    {{-- ID Card Back --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider">ID Back Scan</label>
                        @if($selectedGuest->id_card_back)
                            <a href="{{ asset('storage/' . $selectedGuest->id_card_back) }}" target="_blank" class="block group relative">
                                <img src="{{ asset('storage/' . $selectedGuest->id_card_back) }}" class="w-full h-36 object-cover rounded-xl border border-slate-200 shadow-sm group-hover:opacity-90">
                                <span class="absolute inset-0 bg-black/40 text-white text-xs font-bold flex items-center justify-center opacity-0 group-hover:opacity-100 rounded-xl transition-all"><i class="fas fa-external-link-alt mr-1"></i> Open Full</span>
                            </a>
                        @else
                            <div class="w-full h-36 rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 p-3 text-center">
                                <i class="fas fa-id-card text-2xl mb-1 text-slate-300"></i>
                                <span class="text-xs font-semibold">No Back Scan</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end border-t border-slate-100 mt-6">
                <button type="button" wire:click="closeIdModal" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md cursor-pointer">Done</button>
            </div>
        </div>
    </div>
    @endif
</div>
