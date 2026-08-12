<?php

namespace App\Mail;

use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class TrialActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Hotel $hotel,
        public int $durationDays,
        public Carbon $expiryDate
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎉 Congratulations! Your {$this->durationDays}-Day Free Trial has been activated - Lodgiko",
        );
    }

    public function build()
    {
        $loginUrl = route('login');

        return $this->html("
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #0d9488; color: #ffffff; padding: 24px; text-align: center;'>
                    <h2 style='margin: 0;'>Hotel Registration Approved!</h2>
                    <p style='margin: 5px 0 0; color: #ccfbf1;'>Your FREE {$this->durationDays}-Day Trial is Now Active</p>
                </div>
                <div style='padding: 24px; color: #334155;'>
                    <p>Hello <strong>{$this->hotel->owner_name}</strong>,</p>
                    <p>Great news! Your hotel, <strong>{$this->hotel->name}</strong> (ID: <code>{$this->hotel->hotel_code}</code>), has been approved by our team.</p>
                    
                    <div style='background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 16px; margin: 20px 0;'>
                        <h4 style='margin: 0 0 10px; color: #166534;'>Trial Plan Details</h4>
                        <p style='margin: 4px 0;'><strong>Duration:</strong> {$this->durationDays} Days Free Access</p>
                        <p style='margin: 4px 0;'><strong>Activated Date:</strong> " . now()->format('d F Y') . "</p>
                        <p style='margin: 4px 0;'><strong>Trial Expires On:</strong> <span style='color: #dc2626; font-weight: bold;'>" . $this->expiryDate->format('d F Y') . "</span></p>
                    </div>

                    <p>You can now log in to your dashboard to set up your rooms, manage bookings, and configure settings.</p>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$loginUrl}' style='background-color: #0d9488; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Login to Hotel Dashboard</a>
                    </div>
                </div>
            </div>
        ");
    }
}
