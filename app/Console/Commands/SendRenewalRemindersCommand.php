<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Mail\SubscriptionExpiryReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendRenewalRemindersCommand extends Command
{
    protected $signature = 'subscriptions:send-reminders';
    protected $description = 'Send renewal reminders for subscriptions expiring soon (3 days, 1 day, 0 days)';

    public function handle(): int
    {
        $now = Carbon::now();
        $this->info("Running subscription renewal reminder process at {$now->toDateTimeString()}");

        $activeSubs = Subscription::whereIn('status', ['Active', 'trialing'])
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', $now)
            ->with('hotel')
            ->get();

        foreach ($activeSubs as $sub) {
            $hotel = $sub->hotel;
            if (!$hotel) continue;

            $daysRemaining = (int) $now->diffInDays($sub->ends_at, false);

            if (in_array($daysRemaining, [3, 1, 0])) {
                try {
                    Mail::to($hotel->email)->send(new SubscriptionExpiryReminderMail($hotel, $sub, $daysRemaining));
                    $this->info("Sent {$daysRemaining}-day expiry reminder to {$hotel->email} for {$hotel->name}");
                } catch (\Exception $e) {
                    $this->error("Failed sending reminder to {$hotel->email}: " . $e->getMessage());
                }
            }
        }

        $this->info("Renewal reminders process completed.");
        return Command::SUCCESS;
    }
}
