<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;

new class extends Component
{
    use WithPagination;

    public string $search = '', $statusFilter = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }

    public function render(): mixed
    {
        $invoices = Invoice::with(['checkout.reservation.guest', 'checkout.reservation.rooms'])
            ->when($this->search, fn ($q) =>
                $q->whereHas('checkout.reservation.guest', fn ($qg) =>
                    $qg->where('name', 'like', "%{$this->search}%")
                )
            )
            ->when($this->statusFilter, function ($q) {
                if ($this->statusFilter !== 'Paid') {
                    $q->whereRaw('1 = 0');
                }
            })
            ->latest()
            ->paginate(15);

        return $this->view([
            'invoices'    => $invoices,
            'totalAmount' => \App\Models\CheckOut::sum('total_amount'),
            'paidCount'   => Invoice::count(),
        ]);
    }
};
