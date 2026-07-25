@php
    $selectedHotel = $this->selectedHotel;
    $currencySymbol = ($selectedHotel && in_array(strtoupper($selectedHotel->currency), ['INR', 'RS'])) ? '₹' : ($selectedHotel->currency ?? '$');
@endphp
<div class="min-h-screen bg-slate-50 text-slate-800 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        {{-- Branding & Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold mb-3 shadow-sm">
                <i class="fas fa-hotel"></i> Secure Reservation Portal
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Complete Your Booking</h1>
            <p class="text-sm text-slate-500 mt-1">Book your stay instantly with our direct reservation system</p>
        </div>

        {{-- Progress Bar --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 1 ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-400 border border-slate-200' }}">1</span>
            <div class="w-12 h-1 {{ $step >= 2 ? 'bg-blue-600' : 'bg-slate-200' }}"></div>
            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 2 ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-400 border border-slate-200' }}">2</span>
            <div class="w-12 h-1 {{ $step >= 3 ? 'bg-blue-600' : 'bg-slate-200' }}"></div>
            <span class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 3 ? 'bg-emerald-500 text-white shadow-md' : 'bg-white text-slate-400 border border-slate-200' }}">
                <i class="fas fa-check"></i>
            </span>
        </div>

        {{-- Step 1: Search & Room List --}}
        @if($step == 1)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Select Hotel *</label>
                    <select wire:model.live="hotel_id" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @foreach($hotels as $h)
                        <option value="{{ $h->id }}">{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Check-in *</label>
                    <input type="date" wire:model.live="checkin_date" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Check-out *</label>
                    <input type="date" wire:model.live="checkout_date" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Guests *</label>
                    <select wire:model.live="guests_count" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
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
                $sortedImages = collect($selectedHotel->images)->sortByDesc('is_primary');
                foreach ($sortedImages as $img) {
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
            <div class="md:col-span-3 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex flex-col justify-between" x-data="{ activeIndex: 0, images: @js($selectedHotelImages) }">
                <div class="relative aspect-video rounded-xl overflow-hidden bg-slate-100 flex-1 border border-slate-200">
                    <template x-for="(img, index) in images" :key="index">
                        <img x-show="activeIndex === index" :src="img" class="w-full h-full object-cover transition-all duration-500">
                    </template>
                    
                    {{-- Nav Controls --}}
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-4 flex justify-between items-center text-white text-xs">
                        <span class="font-bold uppercase tracking-wider text-[10px]" x-text="'Photo ' + (activeIndex + 1) + ' of ' + images.length"></span>
                        <div class="flex gap-2">
                            <button @click="activeIndex = (activeIndex - 1 + images.length) % images.length" class="w-7 h-7 rounded-full bg-white/20 hover:bg-white/40 flex items-center justify-center cursor-pointer transition-all border border-white/20 backdrop-blur-md"><i class="fas fa-chevron-left text-[10px]"></i></button>
                            <button @click="activeIndex = (activeIndex + 1) % images.length" class="w-7 h-7 rounded-full bg-white/20 hover:bg-white/40 flex items-center justify-center cursor-pointer transition-all border border-white/20 backdrop-blur-md"><i class="fas fa-chevron-right text-[10px]"></i></button>
                        </div>
                    </div>
                </div>
                
                {{-- Thumbnails --}}
                <div class="flex gap-2 overflow-x-auto pt-3 pb-1 scrollbar-thin">
                    <template x-for="(img, index) in images" :key="index">
                        <button @click="activeIndex = index" class="w-16 h-12 rounded-lg overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0" :class="activeIndex === index ? 'border-blue-500 scale-95 shadow-md shadow-blue-500/20' : 'border-transparent opacity-60 hover:opacity-100'">
                            <img :src="img" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

            {{-- Hotel Info & Specifications (Column Span 2) --}}
            <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded shadow-sm">
                            {{ $selectedHotel->property_type ?: 'Boutique Hotel' }}
                        </span>
                        <div class="flex items-center gap-0.5 text-amber-400 text-xs">
                            @for($i = 0; $i < $ratingStars; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-slate-900 tracking-tight leading-snug">
                        {{ $selectedHotel->name }}
                    </h2>

                    <div class="flex items-start gap-1.5 text-xs text-slate-500">
                        <i class="fas fa-map-marker-alt text-blue-500 mt-0.5"></i>
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
                        <span class="font-medium text-slate-500">Email:</span>
                        <span class="font-bold text-slate-800">{{ $selectedHotel->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">Phone:</span>
                        <span class="font-bold text-slate-800">{{ $selectedHotel->phone ?: '+91 9876543210' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-slate-500">Website:</span>
                        <span class="font-bold text-blue-600">{{ $selectedHotel->website ?: 'www.harmonyhotel.com' }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-[10px] text-center">
                        <div class="bg-slate-50 rounded-lg p-2 border border-slate-200">
                            <span class="text-slate-500 block font-medium uppercase">Check-In</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">02:00 PM</span>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2 border border-slate-200">
                            <span class="text-slate-500 block font-medium uppercase">Check-Out</span>
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
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col md:flex-row hover:shadow-lg transition-all duration-300 group shadow-sm">
                <div class="w-full md:w-1/3 bg-slate-100 min-h-[180px] flex items-center justify-center relative border-r border-slate-200">
                    <i class="fas fa-bed text-5xl text-slate-400 group-hover:scale-110 group-hover:text-blue-500 transition-all duration-300"></i>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm border border-slate-200 text-slate-700 text-[10px] font-bold px-2.5 py-0.5 rounded-lg shadow-sm">
                        Max Guests: {{ $type->base_occupancy }}
                    </span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors duration-250">{{ $type->name }}</h3>
                            <div class="text-right">
                                <span class="text-2xl font-black text-blue-600">{{ $currencySymbol }}{{ number_format($type->base_price, 2) }}</span>
                                <span class="text-xs text-slate-500 block mt-0.5">/ night</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">Experience ultimate comfort with luxury bedding, high-speed Wi-Fi, air conditioning, and a fully equipped private bathroom.</p>
                        
                        <div class="flex flex-wrap gap-2 mt-4">
                            <span class="bg-slate-50 border border-slate-200 text-[10px] font-semibold text-slate-600 px-2.5 py-1 rounded-lg"><i class="fas fa-wifi mr-1.5 text-blue-500"></i>Free Wi-Fi</span>
                            <span class="bg-slate-50 border border-slate-200 text-[10px] font-semibold text-slate-600 px-2.5 py-1 rounded-lg"><i class="fas fa-coffee mr-1.5 text-blue-500"></i>Breakfast included</span>
                            <span class="bg-slate-50 border border-slate-200 text-[10px] font-semibold text-slate-600 px-2.5 py-1 rounded-lg"><i class="fas fa-snowflake mr-1.5 text-blue-500"></i>AC</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-100 pt-4 mt-6 flex justify-end">
                        <button wire:click="selectRoomType({{ $type->id }})" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2.5 px-6 rounded-lg shadow-md transition-all cursor-pointer flex items-center gap-1.5 hover:shadow-lg">
                            Select & Continue <i class="fas fa-arrow-right text-[10px] ml-0.5"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                <i class="fas fa-bed text-4xl text-slate-300 mb-3"></i>
                <h3 class="font-bold text-slate-800">No rooms available</h3>
                <p class="text-xs text-slate-500 mt-1">Please adjust your dates or check back later.</p>
            </div>
            @endforelse
        </div>
        @endif

        {{-- Step 2: Checkout / Payment Selection --}}
        @if($step == 2)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            {{-- Guest Info & Payment --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3"><i class="fas fa-user-circle mr-2 text-blue-500"></i>1. Guest Information</h2>
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Full Name *</label>
                                <input type="text" wire:model="guest_name" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="e.g. Jane Doe">
                                @error('guest_name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Email *</label>
                                <input type="email" wire:model="guest_email" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="e.g. janedoe@gmail.com">
                                @error('guest_email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Phone Number *</label>
                                <input type="text" wire:model="guest_phone" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="e.g. +91 9876543210">
                                @error('guest_phone') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Nationality *</label>
                                <input type="text" wire:model="guest_nationality" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                @error('guest_nationality') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Payment Method Selection --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider"><i class="fas fa-wallet mr-2 text-blue-500"></i>2. Payment Method</h2>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="payment_method" value="Card" class="peer sr-only">
                            <div class="rounded-xl border border-slate-200 bg-white p-4 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-600 transition-all">
                                <i class="fas fa-credit-card text-2xl mb-2 text-slate-400 peer-checked:text-blue-600 block"></i>
                                <span class="text-xs font-bold text-slate-600 peer-checked:text-blue-700 block">Card</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="payment_method" value="UPI" class="peer sr-only">
                            <div class="rounded-xl border border-slate-200 bg-white p-4 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-600 transition-all">
                                <i class="fas fa-qrcode text-2xl mb-2 text-slate-400 peer-checked:text-blue-600 block"></i>
                                <span class="text-xs font-bold text-slate-600 peer-checked:text-blue-700 block">UPI</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="payment_method" value="Net Banking" class="peer sr-only">
                            <div class="rounded-xl border border-slate-200 bg-white p-4 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-600 transition-all">
                                <i class="fas fa-university text-2xl mb-2 text-slate-400 peer-checked:text-blue-600 block"></i>
                                <span class="text-xs font-bold text-slate-600 peer-checked:text-blue-700 block">Net Banking</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="payment_method" value="Cash" class="peer sr-only">
                            <div class="rounded-xl border border-slate-200 bg-white p-4 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-600 transition-all">
                                <i class="fas fa-money-bill-wave text-2xl mb-2 text-slate-400 peer-checked:text-blue-600 block"></i>
                                <span class="text-xs font-bold text-slate-600 peer-checked:text-blue-700 block">Pay at Hotel</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Summary Sidebar --}}
            <div>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sticky top-6">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-100 pb-3"><i class="fas fa-receipt mr-2 text-blue-500"></i>Summary</h3>
                    <div class="space-y-4 text-xs">
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Room Type:</span>
                            <span class="font-bold text-slate-800">{{ $selectedRoomType->name }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Check-in:</span>
                            <span class="font-bold text-slate-800">{{ date('d M Y', strtotime($checkin_date)) }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Check-out:</span>
                            <span class="font-bold text-slate-800">{{ date('d M Y', strtotime($checkout_date)) }}</span>
                        </div>
                        <div class="flex justify-between pb-3 border-b border-slate-100">
                            <span class="text-slate-500 font-medium">Total Stay:</span>
                            <span class="font-bold text-slate-800">{{ $total_days }} Night(s)</span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-sm font-bold text-slate-800">Total Price:</span>
                            <span class="text-xl font-black text-blue-600">{{ $currencySymbol }}{{ number_format($total_price, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button wire:click="processBooking" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm py-3.5 rounded-xl shadow-lg shadow-blue-600/20 transition-all cursor-pointer flex items-center justify-center gap-2 hover:shadow-xl">
                            <i class="fas fa-lock text-xs"></i> Confirm Reservation
                        </button>
                        <button wire:click="$set('step', 1)" class="w-full rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-semibold text-sm py-3 transition-all cursor-pointer">
                            Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Step 3: Success Screen --}}
        @if($step == 3)
        <div class="max-w-md mx-auto bg-white rounded-2xl border border-slate-200 shadow-xl p-8 text-center animate-fadeIn relative overflow-hidden">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-100 shadow-sm">
                <i class="fas fa-check-circle text-4xl"></i>
            </div>
            
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Booking Requested!</h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Your reservation request has been submitted and is currently pending approval by the hotel administration.</p>

            <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-5 my-6 text-left space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">PNR (Tracker):</span>
                    <span class="font-black text-blue-700 tracking-wider text-base">{{ $pnr }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Booking Ref:</span>
                    <span class="font-bold text-slate-800">{{ $booking_number }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Guest Name:</span>
                    <span class="font-bold text-slate-800">{{ $guest_name }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Dates:</span>
                    <span class="font-bold text-slate-800">{{ date('d M', strtotime($checkin_date)) }} - {{ date('d M Y', strtotime($checkout_date)) }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500">Payment Mode:</span>
                    <span class="font-bold text-slate-800">{{ $payment_method }}</span>
                </div>
                <div class="flex justify-between text-xs items-center border-t border-blue-100 pt-2">
                    <span class="text-slate-500">Status:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200">
                        <i class="fas fa-clock mr-1"></i> Pending Approval
                    </span>
                </div>
            </div>

            <p class="text-xs text-slate-500 mb-6">You will receive an email confirmation once the hotel approves your reservation.</p>

            <div class="space-y-3">
                <a href="{{ url('/') }}" class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm py-3.5 rounded-xl shadow-md transition-all cursor-pointer">
                    <i class="fas fa-search text-xs"></i> Find More Hotels
                </a>
                <a href="{{ url('/track') }}" class="w-full inline-flex items-center justify-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm py-3.5 rounded-xl transition-all cursor-pointer">
                    <i class="fas fa-ticket-alt text-xs"></i> Track Booking
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
