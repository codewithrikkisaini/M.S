<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GuestBlacklist;
use App\Services\GuestBlacklistService;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function isAdmin(): bool
    {
        return auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'));
    }

    public function remove(int $id): void
    {
        abort_unless($this->isAdmin(), 403);

        $service = app(GuestBlacklistService::class);
        $service->removeBlacklist($id);
        $this->dispatch('toast', message: 'Guest blacklisted removed successfully.', type: 'success');
    }

    public function restore(int $id): void
    {
        abort_unless($this->isAdmin(), 403);

        $service = app(GuestBlacklistService::class);
        $service->restoreBlacklist($id);
        $this->dispatch('toast', message: 'Guest blacklist restored.', type: 'success');
    }

    public function delete(int $id): void
    {
        abort_unless($this->isAdmin(), 403);

        $blacklist = GuestBlacklist::with('documents')->findOrFail($id);
        
        // Delete associated document files
        foreach ($blacklist->documents as $doc) {
            $fullPath = $doc->getFullStoragePath();
            if (\Illuminate\Support\Facades\Storage::disk($doc->disk)->exists($fullPath)) {
                \Illuminate\Support\Facades\Storage::disk($doc->disk)->delete($fullPath);
            }
            $doc->delete();
        }

        $name = "{$blacklist->first_name} {$blacklist->last_name}";
        $blacklist->delete();

        \App\Models\ActivityLog::log(
            'Blacklist Deleted',
            "Blacklist record for {$name} has been permanently deleted."
        );

        $this->dispatch('toast', message: 'Blacklist record deleted permanently.', type: 'success');
    }

    public function render(): mixed
    {
        $query = GuestBlacklist::with(['guest', 'blacklister'])
            ->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('id_number', 'like', "%{$this->search}%")
                  ->orWhereHas('guest', function ($gq) {
                      $gq->where('name', 'like', "%{$this->search}%")
                         ->orWhere('email', 'like', "%{$this->search}%");
                  });
            });

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        $blacklists = $query->latest()->paginate(10);

        return $this->view(compact('blacklists'));
    }
};
