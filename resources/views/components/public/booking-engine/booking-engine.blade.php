@php
    $selectedHotel = $this->selectedHotel;
    $currencySymbol = ($selectedHotel && in_array(strtoupper($selectedHotel->currency), ['INR', 'RS'])) ? '₹' : ($selectedHotel->currency ?? '$');
@endphp
<div class="min-h-screen bg-slate-950/40 py-10 px-4 sm:px-6 lg:px-8 relative">
    {{-- Glow Accents --}}
    <div class="absolute top-10 left-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl mx-auto relative z-10">
        {{-- Branding & Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-950/60 border border-indigo-900/50 text-indigo-400 text-xs font-semibold mb-3">
                <i class="fas fa-sparkles text-[10px]"></i> Direct Guest Booking Engine
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">Direct Reservation Portal</h1>
            <p class="text-sm text-slate-400 mt-1">Book your luxury stay with automated check-in and secure billing</p>
        </div>

        {{-- Progress Bar --}}
        <div class="flex items-center justify-center gap-2 mb-10">
            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 1 ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg shadow-indigo-500/20' : 'bg-slate-900 border border-slate-800 text-slate-400' }}">1</span>
            <div class="w-12 h-0.5 {{ $step >= 2 ? 'bg-indigo-500' : 'bg-slate-800' }}"></div>
            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 2 ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg shadow-indigo-500/20' : 'bg-slate-900 border border-slate-800 text-slate-400' }}">2</span>
            <div class="w-12 h-0.5 {{ $step >= 3 ? 'bg-indigo-500' : 'bg-slate-800' }}"></div>
            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 3 ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg shadow-indigo-500/20' : 'bg-slate-900 border border-slate-800 text-slate-400' }}">3</span>
        </div>

        {{-- Step 1: Search & Room List --}}
        @if($step == 1)
        <div class="bg-slate-900/60 rounded-3xl border border-slate-800/80 backdrop-blur-md shadow-2xl p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wider">Select Hotel *</label>
                    <select wire:model.live="hotel_id" class="w-full bg-slate-950/60 border border-slate-800 text-white rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30">
                        @foreach($hotels as $h)
                        <option value="{{ $h->id }}" class="bg-slate-900 text-white">{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wider">Check-in *</label>
                    <input type="date" wire:model.live="checkin_date" class="w-full bg-slate-950/60 border border-slate-800 text-white rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wider">Check-out *</label>
                    <input type="date" wire:model.live="checkout_date" class="w-full bg-slate-950/60 border border-slate-800 text-white rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wider">Guests *</label>
                    <select wire:model.live="guests_count" class="w-full bg-slate-950/60 border border-slate-800 text-white rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30">
                        <option value="1" class="bg-slate-900 text-white">1 Guest</option>
                        <option value="2" class="bg-slate-900 text-white">2 Guests</option>
                        <option value="3" class="bg-slate-900 text-white">3 Guests</option>
                        <option value="4" class="bg-slate-900 text-white">4 Guests</option>
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
        @endphp        @if($selectedHotel)
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8 items-stretch">
            
            {{-- Multi-image Slider (Column Span 3) --}}
            <div class="md:col-span-3 bg-slate-900/60 rounded-3xl border border-slate-800/80 backdrop-blur-md shadow-2xl p-4 flex flex-col justify-between" x-data="{ activeIndex: 0, images: @js($selectedHotelImages) }">
                <div class="relative aspect-video rounded-2xl overflow-hidden bg-slate-950 flex-1">
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
                        <button @click="activeIndex = index" class="w-16 h-12 rounded-xl overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0" :class="activeIndex === index ? 'border-indigo-500 scale-95 shadow-lg shadow-indigo-500/20' : 'border-transparent opacity-50 hover:opacity-100'">
                            <img :src="img" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

            {{-- Hotel Info & Specifications (Column Span 2) --}}
            <div class="md:col-span-2 bg-slate-900/60 rounded-3xl border border-slate-800/80 backdrop-blur-md shadow-2xl p-6 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-indigo-950/80 border border-indigo-900/50 text-indigo-400 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded shadow-sm">
                            {{ $selectedHotel->property_type ?: 'Boutique Hotel' }}
                        </span>
                        <div class="flex items-center gap-0.5 text-amber-500 text-xs">
                            @for($i = 0; $i < $ratingStars; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                    </div>

                    <h2 class="text-xl font-black text-white tracking-tight leading-snug">
                        {{ $selectedHotel->name }}
                    </h2>

                    <div class="flex items-start gap-1.5 text-xs text-slate-450">
                        <i class="fas fa-map-marker-alt text-indigo-400 mt-0.5 animate-pulse"></i>
                        <span>
                            {{ $selectedHotel->address }}, {{ $selectedHotel->city }}, {{ $selectedHotel->state }}, {{ $selectedHotel->country }} - {{ $selectedHotel->postal_code }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-400 leading-relaxed border-t border-slate-800/60 pt-3">
                        Experience premium comfort, world-class hospitality, and upscale amenities in a modern luxury environment tailored for your absolute relaxation.
                    </p>
                </div>

                {{-- Contact & Policy details --}}
                <div class="border-t border-slate-800/60 pt-3 space-y-2 text-[11px] text-slate-400">
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">Email:</span>
                        <span class="font-bold text-slate-200">{{ $selectedHotel->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">Phone:</span>
                        <span class="font-bold text-slate-200">{{ $selectedHotel->phone ?: '+91 9876543210' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">Website:</span>
                        <span class="font-bold text-indigo-400">{{ $selectedHotel->website ?: 'www.harmonyhotel.com' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">GSTIN No:</span>
                        <span class="font-bold text-slate-350 uppercase">{{ $selectedHotel->tax_id ?: '07ABCDE1234F1Z5' }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-800/60 text-[10px] text-center">
                        <div class="bg-slate-950/40 rounded-xl p-2 border border-slate-850">
                            <span class="text-slate-500 block font-medium uppercase">Check-In</span>
                            <span class="font-bold text-slate-300 mt-0.5 block">02:00 PM</span>
                        </div>
                        <div class="bg-slate-950/40 rounded-xl p-2 border border-slate-850">
                            <span class="text-slate-500 block font-medium uppercase">Check-Out</span>
                            <span class="font-bold text-slate-300 mt-0.5 block">11:00 AM</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        @endifif

        {{-- Room Types List --}}
        <div class="space-y-6">
            @forelse($this->roomTypes as $type)
            <div class="bg-slate-900/60 rounded-3xl border border-slate-800/80 backdrop-blur-md overflow-hidden flex flex-col md:flex-row hover:border-slate-700/80 transition-all duration-300 group shadow-2xl">
                <div class="w-full md:w-1/3 bg-slate-950/60 min-h-[180px] flex items-center justify-center relative border-r border-slate-800/65">
                    <i class="fas fa-bed text-5xl text-slate-800 group-hover:scale-110 transition-transform duration-300"></i>
                    <span class="absolute top-4 left-4 bg-slate-950/80 backdrop-blur-sm border border-slate-800 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-xl shadow-md">
                        Max Guests: {{ $type->base_occupancy }}
                    </span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-black text-white tracking-tight group-hover:text-indigo-400 transition-colors duration-250">{{ $type->name }}</h3>
                            <div class="text-right">
                                <span class="text-2xl font-black text-indigo-400">{{ $currencySymbol }}{{ number_format($type->base_price, 2) }}</span>
                                <span class="text-xs text-slate-550 block mt-0.5">/ night</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">Experience ultimate comfort with luxury bedding, high-speed Wi-Fi, air conditioning, and a fully equipped private bathroom.</p>
                        
                        <div class="flex flex-wrap gap-2 mt-4">
                            <span class="bg-slate-950/50 border border-slate-800/60 text-[10px] font-semibold text-slate-450 px-2.5 py-1 rounded-lg"><i class="fas fa-wifi mr-1.5 text-indigo-400"></i>Free Wi-Fi</span>
                            <span class="bg-slate-950/50 border border-slate-800/60 text-[10px] font-semibold text-slate-455 px-2.5 py-1 rounded-lg"><i class="fas fa-coffee mr-1.5 text-indigo-400"></i>Breakfast included</span>
                            <span class="bg-slate-950/50 border border-slate-800/60 text-[10px] font-semibold text-slate-455 px-2.5 py-1 rounded-lg"><i class="fas fa-snowflake mr-1.5 text-indigo-400"></i>AC</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-800/60 pt-4 mt-6 flex justify-end">
                        <button wire:click="selectRoomType({{ $type->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 px-5 rounded-xl shadow-lg shadow-indigo-500/20 transition-all cursor-pointer flex items-center gap-1.5">
                            Select & Continue <i class="fas fa-arrow-right text-[10px] ml-0.5"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-slate-900/60 rounded-3xl border border-slate-800/80 backdrop-blur-md p-12 text-center shadow-2xl">
                <i class="fas fa-hotel text-4xl text-slate-700 mb-3"></i>
                <h3 class="font-bold text-slate-300">No rooms available</h3>
                <p class="text-xs text-slate-500 mt-1">Please adjust your dates or check back later.</p>
            </div>
            @endforelse
        </div>
        @endif        {{-- Step 2: Checkout / Stripe Simulation --}}
        @if($step == 2)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            {{-- Guest Info & Payment --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-slate-900/60 rounded-3xl border border-slate-800/80 backdrop-blur-md shadow-2xl p-6">
                    <h2 class="text-sm font-black text-white uppercase tracking-wider mb-4 border-b border-slate-800/60 pb-3"><i class="fas fa-user-circle mr-2 text-indigo-400"></i>1. Guest Information</h2>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Full Name *</label>
                                <input type="text" wire:model="guest_name" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30" placeholder="e.g. Jane Doe">
                                @error('guest_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Email *</label>
                                <input type="email" wire:model="guest_email" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30" placeholder="e.g. janedoe@gmail.com">
                                @error('guest_email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Phone Number *</label>
                                <input type="text" wire:model="guest_phone" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30" placeholder="e.g. +91 9876543210">
                                @error('guest_phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Nationality *</label>
                                <input type="text" wire:model="guest_nationality" class="w-full bg-slate-950/60 text-white border border-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30">
                                @error('guest_nationality') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Stripe Gateway Mock Simulation --}}
                <div class="bg-slate-900/60 rounded-3xl border border-slate-800/80 backdrop-blur-md shadow-2xl p-6">
                    <div class="flex justify-between items-center mb-4 border-b border-slate-800/60 pb-3">
                        <h2 class="text-sm font-black text-white uppercase tracking-wider"><i class="fab fa-cc-stripe mr-2 text-indigo-400"></i>2. Stripe Secure Payment</h2>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-950/80 text-indigo-400 border border-indigo-900/50 shadow-sm">
                            <i class="fab fa-stripe text-sm"></i> Mock Mode
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div class="border border-slate-800/70 rounded-xl p-4 bg-slate-950/40">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fas fa-info-circle text-indigo-400"></i>
                                <span class="text-xs font-bold text-slate-300">Stripe Checkout Simulator</span>
                            </div>
                            <p class="text-[10px] text-slate-455 leading-relaxed">This component simulates the Stripe checkout process. In production, this launches the Stripe hosted checkout overlay or collects tokenized details securely.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Card Number</label>
                            <div class="relative">
                                <input type="text" readonly value="•••• •••• •••• 4242" class="w-full bg-slate-950/60 text-slate-400 border border-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none">
                                <div class="absolute right-3 top-2.5 text-slate-550"><i class="far fa-credit-card"></i></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 mb-1.5">Expiry Date</label>
                                <input type="text" readonly value="12 / 29" class="w-full bg-slate-950/60 text-slate-400 border border-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-400 mb-1.5">CVC</label>
                                <input type="text" readonly value="123" class="w-full bg-slate-950/60 text-slate-400 border border-slate-800 rounded-xl px-3 py-2 text-sm focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Sidebar --}}
            <div>
                <div class="bg-slate-900/60 rounded-3xl border border-slate-800/80 backdrop-blur-md shadow-2xl p-6 sticky top-6">
                    <h3 class="text-sm font-black text-white uppercase tracking-wider mb-4 border-b border-slate-800/60 pb-3"><i class="fas fa-receipt mr-2 text-indigo-400"></i>Summary</h3>
                    <div class="space-y-4 text-xs">
                        <div class="flex justify-between pb-3 border-b border-slate-800/40">
                            <span class="text-slate-455 font-medium">Room Type:</span>
                            <span class="font-bold text-slate-200">{{ $selectedRoomType->name }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-800/40">
                            <span class="text-slate-455 font-medium">Check-in:</span>
                            <span class="font-bold text-slate-200">{{ date('d M Y', strtotime($checkin_date)) }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-800/40">
                            <span class="text-slate-455 font-medium">Check-out:</span>
                            <span class="font-bold text-slate-200">{{ date('d M Y', strtotime($checkout_date)) }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-800/40">
                            <span class="text-slate-455 font-medium">Total Stay:</span>
                            <span class="font-bold text-slate-200">{{ $total_days }} Night(s)</span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-sm font-bold text-white">Total Price:</span>
                            <span class="text-xl font-black text-indigo-400">{{ $currencySymbol }}{{ number_format($total_price, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button wire:click="processBooking" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs py-3 rounded-xl shadow-lg shadow-emerald-500/10 transition-all cursor-pointer flex items-center justify-center gap-1.5">
                            <i class="fas fa-lock text-[10px]"></i> Pay & Confirm Reservation
                        </button>
                        <button wire:click="$set('step', 1)" class="w-full bg-slate-850 hover:bg-slate-800 text-slate-300 font-bold text-xs py-2.5 rounded-xl transition-all cursor-pointer border border-slate-700/60">
                            Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif     </div>
        @endif

        {{-- Step 3: Success Screen --}}
        @if($step == 3)
        <div class="max-w-md mx-auto bg-slate-900/60 rounded-3xl border border-slate-800/80 backdrop-blur-md shadow-2xl p-8 text-center animate-fadeIn relative overflow-hidden">
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>
            
            <div class="w-16 h-16 bg-emerald-950/80 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-900/50 shadow-lg">
                <i class="fas fa-check text-2xl animate-bounce"></i>
            </div>
            
            <h2 class="text-2xl font-black text-white">Booking Confirmed!</h2>
            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Thank you for booking with us. Your invoice has been marked as paid.</p>

            <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-5 my-6 text-left space-y-3 shadow-inner">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Booking Reference:</span>
                    <span class="font-extrabold text-slate-200">{{ $booking_number }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Guest Name:</span>
                    <span class="font-bold text-slate-200">{{ $guest_name }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Dates:</span>
                    <span class="font-bold text-slate-200">{{ date('d M', strtotime($checkin_date)) }} - {{ date('d M Y', strtotime($checkout_date)) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Amount Paid:</span>
                    <span class="font-black text-indigo-400">{{ $currencySymbol }}{{ number_format($total_price, 2) }}</span>
                </div>
                <div class="flex justify-between text-xs items-center">
                    <span class="text-slate-500">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950/80 text-emerald-400 border border-emerald-900/40">Paid (via Stripe)</span>
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors cursor-pointer">
                    <i class="fas fa-home text-[10px]"></i> Back to Portal
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
