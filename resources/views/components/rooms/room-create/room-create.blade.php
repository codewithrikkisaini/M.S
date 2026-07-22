<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('rooms.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Add Room</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create a new room in the hotel inventory</p>
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Form Panel --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-6">
                <div class="flex items-center gap-2 mb-5 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-plus text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Room Details</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room Number <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="room_number" class="pms-input text-xs" placeholder="e.g. 101">
                        @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Floor <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="floor" class="pms-input text-xs" placeholder="e.g. 1">
                        <p class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1"><i class="fas fa-lightbulb text-indigo-400"></i> Floor is suggested automatically from first digit of room number.</p>
                        @error('floor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room Type <span class="text-red-500">*</span></label>
                            <a href="{{ route('rooms.types') }}" class="text-[11px] font-bold text-indigo-600 hover:underline mb-1">
                                <i class="fas fa-cog text-[9px]"></i> Manage Types
                            </a>
                        </div>
                        <select wire:model="room_type_id" class="pms-select text-xs">
                            <option value="">Select type...</option>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">The nightly room price will auto-populate from the selected room type.</p>
                        @error('room_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Price per Night ($) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="price" class="pms-input text-xs" placeholder="0.00" min="0" step="0.01">
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Status <span class="text-red-500">*</span></label>
                        <select wire:model="status" class="pms-select text-xs">
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Reserved">Reserved</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 mt-6">
                    <a href="{{ route('rooms.index') }}" class="btn-secondary text-xs rounded-lg py-2 font-bold px-4">Cancel</a>
                    <button wire:click="save" wire:loading.attr="disabled" class="btn-primary text-xs rounded-lg py-2 font-bold px-4 cursor-pointer shadow-sm">
                        <span wire:loading wire:target="save" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                        Create Room
                    </button>
                </div>
            </div>
        </div>

        {{-- Right Info Panel --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-info-circle text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Room Guidelines</h3>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Naming Standard</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Rooms are usually named numerically corresponding to their floor (e.g., Room 104 is on Floor 1, Room 305 is on Floor 3).</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Housekeeping Status</h4>
                        <ul class="text-xs text-slate-500 space-y-1.5">
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> <strong>Clean:</strong> Ready for guest check-in</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span> <strong>Dirty:</strong> Needs maid attendance</li>
                            <li class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> <strong>Maintenance:</strong> Out of service</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Reservation States</h4>
                        <ul class="text-xs text-slate-500 space-y-1.5">
                            <li class="flex items-center gap-2"><span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Available</span> Room is empty & clean</li>
                            <li class="flex items-center gap-2"><span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-100">Occupied</span> Guest is in room</li>
                            <li class="flex items-center gap-2"><span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-100">Reserved</span> Room is booked for stay</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
