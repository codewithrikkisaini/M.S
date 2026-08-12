<div>
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Hotel Documents</h1>
            <p class="text-sm text-slate-500 mt-0.5">Upload, preview, download, and manage your hotel compliance & verification documents.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center border border-slate-100">
                    <i class="fas fa-file-alt text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ $totalFiles }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Files</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100">
                    <i class="fas fa-clipboard-check text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ $approvedCount }} / {{ $requiredCount }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Required Completed</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center border border-emerald-100">
                    <i class="fas fa-chart-pie text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-800 tracking-tight">{{ $verificationScore }}%</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Verification Score</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center border
                    {{ $verificationStatus === 'Verified' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' :
                       ($verificationStatus === 'Action Required' ? 'bg-rose-50 text-rose-600 border-rose-100' :
                       ($verificationStatus === 'Pending Review' ? 'bg-amber-50 text-amber-600 border-amber-100' :
                       'bg-slate-50 text-slate-500 border-slate-100')) }}">
                    <i class="fas {{ $verificationStatus === 'Verified' ? 'fa-shield-halved' : ($verificationStatus === 'Action Required' ? 'fa-triangle-exclamation' : 'fa-hourglass-half') }} text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-800 tracking-tight">{{ $verificationStatus }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Verification Status</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content: Upload + Documents List --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6 mb-6">

        {{-- Upload Card --}}
        <div class="xl:col-span-2">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm h-full">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-800">Upload New Document</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Add verification or legal hotel files</p>
                </div>
                <div class="p-5">
                    <form wire:submit="submitUpload">
                        {{-- Drop Zone --}}
                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-indigo-300 hover:bg-indigo-50/20 transition-all duration-200 cursor-pointer mb-4"
                             onclick="document.getElementById('uploadInput').click()"
                             ondragover="event.preventDefault(); this.classList.add('border-indigo-400', 'bg-indigo-50')"
                             ondragleave="this.classList.remove('border-indigo-400', 'bg-indigo-50')"
                             ondrop="event.preventDefault(); this.classList.remove('border-indigo-400', 'bg-indigo-50');">

                            @if($uploadFile)
                                <div class="space-y-2">
                                    <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center mx-auto border border-emerald-100">
                                        <i class="fas fa-check text-lg"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">{{ $uploadFile->getClientOriginalName() }}</p>
                                    <p class="text-xs text-slate-400">{{ round($uploadFile->getSize() / 1024, 1) }} KB &middot; {{ strtoupper($uploadFile->getClientOriginalExtension()) }}</p>
                                    <button type="button" wire:click="$set('uploadFile', null)" class="text-xs text-rose-500 font-semibold hover:underline cursor-pointer">Remove</button>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center mx-auto border border-slate-100">
                                        <i class="fas fa-cloud-arrow-up text-lg"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-600">Browse file or drag & drop</p>
                                    <p class="text-[11px] text-slate-400">PDF, JPG, PNG (max 20 MB)</p>
                                </div>
                            @endif
                        </div>
                        <input type="file" id="uploadInput" wire:model="uploadFile" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                        @error('uploadFile') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror

                        {{-- Document Type --}}
                        <div class="mb-4">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Document Type *</label>
                            <select wire:model="uploadType" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-500 bg-white">
                                <option value="">Select type...</option>
                                @php $allTypes = \App\Services\HotelDocumentService::getAllTypes(); @endphp
                                @foreach($allTypes as $key => $type)
                                    <option value="{{ $key }}">{{ $type['name'] }}</option>
                                @endforeach
                            </select>
                            @error('uploadType') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Document Name --}}
                        <div class="mb-4">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Document Name *</label>
                            <input type="text" wire:model="uploadName" placeholder="e.g. Business License 2026"
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-indigo-500">
                            @error('uploadName') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Submit --}}
                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm cursor-pointer transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="submitUpload">
                                <i class="fas fa-cloud-arrow-up"></i> Upload Document
                            </span>
                            <span wire:loading wire:target="submitUpload" class="flex items-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i> Uploading...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Uploaded Documents List --}}
        <div class="xl:col-span-3">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm h-full flex flex-col">
                <div class="p-5 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Uploaded Documents</h3>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $totalFiles }} file{{ $totalFiles !== 1 ? 's' : '' }} stored</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="relative">
                            <i class="fas fa-magnifying-glass absolute left-3.5 top-2.5 text-slate-400 text-xs"></i>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search documents..."
                                   class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-5 space-y-3" style="max-height: 520px;">
                    @php $filteredDocs = $this->getFilteredDocuments(); @endphp
                    @forelse($filteredDocs as $key => $doc)
                        <div class="border border-slate-200 rounded-xl p-4 hover:border-slate-300 transition-all duration-150">
                            <div class="flex items-start gap-3">
                                {{-- Icon --}}
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border
                                    {{ $doc->is_pdf ? 'bg-red-50 text-red-500 border-red-100' : 'bg-blue-50 text-blue-500 border-blue-100' }}">
                                    <i class="fas {{ $doc->is_pdf ? 'fa-file-pdf' : 'fa-file-image' }} text-sm"></i>
                                </div>
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-bold text-slate-800 truncate">{{ $doc->document_name }}</h4>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $doc->status->color() }}">
                                            {{ $doc->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                        {{ $doc->original_filename }} &middot; {{ $doc->file_size_formatted }} &middot; v{{ $doc->version }}
                                    </p>
                                    @if($doc->rejection_reason)
                                        <div class="mt-2 p-2 bg-rose-50 border border-rose-100 rounded-lg">
                                            <span class="text-[9px] font-bold text-rose-600 uppercase block mb-0.5">Rejection Reason</span>
                                            <p class="text-[11px] text-rose-700">{{ $doc->rejection_reason }}</p>
                                        </div>
                                    @endif
                                    @if($doc->admin_comment && !$doc->rejection_reason)
                                        <div class="mt-2 p-2 bg-blue-50 border border-blue-100 rounded-lg">
                                            <span class="text-[9px] font-bold text-blue-600 uppercase block mb-0.5">Admin Note</span>
                                            <p class="text-[11px] text-blue-700">{{ $doc->admin_comment }}</p>
                                        </div>
                                    @endif
                                </div>
                                {{-- Actions --}}
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <a href="{{ route('document.preview', $doc->id) }}" target="_blank"
                                       class="p-2 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition" title="Preview">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('document.download', $doc->id) }}"
                                       class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Download">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                    @if($doc->status->canBeReplaced())
                                        <button wire:click="startReplace({{ $doc->id }})"
                                                class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer" title="Replace">
                                            <i class="fas fa-sync-alt text-xs"></i>
                                        </button>
                                    @endif
                                    <button wire:click="viewHistory('{{ $key }}')"
                                            class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition cursor-pointer" title="History">
                                        <i class="fas fa-clock-rotate-left text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-100">
                                <i class="fas fa-file-circle-question text-2xl"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-500">{{ $search ? 'No documents match your search' : 'No documents uploaded yet' }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ $search ? 'Try a different search term' : 'Upload your required hotel verification documents to complete the verification process.' }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Verification Checklist --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-800">Verification Checklist</h3>
            <p class="text-xs text-slate-400 mt-0.5">Mandatory files requirement</p>
        </div>
        <div class="p-5">
            <div class="space-y-2">
                @foreach($completeness as $key => $item)
                    @php
                        $type = $item['type'];
                        $isRequired = $item['required'];
                        $doc = $item['document'];
                        $status = $doc?->status;
                    @endphp
                    @if($isRequired)
                        <div class="flex items-center justify-between py-2.5 px-3 rounded-xl hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3">
                                @if($status && $status === \App\Enums\DocumentStatus::Approved)
                                    <div class="w-7 h-7 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>
                                @elseif($status && in_array($status, [\App\Enums\DocumentStatus::Rejected, \App\Enums\DocumentStatus::ReplacementRequired]))
                                    <div class="w-7 h-7 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-xmark text-xs"></i>
                                    </div>
                                @elseif($status)
                                    <div class="w-7 h-7 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clock text-xs"></i>
                                    </div>
                                @else
                                    <div class="w-7 h-7 bg-slate-100 text-slate-400 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-circle text-[8px]"></i>
                                    </div>
                                @endif
                                <span class="text-sm font-semibold text-slate-700">{{ $type['name'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $status->color() }}">
                                        {{ $status->label() }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Required</span>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Replace Modal --}}
    @if($replacingDocumentId)
    @php $replaceDoc = \App\Models\HotelDocument::find($replacingDocumentId); @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-data x-on:keydown.escape.window="$wire.cancelReplace()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full border border-slate-100" @click.outside="$wire.cancelReplace()">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Replace Document</h3>
                    <p class="text-xs text-slate-400 mt-0.5">New version for: {{ $replaceDoc?->document_name }}</p>
                </div>
                <button wire:click="cancelReplace" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <form wire:submit="submitReplace" class="p-5 space-y-4">
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-indigo-300 transition-all cursor-pointer"
                     onclick="document.getElementById('replaceInput').click()">
                    @if($replaceFile)
                        <p class="text-sm font-bold text-slate-700">{{ $replaceFile->getClientOriginalName() }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ round($replaceFile->getSize() / 1024, 1) }} KB</p>
                    @else
                        <div class="space-y-1">
                            <i class="fas fa-cloud-arrow-up text-2xl text-slate-300"></i>
                            <p class="text-xs text-slate-500 font-semibold">Click to select replacement file</p>
                            <p class="text-[10px] text-slate-400">PDF, JPG, PNG (max 20 MB)</p>
                        </div>
                    @endif
                </div>
                <input type="file" id="replaceInput" wire:model="replaceFile" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                @error('replaceFile') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="cancelReplace" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 cursor-pointer transition">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm cursor-pointer transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="submitReplace">Upload Replacement</span>
                        <span wire:loading wire:target="submitReplace" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> Uploading...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- View Document Modal --}}
    @if($showViewModal && $viewingDocument)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-data x-on:keydown.escape.window="$wire.closeViewModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-slate-100" @click.outside="$wire.closeViewModal()">
            <div class="flex items-center justify-between p-5 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-800">{{ $viewingDocument->document_name }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Version {{ $viewingDocument->version }} &middot; {{ $viewingDocument->original_filename }}</p>
                </div>
                <button wire:click="closeViewModal" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-5">
                @if($viewingDocument->is_pdf)
                    <iframe src="{{ route('document.preview', $viewingDocument->id) }}" class="w-full h-96 rounded-lg border border-slate-200"></iframe>
                @elseif($viewingDocument->is_image)
                    <img src="{{ route('document.preview', $viewingDocument->id) }}" class="max-w-full h-auto rounded-lg border border-slate-200 mx-auto" alt="Preview">
                @endif
                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-slate-50 rounded-lg p-3">
                        <span class="text-slate-400 block mb-0.5">Status</span>
                        <span class="font-bold px-2 py-0.5 rounded-full text-[10px] border {{ $viewingDocument->status->color() }}">{{ $viewingDocument->status->label() }}</span>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3">
                        <span class="text-slate-400 block mb-0.5">File Size</span>
                        <span class="font-bold text-slate-700">{{ $viewingDocument->file_size_formatted }}</span>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3">
                        <span class="text-slate-400 block mb-0.5">Uploaded By</span>
                        <span class="font-bold text-slate-700">{{ $viewingDocument->uploader?->name ?? 'System' }}</span>
                    </div>
                    <div class="bg-slate-50 rounded-lg p-3">
                        <span class="text-slate-400 block mb-0.5">Reviewed By</span>
                        <span class="font-bold text-slate-700">{{ $viewingDocument->reviewer?->name ?? 'Pending' }}</span>
                    </div>
                </div>
                @if($viewingDocument->rejection_reason)
                <div class="mt-4 p-3 bg-rose-50 border border-rose-100 rounded-lg">
                    <span class="text-[10px] font-bold text-rose-600 uppercase block mb-0.5">Rejection Reason</span>
                    <p class="text-xs text-rose-700">{{ $viewingDocument->rejection_reason }}</p>
                </div>
                @endif
            </div>
            <div class="flex justify-end gap-2 p-5 border-t border-slate-100">
                <a href="{{ route('document.download', $viewingDocument->id) }}"
                   class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition inline-flex items-center gap-1.5">
                    <i class="fas fa-download"></i> Download
                </a>
                <button wire:click="closeViewModal" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- History Modal --}}
    @if($showHistoryModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" x-data x-on:keydown.escape.window="$wire.closeHistoryModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-slate-100 max-h-[85vh] overflow-hidden flex flex-col" @click.outside="$wire.closeHistoryModal()">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Document History</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ \App\Services\HotelDocumentService::getType($historyType)['name'] ?? $historyType }} &middot; All Versions</p>
                </div>
                <button wire:click="closeHistoryModal" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="overflow-y-auto p-5 space-y-3">
                @forelse($historyDocs as $hDoc)
                    @php $hStatus = \App\Enums\DocumentStatus::from($hDoc['status']); @endphp
                    <div class="border border-slate-200 rounded-xl p-4 {{ $hDoc['is_current'] ? 'bg-indigo-50/30 border-indigo-200' : 'bg-slate-50' }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-800">Version {{ $hDoc['version'] }}</span>
                                @if($hDoc['is_current'])
                                    <span class="text-[9px] font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Current</span>
                                @endif
                            </div>
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
                <button wire:click="closeHistoryModal" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
