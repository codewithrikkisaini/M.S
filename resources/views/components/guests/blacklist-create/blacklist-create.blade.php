<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('guests.blacklist.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Add Blacklisted Guest</h1>
            <p class="text-sm text-gray-500 mt-0.5">Block a guest from making new bookings</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Form Panel --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Guest Selection --}}
            @if($show_guest_search)
            <div class="pms-card shadow-sm border border-slate-100/80 p-6">
                <div class="flex items-center gap-2 mb-5 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-search text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Search Existing Guest (Optional)</h3>
                </div>

                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search_guest"
                           placeholder="Search by name, email, or ID number..."
                           class="pms-input pl-9 py-2 text-xs rounded-lg border border-slate-200 w-full">
                </div>

                @if($guests->count() > 0)
                <div class="mt-3 border border-slate-200 rounded-lg divide-y divide-slate-100 max-h-48 overflow-y-auto">
                    @foreach($guests as $guest)
                    <button wire:click="selectGuest({{ $guest->id }})"
                            class="w-full text-left px-4 py-3 hover:bg-indigo-50 transition-colors flex items-center gap-3 cursor-pointer">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shrink-0">
                            <span class="text-xs font-black text-white">{{ strtoupper(substr($guest->name, 0, 1)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800 truncate">{{ $guest->name }}</p>
                            <p class="text-[10px] text-slate-400">{{ $guest->id_number ?: $guest->passport_number ?: 'No ID' }}</p>
                        </div>
                    </button>
                    @endforeach
                </div>
                @endif

                <div class="mt-4 flex justify-end">
                    <button wire:click="$set('show_guest_search', false)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 cursor-pointer">
                        Skip — Enter Manually <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- Guest Information --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6">
                <div class="flex items-center gap-2 mb-5 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-red-50 text-red-600 rounded-lg flex items-center justify-center border border-red-100"><i class="fas fa-user-slash text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Guest Information</h3>
                    @if($selected_guest)
                        <button wire:click="clearGuest" class="ml-auto text-xs font-bold text-red-500 hover:text-red-700 cursor-pointer">
                            <i class="fas fa-times mr-1"></i> Clear Selection
                        </button>
                    @endif
                </div>

                @if($selected_guest)
                <div class="mb-4 p-3 bg-indigo-50 border border-indigo-100 rounded-lg flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shrink-0">
                        <span class="text-sm font-black text-white">{{ strtoupper(substr($selected_guest->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-indigo-800">{{ $selected_guest->name }}</p>
                        <p class="text-[10px] text-indigo-500">Guest ID: {{ $selected_guest->guest_id }} | {{ $selected_guest->email ?? 'No email' }}</p>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">First Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="first_name" class="pms-input text-xs" placeholder="John">
                        @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="last_name" class="pms-input text-xs" placeholder="Doe">
                        @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Identification Type</label>
                        <select wire:model.live="id_type" class="pms-select text-xs">
                            <option value="">Select ID Type...</option>
                            <option value="Aadhaar Card">Aadhaar Card</option>
                            <option value="Driving License">Driving License</option>
                            <option value="Passport">Passport</option>
                            <option value="Voter ID">Voter ID</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('id_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            {{ $id_type ? ($id_type . ' Number') : 'ID / Passport Number' }}
                        </label>
                        <input type="text" wire:model="id_number" class="pms-input text-xs" 
                               placeholder="{{ $id_type === 'Aadhaar Card' ? 'e.g. 1234 5678 9012' : ($id_type === 'Driving License' || $id_type === 'Driver\'s License' ? 'e.g. DL-1420110012345' : ($id_type === 'Passport' ? 'e.g. P1234567' : ($id_type === 'Voter ID' ? 'e.g. ABC1234567' : 'Enter ID / Document No...'))) }}">
                        @error('id_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Date of Birth</label>
                        <input type="date" wire:model="date_of_birth" class="pms-input text-xs">
                        @error('date_of_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Reason / Comments <span class="text-red-500">*</span></label>
                        <textarea wire:model="reason" rows="3" class="pms-input text-xs resize-none rounded-lg border border-slate-200" placeholder="Describe the reason for blacklisting this guest..."></textarea>
                        @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Supporting Documents --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6">
                <div class="flex items-center gap-2 mb-5 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center border border-amber-100"><i class="fas fa-paperclip text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Supporting Documents</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Choose Files</label>
                        <input type="file" wire:model="documents" multiple
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">PDF, JPG, PNG — Max 10MB per file</p>
                        @error('documents.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if(!empty($documents))
                    <div class="space-y-2">
                        <p class="text-xs font-bold text-slate-600">Selected Documents:</p>
                        @foreach($documents as $doc)
                        @if($doc)
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-100">
                            <i class="fas fa-file text-slate-400"></i>
                            <span class="text-xs text-slate-700 truncate">{{ $doc->getClientOriginalName() }}</span>
                            <span class="text-[10px] text-slate-400 ml-auto">{{ round($doc->getSize() / 1024) }}KB</span>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('guests.blacklist.index') }}" class="btn-secondary text-xs rounded-lg py-2 font-bold px-4">Cancel</a>
                <button wire:click="openConfirmModal" wire:loading.attr="disabled" class="btn-primary text-xs rounded-lg py-2 font-bold px-4 cursor-pointer shadow-sm bg-red-600 hover:bg-red-700">
                    <span wire:loading wire:target="openConfirmModal" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                    <i class="fas fa-ban mr-1"></i> Blacklist Guest
                </button>
            </div>
        </div>

        {{-- Right Info Panel --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-red-50 text-red-600 rounded-lg flex items-center justify-center border border-red-100"><i class="fas fa-info-circle text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Blacklist Policy</h3>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Effect</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Blacklisted guests will be blocked from creating new reservations. Existing reservations are not affected.</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Identity Matching</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">The system matches by ID number and name+DOB to prevent bypassing restrictions through new guest records.</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Supporting Documents</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Upload passport copies, incident reports, or any supporting evidence. Files are stored securely with restricted access.</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Removal</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Blacklists can be removed by authorized administrators. The history is preserved for audit purposes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    @if($show_confirm_modal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" wire:click.self="closeConfirmModal">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Confirm Blacklist</h3>
                    <p class="text-xs text-slate-500">This action will restrict the guest from making new reservations.</p>
                </div>
            </div>

            <div class="space-y-3 mb-6">
                <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Guest</p>
                    <p class="text-sm font-bold text-slate-800">{{ $first_name }} {{ $last_name }}</p>
                </div>
                @if($id_number)
                <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">ID / Passport</p>
                    <p class="text-sm font-mono font-bold text-slate-800">{{ strtoupper($id_type ?: 'ID') }}: {{ $id_number }}</p>
                </div>
                @endif
                <div class="bg-red-50 rounded-lg p-3 border border-red-100">
                    <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider mb-0.5">Reason</p>
                    <p class="text-xs text-red-700">{{ $reason }}</p>
                </div>
                @if(count($documents) > 0)
                <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-0.5">Supporting Documents</p>
                    <p class="text-xs text-amber-700">{{ count($documents) }} file(s) selected</p>
                </div>
                @endif
            </div>

            <div class="flex justify-end gap-3">
                <button wire:click="closeConfirmModal" class="btn-secondary text-xs rounded-lg py-2 font-bold px-4 cursor-pointer">Cancel</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-primary text-xs rounded-lg py-2 font-bold px-4 cursor-pointer shadow-sm bg-red-600 hover:bg-red-700">
                    <span wire:loading wire:target="save" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                    <i class="fas fa-ban mr-1"></i> Confirm Blacklist
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
