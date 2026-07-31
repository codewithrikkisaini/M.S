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
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased bg-slate-50 text-slate-800" x-data="{ showModal: false, selectedRoom: null }">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-paper-plane text-white text-lg"></i>
                </div>
                <span class="font-extrabold text-xl tracking-tight text-slate-900">MERAHKIE</span>
            </a>

            <!-- Right Actions -->
            <div class="flex items-center gap-4">
                <a href="{{ route('booking-engine', ['hotel_id' => $hotel->id]) }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
                    Book Room
                </a>
            </div>
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
                <div class="md:col-span-2 aspect-[4/3] md:aspect-auto">
                    <img src="{{ asset('storage/' . $images[0]->image_path) }}" class="w-full h-full object-cover">
                </div>
                <div class="md:col-span-2 grid grid-cols-2 gap-4">
                    @foreach($images->slice(1, 4) as $img)
                        <div class="aspect-video bg-slate-100 overflow-hidden">
                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
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
                <section class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
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
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-6">Available Rooms</h2>
                    
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
                                    $bookUrl = route('booking-engine', ['hotel_id' => $hotel->id]) . '?room_type_id=' . $room->room_type_id . '&room_id=' . $room->id;
                                @endphp
                                <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm flex flex-col sm:flex-row gap-6 hover:shadow-md transition-all">
                                    <div class="w-full sm:w-1/3 aspect-video sm:aspect-auto rounded-xl bg-slate-100 overflow-hidden shrink-0">
                                        <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <div class="flex justify-between items-start">
                                                <h3 class="text-lg font-bold text-slate-900">{{ $roomTypeName }} (Room {{ $room->room_number }})</h3>
                                                <span class="text-xs font-bold px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg">Available</span>
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
                                                <a href="{{ $bookUrl }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 cursor-pointer">
                                                    <i class="fas fa-eye text-slate-500"></i> View Details
                                                </a>

                                                <a href="{{ $bookUrl }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-1.5 cursor-pointer">
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
            <!-- Modal Header / Image -->
            <div class="relative aspect-video bg-slate-100">
                <img :src="selectedRoom?.image" class="w-full h-full object-cover">
                <button @click="showModal = false" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-slate-900/60 text-white hover:bg-slate-900 flex items-center justify-center cursor-pointer transition-all border border-white/20 backdrop-blur-md">
                    <i class="fas fa-times"></i>
                </button>
                <div class="absolute bottom-4 left-4 bg-slate-900/80 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-lg border border-white/10">
                    <span x-text="selectedRoom?.name + ' (Room ' + selectedRoom?.number + ')'"></span>
                </div>
            </div>

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
                    <a :href="selectedRoom?.bookUrl" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-lg hover:shadow-xl transition-all cursor-pointer flex items-center gap-2">
                        <i class="fas fa-calendar-check text-xs"></i> Book This Room Now
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} Merahkie Bookings. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
