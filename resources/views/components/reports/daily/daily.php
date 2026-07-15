<?php

use Livewire\Component;
use App\Models\Room;
use App\Models\CheckIn;
use App\Models\CheckOut;
use App\Models\Reservation;
use App\Models\Housekeeping;
use App\Models\Setting;
use App\Services\DailyCashSheetService;
use Carbon\Carbon;

new class extends Component
{
    public string $date = '';
    public string $rangeFrom = '';
    public string $rangeTo = '';

    public bool $daily_report_auto_send = false;
    public string $daily_report_email = '';
    public string $daily_report_time = '23:30';

    public function mount(): void
    {
        $this->date = Carbon::today()->toDateString();
        $this->rangeFrom = Carbon::today()->subDays(6)->toDateString();
        $this->rangeTo = Carbon::today()->toDateString();

        $this->daily_report_auto_send = Setting::get('daily_report_auto_send', '0') === '1';
        $this->daily_report_email     = Setting::get('daily_report_email', '');
        $this->daily_report_time      = Setting::get('daily_report_time', '23:30');
    }

    public function saveEmailSchedule(): void
    {
        $this->validate([
            'daily_report_email' => 'required_if:daily_report_auto_send,true|nullable|string',
            'daily_report_time'  => 'required|date_format:H:i',
        ]);

        Setting::set('daily_report_auto_send', $this->daily_report_auto_send ? '1' : '0');
        Setting::set('daily_report_email', $this->daily_report_email);
        Setting::set('daily_report_time', $this->daily_report_time);

        $this->dispatch('toast', message: 'Email schedule saved successfully.', type: 'success');
    }

    public function render(): mixed
    {
        $day = Carbon::parse($this->date);

        $totalRooms      = Room::count();
        $occupiedRooms   = Room::where('status', 'Occupied')->count();
        $availableRooms  = Room::where('status', 'Available')->count();
        $checkInsToday   = CheckIn::whereDate('checkin_datetime', $day)->count();
        $checkOutsToday  = CheckOut::whereDate('checkout_datetime', $day)->count();
        $revenueToday    = CheckOut::whereDate('checkout_datetime', $day)->sum('total_amount');
        $occupancyRate   = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
        $housekeepingPending = Housekeeping::where('status', '!=', 'Clean')->count();

        $recentCheckOuts = CheckOut::with(['reservation.guest', 'reservation.rooms'])
            ->whereDate('checkout_datetime', $day)
            ->latest()
            ->limit(10)
            ->get();

        $cashSheet = app(DailyCashSheetService::class)->build($this->date);

        return $this->view(compact(
            'totalRooms', 'occupiedRooms', 'availableRooms',
            'checkInsToday', 'checkOutsToday', 'revenueToday',
            'occupancyRate', 'housekeepingPending', 'recentCheckOuts',
            'cashSheet'
        ));
    }
};
