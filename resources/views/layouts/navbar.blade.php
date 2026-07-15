{{-- ===== TOP NAVBAR ===== --}}
<header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-4 border-b border-slate-100 bg-white/80 backdrop-blur-md px-4 sm:px-6 shadow-sm">

    {{-- Mobile hamburger --}}
    <button @click="mobileSidebarOpen = true"
            class="lg:hidden text-slate-500 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
        <i class="fas fa-bars text-base"></i>
    </button>

    {{-- Desktop sidebar toggle --}}
    <button @click="sidebarOpen = !sidebarOpen"
            class="hidden lg:flex items-center justify-center w-9 h-9 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-600 border border-transparent hover:border-slate-200 transition-all cursor-pointer">
        <i class="fas fa-bars text-sm" :class="sidebarOpen ? '' : 'rotate-180'" style="transition: transform 0.2s"></i>
    </button>

    {{-- Search --}}
    <div class="flex-1 max-w-xs sm:max-w-md">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
            <input type="text"
                   placeholder="Search reservations, guests..."
                   class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-50/80 border border-slate-200/80 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent placeholder-slate-400/85 transition-all text-slate-800">
        </div>
    </div>

    <div class="flex items-center gap-2.5 ml-auto">

        {{-- Date display --}}
        <div class="hidden md:flex items-center gap-1.5 text-[11px] font-bold text-slate-500 bg-slate-50/85 px-3 py-1.5 rounded-xl border border-slate-200/80">
            <i class="fas fa-calendar-day text-indigo-500"></i>
            <span>{{ now()->format('D, d M Y') }}</span>
        </div>

        {{-- Notifications --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="relative w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-50 hover:text-slate-600 border border-transparent hover:border-slate-200/80 transition-all cursor-pointer">
                <i class="fas fa-bell text-sm"></i>
                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
            </button>
            <div x-show="open" @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 py-2.5 z-50"
                 style="display:none">
                <div class="px-4 py-2 border-b border-slate-50">
                    <p class="text-xs font-bold text-slate-800">Notifications</p>
                </div>
                <div class="px-4 py-8 text-center">
                    <div class="w-10 h-10 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-2.5">
                        <i class="fas fa-bell-slash text-sm"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-400">No new notifications</p>
                </div>
            </div>
        </div>

        {{-- Quick Action --}}
        <a href="{{ route('reservations.create') }}"
           class="hidden sm:inline-flex items-center gap-2 btn-primary btn-sm rounded-lg shadow-sm">
            <i class="fas fa-plus text-[10px]"></i>
            <span>New Booking</span>
        </a>

        {{-- User Dropdown --}}
        @auth
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-200/80 cursor-pointer">
                @php
                    $initials = strtoupper(substr(Auth::user()->name, 0, 1));
                    $gradients = [
                        'A' => 'from-indigo-400 to-indigo-600', 'B' => 'from-emerald-400 to-emerald-600',
                        'C' => 'from-blue-400 to-blue-600', 'D' => 'from-rose-400 to-rose-600',
                        'E' => 'from-amber-400 to-amber-600', 'F' => 'from-orange-400 to-orange-600',
                        'G' => 'from-teal-400 to-teal-600', 'H' => 'from-purple-400 to-purple-600',
                        'I' => 'from-pink-400 to-pink-600', 'J' => 'from-cyan-400 to-cyan-600',
                    ];
                    $gradient = $gradients[$initials] ?? 'from-slate-400 to-slate-600';
                @endphp
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $gradient }} flex items-center justify-center shrink-0 shadow-sm border border-white">
                    <span class="text-white text-xs font-black">{{ $initials }}</span>
                </div>
                <div class="hidden sm:block text-left pr-1">
                    <p class="text-xs font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-[9px] font-semibold text-slate-400 leading-tight mt-0.5">
                        {{ Auth::user()->role?->name ?? 'Staff' }}
                    </p>
                </div>
                <i class="fas fa-chevron-down text-[10px] text-slate-400 hidden sm:block mr-1 transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="open" @click.outside="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50"
                 style="display:none">
                <div class="px-4 py-3 border-b border-slate-50">
                    <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                </div>
                <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-cog w-4 text-slate-400"></i> Settings
                </a>
                <div class="border-t border-slate-50 mt-1 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50/80 transition-colors cursor-pointer">
                            <i class="fas fa-sign-out-alt w-4 text-red-400"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </div>
</header>
