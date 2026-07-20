@php
    $selectedHotel = $this->selectedHotel;
    $currencySymbol = ($selectedHotel && in_array(strtoupper($selectedHotel->currency), ['INR', 'RS'])) ? '₹' : ($selectedHotel->currency ?? '$');
@endphp
<div class="min-h-screen bg-slate-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        {{-- Branding & Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-755 text-xs font-semibold mb-3">
                <i class="fas fa-hotel"></i> Guest Booking Engine
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Direct Reservation Portal</h1>
            <p class="text-sm text-slate-550 mt-1">Book your luxury stay with automated check-in and secure billing</p>
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

        {{-- Selected Hotel Details and Image Gallery --}}
        @php
            $selectedHotelImages = [];
            if ($selectedHotel && !empty($selectedHotel->images) && count($selectedHotel->images) > 0) {
                foreach ($selectedHotel->images as $img) {
                    $selectedHotelImages[] = asset('storage/' . $img['image_path']);
                }
            } else {
                $selectedHotelImages = [
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80'
                ];
            }
            $ratingStars = intval($selectedHotel->category ?? 4);
        @endphp
        @if($selectedHotel)
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8 items-stretch">
            
            {{-- Multi-image Slider (Column Span 3) --}}
            <div class="md:col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex flex-col justify-between" x-data="{ activeIndex: 0, images: @js($selectedHotelImages) }">
                <div class="relative aspect-video rounded-xl overflow-hidden bg-slate-100 flex-1">
                    <template x-for="(img, index) in images" :key="index">
                        <img x-show="activeIndex === index" :src="img" class="w-full h-full object-cover transition-all duration-500">
                    </template>
                    
                    {{-- Nav Controls --}}
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/35 to-transparent p-4 flex justify-between items-center text-white text-xs">
                        <span class="font-bold uppercase tracking-wider text-[10px]" x-text="'Photo ' + (activeIndex + 1) + ' of ' + images.length"></span>
                        <div class="flex gap-2">
                            <button @click="activeIndex = (activeIndex - 1 + images.length) % images.length" class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center cursor-pointer transition-all border border-white/10"><i class="fas fa-chevron-left text-[10px]"></i></button>
                            <button @click="activeIndex = (activeIndex + 1) % images.length" class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center cursor-pointer transition-all border border-white/10"><i class="fas fa-chevron-right text-[10px]"></i></button>
                        </div>
                    </div>
                </div>
                
                {{-- Thumbnails --}}
                <div class="flex gap-2 overflow-x-auto pt-3 pb-1 scrollbar-thin">
                    <template x-for="(img, index) in images" :key="index">
                        <button @click="activeIndex = index" class="w-16 h-12 rounded-lg overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0" :class="activeIndex === index ? 'border-indigo-600 scale-95 shadow-md shadow-indigo-600/10' : 'border-transparent opacity-50 hover:opacity-100'">
                            <img :src="img" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

            {{-- Hotel Info & Specifications (Column Span 2) --}}
            <div class="md:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-indigo-50 border border-indigo-100 text-indigo-755 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded shadow-sm">
                            {{ $selectedHotel->property_type ?: 'Boutique Hotel' }}
                        </span>
                        <div class="flex items-center gap-0.5 text-amber-500 text-xs">
                            @for($i = 0; $i < $ratingStars; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-slate-800 tracking-tight leading-snug">
                        {{ $selectedHotel->name }}
                    </h2>

                    <div class="flex items-start gap-1.5 text-xs text-slate-500">
                        <i class="fas fa-map-marker-alt text-indigo-600 mt-0.5"></i>
                        <span>
                            {{ $selectedHotel->address }}, {{ $selectedHotel->city }}, {{ $selectedHotel->state }}, {{ $selectedHotel->country }} - {{ $selectedHotel->postal_code }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Experience premium comfort, world-class hospitality, and upscale amenities in a modern luxury environment tailored for your absolute relaxation.
                    </p>
                </div>

                {{-- Contact & Policy details --}}
                <div class="border-t border-slate-100 pt-3 space-y-2 text-[11px] text-slate-600">
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-400">Email:</span>
                        <span class="font-bold text-slate-800">{{ $selectedHotel->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-400">Phone:</span>
                        <span class="font-bold text-slate-800">{{ $selectedHotel->phone ?: '+91 9876543210' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-400">Website:</span>
                        <span class="font-bold text-indigo-650">{{ $selectedHotel->website ?: 'www.harmonyhotel.com' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-400">GSTIN No:</span>
                        <span class="font-bold text-slate-700 uppercase">{{ $selectedHotel->tax_id ?: '07ABCDE1234F1Z5' }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-[10px] text-center">
                        <div class="bg-slate-50 rounded-lg p-2 border border-slate-100">
                            <span class="text-slate-400 block font-medium uppercase">Check-In</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">02:00 PM</span>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2 border border-slate-100">
                            <span class="text-slate-400 block font-medium uppercase">Check-Out</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">11:00 AM</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        @endif

        {{-- Room Types List --}}
        <div class="space-y-6">
            @forelse($this->roomTypes as $type)
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden flex flex-col md:flex-row hover:shadow-md transition-shadow duration-300 group">
                <div class="w-full md:w-1/3 bg-slate-50 min-h-[180px] flex items-center justify-center relative border-r border-slate-100">
                    <i class="fas fa-bed text-5xl text-slate-300 group-hover:scale-110 transition-transform duration-300"></i>
                    <span class="absolute top-4 left-4 bg-white/80 backdrop-blur-sm border border-slate-200 text-slate-700 text-[10px] font-bold px-2.5 py-0.5 rounded-lg shadow-sm">
                        Max Guests: {{ $type->base_occupancy }}
                    </span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-655 transition-colors duration-250">{{ $type->name }}</h3>
                            <div class="text-right">
                                <span class="text-2xl font-black text-indigo-650">{{ $currencySymbol }}{{ number_format($type->base_price, 2) }}</span>
                                <span class="text-xs text-slate-400 block mt-0.5">/ night</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Experience ultimate comfort with luxury bedding, high-speed Wi-Fi, air conditioning, and a fully equipped private bathroom.</p>
                        
                        <div class="flex flex-wrap gap-2 mt-4">
                            <span class="bg-slate-50 border border-slate-150 text-[10px] font-semibold text-slate-600 px-2.5 py-1 rounded-lg"><i class="fas fa-wifi mr-1.5 text-indigo-600"></i>Free Wi-Fi</span>
                            <span class="bg-slate-50 border border-slate-150 text-[10px] font-semibold text-slate-600 px-2.5 py-1 rounded-lg"><i class="fas fa-coffee mr-1.5 text-indigo-600"></i>Breakfast included</span>
                            <span class="bg-slate-50 border border-slate-150 text-[10px] font-semibold text-slate-600 px-2.5 py-1 rounded-lg"><i class="fas fa-snowflake mr-1.5 text-indigo-600"></i>AC</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-100 pt-4 mt-6 flex justify-end">
                        <button wire:click="selectRoomType({{ $type->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 px-5 rounded-lg shadow-sm transition-all cursor-pointer flex items-center gap-1.5">
                            Select & Continue <i class="fas fa-arrow-right text-[10px] ml-0.5"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl border border-slate-100 p-12 text-center">
                <i class="fas fa-hotel text-4xl text-slate-300 mb-3"></i>
                <h3 class="font-bold text-slate-800">No rooms available</h3>
                <p class="text-xs text-slate-500 mt-1">Please adjust your dates or check back later.</p>
            </div>
            @endforelse
        </div>
        @endif

        {{-- Step 2: Checkout / Stripe Simulation --}}
        @if($step == 2)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            {{-- Guest Info & Payment --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3"><i class="fas fa-user-circle mr-2 text-indigo-600"></i>1. Guest Information</h2>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Full Name *</label>
                                <input type="text" wire:model="guest_name" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. Jane Doe">
                                @error('guest_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Email *</label>
                                <input type="email" wire:model="guest_email" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. janedoe@gmail.com">
                                @error('guest_email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Phone Number *</label>
                                <input type="text" wire:model="guest_phone" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. +91 9876543210">
                                @error('guest_phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Nationality *</label>
                                <input type="text" wire:model="guest_nationality" class="w-full border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                                @error('guest_nationality') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Stripe Gateway Mock Simulation --}}
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider"><i class="fab fa-cc-stripe mr-2 text-indigo-655"></i>2. Stripe Secure Payment</h2>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-705 border border-indigo-100 shadow-sm">
                            <i class="fab fa-stripe text-sm"></i> Mock Mode
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div class="border border-indigo-100 rounded-lg p-4 bg-indigo-50/50">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-info-circle text-indigo-600"></i>
                                <span class="text-xs font-bold text-indigo-805">Stripe Checkout Simulator</span>
                            </div>
                            <p class="text-[10px] text-slate-500 leading-relaxed">This component simulates the Stripe checkout process. In production, this launches the Stripe hosted checkout overlay or collects tokenized details securely.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Card Number</label>
                            <div class="relative">
                                <input type="text" readonly value="•••• •••• •••• 4242" class="w-full border border-slate-200 text-slate-400 rounded-lg px-3 py-2 text-sm focus:outline-none bg-slate-50">
                                <div class="absolute right-3 top-2.5 text-slate-400"><i class="far fa-credit-card"></i></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Expiry Date</label>
                                <input type="text" readonly value="12 / 29" class="w-full border border-slate-200 text-slate-400 rounded-lg px-3 py-2 text-sm focus:outline-none bg-slate-50">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">CVC</label>
                                <input type="text" readonly value="123" class="w-full border border-slate-200 text-slate-400 rounded-lg px-3 py-2 text-sm focus:outline-none bg-slate-50">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Sidebar --}}
            <div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3"><i class="fas fa-receipt mr-2 text-indigo-600"></i>Summary</h3>
                    <div class="space-y-4 text-xs">
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Room Type:</span>
                            <span class="font-bold text-slate-800">{{ $selectedRoomType->name }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-555 font-medium">Check-in:</span>
                            <span class="font-bold text-slate-800">{{ date('d M Y', strtotime($checkin_date)) }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-555 font-medium">Check-out:</span>
                            <span class="font-bold text-slate-800">{{ date('d M Y', strtotime($checkout_date)) }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-555 font-medium">Total Stay:</span>
                            <span class="font-bold text-slate-800">{{ $total_days }} Night(s)</span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-sm font-bold text-slate-800">Total Price:</span>
                            <span class="text-xl font-black text-indigo-655">{{ $currencySymbol }}{{ number_format($total_price, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button wire:click="processBooking" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-3 rounded-lg shadow-md transition-all cursor-pointer flex items-center justify-center gap-1.5">
                            <i class="fas fa-lock text-[10px]"></i> Pay & Confirm Reservation
                        </button>
                        <button wire:click="$set('step', 1)" class="w-full rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-655 font-semibold text-xs py-2.5 transition-all cursor-pointer">
                            Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Step 3: Success Screen --}}
        @if($step == 3)
        <div class="max-w-md mx-auto bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center animate-fadeIn relative overflow-hidden">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-100 shadow-sm">
                <i class="fas fa-check text-2xl animate-bounce"></i>
            </div>
            
            <h2 class="text-2xl font-bold text-slate-900">Booking Confirmed!</h2>
            <p class="text-xs text-slate-505 mt-1 leading-relaxed">Thank you for booking with us. Your invoice has been marked as paid.</p>

            <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 my-6 text-left space-y-3">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Booking Reference:</span>
                    <span class="font-extrabold text-slate-800">{{ $booking_number }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Guest Name:</span>
                    <span class="font-bold text-slate-800">{{ $guest_name }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Dates:</span>
                    <span class="font-bold text-slate-800">{{ date('d M', strtotime($checkin_date)) }} - {{ date('d M Y', strtotime($checkout_date)) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Amount Paid:</span>
                    <span class="font-black text-indigo-650">{{ $currencySymbol }}{{ number_format($total_price, 2) }}</span>
                </div>
                <div class="flex justify-between text-xs items-center">
                    <span class="text-slate-400">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-150">Paid (via Stripe)</span>
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors cursor-pointer">
                    <i class="fas fa-home text-[10px]"></i> Back to Portal
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
