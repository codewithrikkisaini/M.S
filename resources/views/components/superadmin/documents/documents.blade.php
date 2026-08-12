<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Document Management</h1>
            <p class="text-sm text-slate-500 mt-0.5">Review and manage hotel verification documents</p>
        </div>
        <div class="flex items-center gap-3">
            @if($pendingCount > 0)
                <div class="flex items-center space-x-2 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-2 rounded-xl text-sm font-medium shadow-sm">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    <span>{{ $pendingCount }} Pending Review</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    @php
        $pendingTotal = \App\Models\HotelDocument::whereIn('status', [\App\Enums\DocumentStatus::Pending, \App\Enums\DocumentStatus::UnderReview])->count();
        $approvedTotal = \App\Models\HotelDocument::where('status', \App\Enums\DocumentStatus::Approved)->where('is_current', true)->count();
        $rejectedTotal = \App\Models\HotelDocument::whereIn('status', [\App\Enums\DocumentStatus::Rejected, \App\Enums\DocumentStatus::ReplacementRequired])->where('is_current', true)->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-50 text-slate-500 rounded-lg flex items-center justify-center border border-slate-100">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <p class="text-lg font-black text-slate-800">{{ $totalDocs }}</p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Total Documents</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center border border-amber-100">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-lg font-black text-slate-800">{{ $pendingTotal }}</p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Pending</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center border border-emerald-100">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-lg font-black text-slate-800">{{ $approvedTotal }}</p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Approved</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-lg flex items-center justify-center border border-rose-100">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <p class="text-lg font-black text-slate-800">{{ $rejectedTotal }}</p>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Rejected</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-72">
                <i class="fas fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search hotels, documents, filenames..."
                       class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-500">
            </div>

            <select wire:model.live="filterStatus" class="w-full md:w-44 px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-500">
                <option value="">All Statuses</option>
                @foreach(\App\Enums\DocumentStatus::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterDocumentType" class="w-full md:w-48 px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-500">
                <option value="">All Document Types</option>
                @foreach($allTypes as $key => $type)
                    <option value="{{ $key }}">{{ $type['name'] }}</option>
                @endforeach
            </select>

            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 whitespace-nowrap cursor-pointer">
                <input type="checkbox" wire:model.live="showPendingOnly" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Pending Only
            </label>
        </div>

        <select wire:model.live="sortBy" class="w-full md:w-44 px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-500">
            <option value="latest">Latest First</option>
            <option value="oldest">Oldest First</option>
            <option value="pending_first">Pending First</option>
        </select>
    </div>

    {{-- Documents Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Hotel</th>
                        <th class="py-3.5 px-4">Document</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Uploaded</th>
                        <th class="py-3.5 px-4">Reviewed</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($documents as $doc)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-4 px-4">
                            <div class="font-bold text-slate-900 text-sm">{{ $doc->hotel->name ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $doc->hotel->hotel_code ?? '' }} &middot; {{ $doc->hotel->owner_name ?? '' }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-semibold text-slate-800">{{ $doc->document_name }}</div>
                            <div class="text-[10px] text-slate-400">{{ $doc->original_filename }} &middot; v{{ $doc->version }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $doc->status->color() }}">
                                {{ $doc->status->label() }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-xs text-slate-500">
                            <div>{{ $doc->uploaded_at?->format('d M Y') ?? 'N/A' }}</div>
                            <div class="text-[10px] text-slate-400">by {{ $doc->uploader?->name ?? 'N/A' }}</div>
                        </td>
                        <td class="py-4 px-4 text-xs text-slate-500">
                            @if($doc->reviewed_at)
                                <div>{{ $doc->reviewed_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400">by {{ $doc->reviewer?->name ?? 'N/A' }}</div>
                            @else
                                <span class="text-slate-300 italic">Pending</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right whitespace-nowrap">
                            <button wire:click="viewDocument({{ $doc->id }})"
                                    class="text-sky-600 hover:text-sky-800 font-bold text-xs mr-2 cursor-pointer">
                                <i class="far fa-eye mr-0.5"></i> View
                            </button>

                            @if($doc->status !== \App\Enums\DocumentStatus::Approved)
                                <button wire:click="openReview({{ $doc->id }}, 'approve')"
                                        class="text-emerald-600 hover:text-emerald-800 font-bold text-xs mr-2 cursor-pointer">
                                    <i class="fas fa-check mr-0.5"></i> Approve
                                </button>
                                <button wire:click="openReview({{ $doc->id }}, 'reject')"
                                        class="text-rose-500 hover:text-rose-700 font-bold text-xs mr-2 cursor-pointer">
                                    <i class="fas fa-times mr-0.5"></i> Reject
                                </button>
                                <button wire:click="openReview({{ $doc->id }}, 'request_replacement')"
                                        class="text-amber-600 hover:text-amber-800 font-bold text-xs cursor-pointer">
                                    <i class="fas fa-sync-alt mr-0.5"></i> Request Update
                                </button>
                            @endif

                            <button wire:click="viewHistory({{ $doc->hotel_id }}, '{{ $doc->document_type }}', '{{ addslashes($doc->document_name) }}')"
                                    class="text-slate-400 hover:text-slate-600 font-bold text-xs ml-2 cursor-pointer">
                                <i class="fas fa-clock-rotate-left"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="fas fa-file-alt text-4xl mb-3 block"></i>
                            <p class="font-semibold">No documents found</p>
                            <p class="text-xs mt-1">Adjust your filters or search criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $documents->links() }}
        </div>
    </div>

    {{-- Review Modal --}}
    @if($showReviewModal && $reviewDocument)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full border border-slate-100">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800">
                        @if($reviewAction === 'approve') Approve Document
                        @elseif($reviewAction === 'reject') Reject Document
                        @else Request Document Update
                        @endif
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $reviewDocument->document_name }} &middot; {{ $reviewHotel->name }}</p>
                </div>
                <button wire:click="closeReview" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Document Preview --}}
            <div class="px-5 pt-4">
                @if($reviewDocument->is_pdf)
                    <iframe src="{{ route('superadmin.document.preview', $reviewDocument->id) }}" class="w-full h-48 rounded-lg border border-slate-200"></iframe>
                @elseif($reviewDocument->is_image)
                    <img src="{{ route('superadmin.document.preview', $reviewDocument->id) }}" class="max-h-48 mx-auto rounded-lg border border-slate-200" alt="Preview">
                @endif
            </div>

            <form wire:submit="submitReview" class="p-5 space-y-4">
                @if($reviewAction !== 'approve')
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                        {{ $reviewAction === 'reject' ? 'Rejection Reason *' : 'Reason for Update Request *' }}
                    </label>
                    <textarea wire:model="reviewReason" rows="3" required
                              placeholder="{{ $reviewAction === 'reject' ? 'Explain why this document is rejected...' : 'Explain what needs to be updated...' }}"
                              class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500 resize-none"></textarea>
                    @error('reviewReason') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Admin Comment (optional)</label>
                    <textarea wire:model="reviewComment" rows="2" placeholder="Additional notes..."
                              class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500 resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeReview" class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 cursor-pointer transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg text-white text-sm font-semibold shadow-sm cursor-pointer transition
                            @if($reviewAction === 'approve') bg-emerald-600 hover:bg-emerald-700
                            @elseif($reviewAction === 'reject') bg-rose-600 hover:bg-rose-700
                            @else bg-amber-600 hover:bg-amber-700 @endif">
                        @if($reviewAction === 'approve')
                            <i class="fas fa-check mr-1"></i> Approve
                        @elseif($reviewAction === 'reject')
                            <i class="fas fa-times mr-1"></i> Reject
                        @else
                            <i class="fas fa-sync-alt mr-1"></i> Request Update
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

   {{-- View Document Modal --}}
@if($showViewModal && $viewingDocument)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-3 sm:p-4">

    {{-- Popup --}}
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-hidden border border-slate-100">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 sm:px-5 sm:py-4 border-b border-slate-100">
            <div class="min-w-0 pr-3">
                <h3 class="text-sm sm:text-base font-bold text-slate-800 truncate">
                    {{ $viewingDocument->document_name }}
                </h3>

                <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5 truncate">
                    {{ $viewingDocument->hotel->name }} &middot;
                    Version {{ $viewingDocument->version }}
                </p>
            </div>

            <button
                wire:click="closeView"
                class="flex-shrink-0 text-slate-400 hover:text-slate-600 p-2 rounded-lg cursor-pointer hover:bg-slate-100 transition">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Scrollable Content --}}
        <div class="px-4 py-4 sm:px-5 sm:py-5 overflow-y-auto max-h-[65vh]">

            {{-- Document Preview --}}
            <div class="flex justify-center items-center bg-slate-50 rounded-xl border border-slate-200 p-2 sm:p-3">

                @if($viewingDocument->is_pdf)

                    <iframe
                        src="{{ route('superadmin.document.preview', $viewingDocument->id) }}"
                        class="w-full h-64 sm:h-72 md:h-80 rounded-lg border border-slate-200 bg-white">
                    </iframe>

                @elseif($viewingDocument->is_image)

                    <img
                        src="{{ route('superadmin.document.preview', $viewingDocument->id) }}"
                        class="w-auto max-w-full max-h-72 sm:max-h-80 md:max-h-96 object-contain rounded-lg border border-slate-200"
                        alt="Preview">

                @endif

            </div>

            {{-- Document Information --}}
            <div class="mt-4 grid grid-cols-2 gap-2 sm:gap-3 text-xs">

                <div class="bg-slate-50 rounded-lg p-3">
                    <span class="text-slate-400 block mb-0.5">Status</span>
                    <span class="font-bold border px-2 py-0.5 rounded-full text-[10px] {{ $viewingDocument->status->color() }}">
                        {{ $viewingDocument->status->label() }}
                    </span>
                </div>

                <div class="bg-slate-50 rounded-lg p-3">
                    <span class="text-slate-400 block mb-0.5">File Size</span>
                    <span class="font-bold text-slate-700">
                        {{ $viewingDocument->file_size_formatted }}
                    </span>
                </div>

                <div class="bg-slate-50 rounded-lg p-3">
                    <span class="text-slate-400 block mb-0.5">Uploaded By</span>
                    <span class="font-bold text-slate-700 truncate block">
                        {{ $viewingDocument->uploader?->name ?? 'N/A' }}
                    </span>
                </div>

                <div class="bg-slate-50 rounded-lg p-3">
                    <span class="text-slate-400 block mb-0.5">Reviewed By</span>
                    <span class="font-bold text-slate-700 truncate block">
                        {{ $viewingDocument->reviewer?->name ?? 'Pending' }}
                    </span>
                </div>

            </div>

            {{-- Rejection Reason --}}
            @if($viewingDocument->rejection_reason)
            <div class="mt-4 p-3 bg-rose-50 border border-rose-100 rounded-lg">
                <span class="text-[10px] font-bold text-rose-600 uppercase block mb-0.5">
                    Rejection Reason
                </span>
                <p class="text-xs text-rose-700">
                    {{ $viewingDocument->rejection_reason }}
                </p>
            </div>
            @endif

            {{-- Admin Comment --}}
            @if($viewingDocument->admin_comment)
            <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-lg">
                <span class="text-[10px] font-bold text-blue-600 uppercase block mb-0.5">
                    Admin Comment
                </span>
                <p class="text-xs text-blue-700">
                    {{ $viewingDocument->admin_comment }}
                </p>
            </div>
            @endif

        </div>

       {{-- Footer --}}
<div class="flex-shrink-0 border-t border-slate-100 bg-slate-50 px-4 py-3 sm:px-5 sm:py-4">

    <div class="flex items-center justify-end gap-2">

        <a
            href="{{ route('superadmin.document.download', $viewingDocument->id) }}"
            class="inline-flex items-center justify-center gap-1.5
                   px-3.5 py-2
                   rounded-lg
                   border border-slate-200
                   bg-white
                   text-slate-600
                   text-xs font-semibold
                   shadow-sm
                   hover:bg-slate-50
                   hover:text-slate-800
                   hover:border-slate-300
                   transition-all duration-200">
            <i class="fas fa-download text-[10px]"></i>
            <span>Download</span>
        </a>

        <button
            wire:click="closeView"
            class="inline-flex items-center justify-center gap-1.5
                   px-3.5 py-2
                   rounded-lg
                   bg-slate-800
                   text-white
                   text-xs font-semibold
                   shadow-sm
                   hover:bg-slate-700
                   transition-all duration-200
                   cursor-pointer">
            <i class="fas fa-times text-[10px]"></i>
            <span>Close</span>
        </button>

    </div>

</div>

    </div>
</div>
@endif

    {{-- History Modal --}}
    @if($showHistoryModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-slate-100 max-h-[85vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Document Version History</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $historyTypeName }}</p>
                </div>
                <button wire:click="closeHistory" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="overflow-y-auto p-5 space-y-3">
                @forelse($historyDocs as $hDoc)
                <div class="border border-slate-200 rounded-xl p-4 {{ $hDoc['is_current'] ? 'bg-indigo-50/30 border-indigo-200' : 'bg-slate-50' }}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-800">Version {{ $hDoc['version'] }}</span>
                            @if($hDoc['is_current'])
                                <span class="text-[9px] font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Current</span>
                            @endif
                        </div>
                        @php
                            $hStatus = \App\Enums\DocumentStatus::from($hDoc['status']);
                        @endphp
                        <span class="text-[10px] font-bold border px-2 py-0.5 rounded-full {{ $hStatus->color() }}">{{ $hStatus->label() }}</span>
                    </div>
                    <div class="text-xs text-slate-500 space-y-0.5">
                        <p><span class="font-medium">File:</span> {{ $hDoc['original_filename'] }}</p>
                        <p><span class="font-medium">Uploaded:</span> {{ $hDoc['uploaded_at'] ?? 'N/A' }}</p>
                        @if($hDoc['reviewed_at'])
                            <p><span class="font-medium">Reviewed:</span> {{ $hDoc['reviewed_at'] }}</p>
                        @endif
                        @if($hDoc['rejection_reason'])
                            <p class="text-rose-600 mt-1"><span class="font-medium">Reason:</span> {{ $hDoc['rejection_reason'] }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-400 text-sm">
                    <i class="fas fa-clock-rotate-left text-3xl mb-2 block"></i>
                    No version history found.
                </div>
                @endforelse
            </div>
            <div class="flex justify-end p-5 border-t border-slate-100 shrink-0">
                <button wire:click="closeHistory" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
