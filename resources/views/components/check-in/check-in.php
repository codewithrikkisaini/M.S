<?php

use Livewire\Component;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

new class extends Component
{
    public string $search = '';

    public function checkIn(int $id, ReservationService $service): void
    {
        try {
            $service->processCheckIn($id, Auth::id());
            $this->dispatch('toast', message: 'Guest checked in successfully!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function render(): mixed
    {
        $arrivals = Reservation::with(['guest', 'rooms.roomType'])
            ->whereIn('status', ['Confirmed', 'Reserved'])
            ->whereDate('check_in_date', '<=', Carbon::today())
            ->when($this->search, fn ($q) =>
                $q->whereHas('guest', fn ($qg) =>
                    $qg->where('name', 'like', "%{$this->search}%")
                )
            )
            ->orderBy('check_in_date')
            ->paginate(15);

        $upcoming = Reservation::with(['guest', 'rooms.roomType'])
            ->whereIn('status', ['Confirmed', 'Reserved'])
            ->whereDate('check_in_date', '>', Carbon::today())
            ->when($this->search, fn ($q) =>
                $q->whereHas('guest', fn ($qg) =>
                    $qg->where('name', 'like', "%{$this->search}%")
                )
            )
            ->orderBy('check_in_date')
            ->limit(10)
            ->get();

        $todayCount  = Reservation::whereIn('status', ['Confirmed', 'Reserved'])->whereDate('check_in_date', Carbon::today())->count();
        $pendingTotal = Reservation::whereIn('status', ['Confirmed', 'Reserved'])->whereDate('check_in_date', '<=', Carbon::today())->count();

        return $this->view(compact('arrivals', 'upcoming', 'todayCount', 'pendingTotal'));
    }
};
