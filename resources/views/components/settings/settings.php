<?php

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public string $activeTab = 'hotel';
    
    // Hotel Info
    public string $hotel_name     = '';
    public string $hotel_address  = '';
    public string $hotel_phone    = '';
    public string $hotel_email    = '';
    public string $hotel_website  = '';
    public string $hotel_timezone = 'UTC';

    // Preferences
    public string $currency       = 'USD';
    public string $date_format    = 'd M Y';
    public string $checkin_time   = '14:00';
    public string $checkout_time  = '12:00';

    // Notifications
    public bool $email_notifications = true;
    public bool $sms_notifications   = false;

    // Invoice
    public string $invoice_prefix = 'INV-';
    public string $invoice_footer = '';

    // Email / SMTP
    public bool $smtp_enabled        = false;
    public string $smtp_host         = '';
    public string $smtp_port         = '587';
    public string $smtp_username     = '';
    public string $smtp_password     = '';
    public string $smtp_encryption   = 'tls';
    public string $smtp_from_address = '';
    public string $smtp_from_name    = '';
    public string $test_email        = '';

    public function boot(): void
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function mount(): void
    {
        $s = Setting::all_map();

        $this->hotel_name           = $s['hotel_name']           ?? 'Merahkie PMS Lite';
        $this->hotel_address        = $s['hotel_address']         ?? '';
        $this->hotel_phone          = $s['hotel_phone']           ?? '';
        $this->hotel_email          = $s['hotel_email']           ?? '';
        $this->hotel_website        = $s['hotel_website']         ?? '';
        $this->hotel_timezone       = $s['hotel_timezone']        ?? 'UTC';
        $this->currency             = $s['currency']              ?? 'USD';
        $this->date_format          = $s['date_format']           ?? 'd M Y';
        $this->checkin_time         = $s['checkin_time']          ?? '14:00';
        $this->checkout_time        = $s['checkout_time']         ?? '12:00';
        $this->email_notifications  = ($s['email_notifications']  ?? '1') === '1';
        $this->sms_notifications    = ($s['sms_notifications']    ?? '0') === '1';
        $this->invoice_prefix       = $s['invoice_prefix']        ?? 'INV-';
        $this->invoice_footer       = $s['invoice_footer']        ?? '';

        $this->smtp_enabled        = ($s['smtp_enabled']        ?? '0') === '1';
        $this->smtp_host           = $s['smtp_host']            ?? '';
        $this->smtp_port           = $s['smtp_port']            ?? '587';
        $this->smtp_username       = $s['smtp_username']        ?? '';
        $this->smtp_encryption     = $s['smtp_encryption']      ?? 'tls';
        $this->smtp_from_address   = $s['smtp_from_address']    ?? '';
        $this->smtp_from_name      = $s['smtp_from_name']       ?? '';

        if (!empty($s['smtp_password'])) {
            try {
                $this->smtp_password = Crypt::decryptString($s['smtp_password']);
            } catch (\Exception $e) {
                $this->smtp_password = '';
            }
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function saveHotel(): void
    {
        $this->validate([
            'hotel_name'  => 'required|string|max:255',
            'hotel_email' => 'nullable|email',
        ]);

        Setting::set('hotel_name',     $this->hotel_name);
        Setting::set('hotel_address',  $this->hotel_address);
        Setting::set('hotel_phone',    $this->hotel_phone);
        Setting::set('hotel_email',    $this->hotel_email);
        Setting::set('hotel_website',  $this->hotel_website);
        Setting::set('hotel_timezone', $this->hotel_timezone);

        $this->dispatch('toast', message: 'Hotel settings saved successfully.', type: 'success');
    }

    public function savePreferences(): void
    {
        Setting::set('currency',      $this->currency);
        Setting::set('date_format',   $this->date_format);
        Setting::set('checkin_time',  $this->checkin_time);
        Setting::set('checkout_time', $this->checkout_time);
        Setting::set('hotel_timezone',$this->hotel_timezone);

        $this->dispatch('toast', message: 'Preferences saved successfully.', type: 'success');
    }

    public function saveNotifications(): void
    {
        Setting::set('email_notifications', $this->email_notifications ? '1' : '0');
        Setting::set('sms_notifications',   $this->sms_notifications   ? '1' : '0');

        $this->dispatch('toast', message: 'Notification settings saved.', type: 'success');
    }

    public function saveInvoice(): void
    {
        Setting::set('invoice_prefix', $this->invoice_prefix);
        Setting::set('invoice_footer', $this->invoice_footer);

        $this->dispatch('toast', message: 'Invoice settings saved.', type: 'success');
    }

    public function saveEmail(): void
    {
        $this->validate([
            'smtp_host'         => 'required_if:smtp_enabled,true|nullable|string|max:255',
            'smtp_port'         => 'required_if:smtp_enabled,true|nullable|integer|min:1|max:65535',
            'smtp_username'     => 'nullable|string|max:255',
            'smtp_password'     => 'nullable|string|max:255',
            'smtp_encryption'   => 'required|in:none,tls,ssl',
            'smtp_from_address' => 'nullable|email',
            'smtp_from_name'    => 'nullable|string|max:255',
        ]);

        Setting::set('smtp_enabled',      $this->smtp_enabled ? '1' : '0');
        Setting::set('smtp_host',         $this->smtp_host);
        Setting::set('smtp_port',         $this->smtp_port);
        Setting::set('smtp_username',     $this->smtp_username);
        Setting::set('smtp_password',     $this->smtp_password !== '' ? Crypt::encryptString($this->smtp_password) : '');
        Setting::set('smtp_encryption',   $this->smtp_encryption);
        Setting::set('smtp_from_address', $this->smtp_from_address);
        Setting::set('smtp_from_name',    $this->smtp_from_name);

        $this->dispatch('toast', message: 'Email settings saved successfully.', type: 'success');
    }

    public function sendTestEmail(): void
    {
        $this->validate(['test_email' => 'required|email']);

        if (!$this->smtp_host) {
            $this->dispatch('toast', message: 'Please enter an SMTP host before sending a test email.', type: 'error');
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $this->smtp_host);
        Config::set('mail.mailers.smtp.port', (int) $this->smtp_port);
        Config::set('mail.mailers.smtp.username', $this->smtp_username ?: null);
        Config::set('mail.mailers.smtp.password', $this->smtp_password ?: null);
        Config::set('mail.mailers.smtp.scheme', $this->smtp_encryption === 'ssl' ? 'smtps' : null);
        Config::set('mail.from.address', $this->smtp_from_address ?: 'hello@example.com');
        Config::set('mail.from.name', $this->smtp_from_name ?: 'Merahkie PMS Lite');

        try {
            Mail::raw('This is a test email from Merahkie PMS Lite to confirm your SMTP settings are working.', function ($message) {
                $message->to($this->test_email)->subject('SMTP Test Email');
            });

            $this->dispatch('toast', message: "Test email sent to {$this->test_email}.", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Failed to send test email: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render(): mixed
    {
        return $this->view([]);
    }
};
