<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\DailyCashSheetService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class SendDailyCashSheetReport extends Command
{
    protected $signature = 'report:send-daily-cash-sheet {--force}';

    protected $description = 'Email the daily cash sheet report when auto-send is enabled and the scheduled time has arrived';

    public function handle(DailyCashSheetService $service): int
    {
        if (!Schema::hasTable('settings')) {
            return self::SUCCESS;
        }

        if (!$this->option('force')) {
            if (Setting::get('daily_report_auto_send', '0') !== '1') {
                return self::SUCCESS;
            }

            if (now()->format('H:i') !== Setting::get('daily_report_time', '23:30')) {
                return self::SUCCESS;
            }

            if (Setting::get('daily_report_last_sent_date') === now()->toDateString()) {
                return self::SUCCESS;
            }
        }

        $emails = array_filter(array_map('trim', explode(',', Setting::get('daily_report_email', ''))));
        if (empty($emails)) {
            $this->error('Daily cash sheet auto-send is enabled but no recipient email is configured.');
            return self::FAILURE;
        }

        $date = now()->toDateString();
        $sheets = [$service->build($date)];
        $hotelName = Setting::get('hotel_name', 'Merahkie PMS Lite');

        $pdf = Pdf::loadView('reports.daily-cash-sheet-pdf', compact('sheets', 'hotelName'));
        $fileName = 'daily-cash-sheet-' . $date . '.pdf';

        Mail::raw("Attached is the Daily Cash Sheet for {$date}.", function ($message) use ($emails, $date, $hotelName, $pdf, $fileName) {
            $message->to($emails)
                ->subject("{$hotelName} — Daily Cash Sheet for {$date}")
                ->attachData($pdf->output(), $fileName, ['mime' => 'application/pdf']);
        });

        Setting::set('daily_report_last_sent_date', $date);

        $this->info('Daily cash sheet sent to: ' . implode(', ', $emails));

        return self::SUCCESS;
    }
}
