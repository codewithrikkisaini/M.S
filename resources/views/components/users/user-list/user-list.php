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
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
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

        $data = [
            'name'    => $this->name,
            'email'   => $this->email,
            'role_id' => $this->role_id ?: null,
            'status'  => $this->status,
        ];
        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);

        $this->resetFields();
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
        $users = User::with('role')
            ->when($this->search, fn ($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->latest()
            ->paginate(15);

        return $this->view(['users' => $users, 'roles' => Role::all()]);
    }
};
