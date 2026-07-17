<div>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">OTA Channel Manager</h1>
        <p class="text-sm text-slate-500 mt-0.5">Sync inventory and rates with Online Travel Agencies automatically</p>
    </div>

    {{-- Channel Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($channelsList as $c)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col justify-between">
            <div>
                {{-- Header / Logo --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-650 rounded-xl flex items-center justify-center border border-indigo-100">
                            @if($c->channel_name === 'Booking.com')
                            <i class="fas fa-bold text-lg"></i>
                            @elseif($c->channel_name === 'Expedia')
                            <i class="fas fa-plane text-lg"></i>
                            @else
                            <i class="fab fa-airbnb text-lg"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">{{ $c->channel_name }}</h3>
                            <span class="text-[10px] text-slate-400">XML / JSON API Connection</span>
                        </div>
                    </div>

                    {{-- Connection Badge --}}
                    @if($c->status === 'connected')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        Connected
                    </span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-50 text-slate-500 border border-slate-200">
                        Disconnected
                    </span>
                    @endif
                </div>

                <p class="text-xs text-slate-455 mb-4">Automatically push room inventories, update prices, and import bookings to prevent double-booking issues.</p>
                
                {{-- Sync Information --}}
                @if($c->status === 'connected')
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 space-y-2 mb-4">
                    <div class="flex justify-between text-[10px]">
                        <span class="text-slate-450">Sync Status:</span>
                        <span class="font-bold text-emerald-600 uppercase flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Synced
                        </span>
                    </div>
                    <div class="flex justify-between text-[10px]">
                        <span class="text-slate-450">Last Synchronized:</span>
                        <span class="font-semibold text-slate-700">
                            {{ $c->last_sync_at ? $c->last_sync_at->format('d M H:i') : 'Just now' }}
                        </span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="border-t border-slate-100 pt-4 flex gap-2">
                @if($c->status === 'connected')
                <button wire:click="syncChannel({{ $c->id }})" class="flex-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-755 border border-indigo-100 font-bold text-xs py-2 px-3 rounded-lg transition-colors cursor-pointer text-center">
                    <i class="fas fa-sync-alt mr-1"></i> Sync Now
                </button>
                <button wire:click="toggleConnection({{ $c->id }})" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-100 font-bold text-xs py-2 px-3 rounded-lg transition-colors cursor-pointer text-center">
                    Disconnect
                </button>
                @else
                <button wire:click="toggleConnection({{ $c->id }})" class="w-full bg-indigo-600 hover:bg-indigo-755 text-white font-bold text-xs py-2 px-4 rounded-lg transition-colors shadow-sm cursor-pointer text-center">
                    Connect Channel
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
