<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showDrawer = false, $isEditMode = false;
    public ?int $userId = null;
    public string $name = '', $email = '', $password = '', $role_id = '', $status = 'active';

    public function boot(): void
    {
        if (!Auth::check() || (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('superadmin'))) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function updatedSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetFields();
        $this->showDrawer = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $user = User::findOrFail($id);
        $this->userId     = $user->id;
        $this->name       = $user->name;
        $this->email      = $user->email;
        $this->password   = '';
        $this->role_id    = (string)($user->role_id ?? '');
        $this->status     = $user->status ?? 'active';
        $this->isEditMode = true;
        $this->showDrawer = true;
    }

    public function store(): void
    {
        $rules = [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $this->userId,
            'role_id' => 'required|exists:roles,id',
        ];
        if (!$this->isEditMode) {
            $rules['password'] = 'required|min:6';
        } elseif ($this->password) {
            $rules['password'] = 'min:6';
        }
        $this->validate($rules);

        $hotel_id = Auth::user()->hotel_id ?? null;

        if (!$this->isEditMode && $hotel_id) {
            $activeSub = \App\Models\Subscription::where('hotel_id', $hotel_id)
                ->whereIn('status', ['active', 'trialing'])
                ->with('plan')
                ->latest()
                ->first();

            if ($activeSub && $activeSub->plan && $activeSub->plan->max_users !== null) {
                $currentUsersCount = User::where('hotel_id', $hotel_id)->count();
                if ($currentUsersCount >= $activeSub->plan->max_users) {
                    $this->addError('email', "Plan user limit reached ({$activeSub->plan->max_users} users max). Please upgrade your subscription plan.");
                    $this->dispatch('toast', message: "Plan user limit reached ({$activeSub->plan->max_users} users max). Please upgrade your subscription plan.", type: 'error');
                    return;
                }
            }
        }

        $data = [
            'name'     => $this->name,
            'email'    => $this->email,
            'role_id'  => $this->role_id ?: null,
            'status'   => $this->status,
            'hotel_id' => $hotel_id,
        ];
        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);

        $this->resetFields();
        $this->resetPage();
        $this->showDrawer = false;
        $this->dispatch('toast', message: 'User saved successfully!', type: 'success');
    }

    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        $this->dispatch('toast', message: 'Status updated.', type: 'success');
    }

    public function delete(int $id): void
    {
        User::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'User deleted.', type: 'success');
    }

    private function resetFields(): void
    {
        $this->userId     = null;
        $this->name       = '';
        $this->email      = '';
        $this->password   = '';
        $this->role_id    = '';
        $this->status     = 'active';
        $this->isEditMode = false;
        $this->resetValidation();
    }

    public function render(): mixed
    {
        $hotel_id = Auth::user()->hotel_id ?? null;

        $users = User::with('role')
            ->when($hotel_id, fn ($q) => $q->where('hotel_id', $hotel_id))
            ->when($this->search, fn ($q) =>
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })
            )
            ->latest()
            ->paginate(15);

        return $this->view(['users' => $users, 'roles' => Role::all()]);
    }
};
