<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Guest;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function delete(int $id): void
    {
        Guest::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Guest deleted.', type: 'success');
    }

    public function render(): mixed
    {
        $guests = Guest::where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('guest_id', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return $this->view(['guests' => $guests]);
    }
};
