{{-- ===== LEFT SIDEBAR ===== --}}

{{-- Desktop Sidebar --}}
<aside class="hidden lg:flex lg:flex-col fixed inset-y-0 left-0 z-40 bg-slate-950 border-r border-slate-900/50 transition-all duration-300 shadow-xl"
       :class="sidebarOpen ? 'w-64' : 'w-16'">

    {{-- Logo / Header --}}
    <div class="flex h-16 items-center border-b border-slate-900/60 px-4 shrink-0" :class="sidebarOpen ? 'justify-between' : 'justify-center'">
        <div x-show="sidebarOpen" class="flex items-center gap-2.5" x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md shadow-indigo-500/10 border border-indigo-400/10">
                <i class="fas fa-hotel text-white text-sm"></i>
            </div>
            <span class="text-white font-black text-base tracking-tight">
                {{ Auth::check() && Auth::user()->hasRole('superadmin') ? 'Super Admin HQ' : $hotelName }}
            </span>
        </div>
        <div x-show="!sidebarOpen" class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md shadow-indigo-500/10 border border-indigo-400/10" style="display:none">
            <i class="fas fa-hotel text-white text-sm"></i>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" x-show="sidebarOpen"
                class="text-slate-500 hover:text-white p-1 rounded-lg hover:bg-slate-900 transition-all cursor-pointer">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto no-scrollbar py-4 px-3 space-y-1">

        @if(Auth::check() && Auth::user()->hasRole('superadmin'))
            {{-- Category: HQ Admin --}}
            <div x-show="sidebarOpen" class="px-3 pb-1.5 pt-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">HQ Admin</span>
            </div>

            <a href="{{ route('superadmin.dashboard') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.dashboard') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-tachometer-alt nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Dashboard</span>
            </a>

            <a href="{{ route('superadmin.hotels.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.hotels.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-building nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Manage Hotels</span>
            </a>

            <a href="{{ route('superadmin.saas-plans.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.saas-plans.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-layer-group nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>SaaS Plans</span>
            </a>

            <a href="{{ route('superadmin.saas-billing.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.saas-billing.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-credit-card nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>SaaS Billing</span>
            </a>

            <a href="{{ route('superadmin.saas-invoices.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.saas-invoices.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-file-invoice nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>SaaS Invoices</span>
            </a>

            <a href="{{ route('superadmin.global-settings') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.global-settings') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-sliders-h nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Global Settings</span>
            </a>
        @else
            {{-- Category: Main --}}
            <div x-show="sidebarOpen" class="px-3 pb-1.5 pt-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Main</span>
            </div>

            <a href="{{ route('dashboard') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('dashboard') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-tachometer-alt nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Dashboard</span>
            </a>

            @if(Auth::check() && Auth::user()->hasRole('admin'))
            <a href="{{ route('rooms.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('rooms.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-bed nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Rooms</span>
            </a>
            @endif

            <a href="{{ route('reservations.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('reservations.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-calendar-check nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Reservations</span>
            </a>

            <a href="{{ route('calendar') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('calendar') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-calendar-alt nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Booking Calendar</span>
            </a>

            <a href="{{ route('guests.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('guests.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-users nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Guests</span>
            </a>

            {{-- Category: Operations --}}
            <div x-show="sidebarOpen" class="px-3 pt-5 pb-1.5">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Operations</span>
            </div>
            <div x-show="!sidebarOpen" class="border-t border-slate-900 my-3" style="display:none"></div>

            <a href="{{ route('checkin.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('checkin.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-sign-in-alt nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Check-In</span>
            </a>

            <a href="{{ route('checkout.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('checkout.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-sign-out-alt nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Check-Out</span>
            </a>

            <a href="{{ route('invoices.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('invoices.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-file-invoice-dollar nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Invoices</span>
            </a>

            <a href="{{ route('housekeeping.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('housekeeping.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-broom nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Housekeeping</span>
            </a>

            <a href="{{ route('maintenance.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('maintenance.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-tools nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Maintenance</span>
            </a>

            {{-- Category: Analytics --}}
            <div x-show="sidebarOpen" class="px-3 pt-5 pb-1.5">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Analytics</span>
            </div>
            <div x-show="!sidebarOpen" class="border-t border-slate-900 my-3" style="display:none"></div>

            <a href="{{ route('reports.daily') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('reports.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-chart-bar nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Reports</span>
            </a>

            {{-- Category: Admin --}}
            @if(Auth::check() && Auth::user()->hasRole('admin'))
            <div x-show="sidebarOpen" class="px-3 pt-5 pb-1.5">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Admin</span>
            </div>
            <div x-show="!sidebarOpen" class="border-t border-slate-900 my-3" style="display:none"></div>

            <a href="{{ route('users.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('users.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-user-shield nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Users</span>
            </a>

            <a href="{{ route('settings') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('settings') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-cog nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Settings</span>
            </a>

            <a href="{{ route('billing.index') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('billing.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-wallet nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Billing & Plans</span>
            </a>

            {{-- Category: Integrations --}}
            <div x-show="sidebarOpen" class="px-3 pt-5 pb-1.5">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Integrations</span>
            </div>
            <div x-show="!sidebarOpen" class="border-t border-slate-900 my-3" style="display:none"></div>

            <a href="{{ route('integrations.channels') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('integrations.channels') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-sync nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>OTA Channels</span>
            </a>

            <a href="{{ route('integrations.stripe') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('integrations.stripe') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fab fa-stripe nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Stripe Settings</span>
            </a>

            <a href="{{ route('integrations.templates') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('integrations.templates') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="far fa-bell nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Alert Templates</span>
            </a>

            <a href="{{ route('integrations.api') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('integrations.api') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-key nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Developer API</span>
            </a>

            {{-- Category: Enterprise --}}
            <div x-show="sidebarOpen" class="px-3 pt-5 pb-1.5">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Enterprise</span>
            </div>
            <div x-show="!sidebarOpen" class="border-t border-slate-900 my-3" style="display:none"></div>

            <a href="{{ route('enterprise.logs') }}" wire:navigate
               class="sidebar-link group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('enterprise.logs') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}"
               :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i class="fas fa-shield-alt nav-icon transition-transform duration-200 group-hover:scale-105"></i>
                <span x-show="sidebarOpen" x-transition>Audit Logs</span>
            </a>
            @endif
        @endif

    </nav>

    {{-- Sidebar Footer --}}
    <div class="border-t border-slate-900/60 px-3 py-4 shrink-0 bg-slate-950/40">
        <div class="flex items-center gap-3" :class="sidebarOpen ? '' : 'justify-center'">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0 shadow-md border border-indigo-400/10">
                <span class="text-white text-xs font-black">{{ strtoupper(substr(Auth::user()?->name ?? 'U', 0, 1)) }}</span>
            </div>
            <div x-show="sidebarOpen" x-transition class="min-w-0">
                <p class="text-xs font-extrabold text-slate-200 truncate leading-tight">{{ Auth::user()?->name ?? '' }}</p>
                <p class="text-[10px] text-slate-500 truncate mt-0.5 leading-tight">{{ Auth::user()?->email ?? '' }}</p>
            </div>
        </div>
    </div>
</aside>

{{-- Mobile Sidebar --}}
<aside class="lg:hidden fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 flex flex-col transform transition-transform duration-300 shadow-2xl border-r border-slate-900/50"
       :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex h-16 items-center justify-between border-b border-slate-900/60 px-4 shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center border border-indigo-400/10 shadow-md shadow-indigo-500/10">
                <i class="fas fa-hotel text-white text-sm"></i>
            </div>
            <span class="text-white font-black text-base tracking-tight">
                {{ Auth::check() && Auth::user()->hasRole('superadmin') ? 'Super Admin HQ' : $hotelName }}
            </span>
        </div>
        <button @click="mobileSidebarOpen = false" class="text-slate-500 hover:text-white p-1 rounded-lg transition-all cursor-pointer">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="flex-1 overflow-y-auto no-scrollbar py-4 px-3 space-y-1">
        @if(Auth::check() && Auth::user()->hasRole('superadmin'))
            <a href="{{ route('superadmin.dashboard') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.dashboard') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-tachometer-alt nav-icon"></i><span>Dashboard</span>
            </a>
            
            <a href="{{ route('superadmin.hotels.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.hotels.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-building nav-icon"></i><span>Manage Hotels</span>
            </a>

            <a href="{{ route('superadmin.saas-plans.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.saas-plans.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-layer-group nav-icon"></i><span>SaaS Plans</span>
            </a>

            <a href="{{ route('superadmin.saas-billing.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.saas-billing.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-credit-card nav-icon"></i><span>SaaS Billing</span>
            </a>

            <a href="{{ route('superadmin.saas-invoices.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.saas-invoices.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-file-invoice nav-icon"></i><span>SaaS Invoices</span>
            </a>

            <a href="{{ route('superadmin.global-settings') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('superadmin.global-settings') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-sliders-h nav-icon"></i><span>Global Settings</span>
            </a>
        @else
            <a href="{{ route('dashboard') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('dashboard') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-tachometer-alt nav-icon"></i><span>Dashboard</span>
            </a>
            
            @if(Auth::check() && Auth::user()->hasRole('admin'))
            <a href="{{ route('rooms.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('rooms.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-bed nav-icon"></i><span>Rooms</span>
            </a>
            @endif
            
            <a href="{{ route('reservations.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('reservations.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-calendar-check nav-icon"></i><span>Reservations</span>
            </a>
            
            <a href="{{ route('calendar') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('calendar') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-calendar-alt nav-icon"></i><span>Booking Calendar</span>
            </a>
            
            <a href="{{ route('guests.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('guests.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-users nav-icon"></i><span>Guests</span>
            </a>
            
            <div class="px-3 pt-4 pb-1"><span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Operations</span></div>
            
            <a href="{{ route('checkin.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('checkin.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-sign-in-alt nav-icon"></i><span>Check-In</span>
            </a>
            
            <a href="{{ route('checkout.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('checkout.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-sign-out-alt nav-icon"></i><span>Check-Out</span>
            </a>
            
            <a href="{{ route('invoices.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('invoices.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-file-invoice-dollar nav-icon"></i><span>Invoices</span>
            </a>
            
            <a href="{{ route('housekeeping.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('housekeeping.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-broom nav-icon"></i><span>Housekeeping</span>
            </a>
            
            <a href="{{ route('maintenance.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('maintenance.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-tools nav-icon"></i><span>Maintenance</span>
            </a>
            
            <div class="px-3 pt-4 pb-1"><span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Analytics</span></div>
            
            <a href="{{ route('reports.daily') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('reports.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-chart-bar nav-icon"></i><span>Reports</span>
            </a>
            
            @if(Auth::check() && Auth::user()->hasRole('admin'))
            <div class="px-3 pt-4 pb-1"><span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Admin</span></div>
            
            <a href="{{ route('users.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('users.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-user-shield nav-icon"></i><span>Users</span>
            </a>
            
            <a href="{{ route('settings') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('settings') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-cog nav-icon"></i><span>Settings</span>
            </a>

            <a href="{{ route('billing.index') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('billing.*') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-wallet nav-icon"></i><span>Billing & Plans</span>
            </a>

            <div class="px-3 pt-4 pb-1"><span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Integrations</span></div>

            <a href="{{ route('integrations.channels') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('integrations.channels') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-sync nav-icon"></i><span>OTA Channels</span>
            </a>

            <a href="{{ route('integrations.stripe') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('integrations.stripe') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fab fa-stripe nav-icon"></i><span>Stripe Settings</span>
            </a>

            <a href="{{ route('integrations.templates') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('integrations.templates') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="far fa-bell nav-icon"></i><span>Alert Templates</span>
            </a>

            <a href="{{ route('integrations.api') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('integrations.api') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-key nav-icon"></i><span>Developer API</span>
            </a>

            <div class="px-3 pt-4 pb-1"><span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Enterprise</span></div>

            <a href="{{ route('enterprise.logs') }}" wire:navigate @click="mobileSidebarOpen = false"
               class="sidebar-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 text-sm font-semibold transition-all duration-200 hover:bg-slate-900 hover:text-slate-100 {{ request()->routeIs('enterprise.logs') ? 'active bg-indigo-600 text-white shadow-md shadow-indigo-600/10' : '' }}">
                <i class="fas fa-shield-alt nav-icon"></i><span>Audit Logs</span>
            </a>
            @endif
        @endif
    </nav>
</aside>
