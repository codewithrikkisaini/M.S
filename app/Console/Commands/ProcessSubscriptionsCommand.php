<?php

namespace App\Console\Commands;

use App\Models\Hotel;
use App\Models\Subscription;
use App\Models\ActivityLog;
use App\Mail\TrialExpiredMail;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class ProcessSubscriptionsCommand extends Command
{
    protected $signature = 'subscriptions:process-expiries';
    protected $description = 'Automate trial and subscription expiry processing and hotel suspensions';

    public function handle(): int
    {
        $now = Carbon::now();
        $this->info("Running subscription expiry process at {$now->toDateTimeString()}");

        // 1. Process Expired Trials
        $expiredTrials = Subscription::where('status', 'trialing')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', $now)
            ->with('hotel')
            ->get();

        $this->info("Found {$expiredTrials->count()} expired trials.");

        foreach ($expiredTrials as $sub) {
            $hotel = $sub->hotel;
            if (!$hotel) continue;

            $sub->update(['status' => 'Trial Expired']);

            if ($hotel->account_status !== 'suspended') {
                $prevStatus = $hotel->account_status;
                $hotel->update(['account_status' => 'suspended']);

                ActivityLog::logAdminAction(
                    $hotel,
                    'Trial Expired & Suspended',
                    $prevStatus,
                    'suspended',
                    "Free trial period expired on {$sub->trial_ends_at->format('Y-m-d')}. Hotel automatically suspended."
                );

                try {
                    Mail::to($hotel->email)->send(new TrialExpiredMail($hotel));
                    $this->info("Sent trial expired email to {$hotel->email}");
                } catch (\Exception $e) {
                    $this->error("Failed sending email to {$hotel->email}: " . $e->getMessage());
                }
            }
        }

        // 2. Process Expired Subscriptions
        $expiredSubs = Subscription::where('status', 'Active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->with('hotel')
            ->get();

        $this->info("Found {$expiredSubs->count()} expired active subscriptions.");

        foreach ($expiredSubs as $sub) {
            $hotel = $sub->hotel;
            if (!$hotel) continue;

            $sub->update(['status' => 'Expired']);

            if ($hotel->account_status !== 'suspended') {
                $prevStatus = $hotel->account_status;
                $hotel->update(['account_status' => 'suspended']);

                ActivityLog::logAdminAction(
                    $hotel,
                    'Subscription Expired & Suspended',
                    $prevStatus,
                    'suspended',
                    "Paid subscription ended on {$sub->ends_at->format('Y-m-d')}. Hotel automatically suspended."
                );
            }
        }

        $this->info("Subscription expiry check completed successfully!");
        return Command::SUCCESS;
    }
}
