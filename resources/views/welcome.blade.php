<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MERAHKIE - Book Hotels, Resorts & Homestays</title>
    
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
                        secondary: '#1E40AF',
                    }
                }
            }
        }
    </script>
</head>
<body class="antialiased bg-slate-50 text-slate-800">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
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
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="/" class="hover:text-blue-600 transition-colors">Home</a>
                <a href="#destinations" class="hover:text-blue-600 transition-colors">Destinations</a>
                <a href="{{ route('track-booking') }}" class="hover:text-blue-600 transition-colors">Track Booking</a>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="{{ route('register-hotel') }}" class="hidden sm:inline-flex text-xs font-bold text-slate-500 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 px-4 py-2.5 rounded-xl transition-all">
                    List Your Property
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
                    <i class="far fa-user-circle mr-2"></i> Log In
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Search Section -->
    <section class="relative pt-20 pb-32 bg-blue-700 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">
                <path fill="#ffffff" d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z"></path>
            </svg>
        </div>
        
        <div class="max-w-5xl mx-auto px-4 relative z-10 text-center">
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-white mb-4">
                Find Your Perfect Stay
            </h1>
            <p class="text-blue-100 text-lg mb-10 max-w-2xl mx-auto">
                Search through premium hotels, luxury resorts, and cozy homestays at the best prices.
            </p>

            <!-- Search Bar Component -->
            <div class="bg-white p-3 sm:p-4 rounded-3xl shadow-2xl mx-auto max-w-4xl text-left transform translate-y-10">
                <form action="#destinations" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="col-span-1 md:col-span-1 bg-slate-50 border border-slate-200 rounded-2xl p-3 relative group">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Destination</label>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-blue-500"></i>
                            <input type="text" placeholder="City or Hotel Name" class="w-full bg-transparent border-none outline-none text-sm font-semibold text-slate-800 placeholder-slate-400">
                        </div>
                    </div>
                    
                    <div class="col-span-1 md:col-span-1 bg-slate-50 border border-slate-200 rounded-2xl p-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Check-In</label>
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar-alt text-blue-500"></i>
                            <input type="date" class="w-full bg-transparent border-none outline-none text-sm font-semibold text-slate-800">
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-1 bg-slate-50 border border-slate-200 rounded-2xl p-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Check-Out</label>
                        <div class="flex items-center gap-2">
                            <i class="far fa-calendar-alt text-blue-500"></i>
                            <input type="date" class="w-full bg-transparent border-none outline-none text-sm font-semibold text-slate-800">
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-1 flex">
                        <button type="submit" class="w-full h-full bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-colors text-lg shadow-lg">
                            Search <i class="fas fa-search"></i>
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
                        
                        <a href="{{ route('hotel.show', $hotel->id) }}" class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-100 transition-all duration-300 flex flex-col group cursor-pointer block">
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
                    <h4 class="text-white font-bold mb-4">Company</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-blue-400 transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Cancellation Policy</a></li>
                        <li><a href="{{ route('track-booking') }}" class="hover:text-blue-400 transition-colors">Track Booking</a></li>
                        <li><a href="#" class="hover:text-blue-400 transition-colors">Contact Us</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-bold mb-4">For Hotels</h4>
                    <p class="text-sm text-slate-400 mb-4">Grow your bookings by listing your property on our platform.</p>
                    <a href="{{ route('register-hotel') }}" class="inline-block px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold rounded-lg border border-slate-700 transition-colors">
                        Partner With Us
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

