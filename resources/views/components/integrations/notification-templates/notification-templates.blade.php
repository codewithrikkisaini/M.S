<div>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Notification Templates</h1>
        <p class="text-sm text-slate-500 mt-0.5">Customize transaction emails and automated WhatsApp alerts</p>
    </div>

    {{-- Templates Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($templatesList as $t)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center border {{ str_contains($t->type, 'whatsapp') ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-indigo-50 text-indigo-650 border-indigo-100' }}">
                        <i class="{{ str_contains($t->type, 'whatsapp') ? 'fab fa-whatsapp' : 'far fa-envelope' }} text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wide">
                            {{ str_replace('_', ' ', $t->type) }}
                        </h3>
                        <span class="text-[9px] text-slate-400">Triggered automatically</span>
                    </div>
                </div>

                @if($t->subject)
                <div class="mb-3">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Subject:</span>
                    <p class="text-xs font-bold text-slate-800 truncate">{{ $t->subject }}</p>
                </div>
                @endif

                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Message Body:</span>
                    <p class="text-xs text-slate-655 line-clamp-4 whitespace-pre-line bg-slate-50 p-2.5 rounded-xl border border-slate-100/60 mt-1 font-mono text-[10px]">
                        {{ $t->body }}
                    </p>
                </div>

                {{-- Merge Tags --}}
                <div class="mt-4">
                    <span class="text-[9px] font-bold text-slate-400 uppercase block mb-1">Merge Fields:</span>
                    <div class="flex flex-wrap gap-1">
                        @foreach(explode(',', $t->variables) as $var)
                        <span class="bg-slate-100 text-slate-600 text-[9px] font-semibold px-2 py-0.5 rounded">
                            {{ '{{' . $var . '}}' }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 mt-6">
                <button wire:click="editTemplate({{ $t->id }})" class="w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-755 border border-indigo-100 font-bold text-xs py-2 rounded-lg transition-colors cursor-pointer text-center">
                    <i class="far fa-edit mr-1"></i> Edit Template
                </button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Edit Template Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-xl w-full border border-slate-100 animate-fadeIn duration-200">
            <div class="flex items-center justify-between p-6 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">
                    Edit {{ str_replace('_', ' ', $type) }} Template
                </h3>
                <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg transition-colors cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveTemplate" class="p-6 space-y-4">
                @if(!str_contains($type, 'whatsapp'))
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Subject</label>
                    <input type="text" wire:model="subject" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                    @error('subject') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Message Body</label>
                    <textarea wire:model="body" rows="6" class="w-full text-slate-800 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 font-mono" placeholder="Message content..."></textarea>
                    @error('body') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Reference tags list --}}
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                    <span class="text-[9px] font-bold text-slate-400 uppercase block mb-1">Available placeholders:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(explode(',', $variables) as $var)
                        <button type="button" onclick="navigator.clipboard.writeText('{{ '{{' . $var . '}}' }}'); alert('Copied placeholder: {{ '{{' . $var . '}}' }}')" class="bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-semibold px-2 py-0.5 rounded cursor-pointer transition-colors">
                            {{ '{{' . $var . '}}' }}
                        </button>
                        @endforeach
                    </div>
                    <p class="text-[9px] text-slate-400 mt-2"><i class="fas fa-info-circle mr-1"></i> Click on any badge to copy the placeholder code.</p>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                    <button type="button" wire:click="$set('showModal', false)" class="rounded-lg px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-655 font-semibold text-xs cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary rounded-lg px-5 py-2 bg-indigo-600 hover:bg-indigo-755 text-white font-semibold text-xs cursor-pointer shadow-md">
                        Save Template
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
