<div>
    {{-- Page Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('reservations.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">New Reservation</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create a room booking and payment record for a guest</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Form Panel --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-user-friends text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Guest & Stay Information</h3>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider mb-0">Guest Details <span class="text-red-500">*</span></label>
                            <div class="flex bg-slate-100 p-0.5 rounded-lg border border-slate-200">
                                <button type="button" wire:click="$set('is_new_guest', false)" class="px-2.5 py-1 text-[10px] font-bold rounded-md transition-all cursor-pointer {{ !$is_new_guest ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">Existing Guest</button>
                                <button type="button" wire:click="$set('is_new_guest', true)" class="px-2.5 py-1 text-[10px] font-bold rounded-md transition-all cursor-pointer {{ $is_new_guest ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">New Guest</button>
                            </div>
                        </div>

                        @if(!$is_new_guest)
                            <select wire:model="guest_id" class="pms-select text-xs">
                                <option value="">Select guest...</option>
                                @foreach($guests as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->email }})</option>
                                @endforeach
                            </select>
                            @error('guest_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 bg-slate-50/50 p-4 rounded-xl border border-dashed border-slate-250">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Guest Name <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="new_guest_name" class="pms-input text-xs mt-1" placeholder="Enter name...">
                                    @error('new_guest_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Email Address</label>
                                    <input type="email" wire:model="new_guest_email" class="pms-input text-xs mt-1" placeholder="email@example.com">
                                    @error('new_guest_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Phone Number</label>
                                    <input type="text" wire:model="new_guest_phone" class="pms-input text-xs mt-1" placeholder="+1 (555) 000-0000">
                                    @error('new_guest_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Adults</label>
                        <input type="number" wire:model="adults" class="pms-input text-xs" min="1">
                        @error('adults') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Children</label>
                        <input type="number" wire:model="children" class="pms-input text-xs" min="0">
                    </div>

                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Check-In <span class="text-red-500">*</span></label>
                        <input type="date" wire:model.live="check_in_date" class="pms-input text-xs">
                        @error('check_in_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Check-Out <span class="text-red-500">*</span></label>
                        <input type="date" wire:model.live="check_out_date" class="pms-input text-xs">
                        @error('check_out_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if(!$check_in_date || !$check_out_date)
                    <div class="col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room(s) <span class="text-red-500">*</span></label>
                        <div class="p-3 text-xs bg-slate-50 border border-slate-100 text-slate-400 rounded-lg font-medium flex items-center gap-2">
                            <i class="fas fa-info-circle text-slate-400"></i> Please specify check-in and check-out dates to view available rooms.
                        </div>
                        @error('room_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @else
                    <div class="col-span-2 relative" wire:key="reservation-create-room-selector" x-data="{
                        open: false,
                        search: '',
                        rooms: @js($rooms->map(fn($r) => [
                            'id' => $r->id,
                            'room_number' => $r->room_number,
                            'type' => $r->roomType->name ?? '',
                            'price' => $r->price,
                            'hk' => optional($r->latestHousekeeping)->status ?? 'Clean',
                            'maint' => $r->activeMaintenanceTickets->count()
                        ])),
                        selectedIds: @entangle('room_ids').live,
                        toggleRoom(room) {
                            const idx = this.selectedIds.indexOf(room.id);
                            if (idx === -1) { this.selectedIds.push(room.id); } else { this.selectedIds.splice(idx, 1); }
                        },
                        isSelected(room) { return this.selectedIds.includes(room.id); },
                        get selectedRooms() { return this.rooms.filter(r => this.selectedIds.includes(r.id)); },
                        get totalPerNight() { return this.selectedRooms.reduce((sum, r) => sum + Number(r.price), 0); },
                        get filteredRooms() {
                            if (!this.search) return this.rooms;
                            return this.rooms.filter(r =>
                                r.room_number.toLowerCase().includes(this.search.toLowerCase()) ||
                                r.type.toLowerCase().includes(this.search.toLowerCase())
                            );
                        }
                    }">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room(s) <span class="text-red-500">*</span> <span class="text-[10px] text-slate-400 font-normal lowercase">(select multiple for family bookings)</span></label>

                        <button type="button" @click.stop="open = !open"
                                class="w-full flex items-center justify-between pms-input text-left bg-white border border-slate-300 rounded-lg shadow-sm px-3.5 py-2.5 text-xs text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 cursor-pointer transition-all">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-door-open text-slate-400"></i>
                                <template x-if="selectedRooms.length > 0">
                                    <span class="font-semibold text-slate-800" x-text="selectedRooms.length + ' room' + (selectedRooms.length > 1 ? 's' : '') + ' selected'"></span>
                                </template>
                                <template x-if="selectedRooms.length === 0">
                                    <span class="text-gray-400">Select room(s)...</span>
                                </template>
                            </div>
                            <div class="flex items-center gap-2">
                                <template x-if="selectedRooms.length > 0">
                                    <span class="text-xs font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full" x-text="'$' + totalPerNight.toFixed(2) + '/night'"></span>
                                </template>
                                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </div>
                        </button>

                        <template x-if="selectedRooms.length > 0">
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <template x-for="room in selectedRooms" :key="room.id">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold shadow-sm animate-fade-in">
                                        <span x-text="'Room ' + room.room_number"></span>
                                        <button type="button" @click.stop="toggleRoom(room)" class="text-indigo-400 hover:text-indigo-700 transition-colors p-0.5 hover:bg-indigo-100 rounded-full inline-flex items-center justify-center">
                                            <i class="fas fa-times text-[10px]"></i>
                                        </button>
                                    </span>
                                </template>
                            </div>
                        </template>

                        <div x-show="open"
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute z-30 w-full mt-1.5 bg-white border border-slate-200 rounded-xl shadow-2xl max-h-72 overflow-hidden flex flex-col"
                             style="display:none;">

                            <div class="p-2.5 border-b border-slate-100 sticky top-0 bg-white z-10">
                                <div class="relative">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="text" x-model="search" placeholder="Search by room number or type..."
                                           class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 pl-8 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-slate-50 hover:bg-slate-50/50">
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                                <template x-for="room in filteredRooms" :key="room.id">
                                    <button type="button" @click="toggleRoom(room)"
                                            class="w-full text-left px-4 py-3 hover:bg-slate-50/60 flex items-center justify-between transition-colors cursor-pointer"
                                            :class="{ 'bg-indigo-50/40': isSelected(room) }">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-5 h-5 rounded border transition-all shrink-0"
                                                 :class="isSelected(room) ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-slate-300 bg-white hover:border-slate-400'">
                                                <template x-if="isSelected(room)">
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </template>
                                            </div>
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-slate-800 text-sm" x-text="'Room ' + room.room_number"></span>
                                                    <span class="text-[10px] font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded" x-text="room.type"></span>
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-1">
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold border"
                                                          :class="{
                                                              'bg-emerald-50 text-emerald-700 border-emerald-100': room.hk === 'Clean',
                                                              'bg-rose-50 text-rose-700 border-rose-100': room.hk === 'Dirty',
                                                              'bg-amber-50 text-amber-700 border-amber-100': room.hk === 'Inspecting',
                                                              'bg-orange-50 text-orange-700 border-orange-100': room.hk === 'Maintenance'
                                                          }">
                                                        <span class="w-1.5 h-1.5 rounded-full"
                                                              :class="{
                                                                  'bg-emerald-500': room.hk === 'Clean',
                                                                  'bg-rose-500': room.hk === 'Dirty',
                                                                  'bg-amber-500': room.hk === 'Inspecting',
                                                                  'bg-orange-500': room.hk === 'Maintenance'
                                                              }"></span>
                                                        <span x-text="room.hk"></span>
                                                    </span>
                                                    <template x-if="room.maint > 0">
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-100 animate-pulse">
                                                            <i class="fas fa-tools text-[8px]"></i>
                                                            <span x-text="room.maint + ' Ticket' + (room.maint > 1 ? 's' : '')"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right flex flex-col items-end shrink-0">
                                            <span class="font-bold text-slate-800 text-sm" x-text="'$' + Number(room.price).toFixed(2)"></span>
                                            <span class="text-[10px] text-slate-400 font-medium">/night</span>
                                        </div>
                                    </button>
                                </template>
                                <template x-if="filteredRooms.length === 0">
                                    <div class="p-5 text-center text-xs text-gray-400">
                                        <i class="fas fa-info-circle mb-1 block text-lg text-gray-300"></i>
                                        No rooms available for these dates
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    @error('room_ids') <p class="text-red-500 text-xs mt-1 col-span-2">{{ $message }}</p> @enderror

                    @if(!empty($room_ids))
                        @php
                            $selectedRoomModels = $rooms->whereIn('id', $room_ids);
                            $alertRooms = $selectedRoomModels->filter(function($r) {
                                $hk = optional($r->latestHousekeeping)->status ?? 'Clean';
                                return $hk !== 'Clean' || $r->activeMaintenanceTickets->count() > 0;
                            });
                        @endphp
                        @if($alertRooms->isNotEmpty())
                            <div class="mt-2.5 rounded-lg p-3 text-xs bg-amber-50 border border-amber-200 text-amber-800 space-y-1 col-span-2 shadow-sm">
                                <p class="font-semibold flex items-center gap-1.5"><i class="fas fa-exclamation-triangle"></i> Room Status Alert:</p>
                                @foreach($alertRooms as $r)
                                    @php
                                        $hkStatus = optional($r->latestHousekeeping)->status ?? 'Clean';
                                        $maintCount = $r->activeMaintenanceTickets->count();
                                    @endphp
                                    <p>• Room {{ $r->room_number }}:
                                        @if($hkStatus !== 'Clean') Housekeeping is <strong>{{ $hkStatus }}</strong>. @endif
                                        @if($maintCount > 0) <strong>{{ $maintCount }}</strong> active maintenance ticket(s). @endif
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    @endif
                    @endif
                    
                    <div class="col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Special Notes</label>
                        <textarea wire:model="special_notes" rows="3" class="pms-input text-xs resize-none rounded-lg border border-slate-200 focus:ring-1 focus:ring-indigo-500" placeholder="Any special requests..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Payment & Summary Panel --}}
        <div class="space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-dollar-sign text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Billing & Pricing</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Discount Type</label>
                        <select wire:model.live="discount_type" class="pms-select text-xs">
                            <option value="Fixed">Fixed ($)</option>
                            <option value="Percentage">Percentage (%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Discount Value</label>
                        <input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="discount_value" class="pms-input text-xs" placeholder="0">
                        @error('discount_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Tax Rate (%)</label>
                        <input type="number" step="0.01" min="0" max="100" wire:model.live.debounce.400ms="tax_rate" class="pms-input text-xs" placeholder="18">
                        @error('tax_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Rate Plan</label>
                        <select wire:model.live="pricing_mode" class="pms-select text-xs">
                            <option value="auto">Auto (Best rate: daily/weekly/monthly)</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                        @error('pricing_mode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Misc. Charges ($)</label>
                        <input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="misc_charge" class="pms-input text-xs" placeholder="0.00">
                        @error('misc_charge') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($charges)
                    <div class="bg-slate-50 rounded-xl p-4 text-xs space-y-2.5 border border-slate-150 shadow-inner">
                        <h4 class="font-bold text-slate-700 border-b border-slate-200/60 pb-1.5 mb-1 text-[11px] uppercase tracking-wider">Invoice Summary</h4>
                        <div class="flex justify-between text-slate-600 font-medium">
                            <span>Subtotal</span>
                            <span>${{ number_format($charges['subtotal'], 2) }}</span>
                        </div>
                        @if($charges['discount'] > 0)
                        <div class="flex justify-between text-emerald-600 font-bold">
                            <span>Discount</span>
                            <span>-${{ number_format($charges['discount'], 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-slate-500 font-medium">
                            <span>Rate Plan</span>
                            <span class="uppercase">{{ $pricing_mode === 'auto' ? 'Auto' : ucfirst($pricing_mode) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 font-medium">
                            <span>Tax ({{ $charges['tax_rate'] }}%)</span>
                            <span>${{ number_format($charges['tax'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600 font-medium">
                            <span>Misc. Charges</span>
                            <span>${{ number_format($charges['misc'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-900 font-black border-t border-slate-200/80 pt-2 text-sm">
                            <span>Total Amount</span>
                            <span>${{ number_format($charges['total'], 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-dashed border-slate-200/80 pt-2 font-black text-sm {{ $balanceDue > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            <span>Balance Due</span>
                            <span>${{ number_format($balanceDue, 2) }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="border-t border-slate-100 pt-4 mt-2">
                        <div class="space-y-4">
                            <div>
                                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Payment Method</label>
                                <select wire:model="payment_type" class="pms-select text-xs">
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="UPI">UPI</option>
                                </select>
                                @error('payment_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Advance Payment ($)</label>
                                <input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="payment_amount" class="pms-input text-xs" placeholder="0.00">
                                @error('payment_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2.5 pt-5 border-t border-slate-100 mt-4">
                    <button wire:click="save" wire:loading.attr="disabled" class="btn-primary w-full justify-center rounded-lg shadow-sm text-xs py-2 font-bold cursor-pointer">
                        <span wire:loading wire:target="save" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                        Create Booking
                    </button>
                    <a href="{{ route('reservations.index') }}" class="btn-secondary w-full justify-center rounded-lg text-xs py-2 font-bold text-center">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
