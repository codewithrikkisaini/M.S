<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use Illuminate\Support\Carbon;

class InvoiceService
{
    /**
     * Create a new subscription invoice for a hotel
     */
    public function createInvoice(Hotel $hotel, Subscription $subscription, float $amount, string $currency = 'USD', ?string $notes = null): SubscriptionInvoice
    {
        $invoiceNumber = SubscriptionInvoice::generateNextInvoiceNumber();

        return SubscriptionInvoice::create([
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
            'notes' => $notes ?: 'Subscription Invoice',
        ]);
    }

    /**
     * Cancel an unpaid invoice
     */
    public function cancelInvoice(SubscriptionInvoice $invoice, ?string $reason = null): SubscriptionInvoice
    {
        if ($invoice->status === 'paid' || $invoice->payment_status === 'paid') {
            throw new \Exception("Paid invoices cannot be cancelled directly.");
        }

        $invoice->update([
            'status' => 'cancelled',
            'payment_status' => 'cancelled',
            'notes' => ($invoice->notes ? $invoice->notes . ' | ' : '') . 'Cancelled: ' . ($reason ?: 'By Admin'),
        ]);

        return $invoice;
    }
}
