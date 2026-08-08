<?php

namespace App\Mail;

use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewHotelNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Hotel $hotel)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🚀 New Hotel Registration: {$this->hotel->name} ({$this->hotel->hotel_code})",
        );
    }

    public function content(): Content
    {
        return new Content(
            html: "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;'>
                    <div style='background-color: #1e293b; color: #ffffff; padding: 20px; text-align: center;'>
                        <h2 style='margin: 0;'>New Hotel Registration Received</h2>
                        <p style='margin: 5px 0 0; color: #94a3b8;'>Admin.Lodgiko.com System Notification</p>
                    </div>
                    <div style='padding: 24px; color: #334155;'>
                        <p>A new hotel has just registered on Lodgiko and is awaiting your review and approval.</p>
                        
                        <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                            <tr style='border-bottom: 1px solid #f1f5f9;'><td style='padding: 8px; font-weight: bold;'>Hotel Code:</td><td style='padding: 8px;'>{$this->hotel->hotel_code}</td></tr>
                            <tr style='border-bottom: 1px solid #f1f5f9;'><td style='padding: 8px; font-weight: bold;'>Hotel Name:</td><td style='padding: 8px;'>{$this->hotel->name}</td></tr>
                            <tr style='border-bottom: 1px solid #f1f5f9;'><td style='padding: 8px; font-weight: bold;'>Owner / Contact:</td><td style='padding: 8px;'>{$this->hotel->owner_name}</td></tr>
                            <tr style='border-bottom: 1px solid #f1f5f9;'><td style='padding: 8px; font-weight: bold;'>Email:</td><td style='padding: 8px;'>{$this->hotel->email}</td></tr>
                            <tr style='border-bottom: 1px solid #f1f5f9;'><td style='padding: 8px; font-weight: bold;'>Phone:</td><td style='padding: 8px;'>{$this->hotel->phone}</td></tr>
                            <tr style='border-bottom: 1px solid #f1f5f9;'><td style='padding: 8px; font-weight: bold;'>City / Country:</td><td style='padding: 8px;'>{$this->hotel->city}, {$this->hotel->country}</td></tr>
                            <tr style='border-bottom: 1px solid #f1f5f9;'><td style='padding: 8px; font-weight: bold;'>Registration Date:</td><td style='padding: 8px;'>{$this->hotel->created_at->format('M d, Y H:i')}</td></tr>
                        </table>

                        <div style='text-align: center; margin-top: 30px;'>
                            <a href='" . route('superadmin.hotels.index') . "' style='background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Review & Approve Hotel</a>
                        </div>
                    </div>
                </div>
            ",
        );
    }
}
