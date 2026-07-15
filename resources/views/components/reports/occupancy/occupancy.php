<?php

use Livewire\Component;
use App\Models\Room;
use App\Models\Reservation;
use Carbon\Carbon;

new class extends Component
{
    public string $month = '';

    public function mount(): void
    {
        $this->month = Carbon::today()->format('Y-m');
    }

    public function render(): mixed
    {
        $start = Carbon::parse($this->month . '-01')->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $totalRooms    = Room::count();
        $occupiedRooms = Room::where('status', 'Occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // Daily occupancy for the month
        $dailyData = [];
        $day = $start->copy();
        while ($day <= $end) {
            $occupied = Reservation::where('status', 'Checked-In')
                ->whereDate('check_in_date', '<=', $day)
                ->whereDate('check_out_date', '>', $day)
                ->count();
            $dailyData[] = [
                'date'  => $day->format('d'),
                'count' => $occupied,
                'rate'  => $totalRooms > 0 ? round(($occupied / $totalRooms) * 100) : 0,
            ];
            $day->addDay();
        }

        $roomTypeStats = Room::with('roomType')
            ->selectRaw('room_type_id, status, count(*) as count')
            ->groupBy('room_type_id', 'status')
            ->get()
            ->groupBy('room_type_id');

        return $this->view(compact(
            'totalRooms', 'occupiedRooms', 'occupancyRate', 'dailyData', 'roomTypeStats'
        ));
    }
};
