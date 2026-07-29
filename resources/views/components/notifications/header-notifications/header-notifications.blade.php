<div wire:poll.20s x-data="{ open: false }" class="relative">
    <button @click="open = !open"
            class="relative w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-50 hover:text-slate-600 border border-transparent hover:border-slate-200/80 transition-all cursor-pointer">
        <i class="fas fa-bell text-sm"></i>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white ring-2 ring-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" @click.outside="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50 overflow-hidden"
         style="display:none">
        
        {{-- Dropdown Header --}}
        <div class="px-4 py-2.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-800">Notifications</span>
                @if($unreadCount > 0)
                    <span class="px-2 py-0.5 text-[10px] font-bold bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100">
                        {{ $unreadCount }} Unread
                    </span>
                @endif
            </div>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 transition-colors cursor-pointer">
                    Mark all read
                </button>
            @endif
        </div>

        {{-- Notifications List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
            @forelse($notifications as $notification)
                @php
                    $iconClass = match($notification->type) {
                        'hotel_registered' => 'fa-building text-indigo-500 bg-indigo-50',
                        'hotel_approved'   => 'fa-check-circle text-emerald-500 bg-emerald-50',
                        'room_booked'      => 'fa-bed text-purple-500 bg-purple-50',
                        default            => 'fa-bell text-blue-500 bg-blue-50',
                    };
                @endphp
                <div wire:key="notification-{{ $notification->id }}" 
                     wire:click="markAsRead({{ $notification->id }})"
                     class="p-3.5 hover:bg-slate-50/80 transition-colors cursor-pointer flex gap-3 items-start relative group {{ $notification->is_read ? 'opacity-70 bg-white' : 'bg-indigo-50/20' }}">
                    
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5 {{ explode(' ', $iconClass)[1] ?? 'bg-slate-100' }}">
                        <i class="fas {{ $notification->type === 'hotel_registered' ? 'fa-building' : ($notification->type === 'hotel_approved' ? 'fa-check-circle' : ($notification->type === 'room_booked' ? 'fa-bed' : 'fa-bell')) }} text-xs"></i>
                    </div>

                    <div class="flex-1 min-w-0">
                        @if($notification->link)
                            <a href="{{ $notification->link }}" wire:navigate class="text-xs font-bold text-slate-800 hover:text-indigo-600 block leading-tight truncate">
                                {{ $notification->title }}
                            </a>
                        @else
                            <p class="text-xs font-bold text-slate-800 leading-tight truncate">{{ $notification->title }}</p>
                        @endif
                        <p class="text-[11px] text-slate-600 mt-1 leading-snug break-words line-clamp-2">{{ $notification->message }}</p>
                        <p class="text-[9px] font-medium text-slate-400 mt-1.5 flex items-center gap-1">
                            <i class="far fa-clock text-[8px]"></i>
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>

                    @if(!$notification->is_read)
                        <span class="w-2 h-2 rounded-full bg-indigo-500 shrink-0 mt-1.5"></span>
                    @endif
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <div class="w-10 h-10 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-bell-slash text-sm"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-400">No notifications yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
