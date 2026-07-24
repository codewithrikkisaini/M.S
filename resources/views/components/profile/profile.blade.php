<div wire:poll.visible.60s="loadData" class="space-y-6">
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
                            <img src="{{ \Illuminate\Support\Str::startsWith($profile_photo_path, ['http://', 'https://']) ? $profile_photo_path : asset('storage/' . $profile_photo_path) }}" class="w-full h-full object-cover">
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

                <div class="w-full pt-2 flex flex-col gap-2">
                    <label class="btn-primary text-xs font-bold rounded-lg py-2.5 cursor-pointer shadow-sm text-center block w-full hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-upload mr-1.5"></i> Upload Photo
                        <input type="file" wire:model="photo" class="hidden" accept="image/*">
                    </label>
                    <button type="button" wire:click="setRandomProfilePhoto" class="btn-secondary text-xs font-bold rounded-lg py-2 text-center block w-full hover:bg-slate-100 transition-colors">
                        <i class="fas fa-dice mr-1.5 text-indigo-600"></i> Random Avatar
                    </button>
                    @error('photo') <p class="text-red-500 text-[10px] mt-1 text-center">{{ $message }}</p> @enderror
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
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" wire:click="addRandomGalleryPhoto" class="btn-secondary text-xs font-bold rounded-lg py-1.5 px-3 shadow-sm flex items-center gap-1.5 hover:bg-slate-100 transition-colors cursor-pointer">
                            <i class="fas fa-dice text-indigo-600"></i> Add Random Photo
                        </button>
                        <label class="btn-primary text-xs font-bold rounded-lg py-1.5 px-3 cursor-pointer shadow-sm flex items-center gap-1.5 hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-plus"></i> Upload Photos
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
                        <div class="flex flex-wrap justify-center gap-2">
                            <button type="button" wire:click="addRandomGalleryPhoto" class="btn-secondary text-xs font-bold rounded-lg py-2 px-4 shadow-sm inline-block hover:bg-slate-100 transition-colors cursor-pointer">
                                <i class="fas fa-dice text-indigo-600 mr-1.5"></i> Add Random Photo
                            </button>
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
                    {{-- Grid Layout mimicking user mockup --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            
                            {{-- Main Large Photo (Facade / Primary) --}}
                            @php
                                $primary = collect($gallery_images)->firstWhere('is_primary', true) ?: $gallery_images[0];
                                $otherImages = collect($gallery_images)->reject(fn($img) => $img['id'] === $primary['id'])->values()->all();
                                $rightImages = array_slice($otherImages, 0, 2);
                                $bottomImages = array_slice($otherImages, 2);
                            @endphp

                            {{-- Left Column: Main Big Photo --}}
                            <div class="md:col-span-2 relative group rounded-2xl overflow-hidden border border-slate-100 shadow-sm aspect-[4/3] md:aspect-auto md:h-[350px]">
                                <img src="{{ $primary['image_url'] }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-white text-xs font-bold"><i class="fas fa-star text-yellow-400 mr-1"></i> Primary Cover</span>
                                        <button wire:click="deleteImage({{ $primary['id'] }})" wire:confirm="Are you sure you want to delete this photo?" class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition-colors cursor-pointer shadow">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded shadow">
                                    Primary
                                </div>
                            </div>

                            {{-- Right Column: 2 Stacked Photos --}}
                            <div class="md:col-span-1 flex flex-col gap-3 h-[350px]">
                                @for($i = 0; $i < 2; $i++)
                                    <div wire:key="hotel-gallery-right-{{ $i }}" class="flex-1 relative group rounded-2xl overflow-hidden border border-slate-100 shadow-sm min-h-[160px]">
                                        @if(isset($rightImages[$i]))
                                            <img src="{{ $rightImages[$i]['image_url'] }}" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                                                <div class="flex justify-between items-center">
                                                    <button wire:click="setPrimaryImage({{ $rightImages[$i]['id'] }})" class="text-white text-[10px] font-bold hover:underline flex items-center gap-1 cursor-pointer">
                                                        <i class="far fa-star"></i> Set Cover
                                                    </button>
                                                    <button wire:click="deleteImage({{ $rightImages[$i]['id'] }})" wire:confirm="Are you sure you want to delete this photo?" class="w-7 h-7 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition-colors cursor-pointer shadow">
                                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <div class="w-full h-full bg-slate-50 border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-300">
                                                <i class="fas fa-image text-xl mb-1"></i>
                                                <span class="text-[9px] font-semibold uppercase tracking-wider">Empty Slot</span>
                                            </div>
                                        @endif
                                    </div>
                                @endfor
                            </div>

                        </div>

                        {{-- Bottom Row: Responsive Thumbnails --}}
                        @if(count($bottomImages) > 0)
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                                @foreach(array_slice($bottomImages, 0, 5) as $index => $img)
                                    @php
                                        $isLastSlot = $index === 4 && count($bottomImages) > 5;
                                        $remainingCount = count($bottomImages) - 4;
                                    @endphp
                                    <div wire:key="hotel-gallery-thumb-{{ $img['id'] }}" class="aspect-square relative group rounded-xl overflow-hidden border border-slate-100 shadow-sm">
                                        <img src="{{ $img['image_url'] }}" class="w-full h-full object-cover">
                                        
                                        @if($isLastSlot)
                                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-black text-xs md:text-sm">
                                                +{{ $remainingCount }} Photos
                                            </div>
                                        @else
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-2">
                                                <div class="flex justify-between items-center">
                                                    <button wire:click="setPrimaryImage({{ $img['id'] }})" class="text-white text-[8px] font-bold hover:underline cursor-pointer">
                                                        Star
                                                    </button>
                                                    <button wire:click="deleteImage({{ $img['id'] }})" wire:confirm="Are you sure?" class="w-5 h-5 rounded-full bg-red-600 text-white flex items-center justify-center hover:bg-red-700 transition-colors cursor-pointer shadow">
                                                        <i class="fas fa-trash-alt text-[8px]"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div wire:loading wire:target="gallery_photos" class="text-xs text-indigo-600 font-medium pt-1">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Uploading photos...
                        </div>
                    </div>
                @endif
            </div>

            {{-- Hotel Statistics Card --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-chart-line text-xs"></i></div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">📊 Hotel Statistics</h3>
                            <p class="text-[10px] text-slate-400">Last refreshed: {{ $last_data_refresh ?: now()->format('d M Y h:i A') }}</p>
                        </div>
                    </div>
                    <button wire:click="loadData" class="btn-secondary text-xs font-bold rounded-lg py-2 px-3 shadow-sm hover:bg-slate-100 transition-colors">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh Data
                    </button>
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
                <div class="space-y-4 text-xs">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-slate-600">Occupancy Ratio</span>
                            <span class="font-bold text-slate-800">{{ $stats_occupancy }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-indigo-600" style="width: {{ $stats_occupancy }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-slate-600">Revenue Performance</span>
                            <span class="font-bold text-slate-800">{{ $stats_revenue_progress }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-emerald-500" style="width: {{ $stats_revenue_progress }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-slate-600">Monthly Revenue Goal</span>
                            <span class="font-bold text-slate-800">{{ $stats_monthly_revenue_progress }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-violet-500" style="width: {{ $stats_monthly_revenue_progress }}%"></div>
                        </div>
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
                        <span class="font-bold text-slate-700 mt-0.5">{{ $active_device_count }}</span>
                    </div>
                    <div class="flex flex-col py-1 border-b border-slate-50/50 col-span-2">
                        <span class="text-slate-400 font-medium">Last Login IP</span>
                        <span class="font-bold text-slate-700 mt-0.5">{{ request()->ip() }}</span>
                    </div>
                </div>
 
                {{-- Device list and logout control --}}
                <div class="space-y-4">
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Logged in devices</p>
                                <p class="text-[10px] text-slate-400">Current session is preserved. Use the button below to sign out from any other active sessions.</p>
                            </div>
                            <button type="button" wire:click="$toggle('show_logout_devices')" class="btn-secondary text-xs font-bold rounded-lg py-2 px-3 shadow-sm hover:bg-slate-100 transition-colors">
                                <i class="fas fa-sign-out-alt mr-1"></i> Logout All Devices
                            </button>
                        </div>
                        <div class="grid grid-cols-1 gap-2 text-xs">
                            @foreach($active_devices as $device)
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                <div class="flex justify-between items-center gap-2 mb-2">
                                    <span class="font-semibold text-slate-700">{{ $device['ip_address'] }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $device['last_seen'] }}</span>
                                </div>
                                <p class="text-[10px] text-slate-500 truncate">{{ $device['user_agent'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    @if($show_logout_devices)
                    <div class="rounded-xl border border-slate-100 bg-white p-4 text-xs space-y-3">
                        <div>
                            <label class="pms-label text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Current Password</label>
                            <input type="password" wire:model="current_password" class="pms-input text-xs" placeholder="Enter your current password">
                            @error('current_password') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" wire:click="$set('show_logout_devices', false)" class="px-3 py-1.5 border border-slate-200 text-slate-600 rounded-lg text-[10px] font-bold hover:bg-slate-100 cursor-pointer">Cancel</button>
                            <button type="button" wire:click="logoutAllDevices" class="btn-primary text-[10px] font-bold rounded-lg py-1.5 px-3 cursor-pointer shadow-sm">Confirm Logout</button>
                        </div>
                    </div>
                    @endif
                </div>

            </div>

        </div>

    </div>
</div>
