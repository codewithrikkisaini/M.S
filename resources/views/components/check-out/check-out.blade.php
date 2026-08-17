<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Check-Out Interface</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track today's departures and process guest check-outs</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-orange-50 text-orange-600 border border-orange-100"><i class="fas fa-calendar-day text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $checkoutsToday }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Departures Scheduled Today</p>
            </div>
        </div>
        <div class="stat-card border border-slate-100/80 hover:shadow-md transition-all duration-200">
            <div class="stat-icon bg-rose-50 text-rose-600 border border-rose-100"><i class="fas fa-exclamation-triangle text-lg"></i></div>
            <div>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $overdueCount }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">Overdue Departures</p>
            </div>
        </div>
    </div>

    {{-- Checked-In Guests Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center border border-emerald-100"><i class="fas fa-hotel text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">In-House Guests</h3>
                    <p class="text-[10px] text-slate-400">Guests currently checked in and residing at the hotel</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search guest..."
                           class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Guest</th>
                        <th class="font-bold">Photo / ID</th>
                        <th class="font-bold">Room</th>
                        <th class="font-bold">Check-In</th>
                        <th class="font-bold">Check-Out Due</th>
                        <th class="font-bold">Nights</th>
                        <th class="font-bold">Outstanding Balance</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($checkedIn as $res)
                    @php
                        $nights = \Carbon\Carbon::parse($res->check_in_date)->diffInDays(\Carbon\Carbon::parse($res->check_out_date));
                        $isOverdue = \Carbon\Carbon::parse($res->check_out_date)->isPast();
                        $isDueToday = \Carbon\Carbon::parse($res->check_out_date)->isToday();
                        
                        $rowBg = $isOverdue ? 'bg-red-50/30' : ($isDueToday ? 'bg-amber-50/30' : 'hover:bg-slate-50/40');
                    @endphp
                    <tr class="{{ $rowBg }} transition-colors">
                        <td>
                            <div>
                                <span class="font-bold text-slate-800 text-sm block leading-none mb-1">{{ $res->guest->name ?? 'N/A' }}</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] text-slate-400 block">{{ $res->guest->phone ?? '' }}</span>
                                    @if(optional($res->guest)->id_card_front || optional($res->guest)->id_card_back || optional($res->guest)->guest_photo)
                                        <button type="button" wire:click="openIdModal({{ $res->guest->id }})"
                                                class="text-[9px] bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200/80 px-1.5 py-0.5 rounded font-bold transition-all cursor-pointer inline-flex items-center gap-1 shadow-2xs"
                                                title="Click to view Guest Photo & ID Scans">
                                            <i class="fas fa-camera text-indigo-500"></i> Docs
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $selectedImage = optional($res->guest)->id_card_front
                                    ?: (optional($res->guest)->guest_photo ?: optional($res->guest)->id_card_back);
                            @endphp
                            @if($selectedImage && optional($res->guest)->id)
                                <button type="button" wire:click="openIdModal({{ $res->guest->id }})" title="View Guest Verification Photo / ID Document"
                                        class="block cursor-pointer group">
                                    <div class="relative inline-block">
                                        <img src="{{ asset('storage/' . $selectedImage) }}" alt="Guest Verification Image"
                                             class="w-11 h-11 rounded-lg object-cover border border-slate-200 shadow-sm group-hover:scale-105 group-hover:border-indigo-400 transition-all">
                                        <span class="absolute -bottom-1 -right-1 bg-emerald-500 text-white rounded-full w-4 h-4 text-[8px] flex items-center justify-center shadow-xs border border-white">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    </div>
                                </button>
                            @else
                                <span class="text-slate-400 text-xs italic">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-black text-slate-800 text-sm bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">{{ $res->rooms->pluck('room_number')->implode(', ') ?: 'N/A' }}</span>
                        </td>
                        <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}</td>
                        <td>
                            <span class="text-xs font-bold @if($isOverdue) text-rose-600 @elseif($isDueToday) text-amber-600 @else text-slate-600 @endif">
                                {{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}
                            </span>
                            @if($isOverdue)
                                <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[9px] font-black bg-rose-50 text-rose-700 border border-rose-100 uppercase ml-1 animate-pulse">Overdue</span>
                            @elseif($isDueToday)
                                <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[9px] font-black bg-amber-50 text-amber-700 border border-amber-100 uppercase ml-1">Due Today</span>
                            @endif
                        </td>
                        <td class="text-slate-600 text-xs font-semibold">
                            <span class="flex items-center gap-1"><i class="fas fa-moon text-slate-400 text-[10px]"></i> {{ $nights }} night{{ $nights !== 1 ? 's' : '' }}</span>
                        </td>
                        <td>
                            @php $balance = $res->balance_due; @endphp
                            @if($balance > 0)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                ${{ number_format($balance, 2) }} due
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Paid
                            </span>
                            @endif
                        </td>
                        <td class="text-right">
                            <button wire:click="checkOut({{ $res->id }})"
                                    wire:confirm="Check out {{ $res->guest->name ?? 'guest' }}? This will generate an invoice."
                                    wire:loading.attr="disabled"
                                    class="btn-warning btn-sm rounded-lg py-1 px-2.5 text-[11px] font-bold shadow-sm cursor-pointer">
                                <i class="fas fa-sign-out-alt text-[10px]"></i> Check Out
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center text-slate-400">
                            <i class="fas fa-hotel text-5xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-semibold text-slate-500">No guests currently checked in</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($checkedIn->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $checkedIn->links() }}</div>
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