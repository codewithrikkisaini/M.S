<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TrialActivatedMail;
use App\Mail\PaidInvoiceCreatedMail;
use App\Mail\WelcomeActivationMail;

class SubscriptionService
{
    /**
     * Approve 7 Day Free Trial
     */
    public function approve7DayTrial(Hotel $hotel, ?string $notes = null): Subscription
    {
        return DB::transaction(function () use ($hotel, $notes) {
            $prevStatus = $hotel->account_status;

            $hotel->update([
                'account_status' => 'active',
                'status' => 'approved',
                'approved_at' => Carbon::now(),
                'approved_by' => auth()->id(),
            ]);

            $plan = \App\Models\SubscriptionPlan::firstOrCreate(
                ['slug' => 'trial'],
                ['name' => 'Free Trial Plan', 'price' => 0.00, 'billing_cycle' => 'trial', 'trial_days' => 15, 'status' => 'active']
            );

            $subscription = Subscription::create([
                'hotel_id' => $hotel->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'Trial 7 Days',
                'amount' => 0.00,
                'currency' => $hotel->currency ?: 'USD',
                'billing_cycle' => 'trial',
                'trial_starts_at' => Carbon::now(),
                'trial_ends_at' => Carbon::now()->addDays(7),
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addDays(7),
            ]);

            ActivityLog::logAdminAction(
                $hotel,
                'Approve 7-Day Trial',
                $prevStatus,
                'active (Trial 7 Days)',
                $notes ?: 'Approved 7-day free trial'
            );

            // Dispatch email notification
            try {
                Mail::to($hotel->email)->send(new TrialActivatedMail($hotel, 7, $subscription->trial_ends_at));
            } catch (\Exception $e) {
                logger()->error('Failed to send Trial 7 Days email: ' . $e->getMessage());
            }

            return $subscription;
        });
    }

    /**
     * Approve 15 Day Free Trial
     */
    public function approve15DayTrial(Hotel $hotel, ?string $notes = null): Subscription
    {
        return DB::transaction(function () use ($hotel, $notes) {
            $prevStatus = $hotel->account_status;

            $hotel->update([
                'account_status' => 'active',
                'status' => 'approved',
                'approved_at' => Carbon::now(),
                'approved_by' => auth()->id(),
            ]);

            $plan = \App\Models\SubscriptionPlan::firstOrCreate(
                ['slug' => 'trial'],
                ['name' => 'Free Trial Plan', 'price' => 0.00, 'billing_cycle' => 'trial', 'trial_days' => 15, 'status' => 'active']
            );

            $subscription = Subscription::create([
                'hotel_id' => $hotel->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'Trial 15 Days',
                'amount' => 0.00,
                'currency' => $hotel->currency ?: 'USD',
                'billing_cycle' => 'trial',
                'trial_starts_at' => Carbon::now(),
                'trial_ends_at' => Carbon::now()->addDays(15),
                'starts_at' => Carbon::now(),
                'ends_at' => Carbon::now()->addDays(15),
            ]);

            ActivityLog::logAdminAction(
                $hotel,
                'Approve 15-Day Trial',
                $prevStatus,
                'active (Trial 15 Days)',
                $notes ?: 'Approved 15-day free trial'
            );

            // Dispatch email notification
            try {
                Mail::to($hotel->email)->send(new TrialActivatedMail($hotel, 15, $subscription->trial_ends_at));
            } catch (\Exception $e) {
                logger()->error('Failed to send Trial 15 Days email: ' . $e->getMessage());
            }

            return $subscription;
        });
    }

    /**
     * Approve Paid Subscription & Create Invoice
     */
    public function createPaidSubscription(Hotel $hotel, float $amount, string $currency = 'USD', string $billingCycle = 'monthly', ?string $notes = null): SubscriptionInvoice
    {
        return DB::transaction(function () use ($hotel, $amount, $currency, $billingCycle, $notes) {
            $prevStatus = $hotel->account_status;

            // Hotel remains awaiting payment until invoice is paid
            $hotel->update([
                'account_status' => 'pending_approval',
                'status' => 'approved',
                'approved_at' => Carbon::now(),
                'approved_by' => auth()->id(),
            ]);

            $planSlug = $billingCycle === 'annual' ? 'yearly' : 'monthly';
            $plan = \App\Models\SubscriptionPlan::firstOrCreate(
                ['slug' => $planSlug],
                ['name' => ucfirst($planSlug) . ' Plan', 'price' => $amount, 'billing_cycle' => $billingCycle, 'status' => 'active']
            );

            $subscription = Subscription::create([
                'hotel_id' => $hotel->id,
                'subscription_plan_id' => $plan->id,
                'status' => 'Awaiting Payment',
                'amount' => $amount,
                'currency' => $currency,
                'billing_cycle' => $billingCycle,
                'starts_at' => Carbon::now(),
                'ends_at' => $billingCycle === 'annual' ? Carbon::now()->addYear() : Carbon::now()->addMonth(),
            ]);

            $invoiceNumber = SubscriptionInvoice::generateNextInvoiceNumber();

            $invoice = SubscriptionInvoice::create([
                'hotel_id' => $hotel->id,
                'subscription_id' => $subscription->id,
                'subscription_plan_id' => $subscription->subscription_plan_id,
                'invoice_number' => $invoiceNumber,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
                'payment_status' => 'pending',
                'billing_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays(7),
                'notes' => $notes ?: "Paid Subscription Invoice ({$billingCycle})",
            ]);

            ActivityLog::logAdminAction(
                $hotel,
                'Create Paid Subscription Invoice',
                $prevStatus,
                'Awaiting Payment',
                "Created invoice {$invoiceNumber} for amount {$currency} {$amount}"
            );

            // Send Paid Invoice Email
            try {
                Mail::to($hotel->email)->send(new PaidInvoiceCreatedMail($hotel, $invoice));
            } catch (\Exception $e) {
                logger()->error('Failed to send Paid Invoice email: ' . $e->getMessage());
            }

            return $invoice;
        });
    }

    /**
     * Extend Trial manually by N days
     */
    public function extendTrial(Hotel $hotel, int $additionalDays, ?string $notes = null): Subscription
    {
        return DB::transaction(function () use ($hotel, $additionalDays, $notes) {
            $subscription = $hotel->subscriptions()->latest()->first();

            if (!$subscription) {
                throw new \Exception("No subscription found for this hotel to extend.");
            }

            $oldExpiry = $subscription->trial_ends_at ?: $subscription->ends_at;
            $newExpiry = Carbon::parse($oldExpiry)->addDays($additionalDays);

            $subscription->update([
                'trial_ends_at' => $newExpiry,
                'ends_at' => $newExpiry,
                'status' => 'Trial ' . ($subscription->trial_starts_at ? $subscription->trial_starts_at->diffInDays($newExpiry) : $additionalDays) . ' Days',
            ]);

            $hotel->update(['account_status' => 'active']);

            ActivityLog::logAdminAction(
                $hotel,
                'Trial Extended',
                'Expired / Trial',
                'active',
                "Trial extended by {$additionalDays} days. New Expiry: {$newExpiry->format('Y-m-d')}. Notes: {$notes}"
            );

            return $subscription;
        });
    }

    /**
     * Suspend Hotel
     */
    public function suspendHotel(Hotel $hotel, ?string $reason = null): Hotel
    {
        return DB::transaction(function () use ($hotel, $reason) {
            $prevStatus = $hotel->account_status;

            $hotel->update([
                'account_status' => 'suspended',
            ]);

            if ($subscription = $hotel->subscriptions()->latest()->first()) {
                $subscription->update(['status' => 'Suspended']);
            }

            ActivityLog::logAdminAction(
                $hotel,
                'Hotel Suspended',
                $prevStatus,
                'suspended',
                "Reason: " . ($reason ?: 'Administrative suspension')
            );

            return $hotel;
        } );
    }

    /**
     * Activate Hotel
     */
    public function activateHotel(Hotel $hotel, ?string $notes = null): Hotel
    {
        return DB::transaction(function () use ($hotel, $notes) {
            $prevStatus = $hotel->account_status;

            $hotel->update([
                'account_status' => 'active',
            ]);

            if ($subscription = $hotel->subscriptions()->latest()->first()) {
                if (in_array($subscription->status, ['Suspended', 'Awaiting Payment', 'Pending Approval'])) {
                    $subscription->update(['status' => 'Active']);
                }
            }

            ActivityLog::logAdminAction(
                $hotel,
                'Hotel Activated',
                $prevStatus,
                'active',
                "Notes: " . ($notes ?: 'Activated by Administrator')
            );

            return $hotel;
        });
    }
}
