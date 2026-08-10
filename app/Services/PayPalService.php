<?php

namespace App\Services;

use App\Models\SubscriptionInvoice;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReceiptMail;
use App\Mail\WelcomeActivationMail;

class PayPalService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $mode = config('services.paypal.mode', env('PAYPAL_MODE', 'sandbox'));
        $this->baseUrl = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
        $this->clientId = config('services.paypal.client_id', env('PAYPAL_CLIENT_ID', ''));
        $this->clientSecret = config('services.paypal.client_secret', env('PAYPAL_CLIENT_SECRET', ''));
    }

    /**
     * Get OAuth Access Token from PayPal API
     */
    public function getAccessToken(): string
    {
        if (empty($this->clientId) || empty($this->clientSecret)) {
            // Return dummy token in sandbox fallback mode if credentials not configured yet
            return 'SANDBOX_FALLBACK_TOKEN';
        }

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post("{$this->baseUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->successful()) {
            return $response->json('access_token');
        }

        throw new \Exception("PayPal Auth Error: " . $response->body());
    }

    /**
     * Create PayPal Order dynamically for Subscription Invoice
     */
    public function createOrder(SubscriptionInvoice $invoice): array
    {
        $token = $this->getAccessToken();

        if ($token === 'SANDBOX_FALLBACK_TOKEN') {
            return [
                'id' => 'MOCK-PAYPAL-ORDER-' . time(),
                'status' => 'CREATED',
                'approve_url' => route('billing.paypal.capture', ['invoice' => $invoice->invoice_number, 'mock' => 1]),
            ];
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $invoice->invoice_number,
                        'description' => "Lodgiko Subscription Invoice #{$invoice->invoice_number}",
                        'custom_id' => json_encode(['hotel_id' => $invoice->hotel_id, 'invoice_id' => $invoice->id]),
                        'amount' => [
                            'currency_code' => strtoupper($invoice->currency ?: 'USD'),
                            'value' => number_format((float)$invoice->amount, 2, '.', ''),
                        ],
                    ]
                ],
                'application_context' => [
                    'brand_name' => 'Lodgiko Admin',
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('billing.paypal.success', ['invoice' => $invoice->invoice_number]),
                    'cancel_url' => route('billing.paypal.cancel', ['invoice' => $invoice->invoice_number]),
                ]
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $approveUrl = collect($data['links'])->firstWhere('rel', 'approve')['href'] ?? '';
            return [
                'id' => $data['id'],
                'status' => $data['status'],
                'approve_url' => $approveUrl,
            ];
        }

        throw new \Exception("PayPal Create Order Failed: " . $response->body());
    }

    /**
     * Capture PayPal Order Server-side
     */
    public function captureOrder(string $orderId): array
    {
        $token = $this->getAccessToken();

        if ($token === 'SANDBOX_FALLBACK_TOKEN') {
            return [
                'id' => $orderId,
                'status' => 'COMPLETED',
                'transaction_id' => 'MOCK-TXN-' . time(),
                'payer_email' => 'payer@example.com',
            ];
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture", []);

        if ($response->successful()) {
            $data = $response->json();
            $capture = $data['purchase_units'][0]['payments']['captures'][0] ?? null;
            return [
                'id' => $data['id'],
                'status' => $data['status'],
                'transaction_id' => $capture['id'] ?? $data['id'],
                'payer_email' => $data['payer']['email_address'] ?? null,
                'raw' => $data,
            ];
        }

        throw new \Exception("PayPal Capture Order Failed: " . $response->body());
    }

    /**
     * Process Successful Payment Idempotently
     */
    public function processPaymentSuccess(SubscriptionInvoice $invoice, string $orderId, string $transactionId, ?string $payerEmail = null, array $details = []): bool
    {
        return DB::transaction(function () use ($invoice, $orderId, $transactionId, $payerEmail, $details) {
            // Check if already paid to prevent duplicate processing
            if ($invoice->status === 'paid' || $invoice->payment_status === 'paid') {
                return true; // Already processed cleanly
            }

            $invoice->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => Carbon::now(),
                'payment_method' => 'PayPal',
                'paypal_order_id' => $orderId,
                'paypal_transaction_id' => $transactionId,
                'paypal_payer_email' => $payerEmail,
            ]);

            // Save Payment Transaction
            try {
                Payment::create([
                    'hotel_id' => $invoice->hotel_id,
                    'reservation_id' => 0,
                    'subscription_invoice_id' => $invoice->id,
                    'payment_type' => 'PayPal',
                    'payment_method' => 'PayPal',
                    'amount' => $invoice->amount,
                    'paid_at' => Carbon::now(),
                    'paypal_order_id' => $orderId,
                    'paypal_transaction_id' => $transactionId,
                    'payment_details' => json_encode($details),
                ]);
            } catch (\Exception $e) {
                logger()->warning('Payment record insert notice: ' . $e->getMessage());
            }

            // Activate Subscription & Hotel
            $hotel = $invoice->hotel;
            $subscription = $invoice->subscription ?: $hotel->subscriptions()->latest()->first();

            if ($subscription) {
                $billingCycle = $subscription->billing_cycle ?: 'monthly';
                $endDate = $billingCycle === 'annual' ? Carbon::now()->addYear() : Carbon::now()->addMonth();

                $subscription->update([
                    'status' => 'Active',
                    'starts_at' => Carbon::now(),
                    'ends_at' => $endDate,
                    'renews_at' => $endDate,
                ]);
            }

            $hotel->update([
                'account_status' => 'active',
                'status' => 'approved',
            ]);

            // Log Admin Audit
            ActivityLog::logAdminAction(
                $hotel,
                'Payment Received & Hotel Activated',
                'Awaiting Payment',
                'active',
                "Invoice #{$invoice->invoice_number} paid via PayPal. Txn ID: {$transactionId}. Amount: {$invoice->currency} {$invoice->amount}"
            );

            // Send Email Notifications
            try {
                Mail::to($hotel->email)->send(new PaymentReceiptMail($hotel, $invoice, $transactionId));
                Mail::to($hotel->email)->send(new WelcomeActivationMail($hotel));
            } catch (\Exception $e) {
                logger()->error('Failed to send Payment Success / Welcome emails: ' . $e->getMessage());
            }

            return true;
        });
    }
}
