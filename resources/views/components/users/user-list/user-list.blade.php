<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Staff Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage user credentials, assign roles, and monitor login activity</p>
        </div>
        <button wire:click="openCreate" class="btn-primary btn-sm rounded-lg shadow-sm cursor-pointer">
            <i class="fas fa-user-plus text-xs"></i> Add Staff Account
        </button>
    </div>

    {{-- Table Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-user-shield text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">User Registry</h3>
                    <p class="text-[10px] text-slate-400">Total staff accounts registered in the system</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search users..." class="pms-input pl-9 py-1.5 text-xs rounded-lg border border-slate-200">
                </div>
                <span class="text-xs font-semibold text-slate-500 bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 shrink-0">
                    {{ $users->total() }} total
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="pms-table">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                        <th class="font-bold">Staff Member</th>
                        <th class="font-bold">Role</th>
                        <th class="font-bold">Status</th>
                        <th class="font-bold">Last Activity</th>
                        <th class="font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td>
                            <div class="flex items-center gap-3">
                                @php
                                    $initials = strtoupper(substr($user->name, 0, 1));
                                    $gradients = [
                                        'A' => 'from-indigo-400 to-indigo-600', 'B' => 'from-emerald-400 to-emerald-600',
                                        'C' => 'from-blue-400 to-blue-600', 'D' => 'from-rose-400 to-rose-600',
                                        'E' => 'from-amber-400 to-amber-600', 'F' => 'from-orange-400 to-orange-600',
                                        'G' => 'from-teal-400 to-teal-600', 'H' => 'from-purple-400 to-purple-600',
                                        'I' => 'from-pink-400 to-pink-600', 'J' => 'from-cyan-400 to-cyan-600',
                                    ];
                                    $gradient = $gradients[$initials] ?? 'from-slate-400 to-slate-600';
                                @endphp
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center shrink-0 shadow-sm border border-white">
                                    <span class="text-xs font-black text-white">{{ $initials }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 text-sm block leading-none mb-1">{{ $user->name }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->role)
                            @php
                                $roleClass = match($user->role->name) {
                                    'admin' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
                                    default => 'bg-slate-100 text-slate-600 border-slate-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $roleClass }}">
                                {{ ucfirst($user->role->name) }}
                            </span>
                            @endif
                        </td>
                        <td>
                            @php
                                $status = $user->status ?? 'active';
                                $statusClass = match($status) {
                                    'active' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    default => 'bg-rose-50 text-red-700 border-rose-100',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="text-slate-500 text-xs font-medium">
                            {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : 'Never logged in' }}
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button wire:click="edit({{ $user->id }})" class="btn-icon text-indigo-500 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 shadow-sm cursor-pointer" title="Edit">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button wire:click="toggleStatus({{ $user->id }})" class="btn-icon text-amber-500 hover:bg-amber-50 border border-slate-100 hover:border-amber-100 shadow-sm cursor-pointer" title="Toggle Status">
                                    <i class="fas fa-power-off text-xs"></i>
                                </button>
                                <button wire:click="delete({{ $user->id }})" wire:confirm="Delete user {{ $user->name }}?" class="btn-icon text-red-500 hover:bg-red-50 border border-slate-100 hover:border-red-100 shadow-sm cursor-pointer" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <p class="text-sm font-medium">No users found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())<div class="px-5 py-4 border-t border-slate-100">{{ $users->links() }}</div>@endif
    </div>

    {{-- Slide-over Drawer --}}
    <div x-show="$wire.showDrawer" class="drawer-overlay" @click="$wire.showDrawer = false"
         x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display:none"></div>
    <div x-show="$wire.showDrawer" class="drawer-panel"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         style="display:none">
        <div class="drawer-header border-b border-slate-100 px-6 py-4 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">{{ $isEditMode ? 'Edit User Credentials' : 'Add New User' }}</h3>
            <button @click="$wire.showDrawer = false" class="btn-icon text-slate-400 hover:text-slate-600 cursor-pointer"><i class="fas fa-times"></i></button>
        </div>
        <div class="drawer-body space-y-5 px-6 py-5">
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Full Name <span class="text-red-500">*</span></label>
                <input type="text" wire:model="name" class="pms-input text-xs" placeholder="John Doe">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Email Address <span class="text-red-500">*</span></label>
                <input type="email" wire:model="email" class="pms-input text-xs" placeholder="john@hotel.com">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Password {{ $isEditMode ? '(leave blank to keep current)' : '' }} <span class="text-red-500">*</span></label>
                <input type="password" wire:model="password" class="pms-input text-xs" placeholder="Min. 6 characters">
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Assigned Role <span class="text-red-500">*</span></label>
                <select wire:model="role_id" class="pms-select text-xs">
                    <option value="">Select role...</option>
                    @foreach($roles as $role)<option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>@endforeach
                </select>
                @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Account Status</label>
                <select wire:model="status" class="pms-select text-xs">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="drawer-footer border-t border-slate-100 px-6 py-4 flex items-center justify-end gap-3">
            <button @click="$wire.showDrawer = false" class="btn-secondary text-xs font-bold rounded-lg py-2">Cancel</button>
            <button wire:click="store" wire:loading.attr="disabled" class="btn-primary text-xs font-bold rounded-lg py-2 cursor-pointer">
                <span wire:loading wire:target="store" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                {{ $isEditMode ? 'Update Account' : 'Create Account' }}
            </button>
        </div>
    </div>
</div>