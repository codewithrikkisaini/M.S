<?php

use Livewire\Component;
use App\Models\ApiKey;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

new class extends Component
{
    public $keysList = [];
    public $name;
    public $showCreateModal = false;
    public $newlyCreatedKey = null;

    public function mount(): void
    {
        $this->loadKeys();
    }

    public function loadKeys(): void
    {
        $this->keysList = ApiKey::where('hotel_id', Auth::user()->hotel_id)->latest()->get();
    }

    public function openCreateModal(): void
    {
        $this->name = '';
        $this->newlyCreatedKey = null;
        $this->showCreateModal = true;
    }

    public function createKey(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
        ]);

        $hotelId = Auth::user()->hotel_id;
        $generatedKey = 'pms_live_' . Str::random(32);

        $apiKey = ApiKey::create([
            'hotel_id' => $hotelId,
            'name' => $this->name,
            'key' => $generatedKey,
            'status' => 'active',
        ]);

        ActivityLog::create([
            'hotel_id' => $hotelId,
            'action' => 'API Key Created',
            'description' => "Created API Key named '{$this->name}' for integrations.",
            'ip_address' => request()->ip(),
            'user_id' => Auth::id(),
        ]);

        $this->newlyCreatedKey = $generatedKey;
        $this->loadKeys();
    }

    public function toggleStatus($id): void
    {
        $key = ApiKey::findOrFail($id);
        $newStatus = $key->status === 'active' ? 'inactive' : 'active';
        $key->update(['status' => $newStatus]);

        ActivityLog::create([
            'hotel_id' => Auth::user()->hotel_id,
            'action' => 'API Key Update',
            'description' => "Toggled status of API Key '{$key->name}' to {$newStatus}.",
            'ip_address' => request()->ip(),
            'user_id' => Auth::id(),
        ]);

        $this->loadKeys();
        
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => "API Key status updated to {$newStatus}!"
        ]);
    }

    public function deleteKey($id): void
    {
        $key = ApiKey::findOrFail($id);
        $keyName = $key->name;
        $key->delete();

        ActivityLog::create([
            'hotel_id' => Auth::user()->hotel_id,
            'action' => 'API Key Deleted',
            'description' => "Deleted API Key '{$keyName}' permanently.",
            'ip_address' => request()->ip(),
            'user_id' => Auth::id(),
        ]);

        $this->loadKeys();
        
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => "API Key '{$keyName}' deleted."
        ]);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
