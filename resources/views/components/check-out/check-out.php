<?php

use Livewire\Component;
use App\Models\Reservation;
use App\Services\ReservationService;
use Carbon\Carbon;

new class extends Component
{
    public string $search = '';
    public bool $showIdModal = false;
    public ?\App\Models\Guest $selectedGuest = null;

    public function openIdModal(int $guestId): void
    {
        $this->selectedGuest = \App\Models\Guest::find($guestId);
        $this->showIdModal = true;
    }

    public function closeIdModal(): void
    {
        $this->showIdModal = false;
        $this->selectedGuest = null;
    }

    public function checkOut(int $id, ReservationService $service): void
    {
        try {
            $service->processCheckOut($id);
            $this->dispatch('toast', message: 'Guest checked out successfully!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function render(): mixed
    {
        $checkedIn = Reservation::with(['guest', 'rooms', 'payments'])
            ->where('status', 'Checked-In')
            ->when($this->search, fn ($q) =>
                $q->whereHas('guest', fn ($qg) =>
                    $qg->where('name', 'like', "%{$this->search}%")
                )
            )
            ->orderBy('check_out_date')
            ->paginate(15);

        $checkoutsToday = Reservation::where('status', 'Checked-In')
            ->whereDate('check_out_date', Carbon::today())
            ->count();

        $overdueCount = Reservation::where('status', 'Checked-In')
            ->whereDate('check_out_date', '<', Carbon::today())
            ->count();

        return $this->view(compact('checkedIn', 'checkoutsToday', 'overdueCount'));
    }
};
