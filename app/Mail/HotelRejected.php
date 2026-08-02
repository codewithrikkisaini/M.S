<?php

namespace App\Mail;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HotelRejected extends Mailable
{
    use Queueable, SerializesModels;

    public Hotel $hotel;
    public ?User $adminUser;

    /**
     * Create a new message instance.
     */
    public function __construct(Hotel $hotel, ?User $adminUser = null)
    {
        $this->hotel = $hotel;
        $this->adminUser = $adminUser;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hotel Registration Update',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.hotel-rejected',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
