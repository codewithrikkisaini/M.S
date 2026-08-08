<?php

namespace App\Mail;

use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Hotel $hotel) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🌟 Welcome to Lodgiko! Your Hotel Dashboard is Now Active",
        );
    }

    public function content(): Content
    {
        $loginUrl = route('login');

        return new Content(
            html: "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                    <div style='background-color: #4f46e5; color: #ffffff; padding: 24px; text-align: center;'>
                        <h2 style='margin: 0;'>Welcome to Lodgiko!</h2>
                        <p style='margin: 5px 0 0; color: #c7d2fe;'>Your hotel account is ready</p>
                    </div>
                    <div style='padding: 24px; color: #334155;'>
                        <p>Hello <strong>{$this->hotel->owner_name}</strong>,</p>
                        <p>Welcome aboard! Your subscription for <strong>{$this->hotel->name}</strong> (ID: <code>{$this->hotel->hotel_code}</code>) is now fully active.</p>

                        <p>You can manage all your room inventories, bookings, check-ins, invoices, housekeeping, and revenue analytics directly from your dashboard.</p>

                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$loginUrl}' style='background-color: #4f46e5; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Go to Hotel Dashboard</a>
                        </div>
                    </div>
                </div>
            ",
        );
    }
}
