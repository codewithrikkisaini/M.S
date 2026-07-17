<?php

use Livewire\Component;
use App\Models\Setting;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $stripe_publishable_key;
    public $stripe_secret_key;
    public $stripe_webhook_secret;
    public $stripe_enabled = false;

    // Test Charge variables
    public $test_amount = 50.00;
    public $test_invoice_id;
    public $invoicesList = [];

    public function mount(): void
    {
        $this->stripe_publishable_key = Setting::get('stripe_publishable_key', '');
        $this->stripe_secret_key = Setting::get('stripe_secret_key', '');
        $this->stripe_webhook_secret = Setting::get('stripe_webhook_secret', '');
        $this->stripe_enabled = (bool) Setting::get('stripe_enabled', '0');

        $this->loadUnpaidInvoices();
    }

    public function loadUnpaidInvoices(): void
    {
        $hotelId = Auth::user()->hotel_id;
        $this->invoicesList = Invoice::where('hotel_id', $hotelId)
            ->where('status', 'unpaid')
            ->get();

        if ($this->invoicesList->count() > 0) {
            $this->test_invoice_id = $this->invoicesList->first()->id;
            $this->test_amount = $this->invoicesList->first()->amount;
        } else {
            $this->test_invoice_id = null;
        }
    }

    public function updatedTestInvoiceId($value): void
    {
        if ($value) {
            $inv = Invoice::find($value);
            if ($inv) {
                $this->test_amount = $inv->amount;
            }
        }
    }

    public function saveSettings(): void
    {
        Setting::set('stripe_publishable_key', $this->stripe_publishable_key);
        Setting::set('stripe_secret_key', $this->stripe_secret_key);
        Setting::set('stripe_webhook_secret', $this->stripe_webhook_secret);
        Setting::set('stripe_enabled', $this->stripe_enabled ? '1' : '0');

        ActivityLog::create([
            'hotel_id' => Auth::user()->hotel_id,
            'action' => 'Stripe Gateway',
            'description' => "Updated Stripe Gateway API credentials.",
            'ip_address' => request()->ip(),
            'user_id' => Auth::id(),
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Stripe Gateway credentials saved!"
        ]);
    }

    public function runTestCharge(): void
    {
        if (!$this->stripe_enabled) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => "Please enable Stripe Integration first."
            ]);
            return;
        }

        $hotelId = Auth::user()->hotel_id;

        DB::transaction(function () use ($hotelId) {
            // Mock transaction
            $payment = Payment::create([
                'hotel_id' => $hotelId,
                'invoice_id' => $this->test_invoice_id,
                'amount' => $this->test_amount,
                'payment_method' => 'Stripe Checkout (Simulated)',
                'transaction_id' => 'ch_stripe_test_' . str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'),
                'payment_date' => now(),
            ]);

            if ($this->test_invoice_id) {
                Invoice::where('id', $this->test_invoice_id)->update(['status' => 'paid']);
            }

            ActivityLog::create([
                'hotel_id' => $hotelId,
                'action' => 'Stripe Checkout',
                'description' => "Executed simulated Stripe charge of $" . number_format($this->test_amount, 2) . " (Transaction: {$payment->transaction_id}).",
                'ip_address' => request()->ip(),
                'user_id' => Auth::id(),
            ]);
        });

        $this->loadUnpaidInvoices();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Mock Stripe Checkout Charge succeeded!"
        ]);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
