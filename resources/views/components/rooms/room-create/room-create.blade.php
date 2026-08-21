<div>
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('rooms.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Add Room & Tariff Setup</h1>
                <p class="text-sm text-gray-500 mt-0.5">Single form to configure room number, room type, tariff rates, and inventory status</p>
            </div>
        </div>
    </div>

    {{-- Single Unified Form Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80 p-6 mb-8">
        <form wire:submit.prevent="saveRoom" class="space-y-6">
            {{-- Section 1: Room Identity & Configuration (Room Number, Floor, Status, Bed Type) --}}
            <div class="space-y-5">
                <div class="flex items-center gap-2 mb-2 pb-3 border-b border-slate-50">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                        <i class="fas fa-door-open text-xs"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">1. Room Identity & Configuration</h4>
                        <p class="text-[11px] text-slate-400">Set room number, floor assignment, bed type, and status</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Room Number(s) <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="room_number" class="pms-input text-sm font-extrabold text-slate-800" placeholder="e.g. 101, 102, 201-205">
                        <p class="text-[10px] text-slate-400 mt-1">Single room or comma/range (e.g. 101, 102-105)</p>
                        @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Floor <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="floor" class="pms-input text-sm font-semibold" placeholder="e.g. 1">
                        <p class="text-[10px] text-slate-400 mt-1">Default floor number</p>
                        @error('floor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Room Status <span class="text-red-500">*</span></label>
                        <select wire:model="status" class="pms-input text-sm font-bold">
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Reserved">Reserved</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3">
                    <div class="space-y-4">
                        <div>
                            <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Bed Type <span class="text-red-500">*</span></label>
                            <select wire:model.live="bed_type" class="pms-input text-sm font-bold text-slate-800">
                                @foreach($this->bedTypes as $bedType)
                                    <option value="{{ $bedType }}">{{ $bedType }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Max Guests (Capacity) <span class="text-red-500">*</span></label>
                            <select wire:model="capacity" class="pms-input text-sm font-bold text-slate-800">
                                <option value="1">1 Guest</option>
                                <option value="2">2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="5">5 Guests</option>
                                <option value="6">6 Guests</option>
                                <option value="7">7 Guests</option>
                                <option value="8">8 Guests</option>
                                <option value="10">10 Guests</option>
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1">Maximum allowed guests for this room</p>
                            @error('capacity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider mb-0">Room Option / Feature</label>
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


                {{-- Tariff Rates (Daily, Weekly, Monthly, Tax %) --}}
                <div class="pt-4 border-t border-slate-100">
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
            </div>

            {{-- Section 2: Room Media & Overview (Photos & Description) --}}
            <div class="pt-6 border-t border-slate-100 space-y-5">
                <div class="flex items-center gap-2 mb-2 pb-3 border-b border-slate-50">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                        <i class="fas fa-images text-xs"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">2. Room Media & Overview</h4>
                        <p class="text-[11px] text-slate-400">Add room photos, select main thumbnail, and provide room description</p>
                    </div>
                </div>

                {{-- Room Photos Section --}}
                <div class="space-y-3">
                    <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider block">
                        <i class="fas fa-camera mr-1 text-indigo-500"></i> Room Photos / Image Gallery (Select Multiple)
                    </label>

                    <div class="border-2 border-dashed border-indigo-100 hover:border-indigo-300 bg-indigo-50/40 rounded-2xl p-4 transition-all">
                        <label class="block text-xs font-bold text-indigo-900 mb-1">
                            <i class="fas fa-cloud-upload-alt text-indigo-600 mr-1"></i> Upload Image Files (Multiple)
                        </label>
                        <input type="file" wire:model="photos" multiple accept="image/*" class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Select one or multiple photos from your device (JPG, PNG, WEBP).</p>
                        @error('photos.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="photos" class="text-xs text-indigo-600 font-bold mt-1">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Uploading photos...
                        </div>
                    </div>

                    @php
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
                    @endphp

                    @if(count($uploadedImages) > 0)
                    <div class="mt-3 bg-white p-3.5 rounded-2xl border border-slate-200 shadow-2xs">
                        <p class="text-[11px] font-extrabold text-slate-700 mb-2.5 flex items-center justify-between">
                            <span><i class="fas fa-eye text-emerald-500 mr-1"></i> Selected Gallery Photos Preview ({{ count($uploadedImages) }} photo{{ count($uploadedImages) > 1 ? 's' : '' }})</span>
                            <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">First photo will be main thumbnail</span>
                        </p>
                        <div class="flex flex-wrap gap-3">
                            @foreach($uploadedImages as $idx => $img)
                            <div class="relative w-24 h-20 rounded-xl overflow-hidden border-2 {{ $idx === 0 ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-slate-200' }} shadow-sm group">
                                <img src="{{ $img['src'] }}" class="w-full h-full object-cover">
                                @if($idx === 0)
                                <span class="absolute top-1 left-1 bg-indigo-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow">Main</span>
                                @endif
                                <button type="button" wire:click="removeUploadedImage({{ $img['index'] }})" class="absolute top-1 right-1 bg-rose-600 hover:bg-rose-700 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center shadow opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                    <i class="fas fa-times text-[8px]"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Room Description Section --}}
                <div class="space-y-1.5">
                    <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider block">
                        <i class="fas fa-align-left mr-1 text-indigo-500"></i> Room Description
                    </label>
                    <textarea wire:model="description" rows="3" class="pms-input text-sm font-medium" placeholder="Enter detailed room description, features, amenities, or notes..."></textarea>
                    <p class="text-[10px] text-slate-400">Provide an overview of the room features, view, and unique guest experience.</p>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Form Submit Button --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('rooms.index') }}" class="btn-secondary text-xs rounded-xl py-2.5 px-5 font-bold">Cancel</a>
                <button type="submit" wire:loading.attr="disabled" class="btn-primary text-xs font-extrabold rounded-xl py-2.5 px-7 cursor-pointer shadow-md flex items-center gap-2">
                    <i class="fas fa-plus text-xs"></i> Save & Add Room
                </button>
            </div>
        </form>
    </div>

    {{-- Registered Rooms Inventory Table --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                    <i class="fas fa-list-alt text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Rooms & Tariff Directory</h3>
                    <p class="text-[10px] text-slate-400">All registered hotel rooms with assigned rate plans</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                {{ $rooms->count() }} total rooms
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Photo</th>
                        <th class="font-bold">Room No.</th>
                        <th class="font-bold">Bed Type</th>
                        <th class="font-bold">Guests</th>
                        <th class="font-bold">Room Option / Feature</th>
                        <th class="font-bold">Daily Rate</th>
                        <th class="font-bold">Weekly Rate</th>
                        <th class="font-bold">Monthly Rate</th>
                        <th class="font-bold">Tax Rate</th>
                        <th class="font-bold">Floor</th>
                        <th class="font-bold">Status</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rooms as $r)
                    <tr wire:key="room-row-{{ $r->id }}" class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <div class="flex items-center gap-1.5">
                                <img src="{{ $r->image_url }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=400&q=80';" alt="Room {{ $r->room_number }}" class="w-12 h-9 object-cover rounded-lg border border-slate-200 shadow-sm shrink-0">
                                @php $imgs = $r->images; @endphp
                                @if(count($imgs) > 1)
                                <span class="px-1.5 py-0.5 text-[10px] font-black text-indigo-700 bg-indigo-50 border border-indigo-100 rounded-md shrink-0" title="{{ count($imgs) }} total photos">
                                    +{{ count($imgs) - 1 }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="font-black text-slate-800 text-sm bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">Room {{ $r->room_number }}</span>
                        </td>
                        <td>
                            <span class="text-xs font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200 shadow-2xs whitespace-nowrap">{{ $r->bed_type ?: 'King Bed' }}</span>
                        </td>
                        <td>
                            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100/80 whitespace-nowrap flex items-center gap-1 w-max">
                                <i class="fas fa-user-friends text-[10px] text-indigo-500"></i>{{ $r->capacity }} {{ Str::plural('Guest', $r->capacity) }}
                            </span>
                        </td>
                        <td>
                            @if(!empty($r->room_option))
                                <div class="flex flex-wrap items-center gap-1 max-w-xs">
                                    @foreach(explode(',', $r->room_option) as $opt)
                                        @if(trim($opt))
                                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100/80">
                                                <i class="fas fa-check-circle text-[9px] mr-1 text-indigo-500"></i>{{ trim($opt) }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-slate-400 font-medium">—</span>
                            @endif
                        </td>
                        <td class="font-bold text-slate-700 text-xs">
                            ${{ number_format($r->roomType->daily_rate ?? $r->price, 2) }}
                        </td>
                        <td class="font-semibold text-slate-600 text-xs">
                            ${{ number_format($r->roomType->weekly_rate ?? 0, 2) }}
                        </td>
                        <td class="font-semibold text-slate-600 text-xs">
                            ${{ number_format($r->roomType->monthly_rate ?? 0, 2) }}
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                {{ number_format($r->roomType->tax_percent ?? 15, 1) }}%
                            </span>
                        </td>
                        <td>
                            <span class="text-xs font-semibold text-slate-600 bg-slate-50 border border-slate-150 px-2 py-0.5 rounded">{{ $r->floor ?? '1' }}</span>
                        </td>
                        <td>
                            @php 
                                $st = $r->status; 
                                $bClass = match($st) {
                                    'Available' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'Occupied' => 'bg-red-50 text-red-700 border-red-100',
                                    'Reserved' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'Maintenance' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-100',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $bClass }}">
                                {{ $st }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('rooms.edit', $r->id) }}" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 shadow-sm" title="Edit Room">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <button wire:click="deleteRoom({{ $r->id }})" wire:confirm="Delete room {{ $r->room_number }}?" class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 shadow-sm cursor-pointer" title="Delete Room">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <i class="fas fa-door-closed text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">No rooms added yet. Fill form above to add your first room.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
