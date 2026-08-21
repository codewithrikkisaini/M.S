<div>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('guests.blacklist.index') }}" class="btn-icon" title="Back to Blacklist">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-slate-800">Blacklist Case</h1>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-600">{{ $blacklist->case_number }}</span>
                        @if($blacklist->status === 'active')
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700"><i class="fas fa-ban mr-1"></i> ACTIVE</span>
                        @else
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700"><i class="fas fa-check-circle mr-1"></i> RELEASED</span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mt-1">{{ $blacklist->first_name }} {{ $blacklist->last_name }} &mdash; Created {{ $blacklist->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($blacklist->status === 'active')
                    <button wire:click="openReleaseModal" class="btn-primary"><i class="fas fa-unlock mr-2"></i> Release Guest</button>
                @else
                    <button wire:click="openReBlacklistModal" class="btn-primary bg-red-600 hover:bg-red-700"><i class="fas fa-ban mr-2"></i> Re-Blacklist</button>
                @endif
                <a href="{{ route('guests.blacklist.edit', $blacklist->id) }}" class="btn-secondary"><i class="fas fa-pen mr-2"></i> Edit</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="pms-card">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <div class="flex items-center gap-2"><i class="fas fa-user text-slate-500"></i><h2 class="text-lg font-semibold text-slate-800">Guest Identity</h2></div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Full Name</span><p class="text-slate-800 font-medium mt-1">{{ $blacklist->first_name }} {{ $blacklist->last_name }}</p></div>
                            <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Date of Birth</span><p class="text-slate-800 font-medium mt-1">{{ $blacklist->date_of_birth ? \Carbon\Carbon::parse($blacklist->date_of_birth)->format('M d, Y') : '—' }}</p></div>
                            <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wide">ID Type</span><p class="text-slate-800 font-medium mt-1">{{ $blacklist->id_type ?: '—' }}</p></div>
                            <div><span class="text-xs font-medium text-slate-500 uppercase tracking-wide">ID Number</span><p class="text-slate-800 font-medium mt-1">{{ $blacklist->id_number ?: '—' }}</p></div>
                        </div>
                        @if($blacklist->guest)
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <a href="{{ route('guests.edit', $blacklist->guest->id) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium"><i class="fas fa-external-link-alt mr-1.5 text-xs"></i> View Linked Guest Profile</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="pms-card">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <div class="flex items-center gap-2"><i class="fas fa-exclamation-triangle text-red-500"></i><h2 class="text-lg font-semibold text-slate-800">Blacklist Reason</h2></div>
                    </div>
                    <div class="p-6">
                        <div class="p-4 bg-red-50 border border-red-200 rounded-xl"><p class="text-red-800 leading-relaxed">{{ $blacklist->reason ?: 'No reason provided.' }}</p></div>
                    </div>
                </div>

                <div class="pms-card">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2"><i class="fas fa-file-alt text-slate-500"></i><h2 class="text-lg font-semibold text-slate-800">Supporting Documents</h2></div>
                            @if($blacklist->documents && $blacklist->documents->count() > 0)
                                <span class="text-sm text-slate-500">{{ $blacklist->documents->count() }} file(s)</span>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        @if($blacklist->documents && $blacklist->documents->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead><tr class="border-b border-slate-200">
                                        <th class="text-left py-3 px-4 font-semibold text-slate-600">Document</th>
                                        <th class="text-left py-3 px-4 font-semibold text-slate-600">Category</th>
                                        <th class="text-left py-3 px-4 font-semibold text-slate-600">Type</th>
                                        <th class="text-left py-3 px-4 font-semibold text-slate-600">Uploaded By</th>
                                        <th class="text-left py-3 px-4 font-semibold text-slate-600">Date</th>
                                        <th class="text-right py-3 px-4 font-semibold text-slate-600">Actions</th>
                                    </tr></thead>
                                    <tbody>
                                        @foreach($blacklist->documents as $doc)
                                            <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-2">
                                                        @if($doc->isPdf())<i class="fas fa-file-pdf text-red-500"></i>
                                                        @elseif($doc->isImage())<i class="fas fa-file-image text-blue-500"></i>
                                                        @else<i class="fas fa-file text-slate-400"></i>@endif
                                                        <span class="text-slate-800">{{ $doc->original_filename }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-slate-600">{{ $doc->getCategoryLabel() }}</td>
                                                <td class="py-3 px-4 text-slate-600">{{ strtoupper(pathinfo($doc->original_filename, PATHINFO_EXTENSION)) }}</td>
                                                <td class="py-3 px-4 text-slate-600">{{ $doc->uploader->name ?? '—' }}</td>
                                                <td class="py-3 px-4 text-slate-600">{{ $doc->created_at->format('M d, Y') }}</td>
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <button wire:click="viewDocument({{ $doc->id }})" class="text-blue-500 hover:text-blue-700 transition-colors" title="View"><i class="fas fa-eye"></i></button>
                                                        <a href="{{ route('guests.blacklist.document.download', $doc->id) }}" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Download"><i class="fas fa-download"></i></a>
                                                        <button wire:click="deleteDocument({{ $doc->id }})" wire:confirm="Delete this document?" class="text-red-500 hover:text-red-700 transition-colors" title="Delete"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 text-slate-400"><i class="fas fa-folder-open text-3xl mb-2"></i><p class="text-sm">No documents uploaded yet.</p></div>
                        @endif
                    </div>
                </div>

                <div class="pms-card">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <div class="flex items-center gap-2"><i class="fas fa-clock text-slate-500"></i><h2 class="text-lg font-semibold text-slate-800">Case Timeline</h2></div>
                    </div>
                    <div class="p-6">
                        @php
                            $timelineEvents = [];
                            $seenDates = [];

                            $blacklistKey = $blacklist->created_at->timestamp . '-blacklisted';
                            if (!in_array($blacklistKey, $seenDates)) {
                                $seenDates[] = $blacklistKey;
                                $timelineEvents[] = [
                                    'type' => 'blacklisted',
                                    'date' => $blacklist->created_at,
                                    'user' => $blacklist->blacklister->name ?? null,
                                    'label' => 'Guest Blacklisted',
                                    'color' => 'red',
                                    'reason' => $blacklist->reason,
                                    'badge' => $blacklist->status === 'active' ? 'CURRENT' : 'RESOLVED',
                                    'badgeColor' => $blacklist->status === 'active' ? 'red' : 'slate',
                                    'sort' => $blacklist->created_at->timestamp,
                                ];
                            }

                            if ($blacklist->documents) {
                                foreach ($blacklist->documents->sortBy('created_at') as $doc) {
                                    $docKey = $doc->created_at->timestamp . '-doc-' . $doc->id;
                                    if (!in_array($docKey, $seenDates)) {
                                        $seenDates[] = $docKey;
                                        $isReleaseDoc = ($doc->category === 'Release Evidence');
                                        $timelineEvents[] = [
                                            'type' => 'document',
                                            'date' => $doc->created_at,
                                            'user' => $doc->uploader->name ?? null,
                                            'label' => $isReleaseDoc ? 'Release Document Added' : 'Document Added',
                                            'color' => $isReleaseDoc ? 'blue' : 'slate',
                                            'document' => $doc->original_filename,
                                            'documentCategory' => $doc->getCategoryLabel(),
                                            'isPdf' => $doc->isPdf(),
                                            'isImage' => $doc->isImage(),
                                            'docId' => $doc->id,
                                            'sort' => $doc->created_at->timestamp * 1000 + $doc->id,
                                        ];
                                    }
                                }
                            }

                            if ($blacklist->status === 'released' && $blacklist->released_at) {
                                $releaseKey = $blacklist->released_at->timestamp . '-released';
                                if (!in_array($releaseKey, $seenDates)) {
                                    $seenDates[] = $releaseKey;
                                    $timelineEvents[] = [
                                        'type' => 'released',
                                        'date' => $blacklist->released_at,
                                        'user' => $blacklist->releaser->name ?? null,
                                        'label' => 'Guest Released',
                                        'color' => 'emerald',
                                        'reason' => $blacklist->release_reason,
                                        'notes' => $blacklist->release_notes,
                                        'badge' => 'CURRENT',
                                        'badgeColor' => 'emerald',
                                        'sort' => $blacklist->released_at->timestamp,
                                    ];
                                }
                            }

                            usort($timelineEvents, fn($a, $b) => $a['sort'] <=> $b['sort']);
                        @endphp

                        <div class="relative pl-6">
                            <div class="absolute left-2 top-2 bottom-2 w-0.5 bg-slate-200"></div>

                            @forelse($timelineEvents as $index => $event)
                                <div class="relative {{ $index < count($timelineEvents) - 1 ? 'mb-6' : '' }}">
                                    @if($event['type'] === 'blacklisted')
                                        <div class="absolute -left-4 top-1 w-4 h-4 rounded-full bg-red-500 border-2 border-white shadow"></div>
                                    @elseif($event['type'] === 'released')
                                        <div class="absolute -left-4 top-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white shadow"></div>
                                    @else
                                        <div class="absolute -left-4 top-1 w-4 h-4 rounded-full bg-blue-400 border-2 border-white shadow"></div>
                                    @endif

                                    <div class="ml-4">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-semibold text-slate-800">{{ $event['label'] }}</h3>
                                            @if(isset($event['badge']))
                                                <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-{{ $event['badgeColor'] }}-100 text-{{ $event['badgeColor'] }}-700">{{ $event['badge'] }}</span>
                                            @endif
                                        </div>

                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $event['date']->format('M d, Y \a\t h:i A') }}
                                            @if($event['user']) &mdash; by {{ $event['user'] }}@endif
                                        </p>

                                        @if($event['type'] === 'blacklisted' && isset($event['reason']) && $event['reason'])
                                            <p class="text-sm text-slate-600 mt-1.5 line-clamp-2">{{ Str::limit($event['reason'], 150) }}</p>
                                        @endif

                                        @if($event['type'] === 'document')
                                            <div class="mt-1.5 flex items-center gap-2 p-2 bg-slate-50 border border-slate-100 rounded-lg">
                                                @if($event['isPdf'])
                                                    <i class="fas fa-file-pdf text-red-500 text-sm shrink-0"></i>
                                                @elseif($event['isImage'])
                                                    <i class="fas fa-file-image text-blue-500 text-sm shrink-0"></i>
                                                @else
                                                    <i class="fas fa-file text-slate-400 text-sm shrink-0"></i>
                                                @endif
                                                <span class="text-sm text-slate-700 truncate">{{ $event['document'] }}</span>
                                                <span class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-slate-200 text-slate-600 shrink-0">{{ $event['documentCategory'] }}</span>
                                            </div>
                                        @endif

                                        @if($event['type'] === 'released')
                                            @if(isset($event['reason']) && $event['reason'])
                                                <p class="text-sm text-slate-600 mt-1.5">Reason: {{ $event['reason'] }}</p>
                                            @endif
                                            @if(isset($event['notes']) && $event['notes'])
                                                <p class="text-sm text-slate-500 mt-1">Notes: {{ Str::limit($event['notes'], 150) }}</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-slate-400">
                                    <i class="fas fa-clock text-3xl mb-2"></i>
                                    <p class="text-sm">No timeline events yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="pms-card">
                    <div class="border-b border-slate-200 px-6 py-4"><div class="flex items-center gap-2"><i class="fas fa-info-circle text-slate-500"></i><h2 class="text-lg font-semibold text-slate-800">Status</h2></div></div>
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between items-center"><span class="text-sm text-slate-500">Case Number</span><span class="text-sm font-semibold text-slate-800">{{ $blacklist->case_number }}</span></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-500">Status</span>
                            @if($blacklist->status === 'active')<span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700"><i class="fas fa-ban mr-1"></i> Active</span>
                            @else<span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700"><i class="fas fa-check-circle mr-1"></i> Released</span>@endif
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-500">Booking Status</span>
                            @if($blacklist->guest && $blacklist->guest->bookings && $blacklist->guest->bookings->where('status', 'active')->count() > 0)
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-700"><i class="fas fa-bed mr-1"></i> Has Active Booking</span>
                            @else<span class="text-sm text-slate-600">None</span>@endif
                        </div>
                    </div>
                </div>

                <div class="pms-card">
                    <div class="border-b border-slate-200 px-6 py-4"><div class="flex items-center gap-2"><i class="fas fa-list-alt text-slate-500"></i><h2 class="text-lg font-semibold text-slate-800">Blacklist Details</h2></div></div>
                    <div class="p-6 space-y-3">
                        <div><span class="text-sm text-slate-500">Blacklisted By</span><p class="text-sm font-medium text-slate-800">{{ $blacklist->blacklister->name ?? '—' }}</p></div>
                        <div><span class="text-sm text-slate-500">Blacklisted At</span><p class="text-sm font-medium text-slate-800">{{ $blacklist->created_at->format('M d, Y h:i A') }}</p></div>
                        <div class="pt-3 border-t border-slate-100">
                            @if($blacklist->status === 'released')
                                <div class="space-y-3">
                                    <div><span class="text-sm text-slate-500">Released By</span><p class="text-sm font-medium text-slate-800">{{ $blacklist->releaser->name ?? '—' }}</p></div>
                                    <div><span class="text-sm text-slate-500">Released At</span><p class="text-sm font-medium text-slate-800">{{ $blacklist->released_at->format('M d, Y h:i A') }}</p></div>
                                    @if($blacklist->release_reason)<div><span class="text-sm text-slate-500">Release Reason</span><p class="text-sm font-medium text-slate-800">{{ $blacklist->release_reason }}</p></div>@endif
                                    @if($blacklist->release_notes)<div><span class="text-sm text-slate-500">Release Notes</span><p class="text-sm text-slate-600">{{ $blacklist->release_notes }}</p></div>@endif
                                </div>
                            @else
                                <p class="text-sm text-slate-400 italic">Guest has not been released.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pms-card">
                    <div class="border-b border-slate-200 px-6 py-4"><div class="flex items-center gap-2"><i class="fas fa-cog text-slate-500"></i><h2 class="text-lg font-semibold text-slate-800">Actions</h2></div></div>
                    <div class="p-6 space-y-3">
                        @if($blacklist->status === 'active')
                            <button wire:click="openReleaseModal" class="w-full btn-primary justify-center"><i class="fas fa-unlock mr-2"></i> Release Guest</button>
                        @else
                            <button wire:click="openReBlacklistModal" class="w-full btn-primary bg-red-600 hover:bg-red-700 justify-center"><i class="fas fa-ban mr-2"></i> Re-Blacklist</button>
                        @endif
                        <a href="{{ route('guests.blacklist.edit', $blacklist->id) }}" class="w-full btn-secondary justify-center text-center"><i class="fas fa-pen mr-2"></i> Edit Case</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                            >
                                Supporting Documents

                                <span class="font-normal text-slate-400">
                                    (Optional)
                                </span>
                            </label>


                            <label
                                class="group flex cursor-pointer items-center gap-3 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-3 transition-all duration-200 hover:border-emerald-400 hover:bg-emerald-50/30"
                            >

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

    @if($showReBlacklistModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="background: rgba(15,23,42,0.6); backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" @click.away="closeReBlacklistModal">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-ban text-red-500 mr-2"></i> Re-Blacklist Guest</h3>
                <button wire:click="closeReBlacklistModal" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-xs text-slate-500">Guest</p>
                    <p class="text-sm font-bold text-slate-800">{{ $blacklist->first_name }} {{ $blacklist->last_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Reason <span class="text-red-500">*</span></label>
                    <textarea wire:model="reBlacklist_reason" rows="4" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Why is this guest being re-blacklisted?"></textarea>
                    @error('reBlacklist_reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                <button wire:click="closeReBlacklistModal" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">Cancel</button>
                <button wire:click="reBlacklist" wire:loading.attr="disabled" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="reBlacklist"><i class="fas fa-ban mr-1"></i> Confirm Re-Blacklist</span>
                    <span wire:loading wire:target="reBlacklist"><i class="fas fa-spinner fa-spin mr-1"></i> Processing...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($showDocumentModal && $viewingDocument)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="background: rgba(15,23,42,0.6); backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden" @click.away="closeDocumentModal">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800"><i class="fas fa-file-alt text-slate-500 mr-2"></i> {{ $viewingDocument->original_filename }}</h3>
                <button wire:click="closeDocumentModal" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
            </div>
            <div class="px-6 py-4 bg-slate-100 flex items-center justify-center" style="min-height: 400px; max-height: 70vh;">
                @if($viewingDocument->isPdf())
                    <iframe src="{{ route('guests.blacklist.document.preview', $viewingDocument->id) }}" class="w-full h-full" style="min-height: 400px;" frameborder="0"></iframe>
                @elseif($viewingDocument->isImage())
                    <img src="{{ route('guests.blacklist.document.preview', $viewingDocument->id) }}" alt="{{ $viewingDocument->original_filename }}" class="max-w-full max-h-full object-contain">
                @else
                    <div class="text-center py-16"><i class="fas fa-file text-4xl text-slate-400 mb-3"></i><p class="text-slate-500">Preview not available for this file type.</p></div>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
                <a href="{{ route('guests.blacklist.document.download', $viewingDocument->id) }}" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50"><i class="fas fa-download mr-1"></i> Download</a>
                <button wire:click="closeDocumentModal" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700"><i class="fas fa-times mr-1"></i> Close</button>
            </div>
        </div>
    </div>
    @endif
</div>
