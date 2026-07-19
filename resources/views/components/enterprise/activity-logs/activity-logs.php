<?php

use Livewire\Component;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $logsList = [];
    public $search = '';
    public $actionFilter = '';

    public function mount(): void
    {
        $this->loadLogs();
    }

    public function loadLogs(): void
    {
        $query = ActivityLog::with('user')->latest();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('ip_address', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->actionFilter) {
            $query->where('action', $this->actionFilter);
        }

        $this->logsList = $query->limit(100)->get();
    }

    public function updatedSearch(): void
    {
        $this->loadLogs();
    }

    public function updatedActionFilter(): void
    {
        $this->loadLogs();
    }

    public function render(): mixed
    {
        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        return $this->view(['actions' => $actions]);
    }
};
