<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionInvoice;
use App\Services\PayPalService;
use Illuminate\Http\Request;

class PayPalController extends Controller
{
    public function __construct(protected PayPalService $payPalService) {}

    /**
     * Show Payment Page for Invoice
     */
    public function showPaymentPage(string $invoiceNumber)
    {
        $invoice = SubscriptionInvoice::where('invoice_number', $invoiceNumber)->with('hotel')->firstOrFail();

        if ($invoice->status === 'paid' || $invoice->payment_status === 'paid') {
            return view('billing.payment-success', [
                'invoice' => $invoice,
                'alreadyPaid' => true,
            ]);
        }

        $clientId = config('services.paypal.client_id', env('PAYPAL_CLIENT_ID', 'test'));
        $currency = strtoupper($invoice->currency ?: 'USD');

        return view('billing.pay', compact('invoice', 'clientId', 'currency'));
    }

    /**
     * API Endpoint to create PayPal order dynamically
     */
    public function createOrder(string $invoiceNumber)
    {
        $invoice = SubscriptionInvoice::where('invoice_number', $invoiceNumber)->firstOrFail();

        try {
            $order = $this->payPalService->createOrder($invoice);
            return response()->json(['id' => $order['id'], 'approve_url' => $order['approve_url'] ?? null]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API Endpoint to capture PayPal order server-side
     */
    public function captureOrder(Request $request, string $invoiceNumber)
    {
        $invoice = SubscriptionInvoice::where('invoice_number', $invoiceNumber)->firstOrFail();
        $orderId = $request->input('orderID') ?: $request->input('token');

        if (!$orderId) {
            return response()->json(['error' => 'Missing Order ID'], 400);
        }

        try {
            $captureResult = $this->payPalService->captureOrder($orderId);

            if (($captureResult['status'] ?? '') === 'COMPLETED') {
                $transactionId = $captureResult['transaction_id'] ?? $orderId;
                $payerEmail = $captureResult['payer_email'] ?? null;

                $this->payPalService->processPaymentSuccess(
                    $invoice,
                    $orderId,
                    $transactionId,
                    $payerEmail,
                    $captureResult
                );

                return response()->json(['status' => 'success', 'redirect' => route('billing.paypal.success', ['invoice' => $invoiceNumber])]);
            }

            return response()->json(['error' => 'PayPal payment not completed.'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Web Redirect Success View
     */
    public function success(string $invoiceNumber)
    {
        $invoice = SubscriptionInvoice::where('invoice_number', $invoiceNumber)->with('hotel')->firstOrFail();

        return view('billing.payment-success', [
            'invoice' => $invoice,
            'alreadyPaid' => false,
        ]);
    }

    /**
     * Web Redirect Cancel View
     */
    public function cancel(string $invoiceNumber)
    {
        $invoice = SubscriptionInvoice::where('invoice_number', $invoiceNumber)->with('hotel')->firstOrFail();

        return view('billing.payment-cancelled', compact('invoice'));
    }

    /**
     * Server-to-Server PayPal Webhook Endpoint
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['event_type'] ?? '';

        logger()->info("PayPal Webhook Received: {$eventType}", ['payload' => $payload]);

        if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
            $resource = $payload['resource'] ?? [];
            $orderId = $resource['supplementary_data']['related_ids']['order_id'] ?? ($resource['id'] ?? null);
            $transactionId = $resource['id'] ?? null;
            $customIdJson = $resource['custom_id'] ?? null;

            $invoice = null;
            if ($customIdJson) {
                $decoded = json_decode($customIdJson, true);
                if (!empty($decoded['invoice_id'])) {
                    $invoice = SubscriptionInvoice::find($decoded['invoice_id']);
                }
            }

            if (!$invoice && !empty($orderId)) {
                $invoice = SubscriptionInvoice::where('paypal_order_id', $orderId)->first();
            }

            if ($invoice && $transactionId) {
                $this->payPalService->processPaymentSuccess(
                    $invoice,
                    $orderId ?: $transactionId,
                    $transactionId,
                    $resource['payer']['email_address'] ?? null,
                    $payload
                );
            }
        }

        return response()->json(['status' => 'webhook_processed'], 200);
    }
}
