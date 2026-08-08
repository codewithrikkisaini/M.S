<?php

namespace App\Mail;

use App\Models\Hotel;
use App\Models\SubscriptionInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Hotel $hotel,
        public SubscriptionInvoice $invoice,
        public string $transactionId
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "✅ Payment Receipt: Invoice #{$this->invoice->invoice_number} Paid - Lodgiko",
        );
    }

    public function content(): Content
    {
        return new Content(
            html: "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                    <div style='background-color: #16a34a; color: #ffffff; padding: 24px; text-align: center;'>
                        <h2 style='margin: 0;'>Payment Confirmation</h2>
                        <p style='margin: 5px 0 0; color: #dcfce7;'>Thank you for your payment!</p>
                    </div>
                    <div style='padding: 24px; color: #334155;'>
                        <p>Hello <strong>{$this->hotel->owner_name}</strong>,</p>
                        <p>We have successfully received your payment for <strong>{$this->hotel->name}</strong> (ID: <code>{$this->hotel->hotel_code}</code>).</p>
                        
                        <div style='background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 16px; margin: 20px 0;'>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr><td style='padding: 6px 0; color: #166534;'>Invoice Number:</td><td style='padding: 6px 0; font-weight: bold; text-align: right;'>{$this->invoice->invoice_number}</td></tr>
                                <tr><td style='padding: 6px 0; color: #166534;'>Amount Paid:</td><td style='padding: 6px 0; font-weight: bold; color: #16a34a; text-align: right;'>{$this->invoice->currency} " . number_format($this->invoice->amount, 2) . "</td></tr>
                                <tr><td style='padding: 6px 0; color: #166534;'>Payment Method:</td><td style='padding: 6px 0; text-align: right;'>PayPal</td></tr>
                                <tr><td style='padding: 6px 0; color: #166534;'>PayPal Transaction ID:</td><td style='padding: 6px 0; font-family: monospace; text-align: right;'>{$this->transactionId}</td></tr>
                                <tr><td style='padding: 6px 0; color: #166534;'>Payment Date:</td><td style='padding: 6px 0; text-align: right;'>" . now()->format('d M Y, H:i') . "</td></tr>
                            </table>
                        </div>

                        <p>Your subscription is now fully active.</p>
                    </div>
                </div>
            ",
        );
    }
}
