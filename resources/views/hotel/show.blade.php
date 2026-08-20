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
@php
    $roomsData = $hotel->rooms->map(function($room) {
        $roomTypeName = $room->roomType->name ?? 'Standard Room';
        $primaryType = $room->bed_type ?: $roomTypeName;
        $roomPrice = (float)($room->price ?: ($room->roomType->base_price ?? 2500));
        $isMaintenance = ($room->status === 'Maintenance' || ($room->activeMaintenanceTickets && $room->activeMaintenanceTickets->count() > 0));
        $hkStatus = $room->latestHousekeeping?->status ?? 'Clean';
        $isDirty = in_array($hkStatus, ['Dirty', 'Maintenance']);
        $isOccupied = ($room->status === 'Occupied');
        
        $activeReservations = $room->reservations 
            ? $room->reservations->whereIn('status', ['Confirmed', 'Checked-In', 'Pending'])
                                 ->map(fn($r) => [
                                     'check_in' => $r->check_in_date,
                                     'check_out' => $r->check_out_date,
                                 ])->values()->toArray()
            : [];

        return [
            'id' => $room->id,
            'room_number' => (string)$room->room_number,
            'name' => $primaryType,
            'type' => $roomTypeName,
            'price' => $roomPrice,
            'price_formatted' => '₹' . number_format($roomPrice),
            'bed_type' => $room->bed_type ?: 'King Bed',
            'capacity' => (int)($room->capacity ?? 2),
            'room_option' => $room->room_option ?? '',
            'description' => $room->description ?: ("Experience ultimate comfort in Room " . $room->room_number . ". Designed with modern luxury aesthetics, premium mattresses, soundproof acoustic windows, complimentary high-speed Wi-Fi, 24/7 room service, and private en-suite bathroom."),
            'image_url' => $room->image_url,
            'images' => $room->images,
            'is_maintenance' => $isMaintenance,
            'is_dirty' => $isDirty,
            'is_occupied' => $isOccupied,
            'reservations' => $activeReservations,
        ];
    })->values();

    $hasInitialSearch = request()->filled('checkin') && request()->filled('checkout');
    $initialCheckIn = request('checkin', date('Y-m-d'));
    $initialCheckOut = request('checkout', date('Y-m-d', strtotime('+1 day')));
    $initialGuests = (int)request('guests', 1);
@endphp
<body class="antialiased bg-slate-50 text-slate-800" x-data="{ 
    showModal: false, 
    selectedRoom: null,
    modalImgIdx: 0,
    showBookingModal: false,
    bookingSubmitted: false,
    selectedRoomForBooking: null,
    allRooms: @js($roomsData),
    searchCheckIn: '{{ $initialCheckIn }}',
    searchCheckOut: '{{ $initialCheckOut }}',
    searchGuests: {{ $initialGuests }},
    promoCode: '{{ request('code', '') }}',
    hasSearched: true,
    roomTypeFilter: 'all',
    showFilterDropdown: false,
    bookingData: { 
        guest_name: '', 
        guest_email: '', 
        guest_phone: '', 
        checkin_date: '{{ $initialCheckIn }}', 
        checkout_date: '{{ $initialCheckOut }}', 
        guests: {{ $initialGuests }},
        special_requests: '', 
        payment_method: 'Cash' 
    },
    get searchNights() {
        if (!this.searchCheckIn || !this.searchCheckOut) return 1;
        let d1 = new Date(this.searchCheckIn);
        let d2 = new Date(this.searchCheckOut);
        let diffTime = d2.getTime() - d1.getTime();
        let diffDays = Math.ceil(diffTime / (1000 * 3600 * 24));
        return diffDays > 0 ? diffDays : 1;
    },
    get matchingRooms() {
        if (!this.hasSearched) return [];
        let cin = this.searchCheckIn;
        let cout = this.searchCheckOut;
        let guests = parseInt(this.searchGuests || 1);

        return this.allRooms.filter(room => {
            if (room.is_maintenance || room.is_dirty || room.is_occupied) return false;
            if (room.capacity < guests) return false;
            if (this.roomTypeFilter !== 'all' && room.type !== this.roomTypeFilter) return false;

            if (cin && cout) {
                let hasCollision = room.reservations.some(res => {
                    return (res.check_in < cout && res.check_out > cin);
                });
                if (hasCollision) return false;
            }

            return true;
        });
    },
    get uniqueRoomTypes() {
        return [...new Set(this.allRooms.map(r => r.type).filter(Boolean))];
    },
    formatDisplayDate(dStr) {
        if (!dStr) return '';
        const d = new Date(dStr + 'T00:00:00');
        if (isNaN(d.getTime())) return dStr;
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
    },
    performSearch() {
        if (!this.searchCheckIn || !this.searchCheckOut) {
            alert('Please select check-in and check-out dates.');
            return;
        }
        if (this.searchCheckIn >= this.searchCheckOut) {
            alert('Check-out date must be after check-in date.');
            return;
        }
        this.hasSearched = true;
        this.bookingData.checkin_date = this.searchCheckIn;
        this.bookingData.checkout_date = this.searchCheckOut;
        this.bookingData.guests = this.searchGuests;

        setTimeout(() => {
            document.getElementById('available-rooms')?.scrollIntoView({ behavior: 'smooth' });
        }, 100);
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
        let rawRate = Number(this.selectedRoomForBooking.price || this.selectedRoomForBooking.rawPrice || 0);
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
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                matchingRoomsCount() {
                    return this.visibleRoomCount;
                },
                bookingData: {
                    guest_name: '',
                    guest_email: '',
                    guest_phone: '',
                    checkin_date: @json($checkInDate ?? date('Y-m-d')),
                    checkout_date: @json($checkOutDate ?? date('Y-m-d', strtotime('+1 day'))),
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
                        let res = await fetch(@json(route('hotel.book-instant')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token())
                            },
                            body: JSON.stringify({
                                hotel_id: {{ (int) $hotel->id }},
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
                        let data = await res.json().catch(() => ({ success: false, message: 'Server error or invalid response. Please try again.' }));
                        if (res.ok && data.success) {
                            this.successResult = data;
                            this.bookingSubmitted = true;
                        } else {
                            this.errorMessage = data.message || 'Error completing booking.';
                        }
                    } catch (e) {
                        this.errorMessage = 'Network error occurred: ' + (e.message || 'Please try again.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>

    <style>
        body {
            background: #f3f4f6;
        }

        .lodgiko-shell {
            max-width: 1200px;
        }

        .lodgiko-topbar {
            background: rgba(255,255,255,0.96);
            border-bottom: 1px solid rgba(148,163,184,0.18);
        }

        .lodgiko-brand {
            letter-spacing: -0.08em;
        }

        .lodgiko-nav a {
            color: #334155;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.01em;
        }

        .lodgiko-nav a:hover {
            color: #0f172a;
        }

        .lodgiko-cta {
            border-radius: 14px;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18);
        }

        .hero-media {
            border-radius: 28px;
        }

        .hero-tile {
            border-radius: 22px;
        }

        .booking-panel {
            border-radius: 26px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
        }

        .room-card-item.is-filtered-out {
            display: none !important;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800" x-data="hotelShowPage()">

    <!-- Navbar Header with Navigation Menu -->
    <header class="lodgiko-topbar sticky top-0 z-50 backdrop-blur-md" x-data="{ mobileMenu: false }">
        <div class="lodgiko-shell mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('images/lodgiko.png') }}" alt="Lodgiko Logo" class="h-11 w-auto">
            </a>

            <nav class="lodgiko-nav hidden md:flex items-center justify-center gap-8 flex-1">
                <a href="/" class="inline-flex items-center gap-2">
                    <i class="fas fa-home text-[10px]"></i> Home
                </a>
                <a href="/#hotels" class="inline-flex items-center gap-2">
                    <i class="fas fa-hotel text-[10px]"></i> Hotels
                </a>
                <a href="#about-property" class="inline-flex items-center gap-2">
                    <i class="fas fa-info-circle text-[10px]"></i> About
                </a>
                <a href="#faq-section" class="inline-flex items-center gap-2">
                    <i class="fas fa-question-circle text-[10px]"></i> FAQ
                </a>
            </nav>

            <div class="flex items-center gap-3">
                <button type="button" onclick="openBookingModal()" class="lodgiko-cta px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-calendar-check text-xs"></i> Book Room
                </button>

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

    <main class="lodgiko-shell mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-7">
            <nav class="flex items-center gap-2 text-[11px] font-medium text-slate-400 mb-4">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <span>/</span>
                <span class="text-slate-600">{{ $hotel->city }}</span>
                <span>/</span>
                <span class="text-slate-900 font-bold">{{ $hotel->name }}</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl lg:text-[3rem] font-black text-slate-900 tracking-[-0.05em] leading-[0.95]">{{ $hotel->name }}</h1>
                    <p class="mt-3 text-sm sm:text-base text-slate-500 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-blue-500"></i>
                        <span>{{ $hotel->city }}, {{ $hotel->country }}</span>
                    </p>
                </div>

                <div class="inline-flex items-center gap-2 bg-white border border-amber-200 rounded-full px-4 py-2 shadow-sm">
                    <span class="text-amber-500 text-base"><i class="fas fa-star"></i></span>
                    <span class="text-sm font-bold text-slate-800">4.8 / 5</span>
                    <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">Rating</span>
                </div>
            </div>
        </div>

        @php
            $images = $hotel->images;
            $mainImageUrl = ($images && $images->count() > 0) ? $images[0]->url : ($hotel->rooms->first()?->image_url ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80');
        @endphp

        <!-- Main Section: Main Image on Left & About Property on Right -->
        <div class="mb-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
            {{-- Left: Main Hotel Image --}}
            <div class="relative overflow-hidden rounded-[32px] border border-slate-200 bg-slate-900 shadow-[0_16px_36px_rgba(15,23,42,0.08)] group min-h-[380px] lg:min-h-[440px]">
                <img src="{{ $mainImageUrl }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                
                {{-- Badges on Image --}}
                <div class="absolute top-5 left-5 flex items-center gap-2">
                    <span class="px-3.5 py-1.5 bg-slate-950/70 backdrop-blur-md border border-white/20 text-white text-xs font-bold rounded-full shadow-md flex items-center gap-1.5">
                        <i class="fas fa-hotel text-blue-400"></i> {{ $hotel->name }}
                    </span>
                    <span class="px-3 py-1.5 bg-emerald-600/90 backdrop-blur-md text-white text-xs font-bold rounded-full shadow-md flex items-center gap-1">
                        <i class="fas fa-check-circle"></i> Verified Property
                    </span>
                </div>

                <div class="absolute bottom-0 inset-x-0 p-6 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent text-white">
                    <p class="text-xs font-semibold text-slate-300 flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-blue-400"></i> {{ $hotel->city }}, {{ $hotel->country }}
                    </p>
                </div>
            </div>

            {{-- Right: About the Property Section --}}
            <section id="about-property" class="bg-white border border-slate-200 rounded-[32px] p-6 sm:p-8 shadow-[0_16px_36px_rgba(15,23,42,0.06)] flex flex-col justify-between scroll-mt-24">
                <div>
                    <div class="flex items-center gap-2 text-blue-600 font-extrabold text-xs uppercase tracking-wider mb-2">
                        <i class="fas fa-building"></i> Property Overview
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mb-4 tracking-tight">About the Property</h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Welcome to <strong>{{ $hotel->name }}</strong>. Located in the heart of {{ $hotel->city }}, this premier property offers modern accommodations with upscale amenities, dedicated 24/7 room service, and authentic top-rated hospitality. Specially crafted for both corporate travelers and vacationing families seeking unforgettable experiences.
                    </p>
                </div>

                <div class="mt-8 border-t border-slate-100 pt-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Popular Amenities & Inclusions</h3>
                    <div class="grid grid-cols-2 gap-3.5">
                        <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs font-bold text-slate-700 hover:bg-blue-50/50 hover:border-blue-200 transition-all">
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0"><i class="fas fa-wifi"></i></div>
                            <span>High-Speed Wi-Fi</span>
                        </div>
                        <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs font-bold text-slate-700 hover:bg-blue-50/50 hover:border-blue-200 transition-all">
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0"><i class="fas fa-swimming-pool"></i></div>
                            <span>Swimming Pool</span>
                        </div>
                        <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs font-bold text-slate-700 hover:bg-blue-50/50 hover:border-blue-200 transition-all">
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0"><i class="fas fa-parking"></i></div>
                            <span>Free Parking</span>
                        </div>
                        <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-50 border border-slate-100 text-xs font-bold text-slate-700 hover:bg-blue-50/50 hover:border-blue-200 transition-all">
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0"><i class="fas fa-utensils"></i></div>
                            <span>Restaurant & Dining</span>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Content Layout (Full Width) -->
        <div class="w-full space-y-10">

            @php
                $sliderImages = [];
                if ($hotel->images && $hotel->images->count() > 0) {
                    foreach ($hotel->images as $img) {
                        $sliderImages[] = [
                            'url' => $img->url,
                            'title' => $img->title ?: $hotel->name,
                            'caption' => $img->description ?: 'Explore the luxury spaces and comfort at ' . $hotel->name
                        ];
                    }
                }

                if ($hotel->rooms && $hotel->rooms->count() > 0) {
                    foreach ($hotel->rooms as $r) {
                        if (!empty($r->images)) {
                            foreach ($r->images as $rImg) {
                                $sliderImages[] = [
                                    'url' => $rImg,
                                    'title' => ($r->roomType->name ?? 'Room') . ' (Room ' . $r->room_number . ')',
                                    'caption' => $r->bed_type ? ($r->bed_type . ' • ' . $r->status) : 'Deluxe Hospitality & Comfort'
                                ];
                            }
                        }
                    }
                }

                // Curated high-res fallback images if fewer images are present
                $fallbacks = [
                    ['url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1400&q=80', 'title' => 'Resort Overview & Scenic Pool', 'caption' => 'Experience serene relaxation and world-class luxury amenities'],
                    ['url' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1400&q=80', 'title' => 'Luxury Guest Suites & Lounge', 'caption' => 'Spacious interiors designed with modern acoustic comfort and aesthetics'],
                    ['url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1400&q=80', 'title' => 'Fine Dining Restaurant & Bar', 'caption' => 'Savor multi-cuisine culinary masterpieces crafted by top chefs'],
                    ['url' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1400&q=80', 'title' => 'Executive Suite Living Space', 'caption' => 'Elegantly curated architectural spaces with premium amenities'],
                    ['url' => 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?auto=format&fit=crop&w=1400&q=80', 'title' => 'Wellness & Spa Oasis', 'caption' => 'Rejuvenate your body and mind in our serene relaxation lounges'],
                ];

                if (count($sliderImages) < 3) {
                    foreach ($fallbacks as $fb) {
                        $sliderImages[] = $fb;
                    }
                }
            @endphp

            <!-- Property Image Slider / Photo Tour Showcase -->
            <section id="property-slider-gallery" class="bg-white border border-slate-200 rounded-[28px] p-6 sm:p-7 shadow-[0_12px_30px_rgba(15,23,42,0.05)] scroll-mt-24"
                x-data="{
                    activeSlide: 0,
                    totalSlides: {{ count($sliderImages) }},
                    autoPlayTimer: null,
                    isPaused: false,
                    slides: {{ Js::from($sliderImages) }},
                    next() {
                        this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                    },
                    prev() {
                        this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
                    },
                    goTo(index) {
                        this.activeSlide = index;
                    },
                    startAutoPlay() {
                        this.autoPlayTimer = setInterval(() => {
                            if (!this.isPaused) {
                                this.next();
                            }
                        }, 5000);
                    },
                    stopAutoPlay() {
                        if (this.autoPlayTimer) clearInterval(this.autoPlayTimer);
                    }
                }"
                x-init="startAutoPlay()"
                @mouseenter="isPaused = true"
                @mouseleave="isPaused = false"
                @keydown.right.window="next()"
                @keydown.left.window="prev()"
            >
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                            <i class="fas fa-images text-blue-600"></i> Property Photo Tour & Highlights
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Explore the ambience, architecture, and luxury spaces of {{ $hotel->name }}</p>
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-full border border-slate-200 shadow-2xs" x-text="`${activeSlide + 1} / ${totalSlides}`"></span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="prev()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 flex items-center justify-center transition-all cursor-pointer shadow-2xs" title="Previous Slide">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </button>
                            <button type="button" @click="next()" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 flex items-center justify-center transition-all cursor-pointer shadow-2xs" title="Next Slide">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Carousel Viewport -->
                <div class="relative w-full h-[360px] sm:h-[480px] lg:h-[540px] rounded-2xl overflow-hidden bg-slate-900 shadow-lg group">
                    <template x-for="(slide, index) in slides" :key="index">
                        <div x-show="activeSlide === index"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 scale-98"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-102"
                             class="absolute inset-0 w-full h-full">
                            <img :src="slide.url" :alt="slide.title" class="w-full h-full object-cover">
                            
                            <!-- Gradient Overlay & Caption -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent flex flex-col justify-end p-6 sm:p-8">
                                <div class="max-w-2xl transform transition-all duration-300">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-600/90 backdrop-blur-md text-white text-[11px] font-extrabold uppercase tracking-wider rounded-lg mb-2 shadow">
                                        <i class="fas fa-camera"></i> Featured Gallery
                                    </span>
                                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white leading-tight drop-shadow-md" x-text="slide.title"></h3>
                                    <p class="text-xs sm:text-sm text-slate-200 mt-1.5 leading-relaxed drop-shadow" x-text="slide.caption"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Floating Left / Right Arrow Buttons -->
                    <button type="button" @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-slate-900/60 hover:bg-slate-900 text-white backdrop-blur-md border border-white/20 flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 shadow-xl cursor-pointer">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </button>
                    <button type="button" @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-slate-900/60 hover:bg-slate-900 text-white backdrop-blur-md border border-white/20 flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 shadow-xl cursor-pointer">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </button>

                    <!-- Bottom Dots Navigation -->
                    <div class="absolute bottom-4 right-6 flex items-center gap-1.5 z-10 bg-slate-900/60 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10 shadow-md">
                        <template x-for="(slide, index) in slides" :key="'dot-' + index">
                            <button type="button" @click="goTo(index)" class="h-2 rounded-full transition-all cursor-pointer" :class="activeSlide === index ? 'w-6 bg-blue-500' : 'w-2 bg-white/50 hover:bg-white'"></button>
                        </template>
                    </div>
                </div>

                <!-- Bottom Thumbnails Strip -->
                <div class="mt-4 flex gap-3 overflow-x-auto pb-2 pt-1 scrollbar-thin">
                    <template x-for="(slide, index) in slides" :key="'thumb-' + index">
                        <button type="button" @click="goTo(index)" class="relative shrink-0 w-24 h-16 sm:w-28 sm:h-18 rounded-xl overflow-hidden border-2 transition-all cursor-pointer shadow-sm" :class="activeSlide === index ? 'border-blue-600 ring-2 ring-blue-300 scale-105 shadow-md' : 'border-slate-200 opacity-60 hover:opacity-100'">
                            <img :src="slide.url" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </section>

            <!-- Available Rooms -->
            <section id="available-rooms" class="scroll-mt-24">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 flex items-center gap-2">
                            <i class="fas fa-bed text-blue-600"></i> Available Rooms & Suites
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Select from our handpicked luxury rooms with best rate guarantee</p>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <button type="button" onclick="openBookingModal()" class="text-xs font-bold px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm cursor-pointer transition-all flex items-center gap-1.5">
                            <i class="fas fa-search text-xs"></i> Change Dates & Guests
                        </button>
                        <span class="text-xs font-bold px-3 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl">
                            <i class="fas fa-check-circle mr-1"></i> {{ $hotel->rooms->count() }} Total Rooms
                        </span>
                    </div>
                </div>

                {{-- Active Filter Bar --}}
                <div x-show="isFilterActive" x-cloak class="mb-6 p-4 rounded-2xl bg-blue-50/90 border border-blue-200 flex flex-wrap items-center justify-between gap-3 text-xs shadow-2xs">
                    <div class="flex flex-wrap items-center gap-3 font-semibold text-slate-800">
                        <span class="px-3 py-1 bg-blue-600 text-white rounded-xl font-extrabold flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-filter text-[10px]"></i> Showing Rooms For:
                        </span>
                        <span class="bg-white px-3 py-1.5 rounded-xl border border-blue-100"><i class="fas fa-user-friends text-blue-500 mr-1"></i> <strong x-text="`${filterGuests} Guest${filterGuests > 1 ? 's' : ''}`"></strong> (<span x-text="`${filterAdults} Adult${filterAdults > 1 ? 's' : ''}${filterChildren > 0 ? ', ' + filterChildren + ' Child' : ''}`"></span>)</span>
                        <span class="bg-white px-3 py-1.5 rounded-xl border border-blue-100"><i class="fas fa-calendar-alt text-blue-500 mr-1"></i> <span x-text="filterCheckIn"></span> → <span x-text="filterCheckOut"></span></span>
                        <span class="bg-white px-3 py-1.5 rounded-xl border border-blue-100"><i class="fas fa-door-open text-blue-500 mr-1"></i> <span x-text="`${filterRooms} Room${filterRooms > 1 ? 's' : ''}`"></span></span>
                        <span x-show="filterRoomTypeName" x-cloak class="bg-white px-3 py-1.5 rounded-xl border border-blue-100"><i class="fas fa-bed text-blue-500 mr-1"></i> <strong x-text="filterRoomTypeName"></strong></span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="openBookingModal()" class="px-3.5 py-1.5 bg-white hover:bg-slate-100 text-blue-600 font-bold rounded-xl border border-blue-200 shadow-2xs cursor-pointer transition-all flex items-center gap-1.5">
                            <i class="fas fa-edit text-xs"></i> Modify
                        </button>
                        <button type="button" @click="resetFilter()" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl shadow-2xs cursor-pointer transition-all flex items-center gap-1">
                            <i class="fas fa-times text-xs"></i> Clear Filter
                        </button>
                    </div>
                </div>
                
                @if($hotel->rooms->isEmpty())
                    <div class="bg-blue-50 border border-blue-100 text-blue-800 p-8 rounded-3xl text-center">
                        <i class="fas fa-hotel text-3xl text-blue-400 mb-2"></i>
                        <p class="font-bold">No rooms currently listed for this property.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($hotel->rooms as $room)
                            @php
                                $roomTypeName = $room->roomType->name ?? 'Standard Room';
                                $roomPrice = $room->price ?: ($room->roomType->base_price ?? 2500);
                                $bookUrl = route('booking-engine.hotel', ['slug' => $hotel->slug ? $hotel->slug . '-' . $hotel->id : $hotel->id]) . '?room_type_id=' . $room->room_type_id . '&room_id=' . $room->id;
                                
                                $isMaintenance = ($room->status === 'Maintenance' || ($room->activeMaintenanceTickets && $room->activeMaintenanceTickets->count() > 0));
                                $hkStatus = $room->latestHousekeeping?->status ?? 'Clean';
                                $isDirty = in_array($hkStatus, ['Dirty', 'Inspecting']);
                                $isOccupied = ($room->status === 'Occupied');
                                
                                $hasActiveReservation = $room->reservations 
                                    ? $room->reservations->whereIn('status', ['Confirmed', 'Checked-In', 'Pending'])
                                                         ->where('check_out_date', '>=', date('Y-m-d'))
                                                         ->count() > 0 
                                    : false;

                <!-- Available Rooms -->
                <section id="available-rooms" class="scroll-mt-24">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-slate-900">Available Rooms & Suites</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Check live room rates and availability for your selected dates.</p>
                        </div>
                        <template x-if="hasSearched">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-xl shadow-2xs">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                                <span x-text="matchingRooms.length + ' Available Room' + (matchingRooms.length === 1 ? '' : 's')"></span>
                            </span>
                        </template>
                    </div>

                    <!-- Search Summary Banner (Shown when searched) -->
                    <template x-if="hasSearched">
                        <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-200/80 rounded-2xl p-4 mb-6 flex flex-wrap items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-2 text-xs font-bold text-emerald-950">
                                <i class="fas fa-calendar-check text-emerald-600 text-sm"></i>
                                <span>Showing rooms for: <strong class="text-slate-900" x-text="formatDisplayDate(searchCheckIn)"></strong> ➔ <strong class="text-slate-900" x-text="formatDisplayDate(searchCheckOut)"></strong> (<span x-text="searchNights + (searchNights === 1 ? ' Night' : ' Nights')"></span>, <span x-text="searchGuests + (searchGuests === 1 ? ' Guest' : ' Guests')"></span>)</span>
                            </div>
                            <button type="button" @click="document.getElementById('booking-sidebar-widget')?.scrollIntoView({ behavior: 'smooth' })" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 cursor-pointer">
                                <i class="fas fa-edit"></i> Modify Dates
                            </button>
                        </div>
                    </template>
                    
                    <div class="space-y-5">
                        <!-- STATE 2: User HAS searched, but NO rooms match -->
                        <template x-if="hasSearched && matchingRooms.length === 0">
                            <div class="bg-amber-50 border border-amber-200 text-amber-900 p-8 rounded-3xl text-center space-y-3 shadow-sm">
                                <div class="w-14 h-14 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center mx-auto text-xl font-bold">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-extrabold text-amber-900">No Rooms Available for Selected Dates</h4>
                                    <p class="text-xs text-amber-700 max-w-md mx-auto mt-1">
                                        All rooms are currently booked or under maintenance from <strong x-text="formatDisplayDate(searchCheckIn)"></strong> to <strong x-text="formatDisplayDate(searchCheckOut)"></strong> for <strong x-text="searchGuests"></strong> guest(s). Please try selecting different dates.
                                    </p>
                                </div>
                                <button type="button" @click="document.getElementById('booking-sidebar-widget')?.scrollIntoView({ behavior: 'smooth' })" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow transition-all cursor-pointer">
                                    <i class="fas fa-redo-alt"></i> Try Different Dates
                                </button>
                            </div>
                        </template>

                        <!-- STATE 3: User HAS searched, and matching rooms are found -->
                        <template x-if="hasSearched && matchingRooms.length > 0">
                            <div class="space-y-4">
                                <template x-for="room in matchingRooms" :key="room.id">
                                    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm flex flex-col sm:flex-row gap-6 hover:shadow-md transition-all">
                                        <div class="w-full sm:w-1/3 aspect-video sm:aspect-auto rounded-xl bg-slate-100 overflow-hidden shrink-0 relative">
                                            <img :src="room.image_url" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=800&q=80';" class="w-full h-full object-cover">
                                            <template x-if="room.images && room.images.length > 1">
                                                <span class="absolute top-3 right-3 bg-slate-900/70 backdrop-blur-sm border border-white/20 text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-sm">
                                                    <i class="fas fa-images text-blue-400"></i> <span x-text="room.images.length + ' Photos'"></span>
                                                </span>
                                            </template>
                                        </div>
                                        <div class="flex-1 flex flex-col justify-between">
                                            <div>
                                                <div class="flex justify-between items-start">
                                                        <div>
                                                            <h3 class="text-lg font-bold text-slate-900" x-text="room.name"></h3>
                                                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                                                <span><i class="fas fa-bed text-blue-500 mr-1"></i> Bed: <strong class="text-slate-800 font-bold" x-text="room.bed_type"></strong></span>
                                                            </p>
                                                        </div>
                                                    <span class="text-xs font-bold px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg flex items-center gap-1">
                                                        <i class="fas fa-check-circle"></i> Available
                                                    </span>
                                                </div>
                                                
                                                <div class="mt-3 flex flex-wrap gap-1.5">
                                                    <template x-if="room.room_option">
                                                        <template x-for="opt in room.room_option.split(',')" :key="opt">
                                                            <span class="text-[10px] font-extrabold text-indigo-700 bg-indigo-50 border border-indigo-100/80 px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-2xs">
                                                                <i class="fas fa-check-circle text-indigo-500 text-[9px]"></i> <span x-text="opt.trim()"></span>
                                                            </span>
                                                        </template>
                                                    </template>
                                                    <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg"><i class="fas fa-wifi mr-1 text-blue-500"></i> Free Wi-Fi</span>
                                                    <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg"><i class="fas fa-snowflake mr-1 text-blue-500"></i> AC</span>
                                                    <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg"><i class="fas fa-tv mr-1 text-blue-500"></i> Flat TV</span>
                                                    <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg"><i class="fas fa-coffee mr-1 text-blue-500"></i> Breakfast Included</span>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-6 flex items-end justify-between border-t border-slate-100 pt-4 gap-3">
                                                <div>
                                                    <div class="flex items-baseline gap-1.5">
                                                        <span class="block text-2xl font-black text-slate-900" x-text="'₹' + Number(room.price * searchNights).toLocaleString('en-IN')"></span>
                                                        <span class="text-xs font-bold text-slate-400" x-show="searchNights > 1" x-text="'(₹' + Number(room.price).toLocaleString('en-IN') + ' / night)'"></span>
                                                    </div>
                                                    <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider" x-text="'Total for ' + searchNights + (searchNights === 1 ? ' night' : ' nights') + ' + taxes'"></span>
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <button @click="selectedRoom = room; modalImgIdx = 0; showModal = true" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 cursor-pointer">
                                                        <i class="fas fa-eye text-slate-500"></i> View Details
                                                    </button>

                                                    <a :href="'/hotel/{{ $hotel->slug ?: $hotel->id }}/reserve/' + room.id + '?checkin=' + searchCheckIn + '&checkout=' + searchCheckOut + '&guests=' + searchGuests + (promoCode ? '&code=' + promoCode : '')" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-800 text-white text-xs font-bold rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                                                        <i class="fas fa-calendar-check text-xs"></i> Book Now
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
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
                        @endforeach
                    </div>

                    {{-- No matching rooms notice when filtered --}}
                    <div x-show="isFilterActive && matchingRoomsCount() === 0" x-cloak class="p-8 rounded-3xl bg-amber-50 border border-amber-200 text-center space-y-3 mt-6">
                        <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto text-xl font-bold">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <h4 class="text-base font-bold text-amber-900">
                            No <span x-text="filterRoomTypeName || 'matching'"></span> rooms for
                            <span x-text="filterGuests"></span> guest<span x-text="filterGuests > 1 ? 's' : ''"></span>
                            in <span x-text="filterRooms"></span> room<span x-text="filterRooms > 1 ? 's' : ''"></span>
                        </h4>
                        <p class="text-xs text-amber-700 max-w-md mx-auto">
                            Please try another room type, fewer guests, more rooms, or view all available rooms.
                        </p>
                        <div class="pt-2 flex justify-center gap-2">
                            <button type="button" onclick="openBookingModal()" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-sm cursor-pointer">
                                <i class="fas fa-sliders-h mr-1"></i> Adjust Guests
                            </button>
                            <button type="button" @click="resetFilter()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl cursor-pointer">
                                Show All Rooms
                            </button>
                        </div>
                    </div>
                @endif
            </section>

            <!-- Right Column: Booking Widget / Summary -->
            <div class="lg:col-span-1" id="booking-sidebar-widget">
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xl sticky top-28">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Book Your Stay</h3>
                    <p class="text-xs text-slate-500 mb-6">Select dates and number of guests to reserve instantly.</p>
                    
                    <form @submit.prevent="performSearch()" class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Check-in Date</label>
                            <input type="date" x-model="searchCheckIn" min="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Check-out Date</label>
                            <input type="date" x-model="searchCheckOut" :min="searchCheckIn" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Guests</label>
                            <select x-model="searchGuests" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="1">1 Guest</option>
                                <option value="2">2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="5">5+ Guests</option>
                            </select>
                        </div>
                        
                        <div class="pt-2">
                            <button type="submit" class="w-full bg-[#10B981] hover:bg-emerald-600 text-white font-bold text-sm py-3.5 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-search text-xs"></i> Check Availability & Book
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Room Details Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full overflow-hidden relative transform transition-all" @click.away="showModal = false">
            <!-- Modal Header / Image Carousel -->
            <div class="relative aspect-video bg-slate-100 group">
                <img :src="selectedRoom?.images && selectedRoom?.images.length > 0 ? selectedRoom?.images[modalImgIdx] : selectedRoom?.image" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1000&q=80';" class="w-full h-full object-cover transition-all duration-300">
                
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
                    <span x-text="selectedRoom?.name"></span>
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
                            <img :src="img" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=600&q=80';" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </template>

            <!-- Modal Body -->
            <div class="p-6 space-y-5">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900" x-text="selectedRoom?.name"></h3>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-3">
                            <span><i class="fas fa-bed text-blue-500 mr-1"></i> <span x-text="selectedRoom?.bed_type"></span></span>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-blue-600" x-text="selectedRoom?.price"></span>
                        <span class="text-xs text-slate-500 block">/ night</span>
                    </div>
                </div>

                <div class="border-t border-b border-slate-100 py-4 space-y-3">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Description</h4>
                        <p class="text-xs text-slate-600 leading-relaxed" x-text="selectedRoom?.description"></p>
                    </div>

                    <template x-if="selectedRoom?.room_option">
                        <div>
                            <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <i class="fas fa-star text-amber-500"></i> Selected Room Option(s) / Features
                            </h4>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="opt in selectedRoom.room_option.split(',')" :key="opt">
                                    <span class="text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-1 rounded-xl flex items-center gap-1.5 shadow-2xs">
                                        <i class="fas fa-check-circle text-indigo-500 text-xs"></i> <span x-text="opt.trim()"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>
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
                    <a :href="'/hotel/{{ $hotel->slug ?: $hotel->id }}/reserve/' + selectedRoom?.id + '?checkin=' + searchCheckIn + '&checkout=' + searchCheckOut + '&guests=' + searchGuests + (promoCode ? '&code=' + promoCode : '')" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold text-xs rounded-xl shadow-lg hover:shadow-xl transition-all cursor-pointer flex items-center gap-2">
                        <i class="fas fa-calendar-check text-xs"></i> Book This Room Now
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Instant Room Booking Modal -->
    <div x-show="showBookingModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full overflow-hidden relative my-8 transform transition-all" @click.away="showBookingModal = false">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 text-white p-6 relative">
                <button @click="showBookingModal = false; bookingSubmitted = false" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center cursor-pointer transition-all border border-white/20">
                    <i class="fas fa-times text-sm"></i>
                </button>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-white text-lg font-bold border border-white/20">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold" x-text="bookingSubmitted ? 'Booking Confirmed!' : 'Instant Room Reservation'"></h3>
                        <p class="text-xs text-blue-100 mt-0.5" x-text="selectedRoomForBooking ? selectedRoomForBooking.name : '{{ addslashes($hotel->name) }}'"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Content Body -->
            <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
                <!-- FORM MODE -->
                <template x-if="!bookingSubmitted">
                    <form @submit.prevent="submitBooking()" class="space-y-4">
                        <!-- Error Alert -->
                        <template x-if="errorMessage">
                            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-3.5 rounded-2xl text-xs font-bold flex items-center gap-2">
                                <i class="fas fa-exclamation-circle text-rose-600 text-base shrink-0"></i>
                                <span x-text="errorMessage"></span>
                            </div>
                        </template>

                        <!-- Room Summary & Price Banner -->
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <template x-if="selectedRoomForBooking?.image">
                                    <img :src="selectedRoomForBooking.image" class="w-14 h-12 rounded-xl object-cover border border-slate-200">
                                </template>
                                <div>
                                    <span class="text-sm font-extrabold text-slate-900 block" x-text="selectedRoomForBooking?.name"></span>
                                    <span class="text-xs text-slate-500" x-text="nights + ' Night' + (nights > 1 ? 's' : '')"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 font-bold block uppercase tracking-wider">Total Amount</span>
                                <span class="text-xl font-black text-blue-600" x-text="totalPayableFormatted"></span>
                            </div>
                        </div>

                        <!-- Date Inputs -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Check-in Date *</label>
                                <input type="date" x-model="bookingData.checkin_date" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-blue-600">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Check-out Date *</label>
                                <input type="date" x-model="bookingData.checkout_date" required class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-blue-600">
                            </div>
                        </div>

                        <!-- Guest Info -->
                        <div class="space-y-3 pt-2">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider">Guest Information</h4>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                                <input type="text" x-model="bookingData.guest_name" required placeholder="Full Name (e.g. Rikki Saini)" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-blue-600">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Email Address *</label>
                                    <input type="email" x-model="bookingData.guest_email" required placeholder="your.email@example.com" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-blue-600">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number *</label>
                                    <input type="text" x-model="bookingData.guest_phone" required placeholder="+91 9876543210" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-blue-600">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Special Request (Optional)</label>
                                <input type="text" x-model="bookingData.special_requests" placeholder="e.g. Early check-in, Quiet room" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-medium focus:outline-none focus:border-blue-600">
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="space-y-3 pt-2">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider">Payment Method</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="bookingData.payment_method" value="Cash" class="peer sr-only">
                                    <div class="p-3 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-blue-600 peer-checked:bg-blue-50/80 text-xs font-bold text-slate-800 transition-all flex items-center gap-2">
                                        <i class="fas fa-money-bill-wave text-emerald-600"></i> Pay at Hotel
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="bookingData.payment_method" value="UPI" class="peer sr-only">
                                    <div class="p-3 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-blue-600 peer-checked:bg-blue-50/80 text-xs font-bold text-slate-800 transition-all flex items-center gap-2">
                                        <i class="fas fa-qrcode text-blue-600"></i> UPI Instant
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Submit CTA -->
                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" :disabled="isSubmitting" class="w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                <template x-if="!isSubmitting">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-lock text-xs"></i> Confirm Booking Now (<span x-text="totalPayableFormatted"></span>)
                                    </span>
                                </template>
                                <template x-if="isSubmitting">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-spinner fa-spin text-xs"></i> Processing Reservation...
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>
                </template>

                <!-- VOUCHER MODE -->
                <template x-if="bookingSubmitted">
                    <div class="space-y-4">
                        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center">
                            <div class="w-12 h-12 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto mb-2 text-xl shadow-md">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4 class="text-lg font-black text-emerald-900">Room Reserved Successfully!</h4>
                            <p class="text-xs text-emerald-700 mt-1">Your reservation code (PNR) is: <strong class="text-amber-700 font-mono text-sm" x-text="successResult?.pnr"></strong></p>
                        </div>

                        <div class="bg-slate-900 text-white p-4 rounded-2xl space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Hotel:</span>
                                <span class="font-bold text-white" x-text="successResult?.hotel_name"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Room:</span>
                                <span class="font-bold text-white" x-text="successResult?.room_type"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Dates:</span>
                                <span class="font-bold text-white" x-text="successResult?.checkin_date + ' to ' + successResult?.checkout_date"></span>
                            </div>
                            <div class="flex justify-between border-t border-slate-800 pt-2">
                                <span class="text-slate-400">Total Payable:</span>
                                <span class="font-black text-emerald-400 text-sm" x-text="'₹' + successResult?.total_price"></span>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button onclick="window.print()" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-print"></i> Print Voucher Slip
                            </button>
                            <button @click="showBookingModal = false; bookingSubmitted = false" class="py-3 px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all cursor-pointer">
                                Done
                            </button>
                        </div>
                    </div>
                </template>
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

    <script>
        window.applyRoomCardVisibility = function(data) {
            const cards = document.querySelectorAll('.room-card-item');
            if (data && data.reset) {
                cards.forEach(function(card) {
                    card.classList.remove('is-filtered-out');
                });
                return cards.length;
            }

            const roomsRequested = Math.max(Number(data.rooms || 1), 1);
            const totalGuests = Math.max(Number(data.totalGuests || 1), 1);
            const guestsPerRoom = Math.ceil(totalGuests / roomsRequested);
            const typeId = String(data.roomTypeId || '').trim().toLowerCase();
            const typeName = String(data.roomTypeName || '').trim().toLowerCase();

            let visible = 0;
            cards.forEach(function(card) {
                const cap = Number(card.getAttribute('data-capacity') || 0);
                const cardTypeId = String(card.getAttribute('data-room-type-id') || '').trim().toLowerCase();
                const cardTypeName = String(card.getAttribute('data-room-type-name') || '').trim().toLowerCase();

                let typeOk = true;
                if (typeId) {
                    typeOk = cardTypeId === typeId
                        || (typeName && cardTypeName === typeName)
                        || (typeName && cardTypeName.indexOf(typeName) !== -1)
                        || (!/^\d+$/.test(typeId) && cardTypeName.indexOf(typeId) !== -1);
                }

                const capOk = cap >= guestsPerRoom;
                const show = typeOk && capOk;
                card.classList.toggle('is-filtered-out', !show);
                if (show) visible += 1;
            });

            return visible;
        };

        window.applyBookingSearchFilter = function(data) {
            const visibleCount = window.applyRoomCardVisibility(data || {});
            const bodyEl = document.querySelector('body');
            if (bodyEl && window.Alpine) {
                const bodyAlpine = Alpine.$data(bodyEl);
                if (bodyAlpine) {
                    bodyAlpine.filterGuests = data.totalGuests;
                    bodyAlpine.filterAdults = data.adults;
                    bodyAlpine.filterChildren = data.children;
                    bodyAlpine.filterRooms = data.rooms;
                    bodyAlpine.filterRoomTypeId = data.roomTypeId || '';
                    bodyAlpine.filterRoomTypeName = data.roomTypeName || '';
                    bodyAlpine.filterCheckIn = data.checkIn;
                    bodyAlpine.filterCheckOut = data.checkOut;
                    bodyAlpine.visibleRoomCount = visibleCount;
                    bodyAlpine.isFilterActive = true;
                    
                    if (bodyAlpine.bookingData) {
                        bodyAlpine.bookingData.checkin_date = data.checkIn;
                        bodyAlpine.bookingData.checkout_date = data.checkOut;
                    }

                    let url = new URL(window.location.href);
                    url.searchParams.set('check_in', data.checkIn);
                    url.searchParams.set('check_out', data.checkOut);
                    url.searchParams.set('adults', data.adults);
                    url.searchParams.set('children', data.children);
                    url.searchParams.set('rooms', data.rooms);
                    if (data.roomTypeId) {
                        url.searchParams.set('room_type_id', data.roomTypeId);
                        if (data.roomTypeName) {
                            url.searchParams.set('room_type_name', data.roomTypeName);
                        }
                    } else {
                        url.searchParams.delete('room_type_id');
                        url.searchParams.delete('room_type_name');
                    }
                    window.history.pushState({}, '', url.toString());
                }
            }

            setTimeout(() => {
                const el = document.getElementById('available-rooms');
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 120);
        };

        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('adults') || params.has('guests') || params.has('check_in') || params.has('room_type_id')) {
                window.applyBookingSearchFilter({
                    checkIn: params.get('check_in'),
                    checkOut: params.get('check_out'),
                    rooms: Number(params.get('rooms') || 1),
                    adults: Number(params.get('adults') || 1),
                    children: Number(params.get('children') || 0),
                    totalGuests: Number(params.get('adults') || 1) + Number(params.get('children') || 0),
                    roomTypeId: params.get('room_type_id') || '',
                    roomTypeName: params.get('room_type_name') || ''
                });
            }
        });
    </script>

    @include('components.booking.booking-search')
</body>
</html>
