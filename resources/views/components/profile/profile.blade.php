<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100 shadow-sm">
            <i class="fas fa-user-circle text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">My Profile</h1>
            <p class="text-xs text-slate-500 font-medium">Manage your personal account credentials and hotel parameters</p>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- LEFT COLUMN: Profile Card, Room Summary, Activity --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Profile Photo Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 flex flex-col items-center text-center space-y-4">
                <div class="relative group">
                    <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white shadow-md bg-slate-50 flex items-center justify-center relative">
                        @if($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($profile_photo_path)
                            <img src="{{ asset('storage/' . $profile_photo_path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-black uppercase">
                                {{ substr($user_name, 0, 1) }}
                            </div>
                        @endif
                        
                        <div wire:loading wire:target="photo" class="absolute inset-0 bg-black/50 flex items-center justify-center text-white text-xs">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Loading...
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-black text-slate-800 tracking-tight leading-tight">{{ $user_name }}</h2>
                    <p class="text-xs font-bold text-indigo-600 mt-0.5">{{ $user_role }}</p>
                </div>

                <div class="w-full border-t border-slate-100 pt-4 text-left space-y-2.5 text-xs text-slate-600">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-slate-400">Email</span>
                        <span class="font-bold text-slate-700 truncate max-w-[150px]">{{ $user_email }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-slate-400">Mobile</span>
                        <span class="font-bold text-slate-700">{{ $user_phone }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-slate-400">Status</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-200">🟢 Active</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-slate-400">Joined</span>
                        <span class="font-bold text-slate-700">{{ $user_joined }}</span>
                    </div>
                </div>

                <div class="w-full pt-2">
                    <label class="btn-primary text-xs font-bold rounded-lg py-2.5 cursor-pointer shadow-sm text-center block w-full hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-upload mr-1.5"></i> Upload Photo
                        <input type="file" wire:model="photo" class="hidden" accept="image/*">
                    </label>
                    @error('photo') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Room Summary Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-bed text-xs"></i></div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">🛏 Room Summary</h3>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Total Rooms</span>
                        <span class="font-black text-slate-800">{{ $rooms_total }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Available Rooms</span>
                        <span class="font-black text-green-600">{{ $rooms_available }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Occupied Rooms</span>
                        <span class="font-black text-indigo-600">{{ $rooms_occupied }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Reserved Rooms</span>
                        <span class="font-black text-orange-600">{{ $rooms_reserved }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Maintenance Rooms</span>
                        <span class="font-black text-red-600">{{ $rooms_maintenance }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Floors</span>
                        <span class="font-black text-slate-800">{{ $rooms_floors }}</span>
                    </div>
                    <div class="pt-2">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider mb-1">Room Types</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach(explode(', ', $room_types_list) as $type)
                            <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold">{{ $type }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-history text-xs"></i></div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">📜 Recent Activity</h3>
                </div>
                <div class="space-y-3">
                    @foreach($recent_activities as $activity)
                    <div class="flex gap-2.5 items-start text-xs">
                        <div class="w-5 h-5 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-500 shrink-0 mt-0.5 border border-indigo-100">
                            <i class="fas fa-check text-[8px]"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-700 leading-tight">{{ $activity['action'] }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5 leading-snug">{{ $activity['description'] }}</p>
                            <span class="text-[9px] text-slate-400 font-medium block mt-0.5">{{ $activity['time'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Forms, Statistics, Security --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Personal Information Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-user-cog text-xs"></i></div>
                        <h3 class="text-sm font-bold text-slate-800">Personal Information</h3>
                    </div>
                    @if(!$editPersonal)
                    <button wire:click="$set('editPersonal', true)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 cursor-pointer">
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
                    <button wire:click="savePersonal" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer shadow-sm">Save Changes</button>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Full Name</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $user_name }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-55">
                        <span class="text-slate-400 font-medium">Username</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $user_username }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Email Address</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $user_email }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Mobile</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $user_phone }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Employee ID</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $user_employee_id }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Role</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $user_role }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50 col-span-2">
                        <span class="text-slate-400 font-medium">Last Login</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $user_last_login }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Hotel Information Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-hotel text-xs"></i></div>
                        <h3 class="text-sm font-bold text-slate-800">🏨 Hotel Information</h3>
                    </div>
                    @if(!$editHotel)
                    <button wire:click="$set('editHotel', true)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 cursor-pointer">
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
                    <button wire:click="saveHotel" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer shadow-sm">Save Changes</button>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Hotel Name</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_name }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Hotel Code</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_code }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Hotel Type</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_type }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Star Rating</span>
                        <span class="font-bold text-slate-700 mt-0.5">
                            {{ str_repeat('⭐', intval($hotel_rating)) }}
                        </span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Owner</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_owner }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Hotel Email</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_email }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Phone</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_phone }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Website</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_website }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">GST Number</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_gst_no }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Check-In / Check-Out</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_checkin_time }} / {{ $hotel_checkout_time }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Currency</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_currency }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Time Zone</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_timezone }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Address Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-map-marker-alt text-xs"></i></div>
                        <h3 class="text-sm font-bold text-slate-800">📍 Address</h3>
                    </div>
                    @if(!$editAddress)
                    <button wire:click="$set('editAddress', true)" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 cursor-pointer">
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
                    <button wire:click="saveAddress" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer shadow-sm">Save Changes</button>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="flex flex-col py-1.5 border-b border-slate-50 sm:col-span-2">
                        <span class="text-slate-400 font-medium">Address</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_address }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">City</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_city }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">State</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_state }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Country</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_country }}</span>
                    </div>
                    <div class="flex flex-col py-1.5 border-b border-slate-50">
                        <span class="text-slate-400 font-medium">Pincode</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $hotel_pincode }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Hotel Gallery Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                            <i class="fas fa-images text-xs"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">📸 Hotel Gallery & Photos</h3>
                            <p class="text-[10px] text-slate-400 font-medium">Upload property photos (facade, lobby, rooms, etc.)</p>
                        </div>
                    </div>
                    <div>
                        <label class="btn-primary text-xs font-bold rounded-lg py-1.5 px-3 cursor-pointer shadow-sm flex items-center gap-1.5 hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-plus"></i> Add Photos
                            <input type="file" wire:model="gallery_photos" class="hidden" multiple accept="image/*">
                        </label>
                    </div>
                </div>

                @if(count($gallery_images) === 0)
                    {{-- Empty State --}}
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 flex flex-col items-center justify-center text-center space-y-3 bg-slate-50/50">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center border border-slate-100 shadow-sm text-slate-400">
                            <i class="fas fa-camera text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700">No hotel photos uploaded yet</p>
                            <p class="text-[10px] text-slate-400 max-w-[280px] mt-1 mx-auto">Upload high-quality images of your rooms, bathroom, facade, lobby, and amenities to show to guests.</p>
                        </div>
                        <div>
                            <label class="btn-primary text-xs font-bold rounded-lg py-2 px-4 cursor-pointer shadow-sm inline-block hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-upload mr-1.5"></i> Select Photos
                                <input type="file" wire:model="gallery_photos" class="hidden" multiple accept="image/*">
                            </label>
                        </div>
                        <div wire:loading wire:target="gallery_photos" class="text-xs text-indigo-600 font-medium">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Uploading photos...
                        </div>
                    </div>
                @else
                    {{-- Dynamic Gallery showing ONLY uploaded/selected images --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($gallery_images as $img)
                                <div class="relative group rounded-2xl overflow-hidden border {{ $img['is_primary'] ? 'border-indigo-500 ring-2 ring-indigo-500/20' : 'border-slate-100' }} shadow-sm bg-white flex flex-col">
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
                                                <button wire:click="editImage({{ $img['id'] }})" title="Edit Title & Description" class="w-7 h-7 rounded-lg bg-white/90 text-slate-700 hover:bg-white flex items-center justify-center transition-colors cursor-pointer shadow">
                                                    <i class="fas fa-pencil-alt text-xs"></i>
                                                </button>
                                                <button wire:click="deleteImage({{ $img['id'] }})" wire:confirm="Are you sure you want to delete this photo?" title="Delete Photo" class="w-7 h-7 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors cursor-pointer shadow">
                                                    <i class="fas fa-trash-alt text-xs"></i>
                                                </button>
                                            </div>
                                            
                                            <div class="flex items-center justify-between">
                                                @if(!$img['is_primary'])
                                                    <button wire:click="setPrimaryImage({{ $img['id'] }})" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg shadow cursor-pointer flex items-center gap-1 transition-colors">
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
                                                    <button wire:click="cancelEditImage" class="px-2 py-1 text-[10px] font-bold border border-slate-200 text-slate-600 rounded hover:bg-slate-100">Cancel</button>
                                                    <button wire:click="saveImageDetails" class="px-2.5 py-1 text-[10px] font-bold bg-indigo-600 text-white rounded hover:bg-indigo-700 shadow-sm">Save</button>
                                                </div>
                                            </div>
                                        @else
                                            <div>
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-xs font-bold text-slate-800 truncate">
                                                        {{ $img['title'] ?: 'Hotel Image' }}
                                                    </h4>
                                                    <button wire:click="editImage({{ $img['id'] }})" class="text-[10px] text-indigo-600 font-bold hover:underline cursor-pointer">
                                                        <i class="fas fa-edit mr-0.5"></i> Edit Description
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

                        <div wire:loading wire:target="gallery_photos" class="text-xs text-indigo-600 font-medium pt-1">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Uploading photos...
                        </div>
                    </div>
                @endif
            </div>

            {{-- Hotel Statistics Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-chart-line text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">📊 Hotel Statistics</h3>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100/50 flex flex-col justify-center">
                        <span class="text-slate-400 font-semibold text-[10px] uppercase">Today's Check-In</span>
                        <span class="text-lg font-black text-slate-800 mt-1">{{ sprintf("%02d", $stats_checkins_today) }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100/50 flex flex-col justify-center">
                        <span class="text-slate-400 font-semibold text-[10px] uppercase">Today's Check-Out</span>
                        <span class="text-lg font-black text-slate-800 mt-1">{{ sprintf("%02d", $stats_checkouts_today) }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100/50 flex flex-col justify-center">
                        <span class="text-slate-400 font-semibold text-[10px] uppercase">Guests Staying</span>
                        <span class="text-lg font-black text-slate-800 mt-1">{{ sprintf("%02d", $stats_guests_staying) }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100/50 flex flex-col justify-center">
                        <span class="text-slate-400 font-semibold text-[10px] uppercase">Occupancy</span>
                        <span class="text-lg font-black text-indigo-600 mt-1">{{ $stats_occupancy }}%</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100/50 flex flex-col justify-center">
                        <span class="text-slate-400 font-semibold text-[10px] uppercase">Today's Revenue</span>
                        <span class="text-lg font-black text-green-600 mt-1">{{ $stats_revenue_today }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100/50 flex flex-col justify-center">
                        <span class="text-slate-400 font-semibold text-[10px] uppercase">Monthly Revenue</span>
                        <span class="text-lg font-black text-purple-600 mt-1">{{ $stats_revenue_month }}</span>
                    </div>
                </div>
            </div>

            {{-- Security Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-shield-alt text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">🔐 Security</h3>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs border-b border-slate-50 pb-4">
                    <div class="flex flex-col py-1 border-b border-slate-50/50">
                        <span class="text-slate-400 font-medium">Username</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ $user_username }}</span>
                    </div>
                    <div class="flex flex-col py-1 border-b border-slate-50/50">
                        <span class="text-slate-400 font-medium">Password</span>
                        <span class="font-bold text-slate-700 mt-0.5">••••••••</span>
                    </div>
                    <div class="flex flex-col py-1 border-b border-slate-50/50">
                        <span class="text-slate-400 font-medium">Two Factor Auth</span>
                        <span class="font-bold text-red-500 mt-0.5">Disabled</span>
                    </div>
                    <div class="flex flex-col py-1 border-b border-slate-50/50">
                        <span class="text-slate-400 font-medium">Active Devices</span>
                        <span class="font-bold text-slate-700 mt-0.5">2</span>
                    </div>
                    <div class="flex flex-col py-1 border-b border-slate-50/50 col-span-2">
                        <span class="text-slate-400 font-medium">Last Login IP</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ request()->ip() }}</span>
                    </div>
                </div>

                {{-- Password Expandable Form --}}
                <div x-data="{ open: {{ $errors->has('current_password') ? 'true' : 'false' }} }" class="space-y-4">
                    <div class="flex justify-between items-center">
                        <button @click="open = !open" type="button" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1 cursor-pointer">
                            <i class="fas fa-key text-[10px]"></i> Change Password
                        </button>
                        <button wire:click="logoutAllDevices" class="px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 rounded-lg text-xs font-bold transition-colors cursor-pointer">
                            Logout All Devices
                        </button>
                    </div>

                    <div x-show="open" x-transition class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-3">
                        <h4 class="text-xs font-bold text-slate-700 uppercase">Change Account Password</h4>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="pms-label text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Current Password</label>
                                <input type="password" wire:model="current_password" class="pms-input text-xs" placeholder="••••••••">
                                @error('current_password') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="pms-label text-[10px] font-semibold text-slate-500 uppercase tracking-wider">New Password</label>
                                <input type="password" wire:model="new_password" class="pms-input text-xs" placeholder="••••••••">
                                @error('new_password') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="pms-label text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Confirm New Password</label>
                                <input type="password" wire:model="new_password_confirmation" class="pms-input text-xs" placeholder="••••••••">
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button @click="open = false" type="button" class="px-3 py-1.5 border border-slate-200 text-slate-600 rounded-lg text-[10px] font-bold hover:bg-slate-100 cursor-pointer">Cancel</button>
                            <button wire:click="updatePassword" class="btn-primary text-[10px] font-bold rounded-lg py-1.5 px-3 cursor-pointer shadow-sm">Update Password</button>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
