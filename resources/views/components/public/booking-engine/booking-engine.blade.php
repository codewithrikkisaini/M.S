@php
    $selectedHotel = $this->selectedHotel;
    $currencySymbol = ($selectedHotel && in_array(strtoupper($selectedHotel->currency), ['INR', 'RS'])) ? '₹' : ($selectedHotel->currency ?? '₹');
@endphp
<div class="min-h-screen bg-slate-50 text-slate-800 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        {{-- Branding & Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold mb-3 shadow-sm">
                <i class="fas fa-shield-alt"></i> Direct Room Booking & Details Portal
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Complete Your Room Booking</h1>
            <p class="text-sm text-slate-500 mt-1">Review room details, check live availability, and reserve instantly</p>
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
                    <label class="block text-xs font-bold text-slate-600 mb-1">Check-in Date *</label>
                    <input type="date" wire:model.live="checkin_date" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Check-out Date *</label>
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

        {{-- Rooms List with Live Availability Badges --}}
        <div class="space-y-6">
            <h2 class="text-2xl font-bold text-slate-900">Available Rooms for {{ date('d M Y', strtotime($checkin_date)) }} - {{ date('d M Y', strtotime($checkout_date)) }}</h2>

            @forelse($this->rooms as $room)
            @php
                $typeName = $room->roomType->name ?? 'Standard Room';
                $price = $room->price ?: ($room->roomType->base_price ?? 2500);
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col md:flex-row hover:shadow-lg transition-all duration-300 group shadow-sm">
                <div class="w-full md:w-1/3 bg-slate-100 min-h-[180px] flex items-center justify-center relative border-r border-slate-200">
                    <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover">
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm border border-slate-200 text-slate-800 text-[11px] font-bold px-3 py-1 rounded-lg shadow-sm">
                        Room {{ $room->room_number }}
                    </span>
                    <span class="absolute bottom-4 left-4">
                        @if($room->is_available)
                            <span class="bg-emerald-500 text-white text-[10px] font-extrabold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-check-circle mr-1"></i> Available
                            </span>
                        @else
                            <span class="bg-rose-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-full shadow-md">
                                <i class="fas fa-times-circle mr-1"></i> Not Available / Occupied
                            </span>
                        @endif
                    </span>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $typeName }} (Room {{ $room->room_number }})</h3>
                                <p class="text-xs text-slate-500 mt-1">Bed Type: {{ ucfirst($room->bed_type ?? 'King / Queen Bed') }} | Max Occupancy: {{ $room->capacity ?? 2 }} Guests</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black text-blue-600">{{ $currencySymbol }}{{ number_format($price) }}</span>
                                <span class="text-xs text-slate-500 block mt-0.5">/ night</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-600 mt-3 leading-relaxed">Experience top-tier hospitality featuring premium bedding, high-speed Wi-Fi, air conditioning, daily housekeeping, and private bathroom.</p>
                        
                        <div class="flex flex-wrap gap-2 mt-4">
                            <span class="bg-slate-50 border border-slate-200 text-[10px] font-semibold text-slate-600 px-2.5 py-1 rounded-lg"><i class="fas fa-wifi mr-1.5 text-blue-500"></i>Free Wi-Fi</span>
                            <span class="bg-slate-50 border border-slate-200 text-[10px] font-semibold text-slate-600 px-2.5 py-1 rounded-lg"><i class="fas fa-coffee mr-1.5 text-blue-500"></i>Breakfast included</span>
                            <span class="bg-slate-50 border border-slate-200 text-[10px] font-semibold text-slate-600 px-2.5 py-1 rounded-lg"><i class="fas fa-snowflake mr-1.5 text-blue-500"></i>AC</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-100 pt-4 mt-6 flex justify-end items-center gap-3">
                        @if($room->is_available)
                            <button wire:click="selectRoom({{ $room->id }})" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2.5 px-6 rounded-xl shadow-md transition-all cursor-pointer flex items-center gap-1.5 hover:shadow-lg">
                                <i class="fas fa-calendar-check text-xs"></i> View Details & Book Room {{ $room->room_number }}
                            </button>
                        @else
                            <button disabled class="bg-slate-200 text-slate-400 font-bold text-xs py-2.5 px-6 rounded-xl cursor-not-allowed flex items-center gap-1.5">
                                <i class="fas fa-ban text-xs"></i> Not Available
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                <i class="fas fa-bed text-4xl text-slate-300 mb-3"></i>
                <h3 class="font-bold text-slate-800">No rooms found</h3>
                <p class="text-xs text-slate-500 mt-1">Please select another hotel or check back later.</p>
            </div>
            @endforelse
        </div>
        @endif

        {{-- Step 2: Room Details & Booking Form --}}
        @if($step == 2)
        <div class="mb-4 flex justify-between items-center">
            <button wire:click="$set('step', 1)" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition-all bg-white border border-slate-200 px-4 py-2 rounded-xl shadow-sm cursor-pointer">
                <i class="fas fa-arrow-left"></i> Back to Rooms List
            </button>
            
            <div class="text-xs font-bold text-slate-500">
                Hotel: <span class="text-slate-900">{{ $selectedHotel->name ?? 'Hotel' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Room Details Showcase Card (7 Columns) --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
                    {{-- Room Header Banner & Image --}}
                    <div class="relative aspect-video bg-slate-900">
                        <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1000&q=80" class="w-full h-full object-cover opacity-90">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-80"></div>
                        
                        <div class="absolute top-4 left-4">
                            @if($is_available)
                                <span class="bg-emerald-500 text-white text-xs font-black px-3.5 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 border border-emerald-400">
                                    <i class="fas fa-check-circle"></i> Available for Selected Dates
                                </span>
                            @else
                                <span class="bg-rose-600 text-white text-xs font-black px-3.5 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 border border-rose-400">
                                    <i class="fas fa-times-circle"></i> Not Available / Occupied
                                </span>
                            @endif
                        </div>

                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <span class="text-xs font-bold text-blue-300 uppercase tracking-widest block">{{ $selectedHotel->name ?? 'Hotel' }}</span>
                            <h2 class="text-2xl font-black text-white tracking-tight mt-0.5">
                                {{ $selectedRoomType->name ?? 'Luxury Suite' }} 
                                @if($selectedRoom)
                                    <span class="text-blue-400">(Room {{ $selectedRoom->room_number }})</span>
                                @endif
                            </h2>
                        </div>
                    </div>

                    {{-- Room Specifications & Highlights --}}
                    <div class="p-6 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Nightly Price</span>
                                <span class="text-3xl font-black text-blue-600">{{ $currencySymbol }}{{ number_format($selectedRoom->price ?? ($selectedRoomType->base_price ?? 2500)) }}</span>
                                <span class="text-xs text-slate-500">/ night</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Capacity</span>
                                <span class="text-sm font-bold text-slate-800"><i class="fas fa-users text-blue-500 mr-1"></i> {{ $selectedRoom->capacity ?? 2 }} Guests Max</span>
                                <span class="text-xs text-slate-500 block"><i class="fas fa-bed text-blue-500 mr-1"></i> {{ ucfirst($selectedRoom->bed_type ?? 'King Bed') }}</span>
                            </div>
                        </div>

                        {{-- Room Overview & Description --}}
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">Room Overview & Details</h3>
                            <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                                Experience ultimate comfort in Room {{ $selectedRoom->room_number ?? '101' }}. Designed with modern luxury aesthetics, premium mattresses, soundproof acoustic windows, complimentary high-speed Wi-Fi, 24/7 room service, and private en-suite bathroom.
                            </p>
                        </div>

                        {{-- Included Amenities --}}
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-3">Room Amenities & Features</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs font-semibold text-slate-700">
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2.5 rounded-xl"><i class="fas fa-wifi text-blue-500"></i> High-Speed Wi-Fi</div>
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2.5 rounded-xl"><i class="fas fa-snowflake text-blue-500"></i> Air Conditioning</div>
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2.5 rounded-xl"><i class="fas fa-tv text-blue-500"></i> HD Smart TV</div>
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2.5 rounded-xl"><i class="fas fa-utensils text-blue-500"></i> Free Breakfast</div>
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2.5 rounded-xl"><i class="fas fa-shower text-blue-500"></i> Private Bathroom</div>
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2.5 rounded-xl"><i class="fas fa-concierge-bell text-blue-500"></i> 24/7 Housekeeping</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Guest Booking Form (5 Columns) --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-6 sticky top-6">
                    <h2 class="text-lg font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-blue-600"></i> Reservation Details
                    </h2>

                    @error('booking')
                    <div class="mt-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs p-3.5 rounded-xl flex items-center gap-2 font-semibold">
                        <i class="fas fa-exclamation-triangle text-rose-500"></i> {{ $message }}
                    </div>
                    @enderror

                    {{-- Dates & Guest Selection Live --}}
                    <div class="mt-4 space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Check-in</label>
                                <input type="date" wire:model.live="checkin_date" class="w-full bg-white border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Check-out</label>
                                <input type="date" wire:model.live="checkout_date" class="w-full bg-white border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Guests</label>
                            <select wire:model.live="guests_count" class="w-full bg-white border border-slate-200 text-slate-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-blue-500">
                                <option value="1">1 Guest</option>
                                <option value="2">2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                            </select>
                        </div>
                    </div>

                    {{-- Price Breakdown Summary --}}
                    <div class="my-4 bg-blue-50/70 border border-blue-100 rounded-2xl p-4 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-600 font-semibold">
                            <span>Rate per Night:</span>
                            <span>{{ $currencySymbol }}{{ number_format($selectedRoom->price ?? ($selectedRoomType->base_price ?? 2500)) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 font-semibold">
                            <span>Duration:</span>
                            <span>{{ $total_days }} Night(s)</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-blue-200 text-slate-900 font-black">
                            <span class="text-sm">Total Payable:</span>
                            <span class="text-xl text-blue-700">{{ $currencySymbol }}{{ number_format($total_price, 2) }}</span>
                        </div>
                    </div>

                    {{-- Guest Info Form --}}
                    <div class="space-y-3 pt-2">
                        <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider"><i class="fas fa-user-edit mr-1 text-blue-500"></i> Guest Contact Info</h3>
                        
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Full Name *</label>
                            <input type="text" wire:model="guest_name" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3 py-2.5 text-xs font-medium focus:outline-none focus:border-blue-500" placeholder="e.g. Rahul Sharma">
                            @error('guest_name') <span class="text-[10px] text-rose-500 mt-0.5 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Email Address *</label>
                            <input type="email" wire:model="guest_email" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3 py-2.5 text-xs font-medium focus:outline-none focus:border-blue-500" placeholder="e.g. rahul@gmail.com">
                            @error('guest_email') <span class="text-[10px] text-rose-500 mt-0.5 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Phone Number *</label>
                            <input type="text" wire:model="guest_phone" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3 py-2.5 text-xs font-medium focus:outline-none focus:border-blue-500" placeholder="e.g. +91 9876543210">
                            @error('guest_phone') <span class="text-[10px] text-rose-500 mt-0.5 block font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nationality</label>
                            <input type="text" wire:model="guest_nationality" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3 py-2.5 text-xs font-medium focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    {{-- Payment Method Selection --}}
                    <div class="pt-4">
                        <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2.5"><i class="fas fa-wallet mr-1 text-blue-500"></i> Payment Mode</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="payment_method" value="Card" class="peer sr-only">
                                <div class="rounded-xl border border-slate-200 bg-white p-2.5 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all text-xs font-bold">
                                    <i class="fas fa-credit-card mr-1 text-blue-500"></i> Card
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="payment_method" value="UPI" class="peer sr-only">
                                <div class="rounded-xl border border-slate-200 bg-white p-2.5 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all text-xs font-bold">
                                    <i class="fas fa-qrcode mr-1 text-blue-500"></i> UPI
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="payment_method" value="Net Banking" class="peer sr-only">
                                <div class="rounded-xl border border-slate-200 bg-white p-2.5 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all text-xs font-bold">
                                    <i class="fas fa-university mr-1 text-blue-500"></i> Net Banking
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="payment_method" value="Cash" class="peer sr-only">
                                <div class="rounded-xl border border-slate-200 bg-white p-2.5 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all text-xs font-bold">
                                    <i class="fas fa-money-bill-wave mr-1 text-blue-500"></i> Pay at Hotel
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- CTA Submit Button --}}
                    <div class="pt-6">
                        @if($is_available)
                            <button wire:click="processBooking" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black text-sm py-4 rounded-2xl shadow-lg shadow-blue-600/30 transition-all cursor-pointer flex items-center justify-center gap-2 hover:shadow-xl hover:scale-[1.01]">
                                <i class="fas fa-lock text-xs"></i> Confirm & Book Room {{ $selectedRoom->room_number ?? '' }} Now
                            </button>
                        @else
                            <button disabled class="w-full bg-slate-300 text-slate-500 font-bold text-sm py-4 rounded-2xl cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fas fa-ban text-xs"></i> Room Not Available for Selected Dates
                            </button>
                        @endif
                    </div>

                </div>
            </div>

        </div>
        @endif

        {{-- Step 3: Success Screen --}}
        @if($step == 3)
        <div class="max-w-md mx-auto bg-white rounded-3xl border border-slate-200 shadow-2xl p-8 text-center animate-fadeIn relative overflow-hidden my-8">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-100 shadow-sm">
                <i class="fas fa-check-circle text-4xl"></i>
            </div>
            
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Room Booking Requested!</h2>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">Your reservation request for Room {{ $selectedRoom->room_number ?? '' }} has been submitted and is currently pending approval by the hotel administration.</p>

            <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-5 my-6 text-left space-y-3">
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
                    <span class="text-slate-500">Room:</span>
                    <span class="font-bold text-slate-800">Room {{ $selectedRoom->room_number ?? '101' }} ({{ $selectedRoomType->name ?? 'Standard' }})</span>
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
                <a href="{{ url('/') }}" class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm py-3.5 rounded-2xl shadow-md transition-all cursor-pointer">
                    <i class="fas fa-search text-xs"></i> Find More Hotels
                </a>
                <a href="{{ url('/track') }}" class="w-full inline-flex items-center justify-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm py-3.5 rounded-2xl transition-all cursor-pointer">
                    <i class="fas fa-ticket-alt text-xs"></i> Track Booking
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
