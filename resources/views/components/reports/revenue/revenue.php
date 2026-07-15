<?php

use Livewire\Component;
use App\Models\CheckOut;
use Carbon\Carbon;

new class extends Component
{
    public string $year = '';

    public function mount(): void
    {
        $this->year = (string)Carbon::today()->year;
    }

    public function render(): mixed
    {
        $monthlyRevenue = [];
        $totalAnnual    = 0;

        for ($m = 1; $m <= 12; $m++) {
            $start   = Carbon::create($this->year, $m, 1)->startOfMonth();
            $end     = $start->copy()->endOfMonth();
            $revenue = CheckOut::whereBetween('checkout_datetime', [$start, $end])->sum('total_amount');
            $count   = CheckOut::whereBetween('checkout_datetime', [$start, $end])->count();

            $monthlyRevenue[] = [
                'month'   => $start->format('M'),
                'revenue' => $revenue,
                'count'   => $count,
            ];
            $totalAnnual += $revenue;
        }

        $currentMonth = CheckOut::whereMonth('checkout_datetime', Carbon::today()->month)
            ->whereYear('checkout_datetime', Carbon::today()->year)
            ->sum('total_amount');

        $lastMonth = CheckOut::whereMonth('checkout_datetime', Carbon::today()->subMonth()->month)
            ->whereYear('checkout_datetime', Carbon::today()->subMonth()->year)
            ->sum('total_amount');

        $growth = $lastMonth > 0 ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1) : 0;

        return $this->view(compact('monthlyRevenue', 'totalAnnual', 'currentMonth', 'lastMonth', 'growth'));
    }
};
