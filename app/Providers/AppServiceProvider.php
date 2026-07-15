<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share hotel settings with ALL views (layouts, sidebar, etc.)
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('settings')) {
                    $hotelName    = Setting::get('hotel_name',    'Merahkie PMS Lite');
                    $hotelPhone   = Setting::get('hotel_phone',   '');
                    $hotelEmail   = Setting::get('hotel_email',   '');
                    $hotelAddress = Setting::get('hotel_address', '');
                    $hotelWebsite = Setting::get('hotel_website', '');
                    $currency     = Setting::get('currency',      'USD');
                } else {
                    $hotelName    = 'Merahkie PMS Lite';
                    $hotelPhone   = $hotelEmail = $hotelAddress = $hotelWebsite = $currency = '';
                }
            } catch (\Exception $e) {
                $hotelName    = 'Merahkie PMS Lite';
                $hotelPhone   = $hotelEmail = $hotelAddress = $hotelWebsite = $currency = '';
            }

            $view->with([
                'hotelName'    => $hotelName,
                'hotelPhone'   => $hotelPhone,
                'hotelEmail'   => $hotelEmail,
                'hotelAddress' => $hotelAddress,
                'hotelWebsite' => $hotelWebsite,
                'currency'     => $currency,
            ]);
        });

        $this->applySmtpSettings();
    }

    /**
     * Override the default mail config with admin-configured SMTP settings,
     * when enabled, so Mail::* calls use the DB-stored credentials instead of .env.
     */
    protected function applySmtpSettings(): void
    {
        try {
            if (!Schema::hasTable('settings') || Setting::get('smtp_enabled', '0') !== '1') {
                return;
            }

            $host = Setting::get('smtp_host');
            if (!$host) {
                return;
            }

            $password = Setting::get('smtp_password');
            try {
                $password = $password ? Crypt::decryptString($password) : null;
            } catch (\Exception $e) {
                $password = null;
            }

            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', $host);
            Config::set('mail.mailers.smtp.port', (int) Setting::get('smtp_port', 587));
            Config::set('mail.mailers.smtp.username', Setting::get('smtp_username') ?: null);
            Config::set('mail.mailers.smtp.password', $password);
            Config::set('mail.mailers.smtp.scheme', Setting::get('smtp_encryption') === 'ssl' ? 'smtps' : null);
            Config::set('mail.from.address', Setting::get('smtp_from_address') ?: Setting::get('hotel_email', 'hello@example.com'));
            Config::set('mail.from.name', Setting::get('smtp_from_name') ?: Setting::get('hotel_name', 'Merahkie PMS Lite'));
        } catch (\Exception $e) {
            // settings table unavailable (e.g. during initial migration) — fall back to .env mail config
        }
    }
}
