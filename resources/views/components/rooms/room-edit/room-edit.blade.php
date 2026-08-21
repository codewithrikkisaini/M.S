<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('rooms.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Room</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update details for room {{ $room->room_number }}</p>
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Form Panel --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-6">
                {{-- Section 1: Room Media & Overview (Main Images & Description) --}}
                <div class="flex items-center gap-2 mb-5 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                        <i class="fas fa-images text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">1. Room Media & Overview</h3>
                        <p class="text-[11px] text-slate-400">Manage room photos, main thumbnail, and description</p>
                    </div>
                </div>

                <div class="space-y-5">
                    {{-- Room Multiple Images Section --}}
                    <div class="space-y-3">
                        <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider block">
                            <i class="fas fa-camera mr-1 text-indigo-500"></i> Room Photos / Image Gallery (Select Multiple)
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Option A: Select / Upload Multiple Image Files --}}
                            <div class="border-2 border-dashed border-indigo-100 hover:border-indigo-300 bg-indigo-50/40 rounded-2xl p-4 transition-all">
                                <label class="block text-xs font-bold text-indigo-900 mb-1">
                                    <i class="fas fa-cloud-upload-alt text-indigo-600 mr-1"></i> Upload Image Files (Multiple)
                                </label>
                                <input type="file" wire:model="photos" multiple accept="image/*" class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                                <p class="text-[10px] text-slate-400 mt-1">Select one or multiple photos to add to this room.</p>
                                @error('photos.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                <div wire:loading wire:target="photos" class="text-xs text-indigo-600 font-bold mt-1">
                                    <i class="fas fa-spinner fa-spin mr-1"></i> Uploading photos...
                                </div>
                            </div>

                            {{-- Option B: Image URLs --}}
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    <i class="fas fa-link text-blue-500 mr-1"></i> Paste Image URLs (One per line or comma-separated)
                                </label>
                                <textarea wire:model.live.debounce.300ms="image_path" rows="2" class="pms-input text-xs font-medium" placeholder="https://images.unsplash.com/photo-1...&#10;https://images.unsplash.com/photo-2..."></textarea>
                                <p class="text-[10px] text-slate-400 mt-1">Paste external image URLs or web links for this room.</p>
                                @error('image_path') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Live Gallery Preview --}}
                        @php
                            $existingImages = [];
                            if (!empty($image_path)) {
                                $urlList = preg_split('/[\r\n,]+/', $image_path);
                                foreach ($urlList as $u) {
                                    $u = trim($u);
                                    if ($u !== '') {
                                        $existingImages[] = [
                                            'src' => \App\Models\Room::formatUrl($u),
                                            'path' => $u
                                        ];
                                    }
                                }
                            }
                            $uploadedImages = [];
                            if (!empty($photos)) {
                                $photoList = is_array($photos) ? $photos : [$photos];
                                foreach ($photoList as $pIdx => $p) {
                                    if ($p && is_object($p) && method_exists($p, 'temporaryUrl')) {
                                        try {
                                            $uploadedImages[] = [
                                                'src' => $p->temporaryUrl(),
                                                'index' => $pIdx
                                            ];
                                        } catch (\Exception $e) {}
                                    }
                                }
                            }

                            // Fallback to room images if no changes are made and both inputs are empty
                            if (empty($existingImages) && empty($uploadedImages) && !empty($room->images)) {
                                foreach ($room->images as $imgUrl) {
                                    $existingImages[] = [
                                        'src' => $imgUrl,
                                        'path' => $imgUrl
                                    ];
                                }
                            }
                        @endphp

                        @if(count($existingImages) > 0 || count($uploadedImages) > 0)
                        <div class="mt-3 bg-white p-3.5 rounded-2xl border border-slate-200 shadow-2xs">
                            <p class="text-[11px] font-extrabold text-slate-700 mb-2.5 flex items-center justify-between">
                                <span><i class="fas fa-eye text-emerald-500 mr-1"></i> Current / Selected Room Gallery ({{ count($existingImages) + count($uploadedImages) }} photo{{ (count($existingImages) + count($uploadedImages)) > 1 ? 's' : '' }})</span>
                                <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">First photo is main thumbnail</span>
                            </p>
                            <div class="flex flex-wrap gap-3">
                                {{-- Render Existing Images --}}
                                @foreach($existingImages as $idx => $img)
                                <div class="relative w-24 h-20 rounded-xl overflow-hidden border-2 {{ $idx === 0 && count($uploadedImages) === 0 ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-slate-200' }} shadow-sm group">
                                    <img src="{{ $img['src'] }}" class="w-full h-full object-cover">
                                    @if($idx === 0 && count($uploadedImages) === 0)
                                    <span class="absolute top-1 left-1 bg-indigo-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow">Main</span>
                                    @endif
                                    {{-- Delete Button --}}
                                    <button type="button" wire:click="removeExistingImage({{ $idx }})" class="absolute top-1 right-1 bg-rose-600 hover:bg-rose-700 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center shadow opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                        <i class="fas fa-times text-[8px]"></i>
                                    </button>
                                </div>
                                @endforeach

                                {{-- Render Newly Uploaded Images --}}
                                @foreach($uploadedImages as $idx => $img)
                                <div class="relative w-24 h-20 rounded-xl overflow-hidden border-2 {{ $idx === 0 ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-slate-200' }} shadow-sm group">
                                    <img src="{{ $img['src'] }}" class="w-full h-full object-cover">
                                    @if($idx === 0)
                                    <span class="absolute top-1 left-1 bg-indigo-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow">Main</span>
                                    @endif
                                    {{-- Delete Button --}}
                                    <button type="button" wire:click="removeUploadedImage({{ $img['index'] }})" class="absolute top-1 right-1 bg-rose-600 hover:bg-rose-700 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center shadow opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                        <i class="fas fa-times text-[8px]"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Room Description --}}
                    <div>
                        <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">
                            <i class="fas fa-align-left mr-1 text-indigo-500"></i> Room Description
                        </label>
                        <textarea wire:model="description" rows="3" class="pms-input text-xs font-medium" placeholder="Enter room description, special features, amenities, or notes..."></textarea>
                        <p class="text-[10px] text-slate-400 mt-1">Provide an overview of the room highlights, view, and unique characteristics.</p>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Section 2: Room Details & Specifications --}}
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                            <i class="fas fa-door-open text-xs"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">2. Room Specifications & Pricing</h3>
                            <p class="text-[11px] text-slate-400">Configure room number, tariff category, bed configuration, and status</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room Number <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.live="room_number" class="pms-input text-xs font-bold text-slate-800" placeholder="e.g. 101">
                            @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Floor <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="floor" class="pms-input text-xs" placeholder="e.g. 1">
                            <p class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1"><i class="fas fa-lightbulb text-indigo-400"></i> Floor is suggested automatically from first digit of room number.</p>
                            @error('floor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Bed Type --}}
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Bed Type <span class="text-red-500">*</span></label>
                            <select wire:model.live="bed_type" class="pms-select text-xs font-bold text-slate-800">
                                @foreach($this->bedTypes as $bedType)
                                    <option value="{{ $bedType }}">{{ $bedType }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Status <span class="text-red-500">*</span></label>
                            <select wire:model="status" class="pms-select text-xs font-bold">
                                <option value="Available">Available</option>
                                <option value="Occupied">Occupied</option>
                                <option value="Reserved">Reserved</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tariff Rates (Daily, Weekly, Monthly, Tax %) --}}
                        <div class="sm:col-span-2 pt-4 border-t border-slate-100">
                            <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 block">Room Tariff Rates & Tax</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div>
                                    <label class="text-[11px] font-bold text-slate-500 uppercase">Daily Rate ($) <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" wire:model.live.debounce.300ms="daily_rate" class="pms-input text-sm font-bold text-slate-800" placeholder="0.00">
                                    @error('daily_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold text-slate-500 uppercase">Weekly Rate ($)</label>
                                    <input type="number" step="0.01" wire:model.live="weekly_rate" class="pms-input text-sm font-bold text-slate-800" placeholder="0.00">
                                    @error('weekly_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold text-slate-500 uppercase">Monthly Rate ($)</label>
                                    <input type="number" step="0.01" wire:model.live="monthly_rate" class="pms-input text-sm font-bold text-slate-800" placeholder="0.00">
                                    @error('monthly_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-[11px] font-bold text-slate-500 uppercase">Tax Rate (%)</label>
                                    <input type="number" step="0.01" wire:model.live="tax_percent" class="pms-input text-sm font-bold text-indigo-700 bg-indigo-50/50" placeholder="0">
                                    @error('tax_percent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider mb-0">Room Option / Feature</label>
                                @if(!empty($room_option) && count($room_option) > 0)
                                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100 flex items-center gap-1">
                                    <i class="fas fa-check-circle text-indigo-500"></i> Multiple Select ({{ count($room_option) }} selected)
                                </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50/80 p-3 rounded-2xl border border-slate-200/80 max-h-48 overflow-y-auto">
                                @foreach($this->availableOptions as $opt)
                                    <label class="flex items-center gap-2.5 p-2 rounded-xl bg-white border border-slate-200/80 hover:border-indigo-300 hover:bg-indigo-50/40 cursor-pointer transition-all shadow-2xs group">
                                        <input type="checkbox" wire:model.live="room_option" value="{{ $opt }}" class="w-4 h-4 text-indigo-600 rounded-md border-slate-300 focus:ring-indigo-500 focus:ring-offset-0 transition-colors">
                                        <span class="text-xs font-bold text-slate-700 group-hover:text-indigo-900 transition-colors leading-tight">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-100 mt-6">
                    <a href="{{ route('rooms.index') }}" class="btn-secondary text-xs rounded-lg py-2 font-bold px-4">Cancel</a>
                    <button wire:click="save" wire:loading.attr="disabled" class="btn-primary text-xs rounded-lg py-2 font-bold px-4 cursor-pointer shadow-sm">
                        <span wire:loading wire:target="save" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                        Save Changes
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
