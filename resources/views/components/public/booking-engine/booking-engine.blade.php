@php
    $selectedHotel = $this->selectedHotel;
    $selectedRoom = $this->selectedRoom;
    $selectedRoomType = $this->selectedRoomType;
    $hotels = $this->hotels;
    $currencySymbol = ($selectedHotel && in_array(strtoupper($selectedHotel->currency ?? ''), ['INR', 'RS'])) ? '₹' : ($selectedHotel->currency ?? '₹');
@endphp
<style>
@media print {
    body {
        background: #ffffff !important;
        color: #000000 !important;
    }
    .no-print, header, footer, nav, .navbar {
        display: none !important;
    }
    #printable-voucher {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }
}
</style>

<div class="min-h-screen bg-slate-50 text-slate-800 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        {{-- Branding & Header --}}
        <div class="text-center mb-8 no-print">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold mb-3 shadow-sm">
                <i class="fas fa-shield-alt"></i> Direct Room Booking & Details Portal
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Complete Your Room Booking</h1>
            <p class="text-sm text-slate-500 mt-1">Review room details, check live availability, and reserve instantly</p>
        </div>

        {{-- Progress Bar --}}
        <div class="flex items-center justify-center gap-2 mb-8 no-print">
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
                <div class="w-full md:w-1/3 bg-slate-100 min-h-[200px] flex items-center justify-center relative border-r border-slate-200 overflow-hidden group/img">
                    <img src="{{ $room->image_url }}" class="w-full h-full object-cover group-hover/img:scale-105 transition-transform duration-500">
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm border border-slate-200 text-slate-800 text-[11px] font-bold px-3 py-1 rounded-lg shadow-sm">
                        Room {{ $room->room_number }}
                    </span>
                    @if(count($room->images) > 1)
                        <span class="absolute top-4 right-4 bg-slate-900/70 backdrop-blur-sm border border-white/20 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm flex items-center gap-1">
                            <i class="fas fa-images text-blue-400"></i> {{ count($room->images) }} Photos
                        </span>
                    @endif
                    <span class="absolute bottom-4 left-4 z-10">
                        @if($room->is_available)
                            <span class="bg-emerald-500 text-white text-[10px] font-extrabold px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> Available
                            </span>
                        @else
                            <span class="bg-rose-600 text-white text-[10px] font-extrabold px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                                <i class="fas fa-times-circle"></i> Not Available / Occupied
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
                                <i class="fas fa-ban text-xs"></i> Not Available / Occupied
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

        {{-- Step 2: Room Details & Customer Booking Form --}}
        @if($step == 2)
        @php
            $roomImages = $selectedRoom ? $selectedRoom->images : [
                'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80'
            ];
        @endphp
        <div class="mb-4 flex justify-between items-center">
            <button wire:click="$set('step', 1)" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-blue-600 transition-all bg-white border border-slate-200 px-4 py-2 rounded-xl shadow-sm cursor-pointer">
                <i class="fas fa-arrow-left"></i> Back to Rooms List
            </button>
            
            <div class="text-xs font-bold text-slate-500">
                Hotel: <span class="text-slate-900">{{ $selectedHotel->name ?? 'Hotel' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Room Showcase Card (7 Columns) --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden" x-data="{ activeRoomImgIdx: 0, rmImages: @js($roomImages) }">
                    <div class="relative aspect-video bg-slate-900 overflow-hidden border-b border-slate-200">
                        <template x-for="(rImg, rIdx) in rmImages" :key="rIdx">
                            <img x-show="activeRoomImgIdx === rIdx" :src="rImg" class="w-full h-full object-cover transition-all duration-500">
                        </template>

                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30 pointer-events-none"></div>

                        <div class="absolute top-4 left-4 z-10">
                            <span class="bg-emerald-500 text-white text-xs font-black px-3.5 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 border border-emerald-400">
                                <i class="fas fa-check-circle"></i> Available for Instant Booking
                            </span>
                        </div>

                        <div class="absolute inset-x-0 bottom-0 p-4 flex justify-between items-end text-white text-xs z-10">
                            <div>
                                <span class="text-xs font-bold text-blue-300 uppercase tracking-widest block">{{ $selectedHotel->name ?? 'Hotel' }}</span>
                                <h2 class="text-2xl font-black text-white tracking-tight mt-0.5">
                                    {{ $selectedRoomType->name ?? 'Luxury Suite' }} 
                                    @if($selectedRoom)
                                        <span class="text-blue-400">(Room {{ $selectedRoom->room_number }})</span>
                                    @endif
                                </h2>
                            </div>
                            <div class="flex items-center gap-2" x-show="rmImages.length > 1">
                                <span class="font-bold text-[10px] bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full border border-white/20 uppercase tracking-wider" x-text="(activeRoomImgIdx + 1) + ' / ' + rmImages.length"></span>
                                <button @click="activeRoomImgIdx = (activeRoomImgIdx - 1 + rmImages.length) % rmImages.length" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/40 flex items-center justify-center cursor-pointer transition-all border border-white/20 backdrop-blur-md">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </button>
                                <button @click="activeRoomImgIdx = (activeRoomImgIdx + 1) % rmImages.length" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/40 flex items-center justify-center cursor-pointer transition-all border border-white/20 backdrop-blur-md">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(count($roomImages) > 1)
                    <div class="p-3 bg-slate-50 border-b border-slate-100 flex gap-2 overflow-x-auto scrollbar-thin">
                        <template x-for="(rImg, rIdx) in rmImages" :key="rIdx">
                            <button @click="activeRoomImgIdx = rIdx" class="w-20 h-14 rounded-xl overflow-hidden border-2 transition-all cursor-pointer flex-shrink-0" :class="activeRoomImgIdx === rIdx ? 'border-blue-600 scale-95 shadow-md ring-2 ring-blue-500/30' : 'border-slate-200 opacity-60 hover:opacity-100'">
                                <img :src="rImg" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                    @endif

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

                        <div>
                            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2">Room Overview & Details</h3>
                            <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 border border-slate-100 p-4 rounded-2xl">
                                Experience ultimate comfort in Room {{ $selectedRoom->room_number ?? '101' }}. Designed with modern luxury aesthetics, premium mattresses, soundproof acoustic windows, complimentary high-speed Wi-Fi, 24/7 room service, and private en-suite bathroom.
                            </p>
                        </div>

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
                        <i class="fas fa-calendar-check text-blue-600"></i> Customer Reservation Form
                    </h2>

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
                            <span>{{ $currencySymbol }}{{ number_format((float) ($selectedRoom?->price ?: ($selectedRoomType?->base_price ?: ($total_price / max(1, $total_days)))), 2) }}</span>
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

                    {{-- Guest Info Inputs --}}
                    <div class="space-y-3 pt-2">
                        <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider"><i class="fas fa-user-edit mr-1 text-blue-500"></i> Guest Contact Details</h3>
                        
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
                    </div>

                    {{-- Payment Mode Selection --}}
                    <div class="pt-4">
                        <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2.5"><i class="fas fa-wallet mr-1 text-blue-500"></i> Payment Mode</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="payment_method" value="Cash" class="peer sr-only">
                                <div class="rounded-xl border border-slate-200 bg-white p-2.5 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all text-xs font-bold shadow-sm">
                                    <i class="fas fa-money-bill-wave mr-1 text-emerald-500"></i> Pay at Hotel
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="payment_method" value="UPI" class="peer sr-only">
                                <div class="rounded-xl border border-slate-200 bg-white p-2.5 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all text-xs font-bold shadow-sm">
                                    <i class="fas fa-qrcode mr-1 text-blue-500"></i> UPI Direct
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="payment_method" value="Card" class="peer sr-only">
                                <div class="rounded-xl border border-slate-200 bg-white p-2.5 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all text-xs font-bold shadow-sm">
                                    <i class="fas fa-credit-card mr-1 text-blue-500"></i> Card
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="payment_method" value="Net Banking" class="peer sr-only">
                                <div class="rounded-xl border border-slate-200 bg-white p-2.5 text-center hover:bg-slate-50 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all text-xs font-bold shadow-sm">
                                    <i class="fas fa-university mr-1 text-blue-500"></i> Net Banking
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- CTA Submit Button --}}
                    <div class="pt-6">
                        <button type="button" wire:click="processBooking" wire:loading.attr="disabled" class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-75 text-white font-black text-sm py-4 rounded-2xl shadow-lg shadow-blue-600/30 transition-all cursor-pointer flex items-center justify-center gap-2 hover:shadow-xl hover:scale-[1.01]">
                            <span wire:loading.remove class="flex items-center gap-2">
                                <i class="fas fa-lock text-xs"></i> Confirm & Book Room {{ $selectedRoom->room_number ?? '' }} Now
                            </span>
                            <span wire:loading class="flex items-center gap-2">
                                <i class="fas fa-spinner fa-spin text-xs"></i> Processing Booking... Please wait
                            </span>
                        </button>
                    </div>

                </div>
            </div>

        </div>
        @endif

        {{-- Step 3: Success Screen (Complete Booking Receipt / Confirmation) --}}
        @if($step == 3)
        <div id="printable-voucher" class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden animate-fadeIn my-8">
            
            {{-- Success Header Banner --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-6 text-white text-center relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-3 border border-white/30 shadow-inner">
                    <i class="fas fa-check text-3xl text-white"></i>
                </div>
                <h2 class="text-2xl font-black tracking-tight">Booking Confirmed!</h2>
                <p class="text-xs text-emerald-100 mt-1">Thank you, your room reservation has been successfully completed.</p>
            </div>

            {{-- Voucher Body --}}
            <div class="p-6 sm:p-8 space-y-6">
                
                {{-- Top Summary Box --}}
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">Reference ID (PNR)</span>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-2xl font-black text-blue-700 tracking-wider font-mono">{{ $pnr }}</span>
                            <button onclick="navigator.clipboard.writeText('{{ $pnr }}'); alert('Reference ID {{ $pnr }} copied to clipboard!');" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition-all flex items-center gap-1 cursor-pointer no-print" title="Copy Reference ID">
                                <i class="fas fa-copy text-xs"></i> Copy Reference ID
                            </button>
                        </div>
                        <span class="text-[11px] text-slate-500 font-semibold block mt-0.5">Ref: {{ $booking_number }}</span>
                    </div>

                    <div class="text-center sm:text-right">
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block mb-1">Booking Status</span>
                        @if(strtolower($booking_status) === 'confirmed')
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm">
                                <i class="fas fa-check-circle text-emerald-600"></i> Confirmed
                            </span>
                        @elseif(strtolower($booking_status) === 'cancelled' || strtolower($booking_status) === 'rejected')
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-300 shadow-sm">
                                <i class="fas fa-times-circle text-rose-600"></i> Cancelled
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-300 shadow-sm">
                                <i class="fas fa-clock text-amber-600"></i> Pending
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Key Details Grid --}}
                <div class="border border-slate-200 rounded-2xl overflow-hidden divide-y divide-slate-100 text-xs">
                    
                    {{-- Row 1: Guest Name & Booking Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-white">
                        <div class="p-4">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5"><i class="fas fa-user text-blue-500 mr-1"></i> Guest Name</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $guest_name }}</span>
                        </div>
                        <div class="p-4">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5"><i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Booking Date</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $booking_date ?: date('d M Y, h:i A') }}</span>
                        </div>
                    </div>

                    {{-- Row 2: Hotel Name & Room Name --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-slate-50/50">
                        <div class="p-4">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5"><i class="fas fa-hotel text-blue-500 mr-1"></i> Hotel Name</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $selectedHotel->name ?? 'Hotel' }}</span>
                            <span class="text-[11px] text-slate-500 block mt-0.5">{{ $selectedHotel->city ?? '' }}, {{ $selectedHotel->country ?? '' }}</span>
                        </div>
                        <div class="p-4">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5"><i class="fas fa-door-open text-blue-500 mr-1"></i> Room Name & Number</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $selectedRoomType->name ?? 'Standard Suite' }}</span>
                            <span class="text-[11px] font-extrabold text-blue-600 block mt-0.5">Room #{{ $selectedRoom->room_number ?? '101' }}</span>
                        </div>
                    </div>

                    {{-- Row 3: Check-in Date & Check-out Date --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-white">
                        <div class="p-4">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5"><i class="fas fa-sign-in-alt text-emerald-500 mr-1"></i> Check-in Date</span>
                            <span class="font-bold text-slate-900 text-sm">{{ date('d M Y', strtotime($checkin_date)) }}</span>
                            <span class="text-[10px] text-slate-500 block">From 02:00 PM</span>
                        </div>
                        <div class="p-4">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5"><i class="fas fa-sign-out-alt text-rose-500 mr-1"></i> Check-out Date</span>
                            <span class="font-bold text-slate-900 text-sm">{{ date('d M Y', strtotime($checkout_date)) }}</span>
                            <span class="text-[10px] text-slate-500 block">Until 11:00 AM ({{ $total_days }} Night/s)</span>
                        </div>
                    </div>

                    {{-- Row 4: Total Amount & Payment Status --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-blue-50/40">
                        <div class="p-4">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5"><i class="fas fa-receipt text-blue-500 mr-1"></i> Total Amount</span>
                            <span class="font-black text-blue-700 text-xl">{{ $currencySymbol }}{{ number_format($total_price, 2) }}</span>
                        </div>
                        <div class="p-4">
                            <span class="text-[10px] font-extrabold uppercase text-slate-400 block mb-0.5"><i class="fas fa-wallet text-blue-500 mr-1"></i> Payment Status</span>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="font-bold text-slate-800">{{ $payment_method }}</span>
                                @if($payment_method === 'Cash')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Pay at Hotel
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Paid
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer Info Notice --}}
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-[11px] text-slate-500 flex items-start gap-2">
                    <i class="fas fa-info-circle text-blue-500 text-sm mt-0.5"></i>
                    <span>A confirmation email with your booking voucher has been dispatched to <strong>{{ $guest_email }}</strong>. Please present your PNR at check-in.</span>
                </div>

                {{-- Action Buttons --}}
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2 no-print">
                    <a href="{{ route('booking.slip.download', ['pnr' => $pnr]) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 px-3 rounded-xl shadow-md transition-all cursor-pointer">
                        <i class="fas fa-file-pdf text-xs"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="w-full inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-black text-white font-bold text-xs py-3 px-3 rounded-xl shadow-md transition-all cursor-pointer">
                        <i class="fas fa-print text-xs"></i> Print Ticket
                    </button>
                    <button onclick="navigator.clipboard.writeText('{{ $pnr }}'); alert('Reference ID {{ $pnr }} copied to clipboard!');" class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs py-3 px-3 rounded-xl transition-all cursor-pointer">
                        <i class="fas fa-copy text-xs text-blue-600"></i> Copy PNR
                    </button>
                    <a href="{{ url('/track') }}" class="w-full inline-flex items-center justify-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs py-3 px-3 rounded-xl shadow-sm transition-all cursor-pointer">
                        <i class="fas fa-search text-xs text-blue-500"></i> Track Booking
                    </a>
                </div>

            </div>
        </div>
        @endif
    </div>
</div>
