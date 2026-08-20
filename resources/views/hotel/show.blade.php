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
<<<<<<< HEAD
    <style>
        [x-cloak] { display: none !important; }
        .is-filtered-out { display: none !important; }
    </style>
=======
>>>>>>> 5f7ad150ec083575746ac84afe2f70478282f8f1
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
                    'Accept': 'application/json',
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
}">

    <!-- Navbar Header with Navigation Menu -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50 shadow-xs" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('images/lodgiko.png') }}" alt="Lodgiko Logo" class="h-11 w-auto">
            </a>

            <nav class="hidden md:flex items-center justify-center gap-8 flex-1 text-sm font-semibold text-slate-700">
                <a href="/" class="inline-flex items-center gap-2 hover:text-blue-600 transition-colors">
                    <i class="fas fa-home text-[10px] text-blue-500"></i> Home
                </a>
                <a href="/#hotels" class="inline-flex items-center gap-2 hover:text-blue-600 transition-colors">
                    <i class="fas fa-hotel text-[10px] text-blue-500"></i> Hotels
                </a>
                <a href="#about-property" class="inline-flex items-center gap-2 hover:text-blue-600 transition-colors">
                    <i class="fas fa-info-circle text-[10px] text-blue-500"></i> About
                </a>
                <a href="#faq-section" class="inline-flex items-center gap-2 hover:text-blue-600 transition-colors">
                    <i class="fas fa-question-circle text-[10px] text-blue-500"></i> FAQ
                </a>
            </nav>

            <div class="flex items-center gap-3">
                <button type="button" onclick="openBookingModal()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all flex items-center gap-2 cursor-pointer">
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

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs, Title & Rating Header --}}
        <div class="mb-6">
            <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-2">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <span>/</span>
                <span class="text-slate-600">{{ $hotel->city }}</span>
                <span>/</span>
                <span class="text-slate-900 font-bold lowercase">{{ $hotel->name }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight lowercase">{{ $hotel->name }}</h1>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-blue-500"></i>
                        <span>Full Street Address: {{ $hotel->address ?: 'Mall Road, ' . $hotel->city }}, {{ $hotel->city }}, {{ $hotel->state ?? 'Uttarakhand' }}, {{ $hotel->country ?? 'United States' }}</span>
                    </p>
                </div>

                <div class="self-start sm:self-auto text-left sm:text-right">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">RATING</span>
                    <span class="text-amber-500 font-extrabold text-sm flex items-center gap-1">
                        <i class="fas fa-star text-amber-400"></i> 4.8 / 5
                    </span>
                </div>
            </div>
        </div>

        @php
            $galleryImages = [];
            if ($hotel->images && $hotel->images->count() > 0) {
                foreach ($hotel->images as $img) {
                    $galleryImages[] = $img->url;
                }
            }
            if ($hotel->rooms && $hotel->rooms->count() > 0) {
                foreach ($hotel->rooms as $r) {
                    if (!empty($r->images)) {
                        foreach ($r->images as $rImg) {
                            $galleryImages[] = $rImg;
                        }
                    }
                }
            }
            $fallbacks = [
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=800&q=80'
            ];
            while (count($galleryImages) < 3) {
                $galleryImages[] = $fallbacks[count($galleryImages) % count($fallbacks)];
            }
            $mainImg = $galleryImages[0];
            $sideImg1 = $galleryImages[1];
            $sideImg2 = $galleryImages[2];
        @endphp

        {{-- Top Photo Showcase Gallery Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 rounded-3xl overflow-hidden mb-8 border border-slate-200 bg-white p-2.5 shadow-xs h-[300px] sm:h-[380px] md:h-[420px] lg:h-[460px]">
            <div class="md:col-span-8 h-full rounded-2xl overflow-hidden bg-slate-100 relative group">
                <img src="{{ $mainImg }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="hidden md:grid md:col-span-4 md:grid-rows-2 gap-3.5 h-full">
                <div class="w-full h-full rounded-2xl overflow-hidden bg-slate-100 relative group">
                    <img src="{{ $sideImg1 }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=800&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
<<<<<<< HEAD
                <div class="w-full h-full rounded-2xl overflow-hidden bg-slate-100 relative group">
                    <img src="{{ $sideImg2 }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=800&q=80';" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
=======

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
>>>>>>> 5f7ad150ec083575746ac84afe2f70478282f8f1
                </div>
            </div>
        </div>

        {{-- 2-Column Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Left Column (8 cols): About Property, Available Rooms, FAQ --}}
            <div class="lg:col-span-8 space-y-8">
                
                {{-- 1. About the Property Card --}}
                <div id="about-property" class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 shadow-xs space-y-4 scroll-mt-24">
                    <h3 class="text-base font-bold text-slate-900">About the Property</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Welcome to {{ $hotel->name }}. Located in {{ $hotel->city }}, this property offers modern accommodations with luxury amenities, 24/7 room service, and top-rated hospitality. Perfect for both business travelers and vacationing families.
                    </p>
                    
                    <div class="pt-2">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">POPULAR AMENITIES</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-blue-50/50 border border-blue-100/80 text-slate-700 text-xs font-bold">
                                <i class="fas fa-wifi text-blue-500 text-sm"></i>
                                <span class="text-[11px]">High-Speed Wi-Fi</span>
                            </div>
                            <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-blue-50/50 border border-blue-100/80 text-slate-700 text-xs font-bold">
                                <i class="fas fa-swimming-pool text-blue-500 text-sm"></i>
                                <span class="text-[11px]">Swimming Pool</span>
                            </div>
                            <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-blue-50/50 border border-blue-100/80 text-slate-700 text-xs font-bold">
                                <i class="fas fa-parking text-blue-500 text-sm"></i>
                                <span class="text-[11px]">Free Parking</span>
                            </div>
                            <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-blue-50/50 border border-blue-100/80 text-slate-700 text-xs font-bold">
                                <i class="fas fa-utensils text-blue-500 text-sm"></i>
                                <span class="text-[11px]">Restaurant</span>
                            </div>
                        </div>
                    </div>
                </div>

<<<<<<< HEAD
                {{-- 2. Available Rooms & Suites Section --}}
                <div id="available-rooms" class="space-y-4 scroll-mt-24">
                    <h2 class="text-xl font-bold text-slate-900">Available Rooms & Suites</h2>

                    @php
                        $availableRooms = $hotel->rooms->filter(function($room) {
                            $isMaintenance = ($room->status === 'Maintenance' || ($room->activeMaintenanceTickets && $room->activeMaintenanceTickets->count() > 0));
                            $hkStatus = $room->latestHousekeeping?->status ?? 'Clean';
                            $isDirty = in_array($hkStatus, ['Dirty', 'Inspecting']);
                            $isOccupied = ($room->status === 'Occupied');
                            
                            $hasActiveReservation = $room->reservations 
                                ? $room->reservations->whereIn('status', ['Confirmed', 'Checked-In', 'Pending'])
                                                     ->where('check_out_date', '>=', date('Y-m-d'))
                                                     ->count() > 0 
                                : false;

                            return ($room->status === 'Available') && !$isMaintenance && !$isDirty && !$isOccupied && !$hasActiveReservation;
                        });
                    @endphp

                    @php
                        $hasSearchedParam = request()->hasAny(['check_in', 'check_out', 'adults', 'guests', 'bed_type', 'searched']);
                    @endphp

                    {{-- Initial Prompt before Search --}}
                    <div id="initialSearchPrompt" style="{{ $hasSearchedParam ? 'display: none;' : '' }}" class="bg-blue-50/60 border border-blue-100/80 rounded-3xl p-8 text-center space-y-3 shadow-2xs">
                        <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto text-xl shadow-2xs">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-slate-900">Find Available Rooms & Rates</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                                Please select your <strong class="text-slate-700">Check-in Date</strong>, <strong class="text-slate-700">Check-out Date</strong>, and <strong class="text-slate-700">Guests</strong> in the Room Search box, then click <strong class="text-blue-600">Search Rooms</strong> to view available inventory.
                            </p>
                        </div>
                    </div>

                    @if($availableRooms->isEmpty())
                        <div id="noRoomsAvailableAtAll" style="{{ $hasSearchedParam ? '' : 'display: none;' }}" class="bg-blue-50/70 border border-blue-100 text-blue-800 p-8 rounded-2xl text-center text-xs font-semibold">
                            No rooms available for your selected dates and guests
                        </div>
                    @else
                        <div id="noFilteredRoomsMsg" style="display: none;" class="bg-amber-50 border border-amber-200 text-amber-900 p-6 sm:p-8 rounded-3xl text-center shadow-xs space-y-2">
                            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-lg">
                                <i class="fas fa-search"></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-900">No rooms available for your selected dates and guests</h4>
                            <p class="text-xs text-slate-500">Try adjusting your check-in / check-out dates, guest count, or selecting "All Bed Types".</p>
                            <button type="button" onclick="window.resetRoomSearchFilters()" class="inline-flex items-center gap-1.5 mt-2 px-3.5 py-1.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 shadow-2xs cursor-pointer">
                                <i class="fas fa-undo text-[10px]"></i> Reset Search Filters
                            </button>
                        </div>
                        <div class="space-y-4" id="roomsListContainer" style="{{ $hasSearchedParam ? '' : 'display: none;' }}">
                            @foreach($availableRooms as $room)
                                @php
                                    $roomTypeName = $room->roomType->name ?? 'Standard Room';
                                    $roomPrice = $room->price ?: ($room->roomType->base_price ?? 2500);
                                    $activeReservationsList = $room->reservations 
                                        ? $room->reservations->whereIn('status', ['Confirmed', 'Checked-In', 'Pending'])->map(function($r) {
                                            return [
                                                'start' => substr((string)$r->check_in_date, 0, 10),
                                                'end' => substr((string)$r->check_out_date, 0, 10),
                                                'status' => $r->status
                                            ];
                                        })->values()
                                        : collect();
                                @endphp

                                <div class="room-card-item bg-white border border-slate-200 rounded-3xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col sm:flex-row gap-5"
                                     data-room-id="{{ $room->id }}"
                                     data-capacity="{{ $room->capacity ?? 2 }}"
                                     data-bed-type="{{ strtolower($room->bed_type ?? '') }}"
                                     data-room-type-id="{{ $room->room_type_id }}"
                                     data-room-type-name="{{ strtolower($roomTypeName) }}"
                                     data-reservations="{{ json_encode($activeReservationsList) }}">
                                    
                                    {{-- Room Thumbnail --}}
                                    <div class="w-full sm:w-1/3 aspect-video sm:aspect-auto rounded-2xl bg-slate-100 overflow-hidden shrink-0 relative min-h-[160px]">
                                        <img src="{{ $room->image_url }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=800&q=80';" class="w-full h-full object-cover">
                                        @if(count($room->images) > 1)
                                            <span class="absolute top-2.5 right-2.5 bg-slate-900/75 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-lg border border-white/20">
                                                <i class="fas fa-images text-blue-400"></i> {{ count($room->images) }} Photos
                                            </span>
                                        @endif
                                        <span class="absolute bottom-2.5 left-2.5 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-bold px-2.5 py-1 rounded-lg">
                                            Room {{ $room->room_number }}
                                        </span>
                                    </div>

                                    {{-- Room Details --}}
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <div class="flex justify-between items-start">
=======
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
>>>>>>> 5f7ad150ec083575746ac84afe2f70478282f8f1
                                                <div>
                                                    <h3 class="text-base sm:text-lg font-bold text-slate-900">{{ $roomTypeName }}</h3>
                                                    <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-3">
                                                        <span><i class="fas fa-bed text-blue-500 mr-1"></i> {{ $room->bed_type ?: 'King Bed' }}</span>
                                                        <span><i class="fas fa-users text-blue-500 mr-1"></i> Max {{ $room->capacity ?? 2 }} Guests</span>
                                                    </p>
                                                </div>
                                                <span class="text-[11px] font-bold px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg flex items-center gap-1">
                                                    <i class="fas fa-check-circle"></i> Available
                                                </span>
                                            </div>

                                            <div class="mt-3 flex flex-wrap gap-1.5">
                                                @if($room->room_option)
                                                    @foreach(explode(',', $room->room_option) as $opt)
                                                        <span class="text-[10px] font-extrabold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-lg flex items-center gap-1">
                                                            <i class="fas fa-check text-indigo-500 text-[8px]"></i> {{ trim($opt) }}
                                                        </span>
                                                    @endforeach
                                                @endif
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-lg"><i class="fas fa-wifi text-blue-500 mr-1"></i> Free Wi-Fi</span>
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-lg"><i class="fas fa-snowflake text-blue-500 mr-1"></i> AC</span>
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-lg"><i class="fas fa-tv text-blue-500 mr-1"></i> Smart TV</span>
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-lg"><i class="fas fa-coffee text-blue-500 mr-1"></i> Breakfast Included</span>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                                            <div>
                                                <div class="flex items-baseline gap-1">
                                                    <span class="text-xl font-black text-slate-900">₹{{ number_format($roomPrice) }}</span>
                                                    <span class="text-[10px] text-slate-400 uppercase font-bold">/ night</span>
                                                </div>
                                                <span class="text-[9px] text-slate-400 font-medium">Excl. taxes & fees</span>
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
                                                    description: {!! json_encode($room->description ?: "Experience luxury comfort in Room " . $room->room_number . ". Featuring modern design, high-speed Wi-Fi, air conditioning, and top-tier amenities.") !!},
                                                    bed_type: {!! json_encode($room->bed_type ?? "King Bed") !!},
                                                    room_option: {!! json_encode($room->room_option ?? "") !!},
                                                    capacity: {!! json_encode(($room->capacity ?? 2) . ' Guests') !!}
                                                }; modalImgIdx = 0; showModal = true" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 cursor-pointer">
                                                    <i class="fas fa-eye text-slate-500"></i> View Details
                                                </button>

                                                <a href="{{ route('hotel.reserve', ['slug' => $hotel->slug ?: $hotel->id, 'room' => $room->id]) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow transition-all flex items-center gap-1.5 cursor-pointer">
                                                    <i class="fas fa-calendar-check text-xs"></i> Book Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- 3. Frequently Asked Questions Card --}}
                <div id="faq-section" class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs scroll-mt-24" x-data="{ openFaq: null }">
                    <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-question-circle text-blue-600"></i> Frequently Asked Questions
                    </h3>
                    <div class="space-y-2.5 text-xs">
                        <div class="border border-slate-100 rounded-2xl overflow-hidden bg-slate-50">
                            <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-4 py-3 text-left font-bold text-slate-800 flex justify-between items-center cursor-pointer">
                                <span>What are the standard Check-in and Check-out times?</span>
                                <i class="fas text-slate-400 text-[10px]" :class="openFaq === 1 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                            <div x-show="openFaq === 1" x-cloak class="p-4 bg-white text-slate-600 border-t border-slate-100 leading-relaxed">
                                Standard Check-in is from 02:00 PM and Check-out is until 11:00 AM. Early check-in or late check-out is subject to availability.
                            </div>
<<<<<<< HEAD
=======
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
>>>>>>> 5f7ad150ec083575746ac84afe2f70478282f8f1
                        </div>

                        <div class="border border-slate-100 rounded-2xl overflow-hidden bg-slate-50">
                            <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-4 py-3 text-left font-bold text-slate-800 flex justify-between items-center cursor-pointer">
                                <span>Is breakfast included with room booking?</span>
                                <i class="fas text-slate-400 text-[10px]" :class="openFaq === 2 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                            <div x-show="openFaq === 2" x-cloak class="p-4 bg-white text-slate-600 border-t border-slate-100 leading-relaxed">
                                Yes, complimentary high-speed Wi-Fi and daily buffet breakfast are included with most room packages.
                            </div>
                        </div>

                        <div class="border border-slate-100 rounded-2xl overflow-hidden bg-slate-50">
                            <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-4 py-3 text-left font-bold text-slate-800 flex justify-between items-center cursor-pointer">
                                <span>What is the cancellation policy?</span>
                                <i class="fas text-slate-400 text-[10px]" :class="openFaq === 3 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                            <div x-show="openFaq === 3" x-cloak class="p-4 bg-white text-slate-600 border-t border-slate-100 leading-relaxed">
                                Free cancellation is available up to 24 hours prior to check-in. For late cancellations, standard 1-night room charges may apply.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<<<<<<< HEAD
            {{-- Right Column (4 cols): Sticky Room Search Card --}}
            <div class="lg:col-span-4 sticky top-24">
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-md space-y-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i class="fas fa-search text-blue-600"></i> Room Search
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Select dates and guests to find available rooms.</p>
                    </div>

                    <form action="{{ route('hotel.show', ['slug' => $hotel->slug ?: $hotel->id]) }}" method="GET" id="sidebarBookingSearchForm" onsubmit="return handleSidebarSearchSubmit(event)" class="space-y-3.5">
=======
            <!-- Right Column: Booking Widget / Summary -->
            <div class="lg:col-span-1" id="booking-sidebar-widget">
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xl sticky top-28">
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Book Your Stay</h3>
                    <p class="text-xs text-slate-500 mb-6">Select dates and number of guests to reserve instantly.</p>
                    
                    <form @submit.prevent="performSearch()" class="space-y-4">
>>>>>>> 5f7ad150ec083575746ac84afe2f70478282f8f1
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">GUESTS</label>
                            <select name="adults" id="sidebar_guests" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-blue-600 focus:bg-white shadow-2xs">
                                <option value="2" {{ ($totalGuests ?? 2) == 2 ? 'selected' : '' }}>2 Guests</option>
                                <option value="3" {{ ($totalGuests ?? 2) == 3 ? 'selected' : '' }}>3 Guests</option>
                                <option value="4" {{ ($totalGuests ?? 2) == 4 ? 'selected' : '' }}>4 Guests</option>
                                <option value="5" {{ ($totalGuests ?? 2) == 5 ? 'selected' : '' }}>5 Guests</option>
                                <option value="1" {{ ($totalGuests ?? 2) == 1 ? 'selected' : '' }}>1 Guest</option>
                                <option value="6" {{ ($totalGuests ?? 2) >= 6 ? 'selected' : '' }}>6+ Guests</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">CHECK-IN DATE</label>
                            <div class="relative">
                                <input type="date" name="check_in" id="sidebar_checkin" value="{{ $checkInDate ?? date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-blue-600 focus:bg-white shadow-2xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">CHECK-OUT DATE</label>
                            <div class="relative">
                                <input type="date" name="check_out" id="sidebar_checkout" value="{{ $checkOutDate ?? date('Y-m-d', strtotime('+1 day')) }}" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-blue-600 focus:bg-white shadow-2xs">
                            </div>
                        </div>

                        <div class="pt-1">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-search text-xs"></i> Search Rooms
                            </button>
                        </div>
<<<<<<< HEAD

                        <p class="text-[10px] text-center text-slate-400 flex items-center justify-center gap-1.5 pt-1">
                            <i class="fas fa-shield-alt text-emerald-500"></i> Best Rate Guaranteed
                        </p>
                    </form>
=======
                    </div>
>>>>>>> 5f7ad150ec083575746ac84afe2f70478282f8f1
                </div>
            </div>
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
                const noRoomsMsg = document.getElementById('noFilteredRoomsMsg');
                if (noRoomsMsg) noRoomsMsg.style.display = 'none';
                return cards.length;
            }

            const roomsRequested = Math.max(Number(data.rooms || 1), 1);
            const totalGuests = Math.max(Number(data.totalGuests || data.adults || 1), 1);
            const guestsPerRoom = Math.ceil(totalGuests / roomsRequested);
            const typeId = String(data.roomTypeId || '').trim().toLowerCase();
            const typeName = String(data.roomTypeName || '').trim().toLowerCase();
            const filterBedType = String(data.bedType || '').trim().toLowerCase();
            const checkIn = data.checkIn || '';
            const checkOut = data.checkOut || '';

            let visible = 0;
            cards.forEach(function(card) {
                const cap = Number(card.getAttribute('data-capacity') || 0);
                const cardTypeId = String(card.getAttribute('data-room-type-id') || '').trim().toLowerCase();
                const cardTypeName = String(card.getAttribute('data-room-type-name') || '').trim().toLowerCase();
                const cardBedType = String(card.getAttribute('data-bed-type') || '').trim().toLowerCase();

                let typeOk = true;
                if (typeId) {
                    typeOk = cardTypeId === typeId
                        || (typeName && cardTypeName === typeName)
                        || (typeName && cardTypeName.indexOf(typeName) !== -1)
                        || (!/^\d+$/.test(typeId) && cardTypeName.indexOf(typeId) !== -1);
                }

                // Capacity check: room capacity must accommodate the requested guests
                const capOk = (cap === 0) || (cap >= guestsPerRoom);

                // Bed Type check
                const bedTypeOk = !filterBedType || cardBedType.includes(filterBedType);

                // Date overlap check with active reservations
                let dateOk = true;
                if (checkIn && checkOut) {
                    const rawRes = card.getAttribute('data-reservations');
                    if (rawRes) {
                        try {
                            const resList = JSON.parse(rawRes);
                            if (Array.isArray(resList)) {
                                for (let i = 0; i < resList.length; i++) {
                                    const r = resList[i];
                                    if (r && r.start && r.end) {
                                        if (r.start < checkOut && r.end > checkIn) {
                                            dateOk = false;
                                            break;
                                        }
                                    }
                                }
                            }
                        } catch(err) {}
                    }
                }

                const show = typeOk && capOk && bedTypeOk && dateOk;
                card.classList.toggle('is-filtered-out', !show);
                if (show) {
                    visible += 1;
                    const bookBtn = card.querySelector('a[href*="reserve"]');
                    if (bookBtn && checkIn && checkOut) {
                        let baseHref = bookBtn.getAttribute('data-base-href') || bookBtn.getAttribute('href').split('?')[0];
                        bookBtn.setAttribute('data-base-href', baseHref);
                        bookBtn.setAttribute('href', `${baseHref}?checkin=${encodeURIComponent(checkIn)}&checkout=${encodeURIComponent(checkOut)}&guests=${encodeURIComponent(totalGuests)}`);
                    }
                }
            });

            const noRoomsMsg = document.getElementById('noFilteredRoomsMsg');
            if (noRoomsMsg) {
                noRoomsMsg.style.display = (visible === 0) ? 'block' : 'none';
            }

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
                    bodyAlpine.filterBedType = data.bedType || '';
                    bodyAlpine.filterCheckIn = data.checkIn;
                    bodyAlpine.filterCheckOut = data.checkOut;
                    bodyAlpine.visibleRoomCount = visibleCount;
                    bodyAlpine.isFilterActive = true;
                    
                    if (bodyAlpine.bookingData) {
                        bodyAlpine.bookingData.checkin_date = data.checkIn;
                        bodyAlpine.bookingData.checkout_date = data.checkOut;
                    }

                    let url = new URL(window.location.href);
                    if (data.checkIn) url.searchParams.set('check_in', data.checkIn);
                    if (data.checkOut) url.searchParams.set('check_out', data.checkOut);
                    if (data.adults) url.searchParams.set('adults', data.adults);
                    if (data.children) url.searchParams.set('children', data.children);
                    if (data.rooms) url.searchParams.set('rooms', data.rooms);
                    if (data.bedType) {
                        url.searchParams.set('bed_type', data.bedType);
                    } else {
                        url.searchParams.delete('bed_type');
                    }
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

            // Reveal rooms list and hide initial prompt
            const initialPrompt = document.getElementById('initialSearchPrompt');
            const roomsContainer = document.getElementById('roomsListContainer');
            if (initialPrompt && !data.reset) initialPrompt.style.display = 'none';
            if (roomsContainer && !data.reset) roomsContainer.style.display = 'block';

            // Sync sidebar form fields if present
            const sideCheckIn = document.getElementById('sidebar_checkin');
            const sideCheckOut = document.getElementById('sidebar_checkout');
            const sideGuests = document.getElementById('sidebar_guests');
            const sideBedType = document.getElementById('sidebar_bed_type');
            if (sideCheckIn && data.checkIn) sideCheckIn.value = data.checkIn;
            if (sideCheckOut && data.checkOut) sideCheckOut.value = data.checkOut;
            if (sideGuests && data.adults) sideGuests.value = String(data.adults);
            if (sideBedType && data.bedType !== undefined) sideBedType.value = data.bedType;

            setTimeout(() => {
                const el = document.getElementById('available-rooms');
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 120);
        };

        window.resetRoomSearchFilters = function() {
            const sideCheckIn = document.getElementById('sidebar_checkin');
            const sideCheckOut = document.getElementById('sidebar_checkout');
            const sideGuests = document.getElementById('sidebar_guests');
            const sideBedType = document.getElementById('sidebar_bed_type');
            
            if (sideCheckIn) sideCheckIn.value = '{{ $checkInDate }}';
            if (sideCheckOut) sideCheckOut.value = '{{ $checkOutDate }}';
            if (sideGuests) sideGuests.value = '2';
            if (sideBedType) sideBedType.value = '';

            const initialPrompt = document.getElementById('initialSearchPrompt');
            const roomsContainer = document.getElementById('roomsListContainer');
            if (initialPrompt) initialPrompt.style.display = 'block';
            if (roomsContainer) roomsContainer.style.display = 'none';

            window.applyRoomCardVisibility({ reset: true });

            let url = new URL(window.location.href);
            url.searchParams.delete('bed_type');
            url.searchParams.delete('room_type_id');
            url.searchParams.delete('room_type_name');
            window.history.pushState({}, '', url.toString());
        };

        function handleSidebarSearchSubmit(e) {
            if (e) e.preventDefault();
            const checkIn = document.getElementById('sidebar_checkin')?.value;
            const checkOut = document.getElementById('sidebar_checkout')?.value;
            const guests = Number(document.getElementById('sidebar_guests')?.value || 2);
            const bedType = document.getElementById('sidebar_bed_type')?.value || '';

            window.applyBookingSearchFilter({
                checkIn: checkIn,
                checkOut: checkOut,
                rooms: 1,
                adults: guests,
                children: 0,
                totalGuests: guests,
                bedType: bedType,
                roomTypeId: '',
                roomTypeName: ''
            });

            return false;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('adults') || params.has('guests') || params.has('check_in') || params.has('room_type_id') || params.has('bed_type')) {
                window.applyBookingSearchFilter({
                    checkIn: params.get('check_in') || '{{ $checkInDate }}',
                    checkOut: params.get('check_out') || '{{ $checkOutDate }}',
                    rooms: Number(params.get('rooms') || 1),
                    adults: Number(params.get('adults') || params.get('guests') || 2),
                    children: Number(params.get('children') || 0),
                    totalGuests: Number(params.get('adults') || params.get('guests') || 2) + Number(params.get('children') || 0),
                    bedType: params.get('bed_type') || '',
                    roomTypeId: params.get('room_type_id') || '',
                    roomTypeName: params.get('room_type_name') || ''
                });
            }
        });
    </script>

    @include('components.booking.booking-search')
</body>
</html>
