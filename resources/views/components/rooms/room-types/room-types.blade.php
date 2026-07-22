<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('rooms.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Room Types</h1>
            <p class="text-sm text-gray-500 mt-0.5">Define room categories, tarification and tax settings for your booking flow.</p>
        </div>
    </div>

    {{-- Add New Type Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80 p-5 mb-6">
        <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
            <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-tag text-xs"></i></div>
            <h3 class="text-sm font-bold text-slate-800">Add New Room Type</h3>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Type Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model="name" class="pms-input text-xs" placeholder="e.g. Single Room">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Daily Rate ($) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" wire:model="daily_rate" class="pms-input text-xs" placeholder="59.95">
                @error('daily_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Weekly Rate ($) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" wire:model="weekly_rate" class="pms-input text-xs" placeholder="249.90">
                @error('weekly_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Monthly Rate ($) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" wire:model="monthly_rate" class="pms-input text-xs" placeholder="990.00">
                @error('monthly_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Tax % <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" max="100" wire:model="tax_percentage" class="pms-input text-xs" placeholder="15.00">
                @error('tax_percentage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Status <span class="text-red-500">*</span></label>
                <select wire:model="status" class="pms-select text-xs">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button wire:click="addType" wire:loading.attr="disabled" class="btn-primary text-xs font-bold rounded-lg py-2 px-4 shadow-sm">
                <i class="fas fa-plus text-[10px]"></i> Add Room Type
            </button>
        </div>
    </div>

    {{-- Directory Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-tags text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Room Type Registry</h3>
                    <p class="text-[10px] text-slate-400">Manage room categories, pricing and tax settings</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                {{ $roomTypes->count() }} total
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Type Name</th>
                        <th class="font-bold">Daily</th>
                        <th class="font-bold">Daily + Tax</th>
                        <th class="font-bold">Weekly</th>
                        <th class="font-bold">Weekly + Tax</th>
                        <th class="font-bold">Monthly</th>
                        <th class="font-bold">Monthly + Tax</th>
                        <th class="font-bold">Tax %</th>
                        <th class="font-bold">Status</th>
                        <th class="font-bold">Rooms</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roomTypes as $type)
                    <tr wire:key="type-{{ $type->id }}" class="hover:bg-slate-50/40 transition-colors">
                        @if($editingId === $type->id)
                            <td class="py-2 pr-2">
                                <input type="text" wire:model="editingName" wire:keydown.enter="updateType" class="pms-input text-xs" autofocus>
                                @error('editingName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </td>
                            <td><input type="number" step="0.01" min="0" wire:model="editingDailyRate" class="pms-input text-xs"></td>
                            <td class="text-slate-600 text-xs font-medium">
                                ${{ number_format((float) $editingDailyRate + ((float) $editingDailyRate * ((float) $editingTaxPercentage / 100)), 2) }}
                            </td>
                            <td><input type="number" step="0.01" min="0" wire:model="editingWeeklyRate" class="pms-input text-xs"></td>
                        <td class="text-slate-600 text-xs font-medium">
                            ${{ number_format((float) $editingWeeklyRate + ((float) $editingWeeklyRate * ((float) $editingTaxPercentage / 100)), 2) }}
                        </td>
                        <td><input type="number" step="0.01" min="0" wire:model="editingMonthlyRate" class="pms-input text-xs"></td>
                        <td class="text-slate-600 text-xs font-medium">
                            ${{ number_format((float) $editingMonthlyRate + ((float) $editingMonthlyRate * ((float) $editingTaxPercentage / 100)), 2) }}
                        </td>
                        <td><input type="number" step="0.01" min="0" max="100" wire:model="editingTaxPercentage" class="pms-input text-xs"></td>
                        <td>
                            <select wire:model="editingStatus" class="pms-select text-xs">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </td>
                        <td class="text-center">{{ $type->rooms_count }}</td>
                        <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button wire:click="updateType" class="btn-icon text-emerald-600 hover:bg-emerald-50 border border-slate-100 hover:border-emerald-100 shadow-sm cursor-pointer" title="Save">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                    <button wire:click="cancelEdit" class="btn-icon text-slate-500 hover:bg-slate-100 border border-slate-100 hover:border-slate-200 shadow-sm cursor-pointer" title="Cancel">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        @else
                            <td class="font-bold text-slate-800 text-sm">{{ $type->name }}</td>
                            <td>${{ number_format($type->daily_rate, 2) }}</td>
                            <td>${{ number_format($type->daily_with_tax, 2) }}</td>
                            <td>${{ number_format($type->weekly_rate, 2) }}</td>
                            <td>${{ number_format($type->weekly_with_tax, 2) }}</td>
                            <td>${{ number_format($type->monthly_rate, 2) }}</td>
                            <td>${{ number_format($type->monthly_with_tax, 2) }}</td>
                            <td>{{ number_format($type->tax_percentage, 2) }}%</td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $type->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $type->status }}
                                </span>
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm">
                                    {{ $type->rooms_count }} room{{ $type->rooms_count !== 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button wire:click="editType({{ $type->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm cursor-pointer" title="Edit">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button wire:click="deleteType({{ $type->id }})"
                                            wire:confirm="Delete room type \"{{ $type->name }}\"?"
                                            class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Delete">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <i class="fas fa-tags text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">No room types registered. Add one above.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
