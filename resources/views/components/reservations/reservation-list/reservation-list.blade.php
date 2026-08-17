<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Reservations</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage bookings, check-ins, check-outs, and guest stays</p>
        </div>
        <a href="{{ route('reservations.create') }}" class="btn-primary btn-sm rounded-lg shadow-sm">
            <i class="fas fa-plus text-xs"></i> New Reservation
        </a>
    </div>

    {{-- Filter/Search Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-calendar-check text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Booking Inventory</h3>
                    <p class="text-[10px] text-slate-400">Search and filter active hotel reservations</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Search guest, ID / Aadhaar No, room, status..."
                           class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200 w-64">
                </div>
                <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                    {{ $reservations->total() }} total
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold"># ID</th>
                        <th class="font-bold">Guest</th>
                        <th class="font-bold">ID Type</th>
                        <th class="font-bold">ID Number</th>
                        <th class="font-bold">Room(s)</th>
                        <th class="font-bold">Check In</th>
                        <th class="font-bold">Check Out</th>
                        <th class="font-bold">Booking Source</th>
                        <th class="font-bold">Payment Status</th>
                        <th class="font-bold">Booking Status</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reservations as $res)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="text-slate-400 text-xs font-semibold">#{{ $res->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                @if(optional($res->guest)->id)
                                    <button wire:click="openIdModal({{ $res->guest->id }})" class="shrink-0 cursor-pointer" title="View Guest Verification Documents">
                                        @if($res->guest->guest_photo)
                                            <img src="{{ asset('storage/' . $res->guest->guest_photo) }}" class="w-9 h-9 rounded-xl object-cover shadow-sm border border-indigo-200 hover:scale-105 transition-all">
                                        @else
                                            @php
                                                $initials = strtoupper(substr($res->guest->name ?? 'G', 0, 1));
                                                $gradients = [
                                                    'A' => 'from-indigo-400 to-indigo-600', 'B' => 'from-emerald-400 to-emerald-600',
                                                    'C' => 'from-blue-400 to-blue-600', 'D' => 'from-rose-400 to-rose-600',
                                                    'E' => 'from-amber-400 to-amber-600', 'F' => 'from-orange-400 to-orange-600',
                                                    'G' => 'from-teal-400 to-teal-600', 'H' => 'from-purple-400 to-purple-600',
                                                    'I' => 'from-pink-400 to-pink-600', 'J' => 'from-cyan-400 to-cyan-600',
                                                ];
                                                $gradient = $gradients[$initials] ?? 'from-slate-400 to-slate-600';
                                            @endphp
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center shadow-sm border border-white hover:scale-105 transition-all">
                                                <span class="text-xs font-black text-white">{{ $initials }}</span>
                                            </div>
                                        @endif
                                    </button>
                                @endif
                                <div>
                                    <span class="font-bold text-slate-800 text-sm block leading-none mb-1">{{ $res->guest->name ?? 'N/A' }}</span>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-[10px] text-slate-400 block">{{ $res->guest->email ?? $res->guest->phone ?? '' }}</span>
                                        @if(optional($res->guest)->id_card_front || optional($res->guest)->id_card_back || optional($res->guest)->guest_photo)
                                            <button wire:click="openIdModal({{ $res->guest->id }})"
                                                    class="text-[9px] bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200/80 px-1.5 py-0.5 rounded font-bold transition-all cursor-pointer inline-flex items-center gap-1 shadow-2xs"
                                                    title="Click to view Guest Photo & ID Scans">
                                                <i class="fas fa-camera text-indigo-500"></i> Docs
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if(optional($res->guest)->id_type)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-2xs">
                                    {{ $res->guest->id_type }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $idNum = optional($res->guest)->id_number ?: optional($res->guest)->passport_number;
                            @endphp
                            @if($idNum)
                                <span class="text-xs font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/80 shadow-2xs">
                                    {{ $idNum }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="font-semibold text-slate-700 text-sm">
                            {{ $res->rooms->pluck('room_number')->implode(', ') ?: 'N/A' }}
                        </td>
                        <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($res->check_in_date)->format('d M Y') }}</td>
                        <td class="text-slate-500 text-xs font-medium">{{ \Carbon\Carbon::parse($res->check_out_date)->format('d M Y') }}</td>
                        <td>
                            @php
                                $bType = $res->booking_type ?: 'Walk in';
                                $bClass = match($bType) {
                                    'Walk in' => 'bg-purple-50 text-purple-700 border-purple-100',
                                    'Direct website' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'OTA' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'Phone' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                $bIcon = match($bType) {
                                    'Walk in' => 'fa-walking',
                                    'Direct website' => 'fa-globe',
                                    'OTA' => 'fa-hotel',
                                    'Phone' => 'fa-phone',
                                    default => 'fa-bookmark',
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $bClass }}">
                                <i class="fas {{ $bIcon }} text-[9px]"></i> {{ $bType }}
                            </span>
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
                                Fully Paid
                            </span>
                            @endif
                        </td>
                        <td>
                            @php 
                                $s = $res->status; 
                                $badgeClass = match($s) {
                                    'Confirmed' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                    'Pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'Checked-In' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Checked-Out' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'Cancelled', 'Rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    default => 'bg-blue-50 text-blue-700 border-blue-100',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                {{ $s }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($res->status == 'Pending')
                                <button wire:click="accept({{ $res->id }})" wire:confirm="Accept this booking request? Confirmation email will be sent to guest."
                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black rounded-lg shadow-md transition-all flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-check-circle text-xs"></i> Accept
                                </button>
                                <button wire:click="reject({{ $res->id }})" wire:confirm="Reject this booking request?"
                                        class="px-2 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-bold rounded-lg shadow-sm transition-all cursor-pointer" title="Reject Booking">
                                    <i class="fas fa-times"></i>
                                </button>
                                @elseif($res->status == 'Confirmed' || $res->status == 'Reserved')
                                <button wire:click="checkIn({{ $res->id }})" wire:confirm="Check-In this guest?"
                                        class="btn-success btn-sm rounded-lg py-1 px-2.5 text-[11px] font-bold shadow-sm cursor-pointer">
                                    <i class="fas fa-sign-in-alt text-[10px]"></i> Check-In
                                </button>
                                @elseif($res->status == 'Checked-In')
                                <button wire:click="checkOut({{ $res->id }})" wire:confirm="Check-Out this guest?"
                                        class="btn-warning btn-sm rounded-lg py-1 px-2.5 text-[11px] font-bold shadow-sm cursor-pointer">
                                    <i class="fas fa-sign-out-alt text-[10px]"></i> Check-Out
                                </button>
                                @elseif($res->status == 'Checked-Out' && optional(optional($res->checkOut)->invoice)->id)
                                <a href="{{ route('invoice.download', $res->checkOut->invoice->id) }}"
                                   target="_blank" class="btn-secondary btn-sm rounded-lg py-1 px-2.5 text-[11px] font-bold shadow-sm">
                                    <i class="fas fa-file-pdf text-[10px] text-red-500"></i> Invoice
                                </a>
                                @endif
                                @if(Auth::user()?->hasRole('admin') || Auth::user()?->hasRole('superadmin'))
                                <a href="{{ route('reservations.edit', $res->id) }}" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <button wire:click="delete({{ $res->id }})" wire:confirm="Delete this reservation?"
                                        class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="py-12 text-center text-slate-400">
                            <i class="fas fa-calendar-times text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">No reservations found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reservations->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $reservations->links() }}</div>
        @endif
    </div>

    {{-- Guest ID Verification Modal Popup --}}
    @if($showIdModal && $selectedGuest)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 transition-all">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    @if($selectedGuest->guest_photo)
                        <img src="{{ asset('storage/' . $selectedGuest->guest_photo) }}" class="w-11 h-11 rounded-xl object-cover border border-indigo-200 shadow-sm">
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
                    {{-- Guest Photo --}}
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

