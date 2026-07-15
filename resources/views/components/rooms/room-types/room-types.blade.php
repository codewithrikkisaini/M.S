<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('rooms.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Room Types</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure and define various room categories for hotel bookings</p>
        </div>
    </div>

    {{-- Add New Type Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80 p-5 mb-6">
        <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
            <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-tag text-xs"></i></div>
            <h3 class="text-sm font-bold text-slate-800">Add New Room Type</h3>
        </div>
        <div class="flex items-end gap-3 max-w-xl">
            <div class="flex-1">
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Type Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model="name" wire:keydown.enter="addType" class="pms-input text-xs" placeholder="e.g. Deluxe Suite, Single Room, Double Bed">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button wire:click="addType" wire:loading.attr="disabled" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer shadow-sm shrink-0">
                <i class="fas fa-plus text-[10px]"></i> Add Type
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
                    <p class="text-[10px] text-slate-400">Total room types registered in system</p>
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
                        <th class="font-bold">Associated Rooms Count</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roomTypes as $type)
                    <tr wire:key="type-{{ $type->id }}" class="hover:bg-slate-50/40 transition-colors">
                        @if($editingId === $type->id)
                            <td colspan="2" class="py-2">
                                <div class="max-w-md">
                                    <input type="text" wire:model="editingName" wire:keydown.enter="updateType"
                                           class="pms-input text-xs py-1.5" autofocus>
                                    @error('editingName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </td>
                            <td class="text-right py-2">
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
                        <td colspan="3" class="py-12 text-center text-slate-400">
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
