<?php

namespace App\Mail;

use App\Models\Hotel;
use App\Models\SubscriptionInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaidInvoiceCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Hotel $hotel,
        public SubscriptionInvoice $invoice
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📄 Subscription Invoice #{$this->invoice->invoice_number} Created - Lodgiko",
        );
    }

    public function content(): Content
    {
        $payUrl = route('billing.pay', ['invoice' => $this->invoice->invoice_number]);

        return new Content(
            html: "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                    <div style='background-color: #2563eb; color: #ffffff; padding: 24px; text-align: center;'>
                        <h2 style='margin: 0;'>Subscription Invoice Issued</h2>
                        <p style='margin: 5px 0 0; color: #bfdbfe;'>Invoice #{$this->invoice->invoice_number}</p>
                    </div>
                    <div style='padding: 24px; color: #334155;'>
                        <p>Hello <strong>{$this->hotel->owner_name}</strong>,</p>
                        <p>Your hotel registration for <strong>{$this->hotel->name}</strong> (ID: <code>{$this->hotel->hotel_code}</code>) has been approved under a Paid Subscription plan.</p>
                        
                        <div style='background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; margin: 20px 0;'>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr><td style='padding: 6px 0; color: #64748b;'>Invoice Number:</td><td style='padding: 6px 0; font-weight: bold; text-align: right;'>{$this->invoice->invoice_number}</td></tr>
                                <tr><td style='padding: 6px 0; color: #64748b;'>Amount Due:</td><td style='padding: 6px 0; font-weight: bold; color: #2563eb; text-align: right;'>{$this->invoice->currency} " . number_format($this->invoice->amount, 2) . "</td></tr>
                                <tr><td style='padding: 6px 0; color: #64748b;'>Due Date:</td><td style='padding: 6px 0; text-align: right;'>{$this->invoice->due_date->format('d M Y')}</td></tr>
                                <tr><td style='padding: 6px 0; color: #64748b;'>Payment Method:</td><td style='padding: 6px 0; text-align: right;'>PayPal / Credit Card</td></tr>
                            </table>
                        </div>

                        <p>Please complete your payment to activate full access to your Lodgiko Hotel Dashboard.</p>

                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$payUrl}' style='background-color: #16a34a; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; display: inline-block;'>💳 Pay Now via PayPal</a>
                        </div>
                    </div>
                </div>
            ",
        );
    }
}
