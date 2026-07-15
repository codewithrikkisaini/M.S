<?php

use Livewire\Component;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\CheckIn;
use App\Models\CheckOut;
use App\Models\Housekeeping;
use Carbon\Carbon;

new class extends Component
{
    public function render(): mixed
    {
        $totalRooms       = Room::count();
        $occupiedRooms    = Room::where('status', 'Occupied')->count();
        $availableRooms   = Room::where('status', 'Available')->count();
        $reservedRooms    = Room::where('status', 'Reserved')->count();
        $checkInsToday    = CheckIn::whereDate('checkin_datetime', Carbon::today())->count();
        $checkOutsToday   = CheckOut::whereDate('checkout_datetime', Carbon::today())->count();
        $revenueToday     = CheckOut::whereDate('checkout_datetime', Carbon::today())->sum('total_amount');
        $occupancyPercent = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
        $housekeepingPending = Housekeeping::where('status', '!=', 'Clean')->count();

        // 7-day revenue trend for chart visualization
        $revenueTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = CheckOut::whereDate('checkout_datetime', $date)->sum('total_amount');
            $revenueTrend[] = [
                'day' => $date->format('D'),
                'revenue' => (float)$revenue,
            ];
        }

        $recentReservations = Reservation::with(['guest', 'rooms'])
            ->latest()
            ->limit(8)
            ->get();

        $rooms = Room::with(['latestHousekeeping', 'activeMaintenanceTickets', 'roomType'])
            ->orderBy('room_number')
            ->get()
            ->map(function ($room) {
                if (empty($room->floor)) {
                    $room->floor = 'Unassigned';
                }
                return $room;
            });

        return $this->view([
            'totalRooms'          => $totalRooms,
            'occupiedRooms'       => $occupiedRooms,
            'availableRooms'      => $availableRooms,
            'reservedRooms'       => $reservedRooms,
            'checkInsToday'       => $checkInsToday,
            'checkOutsToday'      => $checkOutsToday,
            'revenueToday'        => $revenueToday,
            'occupancyPercent'    => $occupancyPercent,
            'housekeepingPending' => $housekeepingPending,
            'recentReservations'  => $recentReservations,
            'rooms'               => $rooms,
            'revenueTrend'        => $revenueTrend,
        ]);
    }
};
