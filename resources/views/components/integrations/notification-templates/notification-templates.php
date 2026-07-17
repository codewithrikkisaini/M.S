<?php

use Livewire\Component;
use App\Models\EmailTemplate;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $templatesList = [];
    public $editingId;
    public $type;
    public $subject;
    public $body;
    public $variables;
    public $showModal = false;

    public function mount(): void
    {
        $this->loadTemplates();
    }

    public function loadTemplates(): void
    {
        $hotelId = Auth::user()->hotel_id;

        // Default template content
        $defaults = [
            [
                'type' => 'booking_confirmation',
                'subject' => 'Booking Confirmed - {{hotel_name}}',
                'body' => "Hi {{guest_name}},\n\nYour booking at {{hotel_name}} has been confirmed!\n\nBooking ID: {{booking_id}}\nCheck-in Date: {{checkin_date}}\nCheck-out Date: {{checkout_date}}\n\nWe look forward to welcoming you!",
                'variables' => 'guest_name,hotel_name,booking_id,checkin_date,checkout_date'
            ],
            [
                'type' => 'check_in_welcome',
                'subject' => 'Welcome to {{hotel_name}}!',
                'body' => "Dear {{guest_name}},\n\nWelcome to {{hotel_name}}!\nWe are delighted to have you stay with us. Your room number is: {{room_number}}.\n\nIf you need anything, please reach out to reception.",
                'variables' => 'guest_name,hotel_name,room_number'
            ],
            [
                'type' => 'whatsapp_booking_alert',
                'subject' => 'WhatsApp Notification',
                'body' => "Hi {{guest_name}}, your booking at {{hotel_name}} is confirmed! Ref: {{booking_id}}.",
                'variables' => 'guest_name,hotel_name,booking_id'
            ]
        ];

        foreach ($defaults as $tmpl) {
            EmailTemplate::firstOrCreate(
                ['hotel_id' => $hotelId, 'type' => $tmpl['type']],
                [
                    'subject' => $tmpl['subject'],
                    'body' => $tmpl['body'],
                    'variables' => $tmpl['variables']
                ]
            );
        }

        $this->templatesList = EmailTemplate::where('hotel_id', $hotelId)->get();
    }

    public function editTemplate($id): void
    {
        $tmpl = EmailTemplate::findOrFail($id);
        $this->editingId = $tmpl->id;
        $this->type = $tmpl->type;
        $this->subject = $tmpl->subject;
        $this->body = $tmpl->body;
        $this->variables = $tmpl->variables;
        
        $this->showModal = true;
    }

    public function saveTemplate(): void
    {
        $this->validate([
            'body' => 'required|string',
            'subject' => 'nullable|string',
        ]);

        $tmpl = EmailTemplate::findOrFail($this->editingId);
        $tmpl->update([
            'subject' => $this->subject,
            'body' => $this->body,
        ]);

        ActivityLog::create([
            'hotel_id' => Auth::user()->hotel_id,
            'action' => 'Template Edit',
            'description' => "Updated notification template for '{$tmpl->type}'.",
            'ip_address' => request()->ip(),
            'user_id' => Auth::id(),
        ]);

        $this->showModal = false;
        $this->loadTemplates();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Notification template saved!"
        ]);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
