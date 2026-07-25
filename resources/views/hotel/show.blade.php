<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $hotel->name }} - Book Now on MERAHKIE</title>
    
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
</head>
<body class="antialiased bg-slate-50 text-slate-800">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-paper-plane text-white text-lg"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-black tracking-tight text-slate-900 leading-none">MERAHKIE</span>
                    <span class="text-[10px] font-bold tracking-widest text-blue-600 uppercase mt-1">Bookings</span>
                </div>
            </a>
            
            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
                    <i class="far fa-user-circle mr-2"></i> Log In
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-blue-600">Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-xs mx-2"></i>
                        <span class="text-slate-800 font-semibold">{{ $hotel->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Title & Basic Info -->
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded uppercase tracking-wide">
                        {{ $hotel->property_type ?? 'Hotel' }}
                    </span>
                    <div class="flex text-amber-400 text-sm">
                        @php $rating = intval($hotel->category ?? 5); @endphp
                        @for($i = 0; $i < $rating; $i++)
                            <i class="fas fa-star"></i>
                        @endfor
                    </div>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">{{ $hotel->name }}</h1>
                <p class="text-sm text-slate-500 mt-2 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                    {{ $hotel->address }}, {{ $hotel->city }}, {{ $hotel->state }}, {{ $hotel->country }}
                </p>
            </div>
            <div>
                <a href="{{ route('booking-engine', ['hotel_id' => $hotel->id]) }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white text-base font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all w-full md:w-auto">
                    Check Availability
                </a>
            </div>
        </div>

        <!-- Image Gallery (Masonry style) -->
        <div class="grid grid-cols-4 grid-rows-2 gap-3 h-[400px] md:h-[500px] mb-12 rounded-3xl overflow-hidden">
            @php
                $images = $hotel->images;
                $defaultImg = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80';
            @endphp
            
            {{-- Primary Image (takes up 2x2 grid) --}}
            <div class="col-span-4 md:col-span-2 row-span-2 relative group cursor-pointer">
                @if($images->isNotEmpty())
                    <img src="{{ asset('storage/' . $images->first()->image_path) }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <img src="{{ $defaultImg }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @endif
            </div>
            
            {{-- Other Images --}}
            @for($i = 1; $i <= 4; $i++)
                <div class="hidden md:block col-span-1 row-span-1 relative overflow-hidden group cursor-pointer">
                    @if($images->count() > $i)
                        <img src="{{ asset('storage/' . $images[$i]->image_path) }}" alt="Gallery image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80" alt="Gallery placeholder" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @endif
                    
                    {{-- Overlay for the 4th small image to show "More photos" --}}
                    @if($i == 4)
                        <div class="absolute inset-0 bg-slate-900/40 flex items-center justify-center hover:bg-slate-900/50 transition-colors">
                            <span class="text-white font-bold text-lg">+ View All</span>
                        </div>
                    @endif
                </div>
            @endfor
        </div>

        <!-- Details & Rooms Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Left Column: Description & Amenities -->
            <div class="lg:col-span-2 space-y-10">
                <!-- Description -->
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">About this property</h2>
                    <div class="text-slate-600 leading-relaxed text-sm space-y-4">
                        <p>Welcome to {{ $hotel->name }}, your premier choice for accommodation in {{ $hotel->city }}. We offer top-notch hospitality, ensuring your stay is comfortable and memorable. Whether you're here for business or leisure, our property provides the perfect blend of modern amenities and exceptional service.</p>
                        <p>Located conveniently at {{ $hotel->address }}, we are easily accessible from major transit points and popular tourist attractions.</p>
                    </div>
                </section>

                <!-- Amenities Placeholder -->
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Most popular facilities</h2>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2 text-slate-700 bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm font-semibold shadow-sm">
                            <i class="fas fa-wifi text-blue-500"></i> Free WiFi
                        </div>
                        <div class="flex items-center gap-2 text-slate-700 bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm font-semibold shadow-sm">
                            <i class="fas fa-parking text-blue-500"></i> Parking
                        </div>
                        <div class="flex items-center gap-2 text-slate-700 bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm font-semibold shadow-sm">
                            <i class="fas fa-utensils text-blue-500"></i> Restaurant
                        </div>
                        <div class="flex items-center gap-2 text-slate-700 bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm font-semibold shadow-sm">
                            <i class="fas fa-swimming-pool text-blue-500"></i> Swimming Pool
                        </div>
                        <div class="flex items-center gap-2 text-slate-700 bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm font-semibold shadow-sm">
                            <i class="fas fa-snowflake text-blue-500"></i> Air Conditioning
                        </div>
                    </div>
                </section>

                <!-- Available Rooms -->
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-6">Available Rooms</h2>
                    
                    <div class="space-y-4">
                        @if($hotel->rooms->isEmpty())
                            <div class="bg-blue-50 border border-blue-100 text-blue-800 p-6 rounded-2xl text-center">
                                No rooms currently listed for this property.
                            </div>
                        @else
                            @foreach($hotel->rooms as $room)
                                <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm flex flex-col sm:flex-row gap-6">
                                    <div class="w-full sm:w-1/3 aspect-video sm:aspect-auto rounded-xl bg-slate-100 overflow-hidden shrink-0">
                                        {{-- We assume room might have images, if not fallback --}}
                                        <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <div class="flex justify-between items-start">
                                                <h3 class="text-lg font-bold text-slate-900">{{ $room->name ?? 'Standard Room' }} ({{ $room->room_number }})</h3>
                                                <span class="text-xs font-bold px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg">Available</span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1">Bed Type: {{ ucfirst($room->bed_type ?? 'Standard') }} | Max Guests: {{ $room->capacity ?? 2 }}</p>
                                            
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded"><i class="fas fa-tv mr-1"></i> Flat TV</span>
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded"><i class="fas fa-shower mr-1"></i> Private Bath</span>
                                                <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded"><i class="fas fa-mug-hot mr-1"></i> Coffee Maker</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-6 flex items-end justify-between border-t border-slate-100 pt-4">
                                            <div>
                                                <span class="block text-2xl font-black text-slate-900">₹{{ number_format($room->price) }}</span>
                                                <span class="text-[10px] text-slate-500 uppercase font-bold tracking-wider">+ Taxes & Fees</span>
                                            </div>
                                            <a href="{{ route('booking-engine', ['hotel_id' => $hotel->id]) }}?room_id={{ $room->id }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
                                                Select Room
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </section>
            </div>

            <!-- Right Column: Booking Widget / Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xl sticky top-28">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Book Your Stay</h3>
                    <p class="text-sm text-slate-600 mb-6">Enter dates to see accurate prices and availability.</p>
                    
                    <form action="{{ route('booking-engine', ['hotel_id' => $hotel->id]) }}" method="GET" class="space-y-4">
                        <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                        
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Check-in</label>
                            <input type="date" name="checkin" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Check-out</label>
                            <input type="date" name="checkout" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Guests</label>
                            <select name="guests" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="1">1 Adult</option>
                                <option value="2" selected>2 Adults</option>
                                <option value="3">3 Adults</option>
                                <option value="4">4 Adults</option>
                            </select>
                        </div>
                        
                        <div class="pt-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-base py-3.5 rounded-xl shadow-lg transition-all">
                                Reserve Now
                            </button>
                        </div>
                        <p class="text-[10px] text-center text-slate-500 mt-3">You won't be charged yet</p>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} Merahkie Bookings. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
