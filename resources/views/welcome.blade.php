<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HotelFlow SaaS - Multi-Tenant Property Management Platform</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (Tailwind CSS v3 CDN for rich design styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }
        .glow-effect {
            position: relative;
        }
        .glow-effect::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, #6366f1, #a855f7);
            filter: blur(80px);
            opacity: 0.15;
            z-index: -1;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="antialiased overflow-x-hidden">
    <!-- Navbar -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i class="fas fa-hotel text-white text-sm"></i>
                </div>
                <span class="text-lg font-black tracking-tight text-white">HotelFlow <span class="text-indigo-400">SaaS</span></span>
            </div>
            
            <nav class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-300 hover:text-white transition-colors">
                    Log In
                </a>
                <a href="{{ route('register-hotel') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/25 transition-all">
                    Register Hotel <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                </a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-20 relative glow-effect">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-950/80 text-indigo-400 border border-indigo-900/50 mb-4">
                <i class="fas fa-sparkles text-[10px]"></i> Version 4.0 Live
            </span>
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white leading-tight">
                The Ultimate Multi-Tenant <br />
                <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">Property Management System</span>
            </h1>
            <p class="mt-6 text-base sm:text-lg text-slate-400 max-w-3xl mx-auto">
                Scale your hotel network effortlessly with a secure multi-tenant architecture. Built-in booking engine, OTA sync, Stripe invoicing, and developer API keys.
            </p>
            
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="{{ route('register-hotel') }}" class="px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-sm rounded-xl shadow-xl shadow-indigo-500/20 transition-all flex items-center gap-2">
                    Start Free Trial <i class="fas fa-magic"></i>
                </a>
                <a href="{{ route('booking-engine') }}" class="px-8 py-3.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-extrabold text-sm rounded-xl transition-all border border-slate-700/50">
                    Live Booking Demo
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 border-t border-slate-800/60 bg-slate-900/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-black text-white">Full-Suite Integrations & Enterprise Features</h2>
                <p class="text-sm text-slate-400 mt-2">Everything you need to automate hotel administrative tasks</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Direct Booking Engine -->
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 hover:border-slate-700/80 transition-all">
                    <div class="w-10 h-10 bg-indigo-950/60 border border-indigo-900/40 text-indigo-400 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-check text-base"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Direct Booking Engine</h3>
                    <p class="text-xs text-slate-455 leading-relaxed">Public-facing reservation portal for guest bookings. Simulates room selection, availability checks, and automatic confirmations.</p>
                </div>

                <!-- OTA Channel Manager -->
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 hover:border-slate-700/80 transition-all">
                    <div class="w-10 h-10 bg-indigo-950/60 border border-indigo-900/40 text-indigo-400 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-sync text-base"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Channel Manager</h3>
                    <p class="text-xs text-slate-455 leading-relaxed">Map inventory and push rates directly to OTAs like Booking.com, Expedia, and Airbnb to prevent double-bookings.</p>
                </div>

                <!-- Stripe Checkout Gateway -->
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 hover:border-slate-700/80 transition-all">
                    <div class="w-10 h-10 bg-indigo-950/60 border border-indigo-900/40 text-indigo-400 rounded-xl flex items-center justify-center mb-4">
                        <i class="fab fa-stripe text-base"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Stripe Payment Gateway</h3>
                    <p class="text-xs text-slate-455 leading-relaxed">Secure credit card processing. Auto-generate PDF invoices and collect guest payments instantly via a Stripe checkout overlay.</p>
                </div>

                <!-- Developer API Keys -->
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 hover:border-slate-700/80 transition-all">
                    <div class="w-10 h-10 bg-indigo-950/60 border border-indigo-900/40 text-indigo-400 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-key text-base"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Developer API Management</h3>
                    <p class="text-xs text-slate-455 leading-relaxed">Create and manage secure API keys starting with <code>pms_live_</code> to integrate keyless locks, housekeeping apps, or POS software.</p>
                </div>

                <!-- Activity & Audit Logs -->
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 hover:border-slate-700/80 transition-all">
                    <div class="w-10 h-10 bg-indigo-950/60 border border-indigo-900/40 text-indigo-400 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-shield-halved text-base"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Tamper-proof Audit Logs</h3>
                    <p class="text-xs text-slate-455 leading-relaxed">Maintain security compliance by tracking all critical administrator actions, login attempts, and database updates in a secure log.</p>
                </div>

                <!-- Automated Notifications -->
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 hover:border-slate-700/80 transition-all">
                    <div class="w-10 h-10 bg-indigo-950/60 border border-indigo-900/40 text-indigo-400 rounded-xl flex items-center justify-center mb-4">
                        <i class="far fa-bell text-base"></i>
                    </div>
                    <h3 class="text-base font-bold text-white mb-2">Email & WhatsApp Templates</h3>
                    <p class="text-xs text-slate-455 leading-relaxed">Customize transactional mail and text notifications. Use merge fields to insert guest name, room details, and reservation prices.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-20 bg-slate-950/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-black text-white">SaaS Subscription Plans</h2>
                <p class="text-sm text-slate-400 mt-2">Select a plan that suits your scale and budget</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Trial Plan -->
                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-indigo-400">Trial Plan</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-3xl font-black text-white">$0.00</span>
                            <span class="text-xs text-slate-400 ml-1">/ 14 Days</span>
                        </div>
                        <p class="text-[11px] text-slate-455 mt-2">Explore the system with full premium features access.</p>
                        
                        <ul class="mt-6 space-y-2 text-xs text-slate-400">
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Up to 5 Rooms</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Up to 2 Users</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Custom Templates</li>
                        </ul>
                    </div>
                    <a href="{{ route('register-hotel') }}" class="w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs py-2 px-4 rounded-xl mt-8 transition-colors block">
                        Get Started
                    </a>
                </div>

                <!-- Monthly Pro -->
                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 flex flex-col justify-between relative shadow-indigo-500/5">
                    <div>
                        <h3 class="text-base font-bold text-indigo-400">Monthly Pro</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-3xl font-black text-white">$29.00</span>
                            <span class="text-xs text-slate-400 ml-1">/ month</span>
                        </div>
                        <p class="text-[11px] text-slate-455 mt-2">Perfect for growing boutique properties.</p>
                        
                        <ul class="mt-6 space-y-2 text-xs text-slate-400">
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Up to 25 Rooms</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Up to 10 Users</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Stripe Integration</li>
                        </ul>
                    </div>
                    <a href="{{ route('register-hotel') }}" class="w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 px-4 rounded-xl mt-8 transition-colors block">
                        Subscribe Now
                    </a>
                </div>

                <!-- Yearly Premium -->
                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-indigo-400">Yearly Premium</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-3xl font-black text-white">$249.00</span>
                            <span class="text-xs text-slate-400 ml-1">/ year</span>
                        </div>
                        <p class="text-[11px] text-slate-455 mt-2">Save with an annual subscription.</p>
                        
                        <ul class="mt-6 space-y-2 text-xs text-slate-400">
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Up to 100 Rooms</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Up to 30 Users</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Priority Support</li>
                        </ul>
                    </div>
                    <a href="{{ route('register-hotel') }}" class="w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs py-2 px-4 rounded-xl mt-8 transition-colors block">
                        Subscribe Now
                    </a>
                </div>

                <!-- Lifetime Enterprise -->
                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-indigo-400">Lifetime Enterprise</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-3xl font-black text-white">$999.00</span>
                            <span class="text-xs text-slate-400 ml-1">/ one-time</span>
                        </div>
                        <p class="text-[11px] text-slate-455 mt-2">Unlimited rooms and lifetime updates.</p>
                        
                        <ul class="mt-6 space-y-2 text-xs text-slate-400">
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Unlimited Rooms</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> Unlimited Users</li>
                            <li><i class="fas fa-check text-emerald-500 mr-2"></i> API Access Keys</li>
                        </ul>
                    </div>
                    <a href="{{ route('register-hotel') }}" class="w-full text-center bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs py-2 px-4 rounded-xl mt-8 transition-colors block">
                        Get Enterprise
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 py-10 bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            <p>© 2026 HotelFlow SaaS Platform. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
