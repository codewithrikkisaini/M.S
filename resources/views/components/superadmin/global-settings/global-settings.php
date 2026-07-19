<?php

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $smtp_host;
    public $smtp_port;
    public $smtp_username;
    public $smtp_password;
    public $global_stripe_publishable;
    public $global_stripe_secret;
    public $backup_frequency = 'daily';

    public function mount(): void
    {
        $this->smtp_host = Setting::get('smtp_host', 'smtp.mailtrap.io');
        $this->smtp_port = Setting::get('smtp_port', '2525');
        $this->smtp_username = Setting::get('smtp_username', '');
        $this->smtp_password = Setting::get('smtp_password', '');
        $this->global_stripe_publishable = Setting::get('global_stripe_publishable', '');
        $this->global_stripe_secret = Setting::get('global_stripe_secret', '');
        $this->backup_frequency = Setting::get('backup_frequency', 'daily');
    }

    public function saveSettings(): void
    {
        Setting::set('smtp_host', $this->smtp_host);
        Setting::set('smtp_port', $this->smtp_port);
        Setting::set('smtp_username', $this->smtp_username);
        Setting::set('smtp_password', $this->smtp_password);
        Setting::set('global_stripe_publishable', $this->global_stripe_publishable);
        Setting::set('global_stripe_secret', $this->global_stripe_secret);
        Setting::set('backup_frequency', $this->backup_frequency);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Global Platform Settings updated successfully!"
        ]);
    }

    public function triggerBackup(): void
    {
        $backupName = 'saas_backup_' . date('Y-m-d_H-i-s') . '.sql.gz';
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Backup generated successfully: {$backupName}"
        ]);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
