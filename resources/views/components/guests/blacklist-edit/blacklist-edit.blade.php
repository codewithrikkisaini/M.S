<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('guests.blacklist.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Edit Blacklist</h1>
            <p class="text-sm text-gray-500 mt-0.5">Update blacklist record for {{ $blacklist->first_name }} {{ $blacklist->last_name }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Form Panel --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Status Banner --}}
            @if($blacklist->status === 'removed')
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                <i class="fas fa-check-circle text-green-600"></i>
                <div>
                    <p class="text-sm font-bold text-green-800">This blacklist has been removed.</p>
                    <p class="text-xs text-green-600">The guest is currently allowed to make bookings.</p>
                </div>
            </div>
            @endif

            {{-- Guest Information --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6">
                <div class="flex items-center gap-2 mb-5 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-red-50 text-red-600 rounded-lg flex items-center justify-center border border-red-100"><i class="fas fa-user-slash text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Guest Information</h3>
                </div>

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
                        <select wire:model="id_type" class="pms-select text-xs">
                            <option value="">Select ID Type...</option>
                            <option value="Passport">Passport</option>
                            <option value="Driver's License">Driver's License</option>
                            <option value="Aadhaar Card">Aadhaar Card</option>
                            <option value="Voter ID">Voter ID</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('id_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">ID / Passport Number</label>
                        <input type="text" wire:model="id_number" class="pms-input text-xs" placeholder="e.g. P1234567">
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

            {{-- Existing Documents --}}
            <div class="pms-card shadow-sm border border-slate-100/80 p-6">
                <div class="flex items-center gap-2 mb-5 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center border border-amber-100"><i class="fas fa-paperclip text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Supporting Documents</h3>
                </div>

                <div class="space-y-4">
                    {{-- Existing Documents --}}
                    @if(count($existing_documents) > 0)
                    <div class="space-y-2">
                        <p class="text-xs font-bold text-slate-600">Uploaded Documents:</p>
                        @foreach($existing_documents as $doc)
                        <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg border border-slate-100">
                            @if(str_contains($doc['mime_type'] ?? '', 'pdf'))
                                <i class="fas fa-file-pdf text-red-400"></i>
                            @else
                                <i class="fas fa-file-image text-blue-400"></i>
                            @endif
                            <span class="text-xs text-slate-700 truncate">{{ $doc['original_filename'] }}</span>
                            <span class="text-[10px] text-slate-400">{{ \App\Models\GuestBlacklistDocument::find($doc['id'])?->file_size_formatted ?? '' }}</span>
                            <button wire:click="deleteDocument({{ $doc['id'] }})" wire:confirm="Delete this document permanently?"
                                    class="ml-auto text-red-400 hover:text-red-600 cursor-pointer" title="Delete">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- New Uploads --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Add More Files</label>
                        <input type="file" wire:model="documents" multiple
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">PDF, JPG, PNG — Max 10MB per file</p>
                        @error('documents.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if(!empty($documents))
                    <div class="space-y-2">
                        <p class="text-xs font-bold text-slate-600">New Documents:</p>
                        @foreach($documents as $doc)
                        @if($doc)
                        <div class="flex items-center gap-2 p-2 bg-indigo-50 rounded-lg border border-indigo-100">
                            <i class="fas fa-file text-indigo-400"></i>
                            <span class="text-xs text-indigo-700 truncate">{{ $doc->getClientOriginalName() }}</span>
                            <span class="text-[10px] text-indigo-400 ml-auto">{{ round($doc->getSize() / 1024) }}KB</span>
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
                <button wire:click="save" wire:loading.attr="disabled" class="btn-primary text-xs rounded-lg py-2 font-bold px-4 cursor-pointer shadow-sm">
                    <span wire:loading wire:target="save" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                    Save Changes
                </button>
            </div>
        </div>

        {{-- Right Info Panel --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center border border-slate-200"><i class="fas fa-history text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Blacklist Details</h3>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Blacklisted By</p>
                        <p class="text-xs text-slate-700">{{ $blacklist->blacklister?->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Blacklisted At</p>
                        <p class="text-xs text-slate-700">{{ $blacklist->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    @if($blacklist->removed_at)
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Removed At</p>
                        <p class="text-xs text-slate-700">{{ $blacklist->removed_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Removed By</p>
                        <p class="text-xs text-slate-700">{{ $blacklist->remover?->name ?? 'Unknown' }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Status</p>
                        @if($blacklist->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-50 text-red-700 border border-red-100">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-50 text-green-700 border border-green-100">Removed</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
