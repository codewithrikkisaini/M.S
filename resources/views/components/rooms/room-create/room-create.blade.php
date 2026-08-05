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
        <div class="flex items-center gap-2 mb-6 border-b border-slate-100 pb-3">
            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                <i class="fas fa-door-open text-base"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Room Details & Tariff Configuration</h3>
                <p class="text-xs text-slate-400">Fill details below to add room into inventory</p>
            </div>
        </div>

        <form wire:submit.prevent="saveRoom" class="space-y-6">
            {{-- Row 1: Room Number, Floor, Room Status --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Room Number <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.live="room_number" class="pms-input text-sm font-extrabold text-slate-800" placeholder="e.g. 101, 102, 201">
                    @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Floor <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="floor" class="pms-input text-sm font-semibold" placeholder="e.g. 1">
                    @error('floor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Room Status <span class="text-red-500">*</span></label>
                    <select wire:model="status" class="pms-input text-sm font-semibold">
                        <option value="Available">Available</option>
                        <option value="Occupied">Occupied</option>
                        <option value="Reserved">Reserved</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Room Multiple Images Section --}}
            <div class="pt-4 border-t border-slate-100 space-y-3">
                <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider block">
                    <i class="fas fa-images mr-1 text-indigo-500"></i> Room Photos / Image Gallery (Select Multiple)
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Option A: Select / Upload Multiple Image Files --}}
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

                {{-- Live Image Gallery Preview --}}
                @php
                    $previewImages = [];
                    if (!empty($photos)) {
                        foreach ($photos as $p) {
                            try { $previewImages[] = $p->temporaryUrl(); } catch (\Exception $e) {}
                        }
                    }
                    if (!empty($image_path)) {
                        $urlList = preg_split('/[\r\n,]+/', $image_path);
                        foreach ($urlList as $u) {
                            $u = trim($u);
                            if ($u !== '') {
                                $previewImages[] = filter_var($u, FILTER_VALIDATE_URL) ? $u : asset('storage/' . $u);
                            }
                        }
                    }
                @endphp

                @if(count($previewImages) > 0)
                <div class="mt-3 bg-white p-3 rounded-2xl border border-slate-200">
                    <p class="text-[11px] font-extrabold text-slate-700 mb-2 flex items-center justify-between">
                        <span><i class="fas fa-eye text-emerald-500 mr-1"></i> Selected Gallery Photos Preview ({{ count($previewImages) }} photo{{ count($previewImages) > 1 ? 's' : '' }})</span>
                        <span class="text-[10px] font-semibold text-slate-400">First photo will be main thumbnail</span>
                    </p>
                    <div class="flex flex-wrap gap-3">
                        @foreach($previewImages as $idx => $imgSrc)
                        <div class="relative w-24 h-20 rounded-xl overflow-hidden border border-slate-200 shadow-sm group">
                            <img src="{{ $imgSrc }}" class="w-full h-full object-cover">
                            @if($idx === 0)
                            <span class="absolute top-1 left-1 bg-indigo-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow">Main</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Row 2: Room Type Selector & Custom Name --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-4 border-t border-slate-100">
                <div>
                    <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Room Type Category <span class="text-red-500">*</span></label>
                    <select wire:model.live="room_type_select" class="pms-input text-sm font-bold text-slate-800 bg-slate-50 border-slate-200">
                        <option value="Single">Single Room (Daily: $59.95 | Weekly: $249.90 | Monthly: $990.00)</option>
                        <option value="Double">Double Room (Daily: $79.95 | Weekly: $349.90 | Monthly: $1190.00)</option>
                        <option value="Apartment">Apartment Suite (Daily: $79.90 | Weekly: $349.90 | Monthly: $1349.00)</option>
                        <option value="custom">+ Add Custom Room Type...</option>
                    </select>
                </div>

                @if($is_custom_type)
                <div>
                    <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Custom Room Type Name <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="room_type_name" class="pms-input text-sm font-bold" placeholder="e.g. Deluxe Suite, Family Room">
                    @error('room_type_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @else
                <div>
                    <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider">Selected Type Name</label>
                    <input type="text" wire:model="room_type_name" class="pms-input text-sm font-bold bg-slate-50 text-slate-600" readonly>
                </div>
                @endif
            </div>

            {{-- Row 3: Tariff Rates (Daily, Weekly, Monthly, Tax %) --}}
            <div class="pt-4 border-t border-slate-100">
                <label class="pms-label text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 block">Room Type Tariff Rates & Tax</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase">Daily Rate ($) *</label>
                        <input type="number" step="0.01" wire:model="daily_rate" class="pms-input text-sm font-bold text-slate-800" placeholder="59.95">
                        @error('daily_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase">Weekly Rate ($) *</label>
                        <input type="number" step="0.01" wire:model="weekly_rate" class="pms-input text-sm font-bold text-slate-800" placeholder="249.90">
                        @error('weekly_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase">Monthly Rate ($) *</label>
                        <input type="number" step="0.01" wire:model="monthly_rate" class="pms-input text-sm font-bold text-slate-800" placeholder="990.00">
                        @error('monthly_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase">Tax Rate (%) *</label>
                        <input type="number" step="0.01" wire:model="tax_percent" class="pms-input text-sm font-bold text-indigo-700 bg-indigo-50/50" placeholder="15">
                        @error('tax_percent') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
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
                        <th class="font-bold">Room No.</th>
                        <th class="font-bold">Room Type</th>
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
                            <span class="font-black text-slate-800 text-sm bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm">Room {{ $r->room_number }}</span>
                        </td>
                        <td>
                            <span class="font-bold text-slate-800 text-xs">{{ $r->roomType->name ?? '—' }}</span>
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
