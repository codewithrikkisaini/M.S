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
    public function mount()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user) {
            if ($user->hasRole('housekeeping')) {
                return redirect()->route('housekeeping.index');
            }
            if ($user->hasRole('maintenance')) {
                return redirect()->route('maintenance.index');
            }
            if ($user->hasRole('receptionist') && request()->routeIs('dashboard')) {
                return redirect()->route('receptionist.dashboard');
            }
        }
    }

    public function render(): mixed
    {
        $hotelId = \Illuminate\Support\Facades\Auth::user()?->hotel_id;

        $totalRooms       = Room::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->count();
        $occupiedRooms    = Room::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->where('status', 'Occupied')->count();
        $availableRooms   = Room::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->where('status', 'Available')->count();
        $reservedRooms    = Room::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->where('status', 'Reserved')->count();
        $checkInsToday    = CheckIn::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->whereDate('checkin_datetime', Carbon::today())->count();
        $checkOutsToday   = CheckOut::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->whereDate('checkout_datetime', Carbon::today())->count();
        $revenueToday     = CheckOut::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->whereDate('checkout_datetime', Carbon::today())->sum('total_amount');
        $occupancyPercent = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
        
        // Housekeeping breakdown
        $hkClean = Housekeeping::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->where('status', 'Clean')->count();
        $hkDirty = Housekeeping::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->where('status', 'Dirty')->count();
        $hkInspecting = Housekeeping::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->where('status', 'Inspecting')->count();
        $housekeepingPending = $hkDirty + $hkInspecting;

        // Maintenance breakdown
        $maintOpen = \Illuminate\Support\Facades\DB::table('maintenance_tickets')
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->where('status', 'Open')->count();
        $maintInProgress = \Illuminate\Support\Facades\DB::table('maintenance_tickets')
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->where('status', 'In Progress')->count();
        $maintCritical = \Illuminate\Support\Facades\DB::table('maintenance_tickets')
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->where('priority', 'Critical')
            ->where('status', '!=', 'Completed')->count();

        // 7-day revenue trend for chart visualization
        $revenueTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = CheckOut::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->whereDate('checkout_datetime', $date)->sum('total_amount');
            $revenueTrend[] = [
                'day' => $date->format('D'),
                'revenue' => (float)$revenue,
            ];
        }

        $recentReservations = Reservation::with(['guest', 'rooms'])
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
            ->latest()
            ->limit(8)
            ->get();

        $rooms = Room::with(['latestHousekeeping', 'activeMaintenanceTickets', 'roomType'])
            ->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))
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
            'hkClean'             => $hkClean,
            'hkDirty'             => $hkDirty,
            'hkInspecting'        => $hkInspecting,
            'maintOpen'           => $maintOpen,
            'maintInProgress'     => $maintInProgress,
            'maintCritical'       => $maintCritical,
            'recentReservations'  => $recentReservations,
            'rooms'               => $rooms,
            'revenueTrend'        => $revenueTrend,
        ]);
    }
};
