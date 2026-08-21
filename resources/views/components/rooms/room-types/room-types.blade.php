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



            {{-- Row 3: Tariff Rates (Daily, Weekly, Monthly, Tax %) --}}
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

            {{-- Form Submit Button --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('rooms.index') }}" class="btn-secondary text-xs rounded-xl py-2.5 px-5 font-bold">Cancel</a>
                <button type="submit" wire:loading.attr="disabled" class="btn-primary text-xs font-extrabold rounded-xl py-2.5 px-7 cursor-pointer shadow-md flex items-center gap-2">
                    <i class="fas fa-plus text-xs"></i> Save & Add Room
                </button>
            </div>
        </form>
    </div>

    <div class="pms-card shadow-sm border border-slate-100/80 mt-8">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-violet-50 text-violet-600 rounded-lg flex items-center justify-center border border-violet-100">
                    <i class="fas fa-tags text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Room Type Directory</h3>
                    <p class="text-[10px] text-slate-400">Manage room rate categories and remove unused types safely</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                {{ $roomTypes->count() }} total types
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Type Name</th>
                        <th class="font-bold">Daily</th>
                        <th class="font-bold">Weekly</th>
                        <th class="font-bold">Monthly</th>
                        <th class="font-bold">Tax</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roomTypes as $type)
                    <tr wire:key="room-type-row-{{ $type->id }}" class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <span class="font-bold text-slate-800 text-xs">{{ $type->name }}</span>
                        </td>
                        <td class="font-semibold text-slate-700 text-xs">${{ number_format((float) ($type->daily_rate ?? 0), 2) }}</td>
                        <td class="font-semibold text-slate-700 text-xs">${{ number_format((float) ($type->weekly_rate ?? 0), 2) }}</td>
                        <td class="font-semibold text-slate-700 text-xs">${{ number_format((float) ($type->monthly_rate ?? 0), 2) }}</td>
                        <td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                {{ number_format((float) ($type->tax_percent ?? 15), 1) }}%
                            </span>
                        </td>
                        <td class="text-right">
                            <button wire:click="deleteRoomType({{ $type->id }})" wire:confirm="Delete room type {{ $type->name }}? Existing rooms will be reassigned to a fallback room type." class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 shadow-sm cursor-pointer" title="Delete Room Type">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="fas fa-tags text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">No room types created yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
