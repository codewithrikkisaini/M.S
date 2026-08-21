<div>
    {{-- HEADER --}}
    <div class="mb-6">
        <a href="{{ route('guests.blacklist.index') }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700 mb-3">
            <i class="fas fa-arrow-left mr-2"></i> Back to Blacklist
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Edit Blacklist</h1>
        <p class="text-slate-500 text-sm mt-1">
            Editing blacklist entry for <span class="font-semibold text-slate-700">{{ $blacklist->first_name }} {{ $blacklist->last_name }}</span>
        </p>
    </div>

    {{-- STATUS BANNER --}}
    @if($blacklist->status === 'released')
        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-emerald-500 text-xl mr-3"></i>
                <div>
                    <p class="font-semibold text-emerald-800">Released</p>
                    <p class="text-sm text-emerald-600">This guest has been released from the blacklist.</p>
                </div>
            </div>
            @if($blacklist->released_at)
                <span class="text-xs text-emerald-600">{{ \Carbon\Carbon::parse($blacklist->released_at)->format('M d, Y h:i A') }}</span>
            @endif
        </div>
    @elseif($blacklist->status === 'active')
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-ban text-red-500 text-xl mr-3"></i>
                <div>
                    <p class="font-semibold text-red-800">Active Blacklist</p>
                    <p class="text-sm text-red-600">This guest is currently blacklisted.</p>
                </div>
            </div>
            <button wire:click="openReleaseModal()" class="btn-primary text-sm">
                <i class="fas fa-unlock mr-1"></i> Release / Unblock
            </button>
        </div>
    @endif

    {{-- MAIN CONTENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN (2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- GUEST INFORMATION --}}
            <div class="pms-card">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-800">
                        <i class="fas fa-user mr-2 text-slate-400"></i> Guest Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="pms-label">First Name <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="first_name" class="pms-input" placeholder="Enter first name">
                            @error('first_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="pms-label">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="last_name" class="pms-input" placeholder="Enter last name">
                            @error('last_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="pms-label">ID Type <span class="text-red-500">*</span></label>
                            <select wire:model.live="id_type" class="pms-select">
                                <option value="">Select ID Type</option>
                                <option value="Aadhaar Card">Aadhaar Card</option>
                                <option value="Driving License">Driving License</option>
                                <option value="Passport">Passport</option>
                                <option value="Voter ID">Voter ID</option>
                                <option value="Other">Other</option>
                            </select>
                            @error('id_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="pms-label">{{ $id_type ? ($id_type . ' Number') : 'ID Number' }} <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="id_number" class="pms-input" 
                                   placeholder="{{ $id_type === 'Aadhaar Card' ? 'e.g. 1234 5678 9012' : ($id_type === 'Driving License' || $id_type === 'Driver\'s License' || $id_type === 'drivers_license' ? 'e.g. DL-1420110012345' : ($id_type === 'Passport' || $id_type === 'passport' ? 'e.g. P1234567' : ($id_type === 'Voter ID' ? 'e.g. ABC1234567' : 'Enter ID number'))) }}">
                            @error('id_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="pms-label">Date of Birth</label>
                            <input type="date" wire:model="date_of_birth" class="pms-input">
                            @error('date_of_birth') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="pms-label">Reason <span class="text-red-500">*</span></label>
                            <textarea wire:model="reason" class="pms-input" rows="3" placeholder="Enter reason for blacklisting"></textarea>
                            @error('reason') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- SUPPORTING DOCUMENTS --}}
            <div class="pms-card">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-800">
                        <i class="fas fa-file-alt mr-2 text-slate-400"></i> Supporting Documents
                    </h2>
                </div>
                <div class="p-6">

                    {{-- Existing Documents --}}
                    @if(count($existing_documents) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-slate-700 mb-3">Existing Documents</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-slate-500 border-b border-slate-200">
                                            <th class="pb-2 font-medium">File Name</th>
                                            <th class="pb-2 font-medium">Category</th>
                                            <th class="pb-2 font-medium">Uploaded</th>
                                            <th class="pb-2 font-medium text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($existing_documents as $doc)
                                            <tr class="border-b border-slate-100">
                                                <td class="py-3">
                                                    <div class="flex items-center gap-2">
                                                        @if(str_contains($doc['mime_type'] ?? '', 'pdf'))
                                                            <i class="fas fa-file-pdf text-red-500"></i>
                                                        @elseif(str_starts_with($doc['mime_type'] ?? '', 'image/'))
                                                            <i class="fas fa-file-image text-blue-500"></i>
                                                        @else
                                                            <i class="fas fa-file text-slate-400"></i>
                                                        @endif
                                                        <span class="text-slate-700">{{ $doc['name'] }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                                        {{ $doc['category'] }}
                                                    </span>
                                                </td>
                                                <td class="py-3 text-slate-500">{{ $doc['uploaded_at'] }}</td>
                                                <td class="py-3 text-right">
                                                    <div class="flex items-center justify-end gap-1.5">
                                                        <a href="{{ $doc['view_url'] }}" target="_blank" class="btn-icon text-slate-500 hover:text-blue-600 border border-slate-100 hover:border-blue-200 shadow-sm" title="View">
                                                            <i class="fas fa-eye text-xs"></i>
                                                        </a>
                                                        <a href="{{ $doc['download_url'] }}" class="btn-icon text-slate-500 hover:text-emerald-600 border border-slate-100 hover:border-emerald-200 shadow-sm" title="Download">
                                                            <i class="fas fa-download text-xs"></i>
                                                        </a>
                                                        <button wire:click="deleteDocument({{ $doc['id'] }})" wire:confirm="Are you sure you want to delete this document?" class="btn-icon text-slate-500 hover:text-red-600 border border-slate-100 hover:border-red-200 shadow-sm" title="Delete">
                                                            <i class="fas fa-trash text-xs"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- New File Upload --}}
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 mb-3">Upload New Documents</h3>
                        <div>
                            <label class="pms-label">Select Files</label>
                            <input type="file" wire:model="documents" multiple class="pms-input" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        @error('documents') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror

                        {{-- New Files Preview --}}
                        @if(!empty($documents) && count($documents) > 0)
                            <div class="mt-3 space-y-2">
                                @foreach($documents as $index => $file)
                                    <div class="flex items-center justify-between bg-slate-50 rounded-lg px-3 py-2 border border-slate-200">
                                        <div class="flex items-center">
                                            <i class="fas fa-paperclip text-slate-400 mr-2"></i>
                                            <span class="text-sm text-slate-700">{{ $file->getClientOriginalName() }}</span>
                                            <span class="text-xs text-slate-400 ml-2">({{ round($file->getSize() / 1024, 1) }} KB)</span>
                                        </div>
                                        <button type="button" wire:click="removeNewDocument({{ $index }})" class="text-slate-400 hover:text-red-500">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('guests.blacklist.index') }}" class="btn-secondary">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button wire:click="save()" wire:loading.attr="disabled" class="btn-primary">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
            </div>
        </div>

        {{-- RIGHT COLUMN (1/3) --}}
        <div class="lg:col-span-1">
            <div class="pms-card sticky top-6">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-800">
                        <i class="fas fa-info-circle mr-2 text-slate-400"></i> Blacklist Details
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Case Number</label>
                        <p class="text-sm font-semibold text-slate-800 mt-1">{{ $blacklist->case_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Blacklisted By</label>
                        <p class="text-sm text-slate-700 mt-1">{{ $blacklist->blacklisted_by ?? 'System' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Blacklisted At</label>
                        <p class="text-sm text-slate-700 mt-1">{{ $blacklist->created_at ? \Carbon\Carbon::parse($blacklist->created_at)->format('M d, Y h:i A') : 'N/A' }}</p>
                    </div>

                    @if($blacklist->removed_at)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Removed At</label>
                            <p class="text-sm text-slate-700 mt-1">{{ \Carbon\Carbon::parse($blacklist->removed_at)->format('M d, Y h:i A') }}</p>
                        </div>
                    @endif

                    @if($blacklist->released_at)
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Released At</label>
                            <p class="text-sm text-slate-700 mt-1">{{ \Carbon\Carbon::parse($blacklist->released_at)->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($blacklist->release_reason)
                            <div>
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Release Reason</label>
                                <p class="text-sm text-slate-700 mt-1">{{ $blacklist->release_reason }}</p>
                            </div>
                        @endif
                    @endif

                    <div>
                        <label class="text-xs font-medium text-slate-500 uppercase tracking-wide">Status</label>
                        <div class="mt-2">
                            @if($blacklist->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-circle text-[6px] mr-1.5"></i> Active
                                </span>
                            @elseif($blacklist->status === 'released')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    <i class="fas fa-circle text-[6px] mr-1.5"></i> Released
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                    <i class="fas fa-circle text-[6px] mr-1.5"></i> {{ ucfirst($blacklist->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RELEASE MODAL --}}
     @if($showReleaseModal)

    {{-- ============================================================
         RELEASE GUEST MODAL
         ============================================================ --}}
    <div
        class="fixed inset-0 z-[9998] mt-10 flex items-center justify-center p-4 sm:p-6"
        style="
            background: rgba(15, 23, 42, 0.48);
            backdrop-filter: blur(7px);
            -webkit-backdrop-filter: blur(7px);
        "
        @click.self="$wire.closeReleaseModal()" >

        {{-- ========================================================
             MODAL CONTAINER
             ======================================================== --}}
        <div
            class="flex w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5"
            style="max-height: calc(100vh - 100px);"
        >

            {{-- ====================================================
                 HEADER
                 ==================================================== --}}
            <div
                class="flex shrink-0 items-center justify-between border-b border-slate-200 bg-white px-5 py-3.5 sm:px-6">

                <div class="flex min-w-0 items-center gap-3">

                    {{-- Icon --}}
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"
                    >
                        <i class="fas fa-unlock"></i>
                    </div>

                    {{-- Title --}}
                    <div class="min-w-0">

                        <h3 class="truncate text-base font-bold text-slate-800 sm:text-lg">
                            Release Guest
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-400">
                            Remove this guest from the blacklist
                        </p>

                    </div>

                </div>

                {{-- Close Button --}}
                <button
                    type="button"
                    wire:click="closeReleaseModal"
                    class="ml-3 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition-all duration-200 hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                    aria-label="Close modal"
                >
                    <i class="fas fa-times"></i>
                </button>

            </div>


            {{-- ====================================================
                 BODY
                 ==================================================== --}}
            <div
                class="min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-6"
            >

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">


                    {{-- =================================================
                         LEFT COLUMN
                         ================================================= --}}
                    <div class="space-y-4">


                        {{-- ================= GUEST ================= --}}
                        <div
                            class="rounded-xl border border-slate-200 bg-slate-50 p-3.5"
                        >

                            <div class="flex items-center gap-3">

                                {{-- Guest Icon --}}
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-500"
                                >
                                    <i class="fas fa-user text-sm"></i>
                                </div>

                                {{-- Guest Details --}}
                                <div class="min-w-0">

                                    <p
                                        class="text-[10px] font-semibold uppercase tracking-wide text-slate-400"
                                    >
                                        Guest
                                    </p>

                                    <p
                                        class="truncate text-sm font-bold text-slate-800"
                                    >
                                        {{ $blacklist->first_name }}
                                        {{ $blacklist->last_name }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- ================= STATUS ================= --}}
                        <div
                            class="rounded-xl border border-red-100 bg-red-50 p-3.5"
                        >

                            <div class="flex items-center justify-between gap-3">

                                <div>

                                    <p
                                        class="text-[10px] font-semibold uppercase tracking-wide text-slate-400"
                                    >
                                        Current Status
                                    </p>

                                    <p
                                        class="mt-0.5 text-sm font-semibold text-slate-700"
                                    >
                                        Blacklisted Guest
                                    </p>

                                </div>

                                {{-- Status Badge --}}
                                <span
                                    class="inline-flex shrink-0 items-center rounded-full bg-red-100 px-2.5 py-1 text-[10px] font-bold text-red-700"
                                >
                                    <i class="fas fa-ban mr-1"></i>
                                    Active
                                </span>

                            </div>

                        </div>


                        {{-- ================= ORIGINAL REASON ================= --}}
                        @if($blacklist->reason)

                            <div
                                class="rounded-xl border border-slate-200 bg-white p-3.5"
                            >

                                <div class="mb-1.5 flex items-center gap-2">

                                    <i
                                        class="fas fa-file-alt text-xs text-red-500"
                                    ></i>

                                    <p
                                        class="text-[10px] font-semibold uppercase tracking-wide text-slate-400"
                                    >
                                        Original Blacklist Reason
                                    </p>

                                </div>

                                <p
                                    class="text-xs leading-5 text-slate-600"
                                >
                                    {{ Str::limit($blacklist->reason, 220) }}
                                </p>

                            </div>

                        @endif


                    </div>


                    {{-- =================================================
                         RIGHT COLUMN
                         ================================================= --}}
                    <div class="space-y-4">


                        {{-- ================= RELEASE REASON ================= --}}
                        <div>

                            <label
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                            >
                                Release Reason
                                <span class="text-red-500">*</span>
                            </label>

                            <textarea
                                wire:model="release_reason"
                                rows="3"
                                class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-700 outline-none transition-all duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                                placeholder="Why is this guest being released?"
                            ></textarea>

                            @error('release_reason')

                                <p
                                    class="mt-1 flex items-center gap-1 text-[10px] font-medium text-red-500"
                                >
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- ================= RELEASE NOTES ================= --}}
                        <div>

                            <label
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                            >
                                Release Notes

                                <span class="font-normal text-slate-400">
                                    (Optional)
                                </span>
                            </label>

                            <textarea
                                wire:model="release_notes"
                                rows="3"
                                class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-700 outline-none transition-all duration-200 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                                placeholder="Additional administrative notes..."
                            ></textarea>

                        </div>


                        {{-- ================= DOCUMENT UPLOAD ================= --}}
                        <div>

                            <label
                                class="mb-1.5 block text-xs font-semibold text-slate-700" >
                                Supporting Documents

                                <span class="font-normal text-slate-400">
                                    (Optional)
                                </span>
                            </label>


                            <label
                                class="group flex cursor-pointer items-center gap-3 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-3 transition-all duration-200 hover:border-emerald-400 hover:bg-emerald-50/30"  >

                                {{-- Upload Icon --}}
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-slate-400 shadow-sm transition group-hover:text-emerald-500"
                                >
                                    <i class="fas fa-cloud-upload-alt text-sm"></i>
                                </div>


                                {{-- Upload Text --}}
                                <div class="min-w-0">

                                    <p
                                        class="text-xs font-semibold text-slate-700"
                                    >
                                        Upload supporting documents
                                    </p>

                                    <p
                                        class="mt-0.5 text-[10px] text-slate-400"
                                    >
                                        PDF, JPG, JPEG or PNG
                                        <span class="mx-1">•</span>
                                        Maximum 10MB each
                                    </p>

                                </div>


                                {{-- File Input --}}
                                <input
                                    type="file"
                                    wire:model="release_documents"
                                    multiple
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="hidden"
                                />

                            </label>


                            {{-- Upload Loading --}}
                            <div
                                wire:loading
                                wire:target="release_documents"
                                class="mt-1.5 flex items-center gap-1 text-[10px] font-medium text-emerald-600"
                            >
                                <i class="fas fa-spinner fa-spin"></i>
                                Uploading documents...
                            </div>

                        </div>


                    </div>

                </div>

            </div>


            {{-- ====================================================
                 FOOTER
                 ==================================================== --}}
            <div
                class="flex shrink-0 items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3.5 sm:px-6"
            >

                {{-- Cancel --}}
                <button
                    type="button"
                    wire:click="closeReleaseModal"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition-all duration-200 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-300"
                >
                    Cancel
                </button>


                {{-- Release --}}
                <button
                    type="button"
                    wire:click="releaseBlacklist"
                    wire:loading.attr="disabled"
                    wire:target="releaseBlacklist"
                    class="rounded-lg bg-emerald-600 px-5 py-2 text-xs font-semibold text-white shadow-sm transition-all duration-200 hover:bg-emerald-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >

                    {{-- Normal State --}}
                    <span
                        wire:loading.remove
                        wire:target="releaseBlacklist"
                    >
                        <i class="fas fa-check mr-1"></i>
                        Release Guest
                    </span>


                    {{-- Loading State --}}
                    <span
                        wire:loading
                        wire:target="releaseBlacklist"
                    >
                        <i class="fas fa-spinner fa-spin mr-1"></i>
                        Releasing...
                    </span>

                </button>

            </div>

        </div>

    </div>

    @endif
</div>
