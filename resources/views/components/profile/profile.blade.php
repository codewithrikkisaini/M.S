<div class="space-y-6" x-data="{ activeTab: 'personal' }">
    {{-- Header Banner & Profile Overview --}}
    <div class="pms-card shadow-sm border border-slate-100/80 p-6 bg-white rounded-2xl">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col md:flex-row items-center gap-5 text-center md:text-left">
                {{-- Avatar --}}
                <div class="relative group">
                    <div class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-md bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center relative shrink-0">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($profile_photo_path)
                            <img src="{{ asset('storage/' . $profile_photo_path) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-white text-3xl font-black uppercase">{{ substr($user_name, 0, 1) }}</span>
                        @endif
                        
                        <div wire:loading wire:target="photo" class="absolute inset-0 bg-black/50 flex items-center justify-center text-white text-xs">
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                        </div>
                    </div>
                </div>

                {{-- User Info Header --}}
                <div>
                    <div class="flex items-center gap-2 justify-center md:justify-start">
                        <h1 class="text-2xl font-black text-slate-800 tracking-tight">{{ $user_name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-200 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Active
                        </span>
                    </div>
                    <p class="text-xs font-bold text-indigo-600 mt-0.5">{{ $user_role }} @if($hotel_name) • {{ $hotel_name }} @endif</p>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 mt-2 text-xs text-slate-500 font-medium">
                        <span class="flex items-center gap-1.5"><i class="fas fa-envelope text-indigo-400"></i> {{ $user_email }}</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-phone text-indigo-400"></i> {{ $user_phone }}</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-id-badge text-indigo-400"></i> {{ $user_employee_id }}</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt text-indigo-400"></i> Joined {{ $user_joined }}</span>
                    </div>
                </div>
            </div>

            {{-- Action Button --}}
            <div class="shrink-0">
                <label class="btn-primary text-xs font-bold rounded-xl py-2.5 px-4 cursor-pointer shadow-sm flex items-center gap-2 hover:bg-indigo-700 transition-all">
                    <i class="fas fa-camera"></i> Change Photo
                    <input type="file" wire:model="photo" class="hidden" accept="image/*">
                </label>
                @error('photo') <p class="text-red-500 text-[10px] mt-1 text-center md:text-right">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Tab Navigation Bar --}}
        <div class="flex items-center gap-2 border-t border-slate-100 mt-6 pt-4 overflow-x-auto scrollbar-none">
            <button @click="activeTab = 'personal'" 
                :class="activeTab === 'personal' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100 font-bold border-indigo-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-slate-200 font-semibold'" 
                class="px-4 py-2.5 rounded-xl border text-xs flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap">
                <i class="fas fa-user-cog text-xs"></i> Personal Profile
            </button>
            <button @click="activeTab = 'hotel'" 
                :class="activeTab === 'hotel' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100 font-bold border-indigo-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-slate-200 font-semibold'" 
                class="px-4 py-2.5 rounded-xl border text-xs flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap">
                <i class="fas fa-hotel text-xs"></i> Hotel Information
            </button>
            <button @click="activeTab = 'gallery'" 
                :class="activeTab === 'gallery' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100 font-bold border-indigo-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-slate-200 font-semibold'" 
                class="px-4 py-2.5 rounded-xl border text-xs flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap">
                <i class="fas fa-images text-xs"></i> Gallery & Photos
            </button>
            <button @click="activeTab = 'stats'" 
                :class="activeTab === 'stats' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100 font-bold border-indigo-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-slate-200 font-semibold'" 
                class="px-4 py-2.5 rounded-xl border text-xs flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap">
                <i class="fas fa-chart-line text-xs"></i> Statistics & Performance
            </button>
            <button @click="activeTab = 'security'" 
                :class="activeTab === 'security' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100 font-bold border-indigo-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border-slate-200 font-semibold'" 
                class="px-4 py-2.5 rounded-xl border text-xs flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap">
                <i class="fas fa-shield-alt text-xs"></i> Security & Password
            </button>
        </div>
    </div>

    {{-- TAB 1: PERSONAL PROFILE --}}
    <div x-show="activeTab === 'personal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Personal Details Card --}}
        <div class="lg:col-span-2 pms-card shadow-sm border border-slate-100/80 p-6 space-y-4 bg-white rounded-2xl">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-user-cog text-xs"></i></div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Personal Details</h3>
                        <p class="text-[10px] text-slate-400 font-medium">Manage your personal credentials and account details</p>
                    </div>
                </div>
                @if(!$editPersonal)
                <button wire:click="$set('editPersonal', true)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 cursor-pointer bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
                    <i class="fas fa-edit text-[10px]"></i> Edit Profile
                </button>
                @endif
            </div>

            @if($editPersonal)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="user_name" class="pms-input text-xs" placeholder="Full Name">
                    @error('user_name') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Username <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="user_username" class="pms-input text-xs" placeholder="username">
                    @error('user_username') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="user_email" class="pms-input text-xs" placeholder="email@example.com">
                    @error('user_email') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Mobile <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="user_phone" class="pms-input text-xs" placeholder="Phone">
                    @error('user_phone') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee ID</label>
                    <input type="text" wire:model="user_employee_id" class="pms-input text-xs" placeholder="Employee ID">
                    @error('user_employee_id') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="pms-label text-xs font-semibold text-slate-400 uppercase tracking-wider">Role</label>
                    <input type="text" class="pms-input text-xs bg-slate-50 text-slate-400" value="{{ $user_role }}" disabled>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                <button wire:click="$set('editPersonal', false)" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-50 cursor-pointer">Cancel</button>
                <button wire:click="savePersonal" class="btn-primary text-xs font-bold rounded-lg py-2 px-4 cursor-pointer shadow-sm">Save Changes</button>
            </div>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                    <span class="text-slate-400 font-medium">Full Name</span>
                    <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $user_name }}</span>
                </div>
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                    <span class="text-slate-400 font-medium">Username</span>
                    <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $user_username }}</span>
                </div>
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                    <span class="text-slate-400 font-medium">Email Address</span>
                    <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $user_email }}</span>
                </div>
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                    <span class="text-slate-400 font-medium">Mobile Number</span>
                    <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $user_phone }}</span>
                </div>
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                    <span class="text-slate-400 font-medium">Employee ID</span>
                    <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $user_employee_id }}</span>
                </div>
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                    <span class="text-slate-400 font-medium">Role</span>
                    <span class="font-bold text-indigo-600 mt-0.5 text-sm">{{ $user_role }}</span>
                </div>
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80 sm:col-span-2">
                    <span class="text-slate-400 font-medium">Last Login Timestamp</span>
                    <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $user_last_login }}</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Recent Activity Sidebar --}}
        <div class="lg:col-span-1 pms-card shadow-sm border border-slate-100/80 p-6 space-y-4 bg-white rounded-2xl">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-history text-xs"></i></div>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">📜 Recent Activity</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Latest actions performed</p>
                </div>
            </div>
            <div class="space-y-3">
                @foreach($recent_activities as $activity)
                <div class="flex gap-3 items-start text-xs p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                    <div class="w-6 h-6 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-500 shrink-0 mt-0.5 border border-indigo-100">
                        <i class="fas fa-check text-[9px]"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-slate-800 leading-tight">{{ $activity['action'] }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">{{ $activity['description'] }}</p>
                        <span class="text-[10px] text-indigo-500 font-semibold block mt-1"><i class="far fa-clock text-[9px] mr-1"></i>{{ $activity['time'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- TAB 2: HOTEL DETAILS --}}
    <div x-show="activeTab === 'hotel'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Hotel Info & Address Column --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Hotel Information Card --}}
                <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4 bg-white rounded-2xl">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-hotel text-xs"></i></div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">🏨 Hotel Information</h3>
                                <p class="text-[10px] text-slate-400 font-medium">Core property identity & business details</p>
                            </div>
                        </div>
                        @if(!$editHotel)
                        <button wire:click="$set('editHotel', true)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 cursor-pointer bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
                            <i class="fas fa-edit text-[10px]"></i> Edit Hotel
                        </button>
                        @endif
                    </div>

                    @if($editHotel)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Hotel Name <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="hotel_name" class="pms-input text-xs" placeholder="Hotel Name">
                            @error('hotel_name') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Hotel Code <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="hotel_code" class="pms-input text-xs" placeholder="Hotel Code">
                            @error('hotel_code') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Hotel Type <span class="text-red-500">*</span></label>
                            <select wire:model="hotel_type" class="pms-input text-xs">
                                <option value="Business Hotel">Business Hotel</option>
                                <option value="Resort">Resort</option>
                                <option value="Motel">Motel</option>
                                <option value="Boutique Hotel">Boutique Hotel</option>
                            </select>
                            @error('hotel_type') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Star Rating <span class="text-red-500">*</span></label>
                            <select wire:model="hotel_rating" class="pms-input text-xs">
                                <option value="1">⭐</option>
                                <option value="2">⭐⭐</option>
                                <option value="3">⭐⭐⭐</option>
                                <option value="4">⭐⭐⭐⭐</option>
                                <option value="5">⭐⭐⭐⭐⭐</option>
                            </select>
                            @error('hotel_rating') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Owner Name <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="hotel_owner" class="pms-input text-xs" placeholder="Owner Name">
                            @error('hotel_owner') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Hotel Email Address <span class="text-red-500">*</span></label>
                            <input type="email" wire:model="hotel_email" class="pms-input text-xs" placeholder="hotel@example.com">
                            @error('hotel_email') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Phone Number</label>
                            <input type="text" wire:model="hotel_phone" class="pms-input text-xs" placeholder="Phone Number">
                            @error('hotel_phone') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Website URL</label>
                            <input type="text" wire:model="hotel_website" class="pms-input text-xs" placeholder="Website URL">
                            @error('hotel_website') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">GST Number</label>
                            <input type="text" wire:model="hotel_gst_no" class="pms-input text-xs" placeholder="GST Number">
                            @error('hotel_gst_no') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                        <button wire:click="$set('editHotel', false)" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-50 cursor-pointer">Cancel</button>
                        <button wire:click="saveHotel" class="btn-primary text-xs font-bold rounded-lg py-2 px-4 cursor-pointer shadow-sm">Save Changes</button>
                    </div>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Hotel Name</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_name }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Hotel Code</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_code }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Hotel Type</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_type }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Star Rating</span>
                            <span class="font-bold text-amber-500 mt-0.5 text-sm">
                                {{ str_repeat('⭐', intval($hotel_rating)) }}
                            </span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Owner</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_owner }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Hotel Email</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_email }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Phone</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_phone }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Website</span>
                            <span class="font-bold text-indigo-600 mt-0.5 text-sm">{{ $hotel_website }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">GST Number</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_gst_no }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Check-In / Check-Out</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_checkin_time }} / {{ $hotel_checkout_time }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Currency</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_currency }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Time Zone</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_timezone }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Address Card --}}
                <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4 bg-white rounded-2xl">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-map-marker-alt text-xs"></i></div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">📍 Property Address</h3>
                                <p class="text-[10px] text-slate-400 font-medium">Geographic location & postal address</p>
                            </div>
                        </div>
                        @if(!$editAddress)
                        <button wire:click="$set('editAddress', true)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 cursor-pointer bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
                            <i class="fas fa-edit text-[10px]"></i> Edit Address
                        </button>
                        @endif
                    </div>

                    @if($editAddress)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Address <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="hotel_address" class="pms-input text-xs" placeholder="123 Main St">
                            @error('hotel_address') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">City <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="hotel_city" class="pms-input text-xs" placeholder="City">
                            @error('hotel_city') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">State <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="hotel_state" class="pms-input text-xs" placeholder="State">
                            @error('hotel_state') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Country <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="hotel_country" class="pms-input text-xs" placeholder="Country">
                            @error('hotel_country') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Pincode <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="hotel_pincode" class="pms-input text-xs" placeholder="Postal Code">
                            @error('hotel_pincode') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                        <button wire:click="$set('editAddress', false)" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-50 cursor-pointer">Cancel</button>
                        <button wire:click="saveAddress" class="btn-primary text-xs font-bold rounded-lg py-2 px-4 cursor-pointer shadow-sm">Save Changes</button>
                    </div>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80 sm:col-span-2">
                            <span class="text-slate-400 font-medium">Street Address</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_address }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">City</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_city }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">State</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_state }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Country</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_country }}</span>
                        </div>
                        <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                            <span class="text-slate-400 font-medium">Pincode / Postal Code</span>
                            <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $hotel_pincode }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Room Summary Sidebar --}}
            <div class="lg:col-span-1 pms-card shadow-sm border border-slate-100/80 p-6 space-y-4 bg-white rounded-2xl">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-bed text-xs"></i></div>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">🛏 Room Inventory</h3>
                        <p class="text-[10px] text-slate-400 font-medium">Room count & category breakdown</p>
                    </div>
                </div>
                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-slate-50 border border-slate-100/80">
                        <span class="text-slate-600 font-medium">Total Rooms</span>
                        <span class="font-black text-slate-800 text-sm">{{ $rooms_total }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-green-50/60 border border-green-100">
                        <span class="text-green-700 font-medium">Available Rooms</span>
                        <span class="font-black text-green-600 text-sm">{{ $rooms_available }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-indigo-50/60 border border-indigo-100">
                        <span class="text-indigo-700 font-medium">Occupied Rooms</span>
                        <span class="font-black text-indigo-600 text-sm">{{ $rooms_occupied }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-orange-50/60 border border-orange-100">
                        <span class="text-orange-700 font-medium">Reserved Rooms</span>
                        <span class="font-black text-orange-600 text-sm">{{ $rooms_reserved }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-red-50/60 border border-red-100">
                        <span class="text-red-700 font-medium">Maintenance Rooms</span>
                        <span class="font-black text-red-600 text-sm">{{ $rooms_maintenance }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-slate-50 border border-slate-100/80">
                        <span class="text-slate-600 font-medium">Total Floors</span>
                        <span class="font-black text-slate-800 text-sm">{{ $rooms_floors }}</span>
                    </div>
                    <div class="pt-2">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-2">Room Types Available</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(explode(', ', $room_types_list) as $type)
                            <span class="bg-indigo-50 text-indigo-700 border border-indigo-100 px-2.5 py-1 rounded-lg text-[10px] font-bold">{{ $type }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 3: GALLERY & PHOTOS --}}
    <div x-show="activeTab === 'gallery'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4 bg-white rounded-2xl">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100 shadow-sm shrink-0">
                        <i class="fas fa-images text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">📸 Hotel Gallery & Showcase Photos</h3>
                        <p class="text-xs text-slate-400 font-medium">Upload photos of property facade, lobby, rooms, and guest amenities</p>
                    </div>
                </div>
                <div>
                    <label class="btn-primary text-xs font-bold rounded-xl py-2 px-4 cursor-pointer shadow-sm flex items-center gap-2 hover:bg-indigo-700 transition-all">
                        <i class="fas fa-plus"></i> Add Photos
                        <input type="file" wire:model="gallery_photos" class="hidden" multiple accept="image/*">
                    </label>
                </div>
            </div>

            @if(count($gallery_images) === 0)
                {{-- Empty State --}}
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-12 flex flex-col items-center justify-center text-center space-y-4 bg-slate-50/50 my-4">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center border border-slate-100 shadow-sm text-indigo-500">
                        <i class="fas fa-camera text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-800">No hotel photos uploaded yet</p>
                        <p class="text-xs text-slate-400 max-w-sm mt-1 mx-auto">Upload high-quality images of your property to feature on guest facing pages and room listings.</p>
                    </div>
                    <div>
                        <label class="btn-primary text-xs font-bold rounded-xl py-2.5 px-5 cursor-pointer shadow-sm inline-flex items-center gap-2 hover:bg-indigo-700 transition-all">
                            <i class="fas fa-upload"></i> Select Photos to Upload
                            <input type="file" wire:model="gallery_photos" class="hidden" multiple accept="image/*">
                        </label>
                    </div>
                    <div wire:loading wire:target="gallery_photos" class="text-xs text-indigo-600 font-bold">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Uploading photos...
                    </div>
                </div>
            @else
                {{-- Dynamic Gallery --}}
                <div class="space-y-4 pt-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                        @foreach($gallery_images as $img)
                            <div class="relative group rounded-2xl overflow-hidden border {{ $img['is_primary'] ? 'border-indigo-500 ring-2 ring-indigo-500/20 shadow-md' : 'border-slate-200/80 shadow-sm' }} bg-white flex flex-col transition-all">
                                {{-- Image Container --}}
                                <div class="relative aspect-[4/3] bg-slate-900 overflow-hidden">
                                    <img src="{{ asset('storage/' . $img['image_path']) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    
                                    {{-- Primary Cover Badge --}}
                                    @if($img['is_primary'])
                                        <div class="absolute top-2.5 left-2.5 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded shadow-md flex items-center gap-1">
                                            <i class="fas fa-star text-yellow-300"></i> Main Front Cover
                                        </div>
                                    @endif

                                    {{-- Actions Overlay --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-3">
                                        <div class="flex justify-end gap-1.5">
                                            <button wire:click="editImage({{ $img['id'] }})" title="Edit Title & Description" class="w-8 h-8 rounded-lg bg-white/90 text-slate-700 hover:bg-white flex items-center justify-center transition-colors cursor-pointer shadow">
                                                <i class="fas fa-pencil-alt text-xs"></i>
                                            </button>
                                            <button wire:click="deleteImage({{ $img['id'] }})" wire:confirm="Are you sure you want to delete this photo?" title="Delete Photo" class="w-8 h-8 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors cursor-pointer shadow">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="flex items-center justify-between">
                                            @if(!$img['is_primary'])
                                                <button wire:click="setPrimaryImage({{ $img['id'] }})" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold px-2.5 py-1.5 rounded-lg shadow cursor-pointer flex items-center gap-1 transition-colors">
                                                    <i class="far fa-star"></i> Set as Main Cover
                                                </button>
                                            @else
                                                <span class="text-xs font-bold text-white flex items-center gap-1"><i class="fas fa-check-circle text-green-400"></i> Active Front Image</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Caption & Description Details --}}
                                <div class="p-3 bg-slate-50/70 border-t border-slate-100 flex-1 flex flex-col justify-between">
                                    @if($editing_image_id === $img['id'])
                                        <div class="space-y-2">
                                            <input type="text" wire:model="editing_image_title" class="pms-input text-xs font-bold" placeholder="Title (e.g. Front Facade / Deluxe Room)">
                                            <textarea wire:model="editing_image_description" rows="2" class="pms-input text-xs" placeholder="Add image description..."></textarea>
                                            <div class="flex gap-2 justify-end pt-1">
                                                <button wire:click="cancelEditImage" class="px-2.5 py-1 text-[10px] font-bold border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-100">Cancel</button>
                                                <button wire:click="saveImageDetails" class="px-3 py-1 text-[10px] font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-sm">Save</button>
                                            </div>
                                        </div>
                                    @else
                                        <div>
                                            <div class="flex items-center justify-between gap-1">
                                                <h4 class="text-xs font-bold text-slate-800 truncate">
                                                    {{ $img['title'] ?: 'Hotel Image' }}
                                                </h4>
                                                <button wire:click="editImage({{ $img['id'] }})" class="text-[10px] text-indigo-600 font-bold hover:underline cursor-pointer shrink-0">
                                                    <i class="fas fa-edit mr-0.5"></i> Edit
                                                </button>
                                            </div>
                                            <p class="text-[11px] text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                                {{ $img['description'] ?: 'No description added yet. Click edit to add description.' }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div wire:loading wire:target="gallery_photos" class="text-xs text-indigo-600 font-bold pt-2">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Uploading photos...
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- TAB 4: STATISTICS & PERFORMANCE --}}
    <div x-show="activeTab === 'stats'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-6 bg-white rounded-2xl">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100 shadow-sm shrink-0">
                    <i class="fas fa-chart-line text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">📊 Hotel Performance & Analytics</h3>
                    <p class="text-xs text-slate-400 font-medium">Real-time statistics for check-ins, occupancy, and revenues</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                <div class="p-5 bg-gradient-to-br from-slate-50 to-indigo-50/30 rounded-2xl border border-slate-100 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-bold text-xs uppercase tracking-wider">Today's Check-Ins</span>
                        <div class="w-9 h-9 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-xs"><i class="fas fa-sign-in-alt"></i></div>
                    </div>
                    <span class="text-3xl font-black text-slate-800">{{ sprintf("%02d", $stats_checkins_today) }}</span>
                </div>

                <div class="p-5 bg-gradient-to-br from-slate-50 to-indigo-50/30 rounded-2xl border border-slate-100 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-bold text-xs uppercase tracking-wider">Today's Check-Outs</span>
                        <div class="w-9 h-9 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-xs"><i class="fas fa-sign-out-alt"></i></div>
                    </div>
                    <span class="text-3xl font-black text-slate-800">{{ sprintf("%02d", $stats_checkouts_today) }}</span>
                </div>

                <div class="p-5 bg-gradient-to-br from-slate-50 to-indigo-50/30 rounded-2xl border border-slate-100 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-bold text-xs uppercase tracking-wider">Guests Staying</span>
                        <div class="w-9 h-9 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-bold text-xs"><i class="fas fa-users"></i></div>
                    </div>
                    <span class="text-3xl font-black text-slate-800">{{ sprintf("%02d", $stats_guests_staying) }}</span>
                </div>

                <div class="p-5 bg-gradient-to-br from-indigo-50/60 to-purple-50/60 rounded-2xl border border-indigo-100 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-indigo-600 font-bold text-xs uppercase tracking-wider">Occupancy Rate</span>
                        <div class="w-9 h-9 bg-indigo-600 text-white rounded-xl flex items-center justify-center font-bold text-xs"><i class="fas fa-percentage"></i></div>
                    </div>
                    <span class="text-3xl font-black text-indigo-600">{{ $stats_occupancy }}%</span>
                </div>

                <div class="p-5 bg-gradient-to-br from-emerald-50/60 to-teal-50/60 rounded-2xl border border-emerald-100 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-emerald-700 font-bold text-xs uppercase tracking-wider">Today's Revenue</span>
                        <div class="w-9 h-9 bg-emerald-600 text-white rounded-xl flex items-center justify-center font-bold text-xs"><i class="fas fa-rupee-sign"></i></div>
                    </div>
                    <span class="text-3xl font-black text-emerald-600">{{ $stats_revenue_today }}</span>
                </div>

                <div class="p-5 bg-gradient-to-br from-purple-50/60 to-pink-50/60 rounded-2xl border border-purple-100 flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-purple-700 font-bold text-xs uppercase tracking-wider">Monthly Revenue</span>
                        <div class="w-9 h-9 bg-purple-600 text-white rounded-xl flex items-center justify-center font-bold text-xs"><i class="fas fa-chart-bar"></i></div>
                    </div>
                    <span class="text-3xl font-black text-purple-600">{{ $stats_revenue_month }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 5: SECURITY & PASSWORD --}}
    <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="max-w-4xl">
        <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-6 bg-white rounded-2xl">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100 shadow-sm shrink-0">
                    <i class="fas fa-shield-alt text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">🔐 Account Security & Password</h3>
                    <p class="text-xs text-slate-400 font-medium">Update account password and manage active device sessions</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                    <span class="text-slate-400 font-medium">Account Username</span>
                    <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ $user_username }}</span>
                </div>
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                    <span class="text-slate-400 font-medium">Password Status</span>
                    <span class="font-bold text-slate-800 mt-0.5 text-sm">••••••••</span>
                </div>
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                    <span class="text-slate-400 font-medium">Two-Factor Authentication (2FA)</span>
                    <span class="font-bold text-red-500 mt-0.5 text-sm">Disabled</span>
                </div>
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80">
                    <span class="text-slate-400 font-medium">Active Device Sessions</span>
                    <span class="font-bold text-slate-800 mt-0.5 text-sm">2 Active Devices</span>
                </div>
                <div class="flex flex-col py-2 px-3 bg-slate-50/70 rounded-xl border border-slate-100/80 sm:col-span-2">
                    <span class="text-slate-400 font-medium">Last Login IP Address</span>
                    <span class="font-bold text-slate-800 mt-0.5 text-sm">{{ request()->ip() }}</span>
                </div>
            </div>

            {{-- Password Change Form Section --}}
            <div class="border-t border-slate-100 pt-6 space-y-4">
                <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-key text-indigo-600"></i> Change Account Password
                </h4>

                <div class="p-5 bg-slate-50/70 rounded-2xl border border-slate-100 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Current Password</label>
                            <input type="password" wire:model="current_password" class="pms-input text-xs bg-white" placeholder="••••••••">
                            @error('current_password') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">New Password</label>
                            <input type="password" wire:model="new_password" class="pms-input text-xs bg-white" placeholder="••••••••">
                            @error('new_password') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-500 uppercase tracking-wider">Confirm New Password</label>
                            <input type="password" wire:model="new_password_confirmation" class="pms-input text-xs bg-white" placeholder="••••••••">
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <button wire:click="logoutAllDevices" class="px-4 py-2 border border-red-200 text-red-600 hover:bg-red-50 rounded-xl text-xs font-bold transition-colors cursor-pointer flex items-center gap-1.5">
                            <i class="fas fa-sign-out-alt"></i> Logout All Devices
                        </button>
                        <button wire:click="updatePassword" class="btn-primary text-xs font-bold rounded-xl py-2 px-5 cursor-pointer shadow-sm flex items-center gap-2 hover:bg-indigo-700 transition-all">
                            <i class="fas fa-check"></i> Update Password
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

