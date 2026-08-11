<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $hotel->name }} - Book Now on LODGIKO</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: '#3B82F6',
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased bg-slate-50 text-slate-800" x-data="{ 
    showModal: false, 
    selectedRoom: null,
    modalImgIdx: 0,
    showBookingModal: false,
    bookingSubmitted: false,
    selectedRoomForBooking: null,
    bookingData: { 
        guest_name: '', 
        guest_email: '', 
        guest_phone: '', 
        checkin_date: '{{ date('Y-m-d') }}', 
        checkout_date: '{{ date('Y-m-d', strtotime('+1 day')) }}', 
        special_requests: '', 
        payment_method: 'Cash' 
    },
    get nights() {
        if (!this.bookingData.checkin_date || !this.bookingData.checkout_date) return 1;
        let d1 = new Date(this.bookingData.checkin_date);
        let d2 = new Date(this.bookingData.checkout_date);
        let diffTime = d2.getTime() - d1.getTime();
        let diffDays = Math.ceil(diffTime / (1000 * 3600 * 24));
        return diffDays > 0 ? diffDays : 1;
    },
    get totalPayable() {
        if (!this.selectedRoomForBooking) return 0;
        let rawRate = Number(this.selectedRoomForBooking.rawPrice || 0);
        return rawRate * this.nights;
    },
    get totalPayableFormatted() {
        return '₹' + this.totalPayable.toLocaleString('en-IN');
    },
    isSubmitting: false,
    successResult: null,
    errorMessage: '',
    async submitBooking() {
        if (!this.selectedRoomForBooking) return;
        this.isSubmitting = true;
        this.errorMessage = '';
        try {
            let res = await fetch('{{ route('hotel.book-instant') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    hotel_id: {{ $hotel->id }},
                    room_id: this.selectedRoomForBooking.id,
                    guest_name: this.bookingData.guest_name,
                    guest_email: this.bookingData.guest_email,
                    guest_phone: this.bookingData.guest_phone,
                    checkin_date: this.bookingData.checkin_date,
                    checkout_date: this.bookingData.checkout_date,
                    special_requests: this.bookingData.special_requests,
                    payment_method: this.bookingData.payment_method
                })
            });
            let data = await res.json();
            if (data.success) {
                this.successResult = data;
                this.bookingSubmitted = true;
            } else {
                this.errorMessage = data.message || 'Error completing booking.';
            }
        } catch (e) {
            this.errorMessage = 'Network error occurred. Please try again.';
        } finally {
            this.isSubmitting = false;
        }
    }
}">

    <!-- Navbar Header with Navigation Menu -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
              <a href="/" class="flex items-center gap-2">
                 <img src="{{ asset('images/lodgiko.png') }}"
                    alt="Merahkie Logo"
                    class="h-12 w-auto">
                <!-- <div class="flex flex-col">
                    <span class="text-xl font-black tracking-tight text-slate-900 leading-none">MERAHKIE</span>
                    <span class="text-[10px] font-bold tracking-widest text-blue-600 uppercase mt-1">Bookings</span>
                </div> -->
            </a>

            <!-- Navigation Links (Home, Hotels, About, FAQ) -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="/" class="hover:text-blue-600 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-home text-xs text-blue-500"></i> Home
                </a>
                <a href="#available-rooms" class="hover:text-blue-600 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-hotel text-xs text-blue-500"></i> Hotels
                </a>
                <a href="#about-property" class="hover:text-blue-600 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-info-circle text-xs text-blue-500"></i> About
                </a>
                <a href="#faq-section" class="hover:text-blue-600 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-question-circle text-xs text-blue-500"></i> FAQ
                </a>
            </nav>

            <!-- Right Actions -->
            <div class="flex items-center gap-3">
                <a href="#available-rooms" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-calendar-check text-xs"></i> Book Room
                </a>

                <!-- Mobile Hamburger Button -->
                <button @click="mobileMenu = !mobileMenu" class="md:hidden w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700 hover:bg-slate-200 transition-all">
                    <i class="fas" :class="mobileMenu ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenu" x-cloak class="md:hidden border-t border-slate-100 bg-white px-4 pt-3 pb-5 space-y-3 shadow-lg">
            <a href="/" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                <i class="fas fa-home w-5 text-blue-500"></i> Home
            </a>
            <a href="#available-rooms" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                <i class="fas fa-hotel w-5 text-blue-500"></i> Hotels
            </a>
            <a href="#about-property" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                <i class="fas fa-info-circle w-5 text-blue-500"></i> About
            </a>
            <a href="#faq-section" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                <i class="fas fa-question-circle w-5 text-blue-500"></i> FAQ
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb & Title -->
        <div class="mb-6">
            <nav class="flex text-xs font-medium text-slate-400 gap-2 mb-2">
                <a href="/" class="hover:text-blue-600">Home</a>
                <span>/</span>
                <span class="text-slate-600">{{ $hotel->city }}</span>
                <span>/</span>
                <span class="text-slate-900 font-bold">{{ $hotel->name }}</span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $hotel->name }}</h1>
                    <p class="text-sm text-slate-500 mt-1 flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-blue-500"></i>
                        {{ $hotel->address }}, {{ $hotel->city }}, {{ $hotel->state }}, {{ $hotel->country }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Rating</span>
                        <span class="text-lg font-bold text-amber-500"><i class="fas fa-star mr-1"></i>4.8 / 5</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Gallery Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10 rounded-3xl overflow-hidden shadow-lg border border-slate-200">
            @php
                $images = $hotel->images;
                $defaultImages = [
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=600&q=80'
                ];
            @endphp

            @if($images && $images->count() > 0)
                <div class="md:col-span-2 aspect-[4/3] md:aspect-auto bg-slate-200">
                    <img src="{{ $images[0]->url }}" onerror="this.onerror=null; this.src='{{ $defaultImages[0] }}';" class="w-full h-full object-cover">
                </div>
                <div class="md:col-span-2 grid grid-cols-2 gap-4">
                    @foreach($images->slice(1, 4) as $idx => $img)
                        <div class="aspect-video bg-slate-100 overflow-hidden">
                            <img src="{{ $img->url }}" onerror="this.onerror=null; this.src='{{ $defaultImages[($idx + 1) % count($defaultImages)] }}';" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="md:col-span-2 aspect-[4/3] md:aspect-auto">
                    <img src="{{ $defaultImages[0] }}" class="w-full h-full object-cover">
                </div>
                <div class="md:col-span-2 grid grid-cols-2 gap-4">
                    <div class="aspect-video bg-slate-100 overflow-hidden">
                        <img src="{{ $defaultImages[1] }}" class="w-full h-full object-cover">
                    </div>
                    <div class="aspect-video bg-slate-100 overflow-hidden">
                        <img src="{{ $defaultImages[2] }}" class="w-full h-full object-cover">
                    </div>
                    <div class="aspect-video bg-slate-100 overflow-hidden">
                        <img src="{{ $defaultImages[3] }}" class="w-full h-full object-cover">
                    </div>
                    <div class="aspect-video bg-slate-100 overflow-hidden relative group cursor-pointer">
                        <img src="{{ $defaultImages[0] }}" class="w-full h-full object-cover blur-sm">
                        <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center text-white font-bold text-sm">
                            + View All Photos
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Content Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Left Column: Hotel Info & Available Rooms -->
            <div class="lg:col-span-2 space-y-10">
                <!-- Overview -->
                <section id="about-property" class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm scroll-mt-24">
                    <h2 class="text-xl font-bold text-slate-900 mb-3">About the Property</h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Welcome to {{ $hotel->name }}. Located in {{ $hotel->city }}, this property offers modern accommodations with luxury amenities, 24/7 room service, and top-rated hospitality. Perfect for both business travelers and vacationing families.
                    </p>

                    <!-- Amenities Icons -->
                    <div class="mt-6 border-t border-slate-100 pt-6">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Popular Amenities</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="flex items-center gap-3 text-xs font-semibold text-slate-700">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-wifi"></i></div>
                                High-Speed Wi-Fi
                            </div>
                            <div class="flex items-center gap-3 text-xs font-semibold text-slate-700">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-swimming-pool"></i></div>
                                Swimming Pool
                            </div>
                            <div class="flex items-center gap-3 text-xs font-semibold text-slate-700">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-parking"></i></div>
                                Free Parking
                            </div>
                            <div class="flex items-center gap-3 text-xs font-semibold text-slate-700">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-utensils"></i></div>
                                Restaurant
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Available Rooms -->
                <section id="available-rooms" class="scroll-mt-24">
                    <h2 class="text-2xl font-bold text-slate-900 mb-6">Available Rooms & Suites</h2>
                    
                    <div class="space-y-4">
                        @if($hotel->rooms->isEmpty())
                            <div class="bg-blue-50 border border-blue-100 text-blue-800 p-6 rounded-2xl text-center">
                                No rooms currently listed for this property.
                            </div>
                        @else
                            @foreach($hotel->rooms as $room)
                                @php
                                    $roomTypeName = $room->roomType->name ?? 'Standard Room';
                                    $roomPrice = $room->price ?: ($room->roomType->base_price ?? 2500);
                                    $bookUrl = route('booking-engine.hotel', ['slug' => $hotel->slug ? $hotel->slug . '-' . $hotel->id : $hotel->id]) . '?room_type_id=' . $room->room_type_id . '&room_id=' . $room->id;
                                    $isAvail = $room->status === 'Available';
                                @endphp
                                <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm flex flex-col sm:flex-row gap-6 hover:shadow-md transition-all">
                                    <div class="w-full sm:w-1/3 aspect-video sm:aspect-auto rounded-xl bg-slate-100 overflow-hidden shrink-0 relative">
                                        <img src="{{ $room->image_url }}" class="w-full h-full object-cover">
                                        @if(count($room->images) > 1)
                                            <span class="absolute top-3 right-3 bg-slate-900/70 backdrop-blur-sm border border-white/20 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-sm">
                                                <i class="fas fa-images text-blue-400"></i> {{ count($room->images) }} Photos
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <div class="flex justify-between items-start">
                                                <h3 class="text-lg font-bold text-slate-900">{{ $roomTypeName }} (Room {{ $room->room_number }})</h3>
                                                @if($isAvail)
                                                    <span class="text-xs font-bold px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg flex items-center gap-1">
                                                        <i class="fas fa-check-circle"></i> Available
                                                    </span>
                                                @else
                                                    <span class="text-xs font-bold px-2.5 py-1 bg-rose-100 text-rose-700 rounded-lg flex items-center gap-1">
                                                        <i class="fas fa-times-circle"></i> Not Available / Occupied
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1">Bed Type: {{ ucfirst($room->bed_type ?? 'King / Queen Bed') }} | Max Capacity: {{ $room->capacity ?? 2 }} Guests</p>
                                            
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg"><i class="fas fa-wifi mr-1 text-blue-500"></i> Free Wi-Fi</span>
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg"><i class="fas fa-snowflake mr-1 text-blue-500"></i> AC</span>
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg"><i class="fas fa-tv mr-1 text-blue-500"></i> Flat TV</span>
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg"><i class="fas fa-coffee mr-1 text-blue-500"></i> Breakfast Included</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-6 flex items-end justify-between border-t border-slate-100 pt-4 gap-3">
                                            <div>
                                                <span class="block text-2xl font-black text-slate-900">₹{{ number_format($roomPrice) }}</span>
                                                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">per night + taxes</span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <button @click="selectedRoom = {
                                                    id: '{{ $room->id }}',
                                                    name: {!! json_encode($roomTypeName) !!},
                                                    number: {!! json_encode($room->room_number) !!},
                                                    price: '₹{{ number_format($roomPrice) }}',
                                                    rawPrice: {{ $roomPrice }},
                                                    image: {!! json_encode($room->image_url) !!},
                                                    images: {!! json_encode($room->images) !!},
                                                    description: {!! json_encode($room->description ?: "Experience ultimate comfort in Room " . $room->room_number . ". Designed with modern luxury aesthetics, premium mattresses, soundproof acoustic windows, complimentary high-speed Wi-Fi, 24/7 room service, and private en-suite bathroom.") !!},
                                                    bed_type: {!! json_encode(ucfirst($room->bed_type ?? "King / Queen Bed")) !!},
                                                    capacity: {!! json_encode(($room->capacity ?? 2) . ' Guests') !!}
                                                }; modalImgIdx = 0; showModal = true" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 cursor-pointer">
                                                    <i class="fas fa-eye text-slate-500"></i> View Details
                                                </button>

                                                <a href="{{ route('hotel.reserve', ['slug' => $hotel->slug ?: $hotel->id, 'room' => $room->id]) }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-1.5 cursor-pointer">
                                                    <i class="fas fa-calendar-check text-xs"></i> Book Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>


                <!-- FAQ Section -->
                <section id="faq-section" class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm scroll-mt-24" x-data="{ openFaq: null }">
                    <h2 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-question-circle text-blue-600 text-lg"></i> Frequently Asked Questions
                    </h2>
                    <div class="space-y-3 text-xs">
                        <div class="border border-slate-100 rounded-2xl overflow-hidden">
                            <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full bg-slate-50 px-4 py-3 text-left font-bold text-slate-800 flex justify-between items-center cursor-pointer">
                                <span>What are the standard Check-in and Check-out times?</span>
                                <i class="fas text-slate-400" :class="openFaq === 1 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                            <div x-show="openFaq === 1" x-cloak class="p-4 bg-white text-slate-600 border-t border-slate-100 leading-relaxed">
                                Standard Check-in is from 02:00 PM and Check-out is until 11:00 AM. Early check-in or late check-out is subject to availability.
                            </div>
                        </div>

                        <div class="border border-slate-100 rounded-2xl overflow-hidden">
                            <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full bg-slate-50 px-4 py-3 text-left font-bold text-slate-800 flex justify-between items-center cursor-pointer">
                                <span>Is breakfast included with room booking?</span>
                                <i class="fas text-slate-400" :class="openFaq === 2 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                            <div x-show="openFaq === 2" x-cloak class="p-4 bg-white text-slate-600 border-t border-slate-100 leading-relaxed">
                                Yes, complimentary high-speed Wi-Fi and daily buffet breakfast are included with most room packages.
                            </div>
                        </div>

                        <div class="border border-slate-100 rounded-2xl overflow-hidden">
                            <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full bg-slate-50 px-4 py-3 text-left font-bold text-slate-800 flex justify-between items-center cursor-pointer">
                                <span>What is the cancellation policy?</span>
                                <i class="fas text-slate-400" :class="openFaq === 3 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                            <div x-show="openFaq === 3" x-cloak class="p-4 bg-white text-slate-600 border-t border-slate-100 leading-relaxed">
                                Free cancellation is available up to 24 hours prior to check-in. For late cancellations, standard 1-night room charges may apply.
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right Column: Booking Widget / Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xl sticky top-28">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Book Your Stay</h3>
                    <p class="text-xs text-slate-500 mb-6">Select dates and number of guests to reserve instantly.</p>
                    
                    <form action="{{ route('booking-engine', ['hotel_id' => $hotel->id]) }}" method="GET" class="space-y-4">
                        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Check-in Date</label>
                            <input type="date" name="checkin" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Check-out Date</label>
                            <input type="date" name="checkout" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Guests</label>
                            <select name="guests" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="1">1 Guest</option>
                                <option value="2" selected>2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                            </select>
                        </div>
                        
                        <div class="pt-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-search text-xs"></i> Check Availability
                            </button>
                        </div>
                        <p class="text-[10px] text-center text-slate-500 mt-3"><i class="fas fa-shield-alt text-emerald-500 mr-1"></i> Best Rate Guaranteed</p>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Room Details Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full overflow-hidden relative transform transition-all" @click.away="showModal = false">
            <!-- Modal Header / Image Carousel -->
            <div class="relative aspect-video bg-slate-100 group">
                <img :src="selectedRoom?.images && selectedRoom?.images.length > 0 ? selectedRoom?.images[modalImgIdx] : selectedRoom?.image" class="w-full h-full object-cover transition-all duration-300">
                
                <!-- Next / Previous Controls -->
                <template x-if="selectedRoom?.images && selectedRoom?.images.length > 1">
                    <div class="absolute inset-0 flex items-center justify-between p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button type="button" @click="modalImgIdx = (modalImgIdx - 1 + selectedRoom.images.length) % selectedRoom.images.length" class="w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white flex items-center justify-center backdrop-blur-md border border-white/20 shadow-md cursor-pointer transition-all">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </button>
                        <button type="button" @click="modalImgIdx = (modalImgIdx + 1) % selectedRoom.images.length" class="w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white flex items-center justify-center backdrop-blur-md border border-white/20 shadow-md cursor-pointer transition-all">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </button>
                    </div>
                </template>

                <!-- Close Button -->
                <button @click="showModal = false" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-slate-900/60 text-white hover:bg-slate-900 flex items-center justify-center cursor-pointer transition-all border border-white/20 backdrop-blur-md z-10 shadow-md">
                    <i class="fas fa-times"></i>
                </button>

                <!-- Room Title Badge -->
                <div class="absolute bottom-4 left-4 bg-slate-900/80 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-lg border border-white/10 shadow-md">
                    <span x-text="selectedRoom?.name + ' (Room ' + selectedRoom?.number + ')'"></span>
                </div>

                <!-- Image Counter Badge -->
                <template x-if="selectedRoom?.images && selectedRoom?.images.length > 1">
                    <div class="absolute top-4 left-4 bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-bold px-2.5 py-1 rounded-lg border border-white/10 shadow-md">
                        <i class="fas fa-images text-blue-400 mr-1"></i> <span x-text="(modalImgIdx + 1) + '/' + selectedRoom?.images.length"></span>
                    </div>
                </template>
            </div>

            <!-- Thumbnail Gallery Strip -->
            <template x-if="selectedRoom?.images && selectedRoom?.images.length > 1">
                <div class="flex gap-2 p-2.5 bg-slate-50 border-b border-slate-200 overflow-x-auto">
                    <template x-for="(img, idx) in selectedRoom.images" :key="idx">
                        <button type="button" @click="modalImgIdx = idx" class="w-14 h-10 rounded-lg overflow-hidden border-2 transition-all shrink-0 cursor-pointer" :class="modalImgIdx === idx ? 'border-blue-500 scale-105 shadow-md' : 'border-slate-200 opacity-60 hover:opacity-100'">
                            <img :src="img" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </template>

            <!-- Modal Body -->
            <div class="p-6 space-y-5">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900" x-text="selectedRoom?.name + ' - Room ' + selectedRoom?.number"></h3>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-3">
                            <span><i class="fas fa-bed text-blue-500 mr-1"></i> <span x-text="selectedRoom?.bed_type"></span></span>
                            <span><i class="fas fa-users text-blue-500 mr-1"></i> Max: <span x-text="selectedRoom?.capacity"></span></span>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-blue-600" x-text="selectedRoom?.price"></span>
                        <span class="text-xs text-slate-500 block">/ night</span>
                    </div>
                </div>

                <div class="border-t border-b border-slate-100 py-4">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Description</h4>
                    <p class="text-xs text-slate-600 leading-relaxed" x-text="selectedRoom?.description"></p>
                </div>

                <div>
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Room Amenities & Services</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs font-semibold text-slate-700">
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl"><i class="fas fa-wifi text-blue-500"></i> Free High-Speed Wi-Fi</div>
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl"><i class="fas fa-snowflake text-blue-500"></i> Air Conditioning</div>
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl"><i class="fas fa-tv text-blue-500"></i> HD Flat Screen TV</div>
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl"><i class="fas fa-coffee text-blue-500"></i> Coffee / Tea Maker</div>
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl"><i class="fas fa-shower text-blue-500"></i> Private Bathroom</div>
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl"><i class="fas fa-concierge-bell text-blue-500"></i> 24/7 Room Service</div>
                    </div>
                </div>

                <!-- Action CTA -->
                <div class="pt-3 flex justify-end gap-3 border-t border-slate-100">
                    <button @click="showModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-all cursor-pointer">
                        Close
                    </button>
                    <a :href="'/hotel/{{ $hotel->slug ?: $hotel->id }}/reserve/' + selectedRoom?.id" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-lg hover:shadow-xl transition-all cursor-pointer flex items-center gap-2">
                        <i class="fas fa-calendar-check text-xs"></i> Book This Room Now
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-16 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 border-b border-slate-800 pb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-paper-plane text-white text-sm"></i>
                        </div>
                        <span class="text-xl font-black text-white">MERAHKIE</span>
                    </div>
                    <p class="text-sm text-slate-400 mb-6">Your premium hotel booking partner for luxury stays, resorts, and vacation rentals worldwide.</p>
                    <div class="flex items-center gap-4">
                        <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-slate-400 hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">Quick Links</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="/" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-home text-blue-500 text-xs"></i> Home</a></li>
                        <li><a href="#available-rooms" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-hotel text-blue-500 text-xs"></i> Available Rooms</a></li>
                        <li><a href="#about-property" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-info-circle text-blue-500 text-xs"></i> About Property</a></li>
                        <li><a href="#contact-property" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-phone-alt text-blue-500 text-xs"></i> Contact & Map</a></li>
                        <li><a href="#faq-section" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-question-circle text-blue-500 text-xs"></i> FAQ</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">Support & Services</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('track-booking') }}" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-search text-blue-500 text-xs"></i> Track Booking</a></li>
                        <li><a href="{{ route('register-hotel') }}" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-building text-blue-500 text-xs"></i> List Property</a></li>
                        <li><a href="#contact-property" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-headset text-blue-500 text-xs"></i> Hotel Front Desk</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-user-lock text-blue-500 text-xs"></i> Partner Login</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">For Hotel Owners</h4>
                    <p class="text-sm text-slate-400 mb-4">Grow your bookings by listing your property on our platform.</p>
                    <a href="{{ route('register-hotel') }}" class="inline-block px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-md">
                        Partner With Us <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} Merahkie Bookings. All rights reserved.</p>
                <div class="flex items-center gap-4 mt-4 md:mt-0">
                    <i class="fab fa-cc-visa text-2xl"></i>
                    <i class="fab fa-cc-mastercard text-2xl"></i>
                    <i class="fab fa-cc-amex text-2xl"></i>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
