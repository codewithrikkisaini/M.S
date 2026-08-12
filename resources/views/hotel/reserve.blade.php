<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book {{ $selectedRoom->roomType?->name ?: 'Room' }} - {{ $hotel->name }} | LODGIKO</title>
    
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

    <style>
        @media print {
            header, footer, .no-print {
                display: none !important;
            }
            .print-only-container {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
@php
    $roomPrice = $selectedRoom->price ?: ($selectedRoom->roomType?->base_price ?? 2500);
@endphp
<body class="antialiased bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between" x-data="bookingApp()">

    <!-- Top Header Navigation -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50 shadow-sm no-print">
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

            <!-- Return Link -->
            <div class="flex items-center gap-4">
                <a href="{{ route('hotel.show', ['slug' => $hotel->slug ?: $hotel->id]) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-left text-slate-500"></i> Back to Property
                </a>
            </div>
        </div>
    </header>

    <!-- Main Section -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 w-full">

        <!-- Progress Stepper Header (Only shown when not submitted) -->
        <div class="mb-8 no-print" x-show="!bookingSubmitted">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Complete Room Reservation</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 flex items-center gap-2">
                        <i class="fas fa-building text-blue-500"></i> {{ $hotel->name }} • {{ $hotel->city }}
                    </p>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-xs font-bold bg-blue-50 text-blue-700 px-4 py-2 rounded-2xl border border-blue-100 shadow-sm">
                    <i class="fas fa-shield-alt text-blue-600"></i> 100% Secure Booking
                </div>
            </div>

            <!-- Stepper Indicators -->
            <div class="grid grid-cols-3 gap-2 sm:gap-4 max-w-2xl text-center">
                <div class="flex items-center gap-2 text-slate-500 text-xs font-bold py-2 border-b-2 border-slate-300">
                    <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[11px]">1</span>
                    <span class="hidden sm:inline">Select Room</span>
                </div>
                <div class="flex items-center gap-2 text-blue-600 text-xs font-black py-2 border-b-2 border-blue-600">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-[11px]">2</span>
                    <span>Guest & Payment</span>
                </div>
                <div class="flex items-center gap-2 text-slate-400 text-xs font-bold py-2 border-b-2 border-slate-200">
                    <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-[11px]">3</span>
                    <span class="hidden sm:inline">Voucher Confirmation</span>
                </div>
            </div>
        </div>

        <!-- FORM CONTAINER (When Reservation Pending) -->
        <div x-show="!bookingSubmitted">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Left Column (8 cols): Guest Form & Payment Options -->
                <div class="lg:col-span-7 xl:col-span-8 space-y-6">
                    
                    <form @submit.prevent="submitBooking()" class="space-y-6">

                        <!-- Occupied Room Warning Banner -->
                        @if(($selectedRoom->status ?? 'Available') !== 'Available')
                            <div class="bg-rose-500/15 border-2 border-rose-500/40 text-rose-200 p-4 rounded-2xl text-xs font-bold flex items-center gap-3 shadow-md">
                                <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 font-extrabold text-lg shadow-sm">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-sm text-white">Bro, ye room already booked / occupied hai!</div>
                                    <div class="text-[11px] text-rose-300 mt-0.5">Room #{{ $selectedRoom->room_number }} is currently {{ $selectedRoom->status }}. Please go back to hotel page and select an available room.</div>
                                </div>
                            </div>
                        @endif

                        <!-- Error Banner -->
                        <template x-if="errorMessage">
                            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-bold flex items-center gap-3 shadow-sm">
                                <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
                                <span x-text="errorMessage"></span>
                            </div>
                        </template>

                        <!-- Card 1: Stay Dates -->
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-blue-600"></i> Select Check-in & Check-out Dates
                                </h2>
                                <span class="text-xs font-bold px-3 py-1 bg-blue-50 text-blue-700 rounded-xl" x-text="nights + ' Night' + (nights > 1 ? 's' : '') + ' Stay'"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Check-in Date *</label>
                                    <input type="date" x-model="bookingData.checkin_date" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 font-bold rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-blue-600 focus:bg-white shadow-sm transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Check-out Date *</label>
                                    <input type="date" x-model="bookingData.checkout_date" required class="w-full bg-slate-50 border border-slate-200 text-slate-900 font-bold rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-blue-600 focus:bg-white shadow-sm transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Guest Details -->
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                            <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                                <i class="fas fa-user-edit text-blue-600"></i> Primary Guest Details
                            </h2>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Customer Full Name *</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-user text-blue-500 text-sm"></i>
                                    </span>
                                    <input type="text" x-model="bookingData.guest_name" required placeholder="Apna Poora Naam Bharein (e.g. Rikki Saini)" class="w-full bg-slate-50 border border-slate-200 text-slate-900 font-bold rounded-2xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-blue-600 focus:bg-white shadow-sm transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Email Address *</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                            <i class="fas fa-envelope text-blue-500 text-sm"></i>
                                        </span>
                                        <input type="email" x-model="bookingData.guest_email" required placeholder="rikki@gmail.com" class="w-full bg-slate-50 border border-slate-200 text-slate-900 font-bold rounded-2xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-blue-600 focus:bg-white shadow-sm transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Mobile / Phone Number *</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                            <i class="fas fa-phone-alt text-blue-500 text-sm"></i>
                                        </span>
                                        <input type="text" x-model="bookingData.guest_phone" required placeholder="+91 9876543210" class="w-full bg-slate-50 border border-slate-200 text-slate-900 font-bold rounded-2xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:border-blue-600 focus:bg-white shadow-sm transition-all">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Special Request (Optional)</label>
                                <input type="text" x-model="bookingData.special_requests" placeholder="e.g. Early check-in, Quiet room, High floor" class="w-full bg-slate-50 border border-slate-200 text-slate-900 font-medium rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-blue-600 focus:bg-white shadow-sm transition-all">
                            </div>
                        </div>

                        <!-- Card 3: Payment Options -->
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm space-y-4">
                            <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                                <i class="fas fa-credit-card text-blue-600"></i> Select Payment Option
                            </h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="bookingData.payment_method" value="Cash" class="peer sr-only">
                                    <div class="p-4 rounded-2xl border-2 border-slate-200 bg-white text-slate-900 peer-checked:border-blue-600 peer-checked:bg-blue-50/80 peer-checked:text-blue-700 font-black transition-all shadow-sm flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-money-bill-wave text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black">Pay at Hotel</div>
                                            <div class="text-xs font-bold text-slate-500 mt-0.5">Pay on Check-in arrival</div>
                                        </div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" x-model="bookingData.payment_method" value="UPI" class="peer sr-only">
                                    <div class="p-4 rounded-2xl border-2 border-slate-200 bg-white text-slate-900 peer-checked:border-blue-600 peer-checked:bg-blue-50/80 peer-checked:text-blue-700 font-black transition-all shadow-sm flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-qrcode text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black">UPI Direct</div>
                                            <div class="text-xs font-bold text-slate-500 mt-0.5">Google Pay / PhonePe / Paytm</div>
                                        </div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" x-model="bookingData.payment_method" value="Card" class="peer sr-only">
                                    <div class="p-4 rounded-2xl border-2 border-slate-200 bg-white text-slate-900 peer-checked:border-blue-600 peer-checked:bg-blue-50/80 peer-checked:text-blue-700 font-black transition-all shadow-sm flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-credit-card text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black">Credit/Debit Card</div>
                                            <div class="text-xs font-bold text-slate-500 mt-0.5">Visa / MasterCard / RuPay</div>
                                        </div>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" x-model="bookingData.payment_method" value="Net Banking" class="peer sr-only">
                                    <div class="p-4 rounded-2xl border-2 border-slate-200 bg-white text-slate-900 peer-checked:border-blue-600 peer-checked:bg-blue-50/80 peer-checked:text-blue-700 font-black transition-all shadow-sm flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-university text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black">Net Banking</div>
                                            <div class="text-xs font-bold text-slate-500 mt-0.5">All Major Indian Banks</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Payment Instructions Sub-Panels -->
                            <!-- 1. Pay at Hotel -->
                            <div x-show="bookingData.payment_method === 'Cash'" class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs space-y-1 text-emerald-900">
                                <div class="font-extrabold flex items-center gap-2 text-emerald-800 text-sm">
                                    <i class="fas fa-check-circle text-emerald-600"></i> Zero Advance Required
                                </div>
                                <p class="text-xs text-emerald-800 leading-relaxed font-medium">
                                    Aapko abhi online payment karne ki zaroorat nahi hai. Room reserve karne ke baad hotel check-in desk par Cash, Card ya UPI se direct pay karein.
                                </p>
                            </div>

                            <!-- 2. UPI Direct QR Panel -->
                            <div x-show="bookingData.payment_method === 'UPI'" class="p-5 bg-slate-900 text-white rounded-3xl space-y-4 shadow-xl border border-slate-800" x-data="{ copied: false }">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                    <span class="font-black text-blue-400 flex items-center gap-2 text-sm">
                                        <i class="fas fa-qrcode text-base"></i> Scan & Pay via UPI (Instant QR)
                                    </span>
                                    <span class="bg-blue-600/30 text-blue-300 text-xs font-extrabold px-3 py-1 rounded-xl border border-blue-500/30">Instant QR</span>
                                </div>

                                <div class="flex flex-col sm:flex-row items-center gap-5">
                                    <!-- QR Code -->
                                    <div class="bg-white p-3 rounded-2xl shadow-lg shrink-0 text-center">
                                        <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=upi://pay?pa=merahkie.hotel@upi%26pn=' + encodeURIComponent('{{ addslashes($hotel->name) }}') + '%26mc=0000%26mode=02%26purpose=00'" alt="UPI QR Code" class="w-32 h-32 mx-auto rounded-xl">
                                        <span class="text-[10px] font-black text-slate-800 mt-2 block">Scan with GPay / PhonePe / Paytm</span>
                                    </div>

                                    <!-- UPI ID & Details -->
                                    <div class="space-y-3 flex-1 w-full">
                                        <div>
                                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Official Hotel UPI ID</label>
                                            <div class="flex items-center gap-2 bg-slate-800 border border-slate-700 rounded-2xl p-2.5">
                                                <code class="text-emerald-400 font-mono font-black text-sm flex-1 truncate">merahkie.hotel@upi</code>
                                                <button type="button" @click="navigator.clipboard.writeText('merahkie.hotel@upi'); copied = true; setTimeout(() => copied = false, 2000)" class="bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs px-3 py-1.5 rounded-xl transition-all cursor-pointer shrink-0">
                                                    <span x-text="copied ? '✓ Copied' : 'Copy ID'"></span>
                                                </button>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">UTR / Transaction Ref No. (Optional)</label>
                                            <input type="text" x-model="bookingData.utr_number" placeholder="Enter 12-digit UTR after payment" class="w-full bg-slate-800 border border-slate-700 text-white placeholder-slate-500 font-mono rounded-2xl px-3.5 py-2 text-xs focus:outline-none focus:border-blue-500">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Credit / Debit Card -->
                            <div x-show="bookingData.payment_method === 'Card'" class="p-5 bg-slate-50 border border-slate-200 rounded-3xl text-xs space-y-3 shadow-sm">
                                <div class="font-extrabold text-slate-800 flex items-center justify-between border-b border-slate-200 pb-2">
                                    <span class="flex items-center gap-2 text-sm"><i class="fas fa-credit-card text-purple-600"></i> Enter Card Details</span>
                                    <div class="flex items-center gap-2 text-lg text-slate-400">
                                        <i class="fab fa-cc-visa"></i>
                                        <i class="fab fa-cc-mastercard"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Cardholder Name</label>
                                    <input type="text" x-model="bookingData.card_name" placeholder="Name as on card" class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-blue-600">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Card Number</label>
                                    <input type="text" x-model="bookingData.card_number" placeholder="4532 •••• •••• 8901" class="w-full bg-white border border-slate-200 text-slate-900 font-mono rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-blue-600">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Expiry (MM/YY)</label>
                                        <input type="text" x-model="bookingData.card_expiry" placeholder="12/28" class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-blue-600">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">CVV Security Code</label>
                                        <input type="password" x-model="bookingData.card_cvv" maxlength="4" placeholder="123" class="w-full bg-white border border-slate-200 text-slate-900 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-blue-600">
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Net Banking -->
                            <div x-show="bookingData.payment_method === 'Net Banking'" class="p-5 bg-slate-50 border border-slate-200 rounded-3xl text-xs space-y-3 shadow-sm">
                                <div class="font-extrabold text-slate-800 border-b border-slate-200 pb-2 flex items-center gap-2 text-sm">
                                    <i class="fas fa-university text-indigo-600"></i> Select Your Bank
                                </div>
                                <select class="w-full bg-white border border-slate-200 text-slate-900 font-bold rounded-2xl px-4 py-3 text-xs focus:outline-none focus:border-blue-600">
                                    <option value="SBI">State Bank of India (SBI)</option>
                                    <option value="HDFC">HDFC Bank</option>
                                    <option value="ICICI">ICICI Bank</option>
                                    <option value="AXIS">Axis Bank</option>
                                    <option value="PNB">Punjab National Bank</option>
                                    <option value="OTHER">Other Major Bank</option>
                                </select>
                            </div>

                        </div>

                        <!-- Final Action CTA Button -->
                        <div class="pt-2">
                            @if(($selectedRoom->status ?? 'Available') !== 'Available')
                                <button type="button" disabled class="w-full py-4 px-6 bg-slate-300 text-slate-500 font-extrabold text-base rounded-2xl flex items-center justify-center gap-3 cursor-not-allowed border border-slate-400">
                                    <i class="fas fa-ban text-rose-500"></i> Already Booked / Occupied
                                </button>
                            @else
                                <button type="submit" :disabled="isSubmitting" class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-800 text-white font-extrabold text-base rounded-2xl shadow-xl hover:shadow-2xl transition-all flex items-center justify-center gap-3 cursor-pointer disabled:opacity-50">
                                    <template x-if="!isSubmitting">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-lock text-sm"></i> Confirm & Book Room Now (<span x-text="totalPayableFormatted"></span>)
                                        </span>
                                    </template>
                                    <template x-if="isSubmitting">
                                        <span class="flex items-center gap-2">
                                            <i class="fas fa-spinner fa-spin text-sm"></i> Processing Reservation...
                                        </span>
                                    </template>
                                </button>
                            @endif
                            <p class="text-center text-xs text-slate-400 mt-3 flex items-center justify-center gap-1.5">
                                <i class="fas fa-shield-alt text-emerald-500"></i> Free cancellation • Guaranteed booking at {{ $hotel->name }}
                            </p>
                        </div>

                    </form>
                </div>

                <!-- Right Column (4 cols): Room Details & Price Breakdown Sticky Sidebar -->
                <div class="lg:col-span-5 xl:col-span-4 sticky top-28 space-y-6">

                    <!-- Hotel & Room Card -->
                    <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-indigo-950 text-white rounded-3xl overflow-hidden shadow-2xl border border-slate-800" x-data="{ activeImgIdx: 0 }">
                        <!-- Image Header with Carousel Controls -->
                        <div class="relative aspect-video bg-slate-800 group">
                            <img :src="selectedRoom.images && selectedRoom.images.length > 0 ? selectedRoom.images[activeImgIdx] : selectedRoom.image" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1000&q=80';" class="w-full h-full object-cover transition-all duration-300">
                            
                            <!-- Next / Previous Controls -->
                            <template x-if="selectedRoom.images && selectedRoom.images.length > 1">
                                <div class="absolute inset-0 flex items-center justify-between p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" @click="activeImgIdx = (activeImgIdx - 1 + selectedRoom.images.length) % selectedRoom.images.length" class="w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white flex items-center justify-center backdrop-blur-md border border-white/20 shadow-md cursor-pointer transition-all">
                                        <i class="fas fa-chevron-left text-xs"></i>
                                    </button>
                                    <button type="button" @click="activeImgIdx = (activeImgIdx + 1) % selectedRoom.images.length" class="w-8 h-8 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white flex items-center justify-center backdrop-blur-md border border-white/20 shadow-md cursor-pointer transition-all">
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </button>
                                </div>
                            </template>

                            <!-- Room Title Badge -->
                            <div class="absolute bottom-3 left-3 bg-slate-900/80 backdrop-blur-md text-white text-xs font-bold px-3 py-1 rounded-xl border border-white/10 shadow-md">
                                <span x-text="selectedRoom.name + ' (Room #' + selectedRoom.number + ')'"></span>
                            </div>

                            <!-- Image Counter Badge -->
                            <template x-if="selectedRoom.images && selectedRoom.images.length > 1">
                                <div class="absolute top-3 right-3 bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-bold px-2.5 py-1 rounded-lg border border-white/10 shadow-md">
                                    <i class="fas fa-images text-blue-400 mr-1"></i> <span x-text="(activeImgIdx + 1) + '/' + selectedRoom.images.length"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Thumbnail Gallery Strip -->
                        <template x-if="selectedRoom.images && selectedRoom.images.length > 1">
                            <div class="flex gap-2 p-2.5 bg-slate-950/80 border-b border-slate-800 overflow-x-auto">
                                <template x-for="(img, idx) in selectedRoom.images" :key="idx">
                                    <button type="button" @click="activeImgIdx = idx" class="w-14 h-10 rounded-lg overflow-hidden border-2 transition-all shrink-0 cursor-pointer" :class="activeImgIdx === idx ? 'border-blue-500 scale-105 shadow-md' : 'border-slate-800 opacity-50 hover:opacity-100'">
                                        <img :src="img" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=600&q=80';" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        </template>

                        <!-- Content -->
                        <div class="p-6 space-y-4">
                            <div>
                                <h3 class="text-xl font-extrabold text-white" x-text="selectedRoom.name + ' - Room #' + selectedRoom.number"></h3>
                                <p class="text-xs text-slate-300 mt-1 flex flex-wrap items-center gap-2">
                                    <span><i class="fas fa-bed text-blue-400 mr-1"></i> <span x-text="selectedRoom.bed_type"></span></span>
                                    <span>•</span>
                                    <span><i class="fas fa-users text-blue-400 mr-1"></i> <span x-text="selectedRoom.capacity"></span></span>
                                </p>
                            </div>

                            <p class="text-xs text-slate-300 leading-relaxed" x-text="selectedRoom.description"></p>

                            <!-- Selected Room Options / Features -->
                            <template x-if="selectedRoom.room_option">
                                <div class="border-t border-slate-800 pt-4">
                                    <h4 class="text-[10px] font-bold text-amber-400 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                                        <i class="fas fa-star text-amber-400"></i> Selected Room Option(s) / Features
                                    </h4>
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="opt in selectedRoom.room_option.split(',')" :key="opt">
                                            <span class="text-[11px] font-extrabold bg-amber-500/15 text-amber-300 border border-amber-500/30 px-2.5 py-1 rounded-xl flex items-center gap-1.5 shadow-2xs">
                                                <i class="fas fa-check-circle text-amber-400 text-[10px]"></i> <span x-text="opt.trim()"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Included Amenities -->
                            <div class="border-t border-slate-800 pt-4">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2.5">Included Room Amenities</h4>
                                <div class="grid grid-cols-2 gap-2 text-[11px] font-semibold text-slate-200">
                                    <div class="flex items-center gap-1.5"><i class="fas fa-wifi text-blue-400"></i> Free High-Speed Wi-Fi</div>
                                    <div class="flex items-center gap-1.5"><i class="fas fa-snowflake text-blue-400"></i> Air Conditioning</div>
                                    <div class="flex items-center gap-1.5"><i class="fas fa-tv text-blue-400"></i> Flat Screen TV</div>
                                    <div class="flex items-center gap-1.5"><i class="fas fa-coffee text-blue-400"></i> Breakfast Included</div>
                                </div>
                            </div>

                            <!-- Pricing Breakdown Box -->
                            <div class="border-t border-slate-800 pt-4 space-y-2">
                                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Price Breakdown</h4>
                                <div class="flex justify-between text-xs text-slate-300">
                                    <span>Rate per Night:</span>
                                    <span class="font-bold text-white" x-text="selectedRoom.price"></span>
                                </div>
                                <div class="flex justify-between text-xs text-slate-300">
                                    <span>Duration:</span>
                                    <span class="font-bold text-white" x-text="nights + ' Night' + (nights > 1 ? 's' : '')"></span>
                                </div>
                                <div class="flex justify-between text-xs text-slate-300">
                                    <span>Taxes & Service Fees:</span>
                                    <span class="font-bold text-emerald-400">Included</span>
                                </div>
                                
                                <div class="border-t border-slate-800/80 pt-3 flex justify-between items-center mt-3">
                                    <div>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Total Amount Payable</span>
                                        <span class="text-xs text-slate-400">Payable at check-in / online</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-black text-emerald-400" x-text="totalPayableFormatted"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Cards -->
                    <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3 text-xs">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-900">Instant Room Confirmation</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Your booking is directly registered in the property's management system.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 border-t border-slate-100 pt-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fas fa-undo"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-900">Free Cancellation</h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">Cancel up to 24 hours before check-in date without penalty.</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>


        <!-- SUCCESS VOUCHER DISPLAY (When Reservation Submitted) -->
        <div x-show="bookingSubmitted" x-cloak>
            <div class="max-w-3xl mx-auto space-y-6 print-only-container">

                <!-- Action Toolbar (No Print) -->
                <div class="flex items-center justify-between no-print bg-emerald-50 border border-emerald-200 rounded-3xl p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-emerald-900">Room Reservation Confirmed!</h2>
                            <p class="text-xs text-emerald-700 font-medium">Your room has been reserved at {{ $hotel->name }}.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-print"></i> Print / Download Slip
                        </button>
                        <a href="{{ route('hotel.show', ['slug' => $hotel->slug ?: $hotel->id]) }}" class="px-4 py-2.5 bg-white border border-emerald-300 text-emerald-800 text-xs font-bold rounded-xl transition-all hover:bg-emerald-100">
                            Return to Hotel
                        </a>
                    </div>
                </div>

                <!-- Printable Voucher Container -->
                <div class="bg-white border border-slate-200 rounded-3xl shadow-xl overflow-hidden">
                    <!-- Voucher Header -->
                    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 text-white p-6 sm:p-8 relative">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-white/20 pb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-md border border-white/20">
                                    <i class="fas fa-hotel"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black tracking-tight" x-text="successResult?.hotel_name || '{{ addslashes($hotel->name) }}'"></h3>
                                    <p class="text-xs text-blue-100 font-medium">{{ $hotel->address }}, {{ $hotel->city }}, {{ $hotel->state }}</p>
                                </div>
                            </div>
                            
                            <div class="text-left sm:text-right bg-white/10 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/20">
                                <span class="block text-[10px] font-bold text-blue-200 uppercase tracking-widest">Booking Ref / PNR</span>
                                <span class="text-xl font-mono font-black tracking-wider text-amber-300" x-text="successResult?.pnr"></span>
                            </div>
                        </div>

                        <div class="pt-4 flex flex-wrap justify-between items-center text-xs text-blue-100 font-semibold gap-2">
                            <span><i class="fas fa-calendar-check mr-1.5 text-amber-300"></i> Issued Date: <span x-text="successResult?.booking_date"></span></span>
                            <span><i class="fas fa-hashtag mr-1.5 text-amber-300"></i> Booking ID: <span x-text="successResult?.booking_number"></span></span>
                        </div>
                    </div>

                    <!-- Voucher Content Body -->
                    <div class="p-6 sm:p-8 space-y-6">

                        <!-- Grid 1: Guest Details -->
                        <div class="border-b border-slate-100 pb-6">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fas fa-user-circle text-blue-600"></i> Guest Information
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Guest Name</span>
                                    <span class="font-extrabold text-slate-900 text-sm" x-text="successResult?.guest_name"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Email Address</span>
                                    <span class="font-bold text-slate-800" x-text="successResult?.guest_email"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Phone / Mobile</span>
                                    <span class="font-bold text-slate-800" x-text="successResult?.guest_phone"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Grid 2: Reservation Details -->
                        <div class="border-b border-slate-100 pb-6">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fas fa-bed text-blue-600"></i> Room & Stay Summary
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-blue-50/60 p-4 rounded-2xl border border-blue-100 text-xs">
                                <div>
                                    <span class="text-[10px] font-bold text-blue-600 uppercase block">Room Type</span>
                                    <span class="font-extrabold text-slate-900" x-text="successResult?.room_type"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-blue-600 uppercase block">Room Number</span>
                                    <span class="font-extrabold text-slate-900" x-text="'Room #' + successResult?.room_number"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-blue-600 uppercase block">Check-in Date</span>
                                    <span class="font-extrabold text-slate-900" x-text="successResult?.checkin_date"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-blue-600 uppercase block">Check-out Date</span>
                                    <span class="font-extrabold text-slate-900" x-text="successResult?.checkout_date"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Grid 3: Payment Breakdown & Status -->
                        <div>
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fas fa-receipt text-blue-600"></i> Billing & Payment Summary
                            </h4>
                            <div class="bg-slate-900 text-white p-5 rounded-2xl space-y-3">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-300">Payment Option Selected:</span>
                                    <span class="font-bold text-amber-300" x-text="successResult?.payment_method"></span>
                                </div>
                                <div class="flex justify-between items-center text-xs border-t border-slate-800 pt-3">
                                    <span class="text-slate-300">Total Room Charges:</span>
                                    <span class="text-xl font-black text-emerald-400" x-text="'₹' + successResult?.total_price"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Voucher Note -->
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-[11px] text-slate-600 space-y-1">
                            <span class="font-bold text-slate-900 block"><i class="fas fa-info-circle text-blue-500 mr-1"></i> Important Instructions for Check-in:</span>
                            <p>Please present this reservation confirmation slip along with a valid Government-issued Photo ID (Aadhaar / Passport / Driving License) at the hotel front desk during check-in.</p>
                        </div>

                    </div>

                    <!-- Voucher Footer -->
                    <div class="bg-slate-100 px-6 py-4 border-t border-slate-200 text-center text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-2">
                        <span>Thank you for booking with {{ $hotel->name }} via MERAHKIE.</span>
                        <span class="font-bold text-slate-700">Need Help? Call Hotel Front Desk: {{ $hotel->phone ?: '+91 9876543210' }}</span>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-12 mt-16 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 space-y-3">
            <p>&copy; {{ date('Y') }} Merahkie Bookings. All rights reserved.</p>
            <p>100% Verified Hotel Reservations & Direct Property PMS Integration.</p>
        </div>
    </footer>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bookingApp', () => ({
            bookingSubmitted: false,
            selectedRoom: {
                id: "{{ $selectedRoom->id }}",
                name: {!! json_encode($selectedRoom->roomType?->name ?: "Standard Room") !!},
                number: "{{ $selectedRoom->room_number }}",
                price: "₹{{ number_format($roomPrice) }}",
                rawPrice: {{ $roomPrice }},
                image: {!! json_encode($selectedRoom->image_url) !!},
                images: {!! json_encode($selectedRoom->images) !!},
                description: {!! json_encode($selectedRoom->description ?: "Experience ultimate comfort in Room " . $selectedRoom->room_number . ".") !!},
                bed_type: {!! json_encode(ucfirst($selectedRoom->bed_type ?? "King / Queen Bed")) !!},
                room_option: {!! json_encode($selectedRoom->room_option ?? "") !!},
                capacity: {!! json_encode(($selectedRoom->capacity ?? 2) . " Guests") !!}
            },
            bookingData: { 
                guest_name: "", 
                guest_email: "", 
                guest_phone: "", 
                checkin_date: "{{ $checkin }}", 
                checkout_date: "{{ $checkout }}", 
                special_requests: "", 
                payment_method: "Cash",
                utr_number: "",
                card_name: "",
                card_number: "",
                card_expiry: "",
                card_cvv: ""
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
                let rawRate = Number(this.selectedRoom.rawPrice || 0);
                return rawRate * this.nights;
            },
            get totalPayableFormatted() {
                return "₹" + this.totalPayable.toLocaleString("en-IN");
            },
            isSubmitting: false,
            successResult: null,
            errorMessage: "",
            async submitBooking() {
                if (!this.selectedRoom) return;
                this.isSubmitting = true;
                this.errorMessage = "";
                try {
                    let res = await fetch("{{ route('hotel.book-instant') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            hotel_id: {{ $hotel->id }},
                            room_id: this.selectedRoom.id,
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
                        window.scrollTo({ top: 0, behavior: "smooth" });
                    } else {
                        this.errorMessage = data.message || "Error completing room reservation.";
                    }
                } catch (e) {
                    this.errorMessage = "Network error occurred: " + (e.message || "Please try again.");
                } finally {
                    this.isSubmitting = false;
                }
            }
        }));
    });
    </script>
</body>
</html>
