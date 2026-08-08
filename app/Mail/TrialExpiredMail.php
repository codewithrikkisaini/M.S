<?php

namespace App\Mail;

use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Hotel $hotel) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔴 Your Free Trial for {$this->hotel->name} Has Expired - Lodgiko",
        );
    }

    public function content(): Content
    {
        $loginUrl = route('login');

        return new Content(
            html: "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                    <div style='background-color: #dc2626; color: #ffffff; padding: 24px; text-align: center;'>
                        <h2 style='margin: 0;'>Free Trial Expired</h2>
                        <p style='margin: 5px 0 0; color: #fecaca;'>Action Required to Continue Service</p>
                    </div>
                    <div style='padding: 24px; color: #334155;'>
                        <p>Hello <strong>{$this->hotel->owner_name}</strong>,</p>
                        <p>Your free trial period for <strong>{$this->hotel->name}</strong> (ID: <code>{$this->hotel->hotel_code}</code>) has now expired.</p>
                        
                        <div style='background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 16px; margin: 20px 0;'>
                            <p style='margin: 0; color: #991b1b;'>Your hotel can no longer receive new online bookings until a paid subscription is activated.</p>
                        </div>

                        <p>Please log in to your account and upgrade to a paid plan to restore full functionality immediately.</p>

                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$loginUrl}' style='background-color: #dc2626; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Upgrade Subscription Now</a>
                        </div>
                    </div>
                </div>
            ",
        );
    }
}
