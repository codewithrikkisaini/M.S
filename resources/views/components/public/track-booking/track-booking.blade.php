<div class="min-h-screen bg-slate-50 text-slate-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="text-center mb-10">
            <a href="/" class="inline-flex items-center gap-2 mb-6">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-paper-plane text-white text-lg"></i>
                </div>
                <div class="flex flex-col text-left">
                    <span class="text-xl font-black tracking-tight text-slate-900 leading-none">MERAHKIE</span>
                    <span class="text-[10px] font-bold tracking-widest text-blue-600 uppercase mt-1">Bookings</span>
                </div>
            </a>
            
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Track Your Booking</h1>
            <p class="text-sm text-slate-500 mt-2">Enter your PNR reference, Booking ID, Phone, or Email to check your reservation.</p>
        </div>

        {{-- Tracking Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8 mb-8">
            <form wire:submit.prevent="trackBooking" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1">PNR / Booking ID *</label>
                    <input type="text" wire:model="pnr" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-semibold" placeholder="e.g. DJ7UKJ, RES-12, or Mobile">
                    @error('pnr') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Email Address <span class="text-slate-400 font-normal">(Optional)</span></label>
                    <input type="email" wire:model="email" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-medium" placeholder="e.g. name@example.com">
                    @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-1">
                    <button type="submit" wire:loading.attr="disabled" class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-75 text-white font-bold text-sm py-3 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove>Track <i class="fas fa-search text-xs"></i></span>
                        <span wire:loading><i class="fas fa-spinner fa-spin text-xs"></i> Searching...</span>
                    </button>
                </div>
            </form>

            @if($error)
                <div class="mt-6 bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl flex items-start gap-3">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <p class="text-sm font-medium">{{ $error }}</p>
                </div>
            @endif
        </div>

        {{-- Result View --}}
        @if($reservation)
            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden animate-fadeIn">
                {{-- Status Header --}}
                <div class="bg-slate-50 p-6 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Reservation Details</div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $reservation->hotel->name ?? 'Merahkie Hotel' }}</h2>
                    </div>
                    <div>
                        @if(strtolower($reservation->status) == 'confirmed')
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 font-black text-xs shadow-sm">
                                <i class="fas fa-check-circle text-emerald-600"></i> Confirmed
                            </span>
                        @elseif(strtolower($reservation->status) == 'cancelled' || strtolower($reservation->status) == 'rejected')
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-rose-100 text-rose-800 border border-rose-300 font-black text-xs shadow-sm">
                                <i class="fas fa-times-circle text-rose-600"></i> Cancelled
                            </span>
                        @else
                            <span class="inline-flex items-center gap-4 px-4 py-2 rounded-full bg-amber-100 text-amber-800 border border-amber-300 font-black text-xs shadow-sm">
                                <i class="fas fa-clock text-amber-600"></i> Pending
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6 md:p-8 space-y-8">
                    {{-- Grid 1: Basic Info --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 mb-1">PNR</span>
                            <span class="block text-base font-black text-slate-900">{{ $reservation->pnr }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 mb-1">Booking Ref</span>
                            <span class="block text-sm font-bold text-slate-800">RES-{{ $reservation->id }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 mb-1">Guest Name</span>
                            <span class="block text-sm font-bold text-slate-800">{{ $reservation->guest->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-500 mb-1">Adults / Kids</span>
                            <span class="block text-sm font-bold text-slate-800">{{ $reservation->adults }} / {{ $reservation->children }}</span>
                        </div>
                    </div>

                    {{-- Grid 2: Dates --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50 flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-lg shadow-sm border border-slate-100 flex flex-col items-center justify-center">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 w-full text-center pb-0.5 mb-0.5">{{ date('M', strtotime($reservation->check_in_date)) }}</span>
                                <span class="text-lg font-black text-slate-900 leading-none">{{ date('d', strtotime($reservation->check_in_date)) }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-0.5">Check-in</span>
                                <span class="block text-sm font-bold text-slate-900">{{ date('l, d M Y', strtotime($reservation->check_in_date)) }}</span>
                                <span class="block text-xs text-slate-500">From 02:00 PM</span>
                            </div>
                        </div>
                        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50 flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-lg shadow-sm border border-slate-100 flex flex-col items-center justify-center">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 w-full text-center pb-0.5 mb-0.5">{{ date('M', strtotime($reservation->check_out_date)) }}</span>
                                <span class="text-lg font-black text-slate-900 leading-none">{{ date('d', strtotime($reservation->check_out_date)) }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-0.5">Check-out</span>
                                <span class="block text-sm font-bold text-slate-900">{{ date('l, d M Y', strtotime($reservation->check_out_date)) }}</span>
                                <span class="block text-xs text-slate-500">Until 11:00 AM</span>
                            </div>
                        </div>
                    </div>

                    {{-- Rooms & Payment --}}
                    <div class="border-t border-slate-200 pt-6">
                        <h3 class="text-sm font-bold text-slate-900 mb-4">Rooms & Charges</h3>
                        
                        <div class="space-y-4 mb-6">
                            @foreach($reservation->rooms as $room)
                                <div class="flex justify-between items-center bg-slate-50 border border-slate-100 p-4 rounded-xl">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-bed"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-bold text-slate-800">{{ $room->roomType->name ?? 'Room' }}</span>
                                            <span class="block text-xs text-slate-500">Room {{ $room->room_number }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-sm font-bold text-slate-900">{{ $reservation->hotel->currency ?? '₹' }}{{ number_format($room->pivot->price ?? 0, 2) }}</span>
                                        <span class="block text-[10px] text-slate-400">/ night</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900 text-white p-6 rounded-xl shadow-lg">
                            <div>
                                <span class="block text-slate-400 text-xs mb-1">{{ $reservation->total_paid > 0 ? 'Total Amount Paid' : 'Total Booking Amount' }}</span>
                                <span class="block text-3xl font-black text-emerald-400">{{ $reservation->hotel->currency ?? '₹' }}{{ number_format($reservation->total_paid > 0 ? $reservation->total_paid : $reservation->estimated_total, 2) }}</span>
                            </div>
                            @if($reservation->status == 'Pending')
                                <div class="bg-white/10 px-4 py-3 rounded-lg border border-white/10 text-sm">
                                    <i class="fas fa-info-circle text-blue-400 mr-2"></i> Your payment is secure. We are awaiting hotel confirmation.
                                </div>
                            @endif
                            <div class="flex items-center gap-3">
                                <button onclick="navigator.clipboard.writeText('{{ $reservation->pnr }}'); alert('Reference ID {{ $reservation->pnr }} copied to clipboard!');" class="px-4 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer border border-slate-700">
                                    <i class="fas fa-copy text-blue-400"></i> Copy PNR
                                </button>
                                <a href="{{ route('booking.slip.download', ['pnr' => $reservation->pnr]) }}" target="_blank" class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition-colors shadow-sm flex items-center gap-2">
                                    <i class="fas fa-file-pdf"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>