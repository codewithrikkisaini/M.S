<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MERAHKIE SILK ROAD GROUP - Hotel PMS & Management Platform</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        serif: ['Cinzel', 'serif'],
                    },
                    colors: {
                        brand: {
                            gold: '#D4AF37',
                            'gold-light': '#F3E5AB',
                            blue: '#4F46E5',
                            accent: '#EC4899',
                            dark: '#070714',
                            card: '#0F0E26',
                            border: '#1E1B4B'
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #070714;
            color: #F1F5F9;
        }

        .gradient-heading {
            background: linear-gradient(135deg, #60A5FA 0%, #A78BFA 45%, #F472B6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gold-glow {
            box-shadow: 0 0 25px rgba(212, 175, 55, 0.15);
        }
        
        .hero-glow {
            background: radial-gradient(circle at 50% 30%, rgba(99, 102, 241, 0.18) 0%, rgba(168, 85, 247, 0.08) 35%, rgba(7, 7, 20, 0) 70%);
        }

        .glass-card {
            background: rgba(15, 14, 38, 0.65);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-card-hover:hover {
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 10px 30px -10px rgba(99, 102, 241, 0.3);
        }

        .pill-bar {
            background: rgba(15, 14, 38, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="antialiased overflow-x-hidden bg-[#070714] text-slate-100 hero-glow">
    <!-- Navbar -->
    <header class="border-b border-slate-800/60 bg-[#070714]/80 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-gradient-to-br from-amber-400 via-amber-600 to-amber-800 rounded-xl flex items-center justify-center p-0.5 shadow-lg shadow-amber-500/10">
                    <div class="w-full h-full bg-[#070714] rounded-[10px] flex items-center justify-center">
                        <i class="fas fa-crown text-amber-400 text-lg"></i>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-serif font-black tracking-widest text-amber-300 uppercase leading-none">MERAHKIE</span>
                    <span class="text-[10px] font-sans font-bold tracking-[0.25em] text-slate-400 uppercase mt-1">SILK ROAD GROUP</span>
                </div>
            </div>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                <a href="#features" class="hover:text-amber-400 transition-colors">Features</a>
                <div class="relative group cursor-pointer flex items-center gap-1 hover:text-amber-400 transition-colors">
                    <span>Solutions</span>
                    <i class="fas fa-chevron-down text-[10px] text-slate-400 group-hover:text-amber-400"></i>
                </div>
                <a href="#pricing" class="hover:text-amber-400 transition-colors">Pricing</a>
                <div class="relative group cursor-pointer flex items-center gap-1 hover:text-amber-400 transition-colors">
                    <span>Resources</span>
                    <i class="fas fa-chevron-down text-[10px] text-slate-400 group-hover:text-amber-400"></i>
                </div>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-300 hover:text-white transition-colors px-3 py-2">
                    Log In
                </a>
                <a href="{{ route('register-hotel') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all hover:scale-105">
                    Register Hotel <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-20 pb-16 relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 text-center relative z-10">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-semibold pill-bar text-slate-200 mb-8 border border-slate-700/50">
                <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                <span>Version 4.0 Live</span>
            </div>

            <!-- Main Heading -->
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-white leading-[1.15]">
                One Platform <br />
                <span class="gradient-heading">to Manage & Grow</span> <br />
                your Hotel Business.
            </h1>

            <!-- Subheading -->
            <p class="mt-8 text-base sm:text-lg text-slate-400 max-w-3xl mx-auto leading-relaxed">
                Merahkie PMS brings reservations, room inventory, front desk operations, housekeeping, payments, guest management, direct bookings and OTA connectivity into one powerful cloud platform.
            </p>
            
            <!-- Hero Action Buttons -->
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="{{ route('register-hotel') }}" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-sm rounded-xl shadow-xl shadow-indigo-600/30 transition-all flex items-center gap-2 hover:scale-105">
                    <i class="fas fa-hotel"></i> Register Your Hotel <i class="fas fa-arrow-right text-xs ml-1"></i>
                </a>
                <a href="{{ route('booking-engine') }}" class="px-8 py-4 bg-slate-900/80 hover:bg-slate-800 text-slate-200 font-extrabold text-sm rounded-xl border border-slate-700/80 transition-all flex items-center gap-2.5 backdrop-blur-md">
                    <i class="fas fa-play-circle text-indigo-400 text-base"></i> Request a Demo
                </a>
            </div>

            <!-- Feature Pills Bar -->
            <div class="mt-16 max-w-4xl mx-auto">
                <div class="pill-bar rounded-2xl py-3.5 px-6 flex flex-wrap items-center justify-around gap-4 text-xs font-semibold text-slate-300">
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-cloud text-indigo-400"></i>
                        <span>Cloud Based</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-building text-indigo-400"></i>
                        <span>Multi-Property</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="far fa-calendar-check text-indigo-400"></i>
                        <span>Direct Booking</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-network-wired text-indigo-400"></i>
                        <span>OTA Ready</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <i class="fas fa-shield-halved text-indigo-400"></i>
                        <span>Secure & Reliable</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sub Header Section -->
    <section class="py-12 border-t border-slate-800/50 bg-[#0A091E]/50">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="text-xs font-extrabold tracking-[0.3em] text-indigo-400 uppercase block mb-2">POWERING MODERN HOSPITALITY</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">Everything Your Hotel Needs. One Connect Platform.</h2>
        </div>
    </section>

    <!-- Registered Hotels Section -->
    <section class="py-20 bg-[#070714]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">
                    <i class="fas fa-gem text-[10px]"></i> Explore Properties
                </span>
                <h2 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">Our Registered Partner Hotels</h2>
                <p class="text-slate-400 text-sm mt-3 max-w-2xl mx-auto">Discover top-rated destinations, view live room inventory, and make instant secure reservations directly through our platform.</p>
            </div>

            @if($hotels->isEmpty())
                <div class="glass-card rounded-3xl p-12 text-center max-w-lg mx-auto border border-slate-800">
                    <div class="w-14 h-14 bg-indigo-900/30 rounded-full flex items-center justify-center border border-indigo-500/30 text-indigo-400 mx-auto mb-4">
                        <i class="fas fa-hotel text-xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-white">No hotels registered yet</h3>
                    <p class="text-xs text-slate-400 mt-2">Become our first partner and list your property on Merahkie PMS today.</p>
                    <a href="{{ route('register-hotel') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl mt-6 transition-all shadow-lg shadow-indigo-600/30">
                        Register Your Hotel <i class="fas fa-plus"></i>
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($hotels as $hotel)
                        @php
                            $imgUrl = $hotel->primary_image_url ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80';
                            $minPrice = $hotel->rooms->min('price');
                            $priceFormatted = $minPrice ? '₹' . number_format($minPrice) : '₹2,500';
                            $ratingStars = intval($hotel->category ?? 5);
                        @endphp
                        <div class="glass-card rounded-3xl overflow-hidden glass-card-hover transition-all duration-300 flex flex-col group">
                            {{-- Image Container --}}
                            <div class="aspect-video w-full overflow-hidden relative bg-slate-900">
                                <img src="{{ $imgUrl }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                
                                {{-- Rating overlay --}}
                                <div class="absolute top-4 right-4 bg-slate-950/80 backdrop-blur-md border border-slate-700/60 px-3 py-1 rounded-xl flex items-center gap-1.5 text-xs font-bold text-amber-400 shadow-lg">
                                    <i class="fas fa-star text-[10px]"></i>
                                    {{ number_format($ratingStars, 1) }}
                                </div>

                                {{-- Property Type overlay --}}
                                <div class="absolute bottom-4 left-4 bg-indigo-600/90 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg shadow-lg">
                                    {{ $hotel->property_type ?: 'Luxury Hotel' }}
                                </div>
                            </div>

                            {{-- Card Info --}}
                            <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <h3 class="text-lg font-bold text-white group-hover:text-indigo-400 transition-colors duration-200">
                                        {{ $hotel->name }}
                                    </h3>
                                    
                                    <div class="flex items-center gap-2 text-xs text-slate-400">
                                        <i class="fas fa-location-dot text-indigo-400"></i>
                                        <span>{{ $hotel->city ?: 'New Delhi' }}, {{ $hotel->state ?: 'India' }}</span>
                                    </div>

                                    <p class="text-xs text-slate-400 leading-relaxed line-clamp-2 pt-1">
                                        Experience modern luxury, premium hospitality, and seamless guest service at {{ $hotel->name }}.
                                    </p>
                                </div>

                                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider block">Starting From</span>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-lg font-extrabold text-white">{{ $priceFormatted }}</span>
                                            <span class="text-[10px] text-slate-400">/ night</span>
                                        </div>
                                    </div>
                                    
                                    <a href="{{ route('booking-engine', ['hotel_id' => $hotel->id]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-md transition-all gap-1.5">
                                        Book Now <i class="fas fa-chevron-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 border-t border-slate-800/60 bg-[#0B0A21]/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-xs font-bold tracking-widest text-indigo-400 uppercase">Enterprise Solutions</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2">Full-Suite Integrations & Platform Features</h2>
                <p class="text-sm text-slate-400 mt-2">Everything you need to automate hospitality administrative operations</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Direct Booking Engine -->
                <div class="glass-card rounded-2xl p-7 glass-card-hover transition-all">
                    <div class="w-12 h-12 bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 rounded-xl flex items-center justify-center mb-5">
                        <i class="fas fa-calendar-check text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Direct Booking Engine</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Public-facing reservation portal for guest bookings. Simulates room selection, availability checks, and automatic confirmations.</p>
                </div>

                <!-- OTA Channel Manager -->
                <div class="glass-card rounded-2xl p-7 glass-card-hover transition-all">
                    <div class="w-12 h-12 bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 rounded-xl flex items-center justify-center mb-5">
                        <i class="fas fa-sync text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Channel Manager</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Map inventory and push rates directly to OTAs like Booking.com, Expedia, and Airbnb to prevent double-bookings.</p>
                </div>

                <!-- Stripe Checkout Gateway -->
                <div class="glass-card rounded-2xl p-7 glass-card-hover transition-all">
                    <div class="w-12 h-12 bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 rounded-xl flex items-center justify-center mb-5">
                        <i class="fab fa-stripe text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Stripe Payment Gateway</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Secure credit card processing. Auto-generate PDF invoices and collect guest payments instantly via a Stripe checkout overlay.</p>
                </div>

                <!-- Developer API Keys -->
                <div class="glass-card rounded-2xl p-7 glass-card-hover transition-all">
                    <div class="w-12 h-12 bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 rounded-xl flex items-center justify-center mb-5">
                        <i class="fas fa-key text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Developer API Management</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Create and manage secure API keys starting with <code>pms_live_</code> to integrate keyless locks, housekeeping apps, or POS software.</p>
                </div>

                <!-- Activity & Audit Logs -->
                <div class="glass-card rounded-2xl p-7 glass-card-hover transition-all">
                    <div class="w-12 h-12 bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 rounded-xl flex items-center justify-center mb-5">
                        <i class="fas fa-shield-halved text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Tamper-proof Audit Logs</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Maintain security compliance by tracking all critical administrator actions, login attempts, and database updates in a secure log.</p>
                </div>

                <!-- Automated Notifications -->
                <div class="glass-card rounded-2xl p-7 glass-card-hover transition-all">
                    <div class="w-12 h-12 bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 rounded-xl flex items-center justify-center mb-5">
                        <i class="far fa-bell text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Email & WhatsApp Templates</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Customize transactional mail and text notifications. Use merge fields to insert guest name, room details, and reservation prices.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 border-t border-slate-800/60 bg-[#070714]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-xs font-bold tracking-widest text-amber-400 uppercase">Flexible Pricing</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-2">SaaS Subscription Plans</h2>
                <p class="text-sm text-slate-400 mt-2">Select a plan that suits your scale and budget</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Trial Plan -->
                <div class="glass-card rounded-2xl p-6 flex flex-col justify-between border border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-indigo-400">Trial Plan</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-3xl font-extrabold text-white">$0.00</span>
                            <span class="text-xs text-slate-400 ml-1">/ 14 Days</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">Explore the system with full premium features access.</p>
                        
                        <ul class="mt-6 space-y-2.5 text-xs text-slate-300">
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Up to 5 Rooms</li>
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Up to 2 Users</li>
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Custom Templates</li>
                        </ul>
                    </div>
                    <a href="{{ route('register-hotel') }}" class="w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs py-3 px-4 rounded-xl mt-8 transition-colors block">
                        Get Started
                    </a>
                </div>

                <!-- Monthly Pro -->
                <div class="glass-card rounded-2xl p-6 flex flex-col justify-between relative border-2 border-indigo-500 shadow-xl shadow-indigo-600/10">
                    <div>
                        <span class="absolute -top-3 right-4 bg-indigo-600 text-white text-[9px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full shadow-md">Popular</span>
                        <h3 class="text-base font-bold text-indigo-400">Monthly Pro</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-3xl font-extrabold text-white">$29.00</span>
                            <span class="text-xs text-slate-400 ml-1">/ month</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">Perfect for growing boutique properties.</p>
                        
                        <ul class="mt-6 space-y-2.5 text-xs text-slate-300">
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Up to 25 Rooms</li>
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Up to 10 Users</li>
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Stripe Integration</li>
                        </ul>
                    </div>
                    <a href="{{ route('register-hotel') }}" class="w-full text-center bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs py-3 px-4 rounded-xl mt-8 transition-colors block shadow-lg shadow-indigo-600/30">
                        Subscribe Now
                    </a>
                </div>

                <!-- Yearly Premium -->
                <div class="glass-card rounded-2xl p-6 flex flex-col justify-between border border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-indigo-400">Yearly Premium</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-3xl font-extrabold text-white">$249.00</span>
                            <span class="text-xs text-slate-400 ml-1">/ year</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">Save with an annual subscription.</p>
                        
                        <ul class="mt-6 space-y-2.5 text-xs text-slate-300">
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Up to 100 Rooms</li>
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Up to 30 Users</li>
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Priority Support</li>
                        </ul>
                    </div>
                    <a href="{{ route('register-hotel') }}" class="w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs py-3 px-4 rounded-xl mt-8 transition-colors block">
                        Subscribe Now
                    </a>
                </div>

                <!-- Lifetime Enterprise -->
                <div class="glass-card rounded-2xl p-6 flex flex-col justify-between border border-slate-800">
                    <div>
                        <h3 class="text-base font-bold text-indigo-400">Lifetime Enterprise</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-3xl font-extrabold text-white">$999.00</span>
                            <span class="text-xs text-slate-400 ml-1">/ one-time</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">Unlimited rooms and lifetime updates.</p>
                        
                        <ul class="mt-6 space-y-2.5 text-xs text-slate-300">
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Unlimited Rooms</li>
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> Unlimited Users</li>
                            <li><i class="fas fa-check text-emerald-400 mr-2"></i> API Access Keys</li>
                        </ul>
                    </div>
                    <a href="{{ route('register-hotel') }}" class="w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs py-3 px-4 rounded-xl mt-8 transition-colors block">
                        Get Enterprise
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 py-12 bg-[#05050F]">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-400 space-y-4">
            <div class="flex items-center justify-center gap-2">
                <span class="font-serif font-bold tracking-widest text-amber-400 uppercase">MERAHKIE SILK ROAD GROUP</span>
            </div>
            <p>© {{ date('Y') }} Merahkie Silk Road Group PMS Platform. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

