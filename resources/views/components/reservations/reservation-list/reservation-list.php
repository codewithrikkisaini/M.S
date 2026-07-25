<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\ReservationService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function delete(int $id, ReservationService $service): void
    {
        $service->deleteReservation($id);
        $this->dispatch('toast', message: 'Reservation deleted.', type: 'success');
    }

    public function accept(int $id, ReservationService $service): void
    {
        try {
            $service->acceptReservation($id);
            $this->dispatch('toast', message: 'Reservation accepted and confirmed.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function reject(int $id, ReservationService $service): void
    {
        try {
            $service->rejectReservation($id);
            $this->dispatch('toast', message: 'Reservation rejected.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function checkIn(int $id, ReservationService $service): void
    {
        try {
            $service->processCheckIn($id, Auth::id());
            $this->dispatch('toast', message: 'Guest checked in successfully!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
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
        $service = App::make(ReservationService::class);
        $reservations = $service->getPaginatedReservations($this->search);

        return $this->view(compact('reservations'));
    }
};
