<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lodgiko - Book Hotels, Resorts & Homestays</title>
    
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
                        primary: '#0892A7',
                        secondary: '#369B71',
                        accent: '#DAAF6C',      // Gold
                        dark: '#1E293B',
                        light: '#F8FAFC',
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="antialiased bg-slate-50 text-slate-800">
    <!-- Navbar -->
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
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-sm font-semibold text-slate-700">
                <a href="/" class="hover:text-blue-600 transition-colors flex items-center gap-1.5 text-blue-600 font-bold">
                    <i class="fas fa-home text-blue-600"></i> Home
                </a>
                <a href="#destinations" class="hover:text-blue-600 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-hotel text-blue-600"></i> Hotels
                </a>
                <a href="#about" class="hover:text-blue-600 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-info-circle text-blue-600"></i> About
                </a>
                <a href="#contact" class="hover:text-blue-600 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-phone-alt text-blue-600"></i> Contact
                </a>
                <a href="#faq" class="hover:text-blue-600 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-question-circle text-blue-600"></i> FAQ
                </a>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="{{ route('track-booking') }}" class="hidden lg:inline-flex text-xs font-bold text-slate-600 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-3.5 py-2.5 rounded-xl transition-all items-center gap-1.5">
                    <i class="fas fa-search text-blue-500"></i> Track Booking
                </a>
                <a href="{{ route('register-hotel') }}" class="hidden sm:inline-flex text-xs font-bold text-slate-600 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-3.5 py-2.5 rounded-xl transition-all">
                    List Your Property
                </a>
<<<<<<< HEAD
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-accent hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
=======
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md transition-all">
>>>>>>> 1b36bc314dcc9c8ee03f104fe115664d808bfc3f
                    <i class="far fa-user-circle mr-2"></i> Log In
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
                <i class="fas fa-home w-5 text-blue-600"></i> Home
            </a>
            <a href="#destinations" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                <i class="fas fa-hotel w-5 text-blue-600"></i> Hotels
            </a>
            <a href="#about" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                <i class="fas fa-info-circle w-5 text-blue-600"></i> About
            </a>
            <a href="#contact" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                <i class="fas fa-phone-alt w-5 text-blue-600"></i> Contact
            </a>
            <a href="#faq" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                <i class="fas fa-question-circle w-5 text-blue-600"></i> FAQ
            </a>
            <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ route('track-booking') }}" class="block text-center text-xs font-bold text-slate-700 bg-slate-100 py-2.5 rounded-xl">
                    <i class="fas fa-search text-blue-500 mr-1"></i> Track Booking
                </a>
                <a href="{{ route('register-hotel') }}" class="block text-center text-xs font-bold text-slate-700 bg-slate-100 py-2.5 rounded-xl">
                    List Your Property
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Search Section -->
<<<<<<< HEAD
    <section class="relative pt-20 pb-32 bg-primary  overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
                <path fill="#ffffff" d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z"></path>
            </svg>
=======
    <section class="relative pt-20 pb-28 bg-gradient-to-br from-blue-600 via-blue-600 to-indigo-700 overflow-hidden">
        <!-- Floating Bokeh & Glowing Background Effect -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-10 left-1/4 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>
            
            {{-- Bokeh circles --}}
            <div class="absolute top-12 left-1/3 w-32 h-32 bg-white/15 rounded-full blur-md"></div>
            <div class="absolute bottom-8 left-1/4 w-20 h-20 bg-white/10 rounded-full blur-sm"></div>
            <div class="absolute top-16 right-1/3 w-36 h-36 bg-white/10 rounded-full blur-md"></div>
            <div class="absolute bottom-6 right-1/4 w-24 h-24 bg-white/15 rounded-full blur-md"></div>
>>>>>>> 1b36bc314dcc9c8ee03f104fe115664d808bfc3f
        </div>
        
        <div class="max-w-6xl mx-auto px-4 relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-white mb-4">
                Find Your Perfect Stay
            </h1>
            <p class="text-blue-100 text-lg mb-10 max-w-2xl mx-auto font-medium">
                Search through premium hotels, luxury resorts, and cozy homestays at the best prices.
            </p>

<<<<<<< HEAD
            <!-- Search Bar Component -->
            <div class="bg-white p-3 sm:p-4 rounded-3xl shadow-2xl mx-auto max-w-4xl text-left transform translate-y-10">
                <form action="#destinations" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="col-span-1 md:col-span-1 bg-slate-50 border border-slate-200 rounded-2xl p-3 relative group">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Destination</label>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-secondary"></i>
                            <input type="text" placeholder="City or Hotel Name" class="w-full bg-transparent border-none outline-none text-sm font-semibold text-slate-800 placeholder-slate-400">
                        </div>
                    </div>
                    
                    <div class="col-span-1 md:col-span-1 bg-slate-50 border border-slate-200 rounded-2xl p-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Check-In</label>
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar-alt  text-secondary"></i>
                            <input type="date" class="w-full bg-transparent border-none outline-none text-sm font-semibold text-slate-800">
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-1 bg-slate-50 border border-slate-200 rounded-2xl p-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Check-Out</label>
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar-alt  text-secondary"></i>
                            <input type="date" class="w-full bg-transparent border-none outline-none text-sm font-semibold text-slate-800">
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-1 flex">
                        <button type="submit" class="w-full h-full bg-accent hover:bg-dark text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-colors text-lg shadow-lg">
                            Search <i class="fas fa-search"></i>
=======
            <!-- Pill Search Bar Component (Exact match to screenshot) -->
            <div class="bg-white p-2 sm:p-3 rounded-[32px] sm:rounded-full shadow-2xl shadow-blue-950/30 mx-auto max-w-5xl text-left border border-white/50">
                <form action="{{ route('booking-engine') }}" method="get" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-2 items-center">
                    
                    {{-- Destination --}}
                    <div class="md:col-span-4 bg-slate-50/90 hover:bg-slate-100/90 border border-slate-100 rounded-2xl sm:rounded-full px-5 py-3.5 transition-all focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:bg-white focus-within:border-blue-300">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">DESTINATION</label>
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-map-marker-alt text-blue-500 text-sm"></i>
                            <input type="text" name="search" placeholder="City or Hotel Name" class="w-full bg-transparent border-none outline-none text-sm font-bold text-slate-800 placeholder-slate-400 p-0 focus:ring-0">
                        </div>
                    </div>
                    
                    {{-- Check-In --}}
                    <div class="md:col-span-3 bg-slate-50/90 hover:bg-slate-100/90 border border-slate-100 rounded-2xl sm:rounded-full px-5 py-3.5 transition-all focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:bg-white focus-within:border-blue-300">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">CHECK-IN</label>
                        <div class="flex items-center gap-2.5">
                            <i class="far fa-calendar-alt text-blue-500 text-sm"></i>
                            <input type="date" name="checkin" class="w-full bg-transparent border-none outline-none text-sm font-bold text-slate-800 p-0 focus:ring-0">
                        </div>
                    </div>

                    {{-- Check-Out --}}
                    <div class="md:col-span-3 bg-slate-50/90 hover:bg-slate-100/90 border border-slate-100 rounded-2xl sm:rounded-full px-5 py-3.5 transition-all focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:bg-white focus-within:border-blue-300">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">CHECK-OUT</label>
                        <div class="flex items-center gap-2.5">
                            <i class="far fa-calendar-alt text-blue-500 text-sm"></i>
                            <input type="date" name="checkout" class="w-full bg-transparent border-none outline-none text-sm font-bold text-slate-800 p-0 focus:ring-0">
                        </div>
                    </div>

                    {{-- Search Button --}}
                    <div class="md:col-span-2 h-full flex">
                        <button type="submit" class="w-full py-4 px-6 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl sm:rounded-full flex items-center justify-center gap-2.5 transition-all text-base shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-blue-600/40 cursor-pointer active:scale-95">
                            <span>Search</span>
                            <i class="fas fa-search text-sm"></i>
>>>>>>> 1b36bc314dcc9c8ee03f104fe115664d808bfc3f
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Popular Destinations / Hotels -->
    <section id="destinations" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Trending Hotels & Resorts</h2>
                <p class="text-slate-500 mt-2">Book the most popular properties across our network</p>
            </div>

            @if($hotels->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center max-w-lg mx-auto border border-slate-200 shadow-sm">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-4">
                        <i class="fas fa-hotel text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">No properties found</h3>
                    <p class="text-sm text-slate-500 mt-2">There are no hotels currently listed on the platform.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($hotels as $hotel)
                        @php
                            $primaryImg = $hotel->images->where('is_primary', true)->first() ?: $hotel->images->first();
                            $imgUrl = $primaryImg 
                                ? asset('storage/' . $primaryImg->image_path) 
                                : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80';
                            
                            $minPrice = $hotel->rooms->min('price');
                            $priceFormatted = $minPrice ? '₹' . number_format($minPrice) : '₹2,500';
                            $ratingStars = intval($hotel->category ?? 5);
                        @endphp
                        
                        <a href="{{ $hotel->url }}" class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-100 transition-all duration-300 flex flex-col group cursor-pointer block">
                            {{-- Image --}}
                            <div class="aspect-[4/3] w-full overflow-hidden relative bg-slate-200">
                                <img src="{{ $imgUrl }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                
                                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-lg flex items-center gap-1 text-xs font-bold text-slate-800 shadow-sm">
                                    <i class="fas fa-star text-amber-500"></i>
                                    {{ number_format($ratingStars, 1) }}
                                </div>
                            </div>

                            {{-- Details --}}
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                        {{ $hotel->name }}
                                    </h3>
                                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ $hotel->city ?: 'Unknown City' }}, {{ $hotel->state ?: 'Unknown State' }}</span>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Starting From</p>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-xl font-black text-slate-900">{{ $priceFormatted }}</span>
                                            <span class="text-xs text-slate-500">/ night</span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center justify-center px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white text-xs font-bold rounded-xl transition-colors">
                                        View Details
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Value Proposition -->
    <section class="py-16 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="p-6">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Best Price Guarantee</h3>
                    <p class="text-sm text-slate-500">We offer the most competitive rates and exclusive discounts for our members.</p>
                </div>
                <div class="p-6">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Safe & Secure</h3>
                    <p class="text-sm text-slate-500">Your payments and personal data are protected with bank-level encryption.</p>
                </div>
                <div class="p-6">
                    <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">24/7 Support</h3>
                    <p class="text-sm text-slate-500">Our dedicated customer service team is always here to help you anywhere.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white border-t border-slate-100 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full mb-4">
                        <i class="fas fa-info-circle"></i> About MERAHKIE
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                        Your Trusted Partner for Premier Stays & Hospitality
                    </h2>
                    <p class="text-slate-600 leading-relaxed mb-6">
                        MERAHKIE Bookings is a state-of-the-art hotel reservation and management system built to connect travelers with hand-picked hotels, luxury resorts, and boutique stays. We focus on providing seamless, transparent, and instant booking experiences with zero hidden charges.
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-6 border-t border-slate-100">
                        <div>
                            <span class="block text-3xl font-black text-blue-600">500+</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Partner Hotels</span>
                        </div>
                        <div>
                            <span class="block text-3xl font-black text-blue-600">50k+</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Happy Guests</span>
                        </div>
                        <div>
                            <span class="block text-3xl font-black text-blue-600">100%</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Instant Booking</span>
                        </div>
                        <div>
                            <span class="block text-3xl font-black text-blue-600">24/7</span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Live Assistance</span>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-200">
                        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1000&q=80" alt="Luxury Hotel" class="w-full h-auto object-cover">
                    </div>
                    <div class="absolute -bottom-6 -left-6 bg-blue-600 text-white p-6 rounded-3xl shadow-xl hidden sm:block border border-white/20">
                        <i class="fas fa-award text-3xl mb-2 text-amber-300"></i>
                        <h4 class="font-bold text-sm">Award-Winning Platform</h4>
                        <p class="text-xs text-blue-100">Top-rated booking engine 2026</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-slate-50 border-t border-slate-200 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full mb-3">
                    <i class="fas fa-phone-alt"></i> Get In Touch
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">We're Here to Help</h2>
                <p class="text-slate-500 mt-2">Have questions about your reservation or interested in listing your property? Reach out to us anytime.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Info Cards -->
                <div class="space-y-4">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shrink-0 text-xl font-bold">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Phone & WhatsApp</h4>
                            <p class="text-xs text-slate-500 mt-1">+91 98765 43210 / +1 (800) 123-4567</p>
                            <span class="text-[10px] text-emerald-600 font-bold uppercase mt-1 block">Mon-Sun 24/7 Available</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shrink-0 text-xl font-bold">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Email Support</h4>
                            <p class="text-xs text-slate-500 mt-1">support@merahkie.com</p>
                            <span class="text-[10px] text-blue-600 font-bold uppercase mt-1 block">Fast response within 2 hours</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shrink-0 text-xl font-bold">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Headquarters</h4>
                            <p class="text-xs text-slate-500 mt-1">123 Hospitality Way, Suite 500, Tech City</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-slate-200 shadow-sm" x-data="{ submitted: false }">
                    <template x-if="submitted">
                        <div class="bg-emerald-50 border border-emerald-200 p-8 rounded-2xl text-center">
                            <i class="fas fa-check-circle text-5xl text-emerald-500 mb-4"></i>
                            <h3 class="text-xl font-bold text-emerald-900">Message Sent Successfully!</h3>
                            <p class="text-xs text-emerald-700 mt-2 max-w-md mx-auto">Thank you for reaching out. Our support team has received your inquiry and will contact you shortly.</p>
                            <button @click="submitted = false" class="mt-6 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all">Send Another Message</button>
                        </div>
                    </template>
                    
                    <template x-if="!submitted">
                        <form @submit.prevent="submitted = true" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Your Name *</label>
                                    <input type="text" required placeholder="John Doe" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Email Address *</label>
                                    <input type="email" required placeholder="john@example.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Subject</label>
                                <input type="text" placeholder="Booking Inquiry / Feedback" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Your Message *</label>
                                <textarea required rows="4" placeholder="How can we help you?" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:border-blue-500 focus:bg-white"></textarea>
                            </div>
                            <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">
                                Send Message <i class="fas fa-paper-plane ml-2"></i>
                            </button>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-white border-t border-slate-100 scroll-mt-20" x-data="{ openFaq: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full mb-3">
                    <i class="fas fa-question-circle"></i> FAQ
                </div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Frequently Asked Questions</h2>
                <p class="text-slate-500 mt-2">Find answers to common queries regarding bookings, cancellations, and support.</p>
            </div>

            <div class="space-y-4">
                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all shadow-sm">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full bg-slate-50 hover:bg-slate-100 px-6 py-4 text-left font-bold text-slate-800 flex justify-between items-center cursor-pointer text-sm">
                        <span><i class="fas fa-search text-blue-500 mr-2"></i> How do I search and book a room on MERAHKIE?</span>
                        <i class="fas text-blue-600" :class="openFaq === 1 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openFaq === 1" x-cloak class="p-6 bg-white text-xs text-slate-600 border-t border-slate-100 leading-relaxed">
                        You can search for hotels by entering your destination or hotel name in the search bar on our home page, choosing check-in and check-out dates, and clicking 'Search'. Select your preferred room type and click 'Book Now' to complete your reservation instantly.
                    </div>
                </div>

                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all shadow-sm">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full bg-slate-50 hover:bg-slate-100 px-6 py-4 text-left font-bold text-slate-800 flex justify-between items-center cursor-pointer text-sm">
                        <span><i class="fas fa-ticket-alt text-blue-500 mr-2"></i> How can I track my existing booking status?</span>
                        <i class="fas text-blue-600" :class="openFaq === 2 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openFaq === 2" x-cloak class="p-6 bg-white text-xs text-slate-600 border-t border-slate-100 leading-relaxed">
                        Click on the 'Track Booking' button in the top navigation bar, enter your unique PNR number and registered email address. You will be able to view details, confirmation status, and download your official booking confirmation slip.
                    </div>
                </div>

                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all shadow-sm">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full bg-slate-50 hover:bg-slate-100 px-6 py-4 text-left font-bold text-slate-800 flex justify-between items-center cursor-pointer text-sm">
                        <span><i class="fas fa-undo text-blue-500 mr-2"></i> What is the cancellation policy?</span>
                        <i class="fas text-blue-600" :class="openFaq === 3 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openFaq === 3" x-cloak class="p-6 bg-white text-xs text-slate-600 border-t border-slate-100 leading-relaxed">
                        Cancellation terms depend on the specific hotel policy. Most partner properties allow free cancellation up to 24-48 hours before check-in. Detailed policy rules are displayed during booking and on your confirmation slip.
                    </div>
                </div>

                <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all shadow-sm">
                    <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full bg-slate-50 hover:bg-slate-100 px-6 py-4 text-left font-bold text-slate-800 flex justify-between items-center cursor-pointer text-sm">
                        <span><i class="fas fa-building text-blue-500 mr-2"></i> How can hotel owners list their property on MERAHKIE?</span>
                        <i class="fas text-blue-600" :class="openFaq === 4 ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="openFaq === 4" x-cloak class="p-6 bg-white text-xs text-slate-600 border-t border-slate-100 leading-relaxed">
                        Hotel managers can click 'List Your Property' in the header to register their hotel. Our team verifies property details and activates the account, allowing you to manage rooms, reservations, pricing, and invoices effortlessly.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 border-b border-slate-800 pb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <!-- <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-paper-plane text-white text-sm"></i>
                        </div> -->
                        <!-- <span class="text-xl font-black text-white">MERAHKIE</span> -->
                          <img src="{{ asset('images/whitelogo.png') }}"
                    alt="Merahkie Logo"
                    class="h-12 w-auto">
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
                        <li><a href="#destinations" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-hotel text-blue-500 text-xs"></i> Hotels</a></li>
                        <li><a href="#about" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-info-circle text-blue-500 text-xs"></i> About Us</a></li>
                        <li><a href="#contact" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-phone-alt text-blue-500 text-xs"></i> Contact Us</a></li>
                        <li><a href="#faq" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-question-circle text-blue-500 text-xs"></i> FAQ</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">Support & Services</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('track-booking') }}" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-search text-blue-500 text-xs"></i> Track Booking</a></li>
                        <li><a href="{{ route('register-hotel') }}" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-building text-blue-500 text-xs"></i> List Property</a></li>
                        <li><a href="#contact" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-headset text-blue-500 text-xs"></i> Help Center</a></li>
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
                <p>&copy; {{ date('Y') }} Lodgiko Bookings. All rights reserved.</p>
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

