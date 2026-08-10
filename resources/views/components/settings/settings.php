<?php

use Livewire\Component;
use App\Models\Setting;
use App\Models\Hotel;
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
    public string $hotel_city     = '';
    public string $hotel_state    = '';
    public string $hotel_country  = 'India';
    public string $hotel_pincode  = '';
    public string $hotel_timezone = 'Asia/Kolkata';

    // Preferences & Timings
    public string $currency       = 'INR';
    public string $date_format    = 'd M Y';
    public string $checkin_time   = '14:00';
    public string $checkout_time  = '12:00';

    // Tax & Invoice Settings
    public string $tax_id         = '';
    public string $tax_rate       = '18';
    public string $invoice_prefix = 'INV-';
    public string $invoice_footer = 'Thank you for staying with us!';

    // Booking Engine & Policies
    public string $cancellation_policy       = 'Free cancellation up to 24 hours prior to check-in date.';
    public string $booking_policy            = 'Government approved photo ID with address proof is required for all guests during check-in.';
    public bool   $auto_confirm_booking      = true;
    public string $advance_payment_percentage = '0';

    // Notifications
    public bool $email_notifications = true;
    public bool $sms_notifications   = false;

    // Email / SMTP
    public bool   $smtp_enabled        = false;
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
        if (!Auth::check() || (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('superadmin'))) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function mount(): void
    {
        $user = Auth::user();
        $hotel = $user->hotel_id ? Hotel::find($user->hotel_id) : null;
        $s = Setting::all_map();

        // Hotel Info (Fallback to Hotel Model if setting is not explicitly saved)
        $this->hotel_name     = $s['hotel_name']     ?? ($hotel->name ?? 'Demo Hotel');
        $this->hotel_phone    = $s['hotel_phone']    ?? ($hotel->phone ?? '');
        $this->hotel_email    = $s['hotel_email']    ?? ($hotel->email ?? '');
        $this->hotel_website  = $s['hotel_website']  ?? ($hotel->website ?? '');
        $this->hotel_address  = $s['hotel_address']  ?? ($hotel->address ?? '');
        $this->hotel_city     = $s['hotel_city']     ?? ($hotel->city ?? '');
        $this->hotel_state    = $s['hotel_state']    ?? ($hotel->state ?? '');
        $this->hotel_country  = $s['hotel_country']  ?? ($hotel->country ?? 'India');
        $this->hotel_pincode  = $s['hotel_pincode']  ?? ($hotel->postal_code ?? '');
        $this->hotel_timezone = $s['hotel_timezone'] ?? ($hotel->timezone ?? 'Asia/Kolkata');

        // Preferences & Timings
        $this->currency      = $s['currency']      ?? ($hotel->currency ?? 'INR');
        $this->date_format   = $s['date_format']   ?? 'd M Y';
        $this->checkin_time  = $s['checkin_time']  ?? '14:00';
        $this->checkout_time = $s['checkout_time'] ?? '12:00';

        // Tax & Invoice
        $this->tax_id         = $s['tax_id']         ?? ($hotel->tax_id ?? '');
        $this->tax_rate       = $s['tax_rate']       ?? '18';
        $this->invoice_prefix = $s['invoice_prefix'] ?? 'INV-';
        $this->invoice_footer = $s['invoice_footer'] ?? 'Thank you for choosing our hotel!';

        // Policies & Booking
        $this->cancellation_policy        = $s['cancellation_policy']        ?? 'Free cancellation up to 24 hours prior to check-in date.';
        $this->booking_policy             = $s['booking_policy']             ?? 'Guests must present valid government ID at check-in.';
        $this->auto_confirm_booking       = ($s['auto_confirm_booking']       ?? '1') === '1';
        $this->advance_payment_percentage = $s['advance_payment_percentage'] ?? '0';

        // Notifications
        $this->email_notifications = ($s['email_notifications'] ?? '1') === '1';
        $this->sms_notifications   = ($s['sms_notifications']   ?? '0') === '1';

        // SMTP
        $this->smtp_enabled      = ($s['smtp_enabled']      ?? '0') === '1';
        $this->smtp_host         = $s['smtp_host']          ?? '';
        $this->smtp_port         = $s['smtp_port']          ?? '587';
        $this->smtp_username     = $s['smtp_username']      ?? '';
        $this->smtp_encryption   = $s['smtp_encryption']    ?? 'tls';
        $this->smtp_from_address = $s['smtp_from_address']  ?? '';
        $this->smtp_from_name    = $s['smtp_from_name']     ?? ($hotel->name ?? 'Hotel FrontDesk');

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
            'hotel_phone' => 'nullable|string|max:50',
        ]);

        Setting::set('hotel_name',     $this->hotel_name);
        Setting::set('hotel_address',  $this->hotel_address);
        Setting::set('hotel_phone',    $this->hotel_phone);
        Setting::set('hotel_email',    $this->hotel_email);
        Setting::set('hotel_website',  $this->hotel_website);
        Setting::set('hotel_city',     $this->hotel_city);
        Setting::set('hotel_state',    $this->hotel_state);
        Setting::set('hotel_country',  $this->hotel_country);
        Setting::set('hotel_pincode',  $this->hotel_pincode);
        Setting::set('hotel_timezone', $this->hotel_timezone);

        $user = Auth::user();
        if ($user->hotel_id) {
            $hotel = Hotel::find($user->hotel_id);
            if ($hotel) {
                $hotel->update([
                    'name'        => $this->hotel_name,
                    'phone'       => $this->hotel_phone,
                    'email'       => $this->hotel_email,
                    'website'     => $this->hotel_website,
                    'address'     => $this->hotel_address,
                    'city'        => $this->hotel_city,
                    'state'       => $this->hotel_state,
                    'country'     => $this->hotel_country,
                    'postal_code' => $this->hotel_pincode,
                    'timezone'    => $this->hotel_timezone,
                ]);
            }
        }

        $this->dispatch('toast', message: 'Hotel profile & details updated successfully.', type: 'success');
    }

    public function savePreferences(): void
    {
        Setting::set('currency',       $this->currency);
        Setting::set('date_format',    $this->date_format);
        Setting::set('checkin_time',   $this->checkin_time);
        Setting::set('checkout_time',  $this->checkout_time);
        Setting::set('hotel_timezone', $this->hotel_timezone);

        $user = Auth::user();
        if ($user->hotel_id) {
            $hotel = Hotel::find($user->hotel_id);
            if ($hotel) {
                $hotel->update([
                    'currency' => $this->currency,
                    'timezone' => $this->hotel_timezone,
                ]);
            }
        }

        $this->dispatch('toast', message: 'Hotel preferences & timings updated successfully.', type: 'success');
    }

    public function saveTaxInvoice(): void
    {
        Setting::set('tax_id',         $this->tax_id);
        Setting::set('tax_rate',       $this->tax_rate);
        Setting::set('invoice_prefix', $this->invoice_prefix);
        Setting::set('invoice_footer', $this->invoice_footer);

        $user = Auth::user();
        if ($user->hotel_id) {
            $hotel = Hotel::find($user->hotel_id);
            if ($hotel) {
                $hotel->update(['tax_id' => $this->tax_id]);
            }
        }

        $this->dispatch('toast', message: 'Tax & Invoice settings saved successfully.', type: 'success');
    }

    public function savePolicies(): void
    {
        Setting::set('cancellation_policy',        $this->cancellation_policy);
        Setting::set('booking_policy',             $this->booking_policy);
        Setting::set('auto_confirm_booking',       $this->auto_confirm_booking ? '1' : '0');
        Setting::set('advance_payment_percentage', $this->advance_payment_percentage);

        $this->dispatch('toast', message: 'Hotel booking & cancellation policies updated.', type: 'success');
    }

    public function saveNotifications(): void
    {
        Setting::set('email_notifications', $this->email_notifications ? '1' : '0');
        Setting::set('sms_notifications',   $this->sms_notifications   ? '1' : '0');

        $this->dispatch('toast', message: 'Notification preferences updated successfully.', type: 'success');
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

        $this->dispatch('toast', message: 'Hotel SMTP Email configuration saved.', type: 'success');
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
        Config::set('mail.from.address', $this->smtp_from_address ?: 'noreply@hotel.com');
        Config::set('mail.from.name', $this->smtp_from_name ?: $this->hotel_name);

        try {
            Mail::raw("This is a test email from {$this->hotel_name} to confirm custom SMTP settings.", function ($message) {
                $message->to($this->test_email)->subject("SMTP Test Email - {$this->hotel_name}");
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
