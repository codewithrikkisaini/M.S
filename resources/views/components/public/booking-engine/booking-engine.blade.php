<div class="min-h-screen bg-slate-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        {{-- Branding & Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-755 text-xs font-semibold mb-3">
                <i class="fas fa-hotel"></i> Guest Booking Engine
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Direct Reservation Portal</h1>
            <p class="text-sm text-slate-500 mt-1">Book your luxury stay with automated check-in and secure billing</p>
        </div>

        {{-- Progress Bar --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 1 ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-655' }}">1</span>
            <div class="w-12 h-0.5 bg-slate-200"></div>
            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 2 ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-655' }}">2</span>
            <div class="w-12 h-0.5 bg-slate-200"></div>
            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 3 ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-655' }}">3</span>
        </div>

        {{-- Step 1: Search & Room List --}}
        @if($step == 1)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Select Hotel *</label>
                    <select wire:model.live="hotel_id" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        @foreach($hotels as $h)
                        <option value="{{ $h->id }}">{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Check-in *</label>
                    <input type="date" wire:model.live="checkin_date" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Check-out *</label>
                    <input type="date" wire:model.live="checkout_date" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">Guests *</label>
                    <select wire:model.live="guests_count" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                        <option value="1">1 Guest</option>
                        <option value="2">2 Guests</option>
                        <option value="3">3 Guests</option>
                        <option value="4">4 Guests</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Room Types List --}}
        <div class="space-y-4">
            @forelse($this->roomTypes as $type)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col md:flex-row">
                <div class="w-full md:w-1/3 bg-slate-100 min-h-[160px] flex items-center justify-center relative">
                    <i class="fas fa-bed text-5xl text-slate-300"></i>
                    <span class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                        Max Guests: {{ $type->base_occupancy }}
                    </span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-bold text-slate-800">{{ $type->name }}</h3>
                            <div class="text-right">
                                <span class="text-2xl font-black text-indigo-650">${{ number_format($type->base_price, 2) }}</span>
                                <span class="text-xs text-slate-400 block">per night</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-450 mt-1">Experience ultimate comfort with luxury bedding, high-speed Wi-Fi, air conditioning, and a fully equipped private bathroom.</p>
                        
                        <div class="flex flex-wrap gap-2 mt-4">
                            <span class="bg-slate-50 border border-slate-150 text-[10px] font-semibold text-slate-500 px-2 py-0.5 rounded-md"><i class="fas fa-wifi mr-1"></i>Free Wi-Fi</span>
                            <span class="bg-slate-50 border border-slate-150 text-[10px] font-semibold text-slate-500 px-2 py-0.5 rounded-md"><i class="fas fa-coffee mr-1"></i>Breakfast included</span>
                            <span class="bg-slate-50 border border-slate-150 text-[10px] font-semibold text-slate-500 px-2 py-0.5 rounded-md"><i class="fas fa-snowflake mr-1"></i>AC</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-100 pt-4 mt-6 flex justify-end">
                        <button wire:click="selectRoomType({{ $type->id }})" class="bg-indigo-600 hover:bg-indigo-755 text-white font-bold text-xs py-2 px-4 rounded-lg shadow-sm transition-colors cursor-pointer">
                            Select & Continue <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-10 text-center">
                <i class="fas fa-hotel text-4xl text-slate-300 mb-2"></i>
                <h3 class="font-bold text-slate-700">No rooms available</h3>
                <p class="text-xs text-slate-450">Please adjust your dates or check back later.</p>
            </div>
            @endforelse
        </div>
        @endif

        {{-- Step 2: Checkout / Stripe Simulation --}}
        @if($step == 2)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Guest Info & Payment --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">1. Guest Information</h2>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Full Name *</label>
                                <input type="text" wire:model="guest_name" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Jane Doe">
                                @error('guest_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Email *</label>
                                <input type="email" wire:model="guest_email" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. janedoe@gmail.com">
                                @error('guest_email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Phone Number *</label>
                                <input type="text" wire:model="guest_phone" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. +91 9876543210">
                                @error('guest_phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Nationality *</label>
                                <input type="text" wire:model="guest_nationality" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                                @error('guest_nationality') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Stripe Gateway Mock Simulation --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">2. Stripe Secure Payment</h2>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            <i class="fab fa-stripe text-base"></i> Mock Mode
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div class="border border-slate-200 rounded-lg p-4 bg-slate-50">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-info-circle text-indigo-500"></i>
                                <span class="text-xs font-bold text-slate-700">Stripe Checkout Simulator</span>
                            </div>
                            <p class="text-[10px] text-slate-455">This component simulates the Stripe checkout process. In production, this launches the Stripe hosted checkout overlay or collects tokenized details securely.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Card Number</label>
                            <div class="relative">
                                <input type="text" readonly value="•••• •••• •••• 4242" class="w-full text-slate-400 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                                <div class="absolute right-3 top-2 text-slate-400"><i class="far fa-credit-card"></i></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Expiry Date</label>
                                <input type="text" readonly value="12 / 29" class="w-full text-slate-400 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">CVC</label>
                                <input type="text" readonly value="123" class="w-full text-slate-400 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Sidebar --}}
            <div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-6">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4">Summary</h3>
                    <div class="space-y-4 text-xs">
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-450 font-medium">Room Type:</span>
                            <span class="font-bold text-slate-800">{{ $selectedRoomType->name }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-450 font-medium">Check-in:</span>
                            <span class="font-bold text-slate-800">{{ date('d M Y', strtotime($checkin_date)) }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-450 font-medium">Check-out:</span>
                            <span class="font-bold text-slate-800">{{ date('d M Y', strtotime($checkout_date)) }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-450 font-medium">Total Stay:</span>
                            <span class="font-bold text-slate-800">{{ $total_days }} Night(s)</span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-sm font-bold text-slate-800">Total Price:</span>
                            <span class="text-xl font-black text-indigo-600">${{ number_format($total_price, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <button wire:click="processBooking" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-3 rounded-lg shadow-md transition-all cursor-pointer flex items-center justify-center gap-1.5">
                            <i class="fas fa-lock"></i> Pay & Confirm Reservation
                        </button>
                        <button wire:click="$set('step', 1)" class="w-full bg-slate-50 hover:bg-slate-100 text-slate-655 font-bold text-xs py-2 rounded-lg transition-colors cursor-pointer border border-slate-200">
                            Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Step 3: Success Screen --}}
        @if($step == 3)
        <div class="max-w-md mx-auto bg-white rounded-3xl border border-slate-100 shadow-xl p-8 text-center animate-fadeIn">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-100">
                <i class="fas fa-check text-2xl animate-bounce"></i>
            </div>
            
            <h2 class="text-2xl font-black text-slate-900">Booking Confirmed!</h2>
            <p class="text-xs text-slate-500 mt-1">Thank you for booking with us. Your invoice has been marked as paid.</p>

            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5 my-6 text-left space-y-3">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-455">Booking Reference:</span>
                    <span class="font-extrabold text-slate-800">{{ $booking_number }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-455">Guest Name:</span>
                    <span class="font-bold text-slate-800">{{ $guest_name }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-455">Dates:</span>
                    <span class="font-bold text-slate-800">{{ date('d M', strtotime($checkin_date)) }} - {{ date('d M Y', strtotime($checkout_date)) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-455">Amount Paid:</span>
                    <span class="font-black text-indigo-600">${{ number_format($total_price, 2) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-455">Status:</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Paid (via Stripe)</span>
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-850 cursor-pointer">
                    <i class="fas fa-home"></i> Back to Portal
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
