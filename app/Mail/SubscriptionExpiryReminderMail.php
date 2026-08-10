<?php

namespace App\Mail;

use App\Models\Hotel;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Hotel $hotel,
        public Subscription $subscription,
        public int $daysRemaining
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->daysRemaining === 0
            ? "⚠️ Action Required: Your Lodgiko Subscription Expires Today"
            : "⏰ Renewal Reminder: Your Subscription Expires in {$this->daysRemaining} Days";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $payUrl = route('superadmin.hotels.index');

        return new Content(
            html: "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                    <div style='background-color: #eab308; color: #ffffff; padding: 24px; text-align: center;'>
                        <h2 style='margin: 0;'>Subscription Renewal Reminder</h2>
                        <p style='margin: 5px 0 0; color: #fef08a;'>{$this->daysRemaining} Days Remaining</p>
                    </div>
                    <div style='padding: 24px; color: #334155;'>
                        <p>Hello <strong>{$this->hotel->owner_name}</strong>,</p>
                        <p>This is a reminder that your subscription for <strong>{$this->hotel->name}</strong> (ID: <code>{$this->hotel->hotel_code}</code>) will expire on <strong>" . ($this->subscription->ends_at ? $this->subscription->ends_at->format('d M Y') : 'soon') . "</strong>.</p>
                        
                        <p>To avoid service disruption or restriction on new bookings, please renew your subscription before the expiry date.</p>

                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$payUrl}' style='background-color: #ca8a04; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Renew Subscription Now</a>
                        </div>
                    </div>
                </div>
            ",
        );
    }
}
