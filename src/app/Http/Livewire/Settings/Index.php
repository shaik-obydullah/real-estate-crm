<?php

namespace App\Http\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Setting;
use Carbon\Carbon;

#[Layout('layouts.app', ['title' => 'Settings'])]
class Index extends Component
{
    public string $activeTab = 'general';

    public string $companyName = '';
    public string $companyWebsite = '';
    public string $companyPhone = '';
    public string $companyEmail = '';
    public string $companyAddress = '';
    public string $companyCity = '';
    public string $companyState = '';
    public string $companyCountry = '';

    public string $smtpHost = '';
    public int $smtpPort = 587;
    public string $smtpUsername = '';
    public string $smtpPassword = '';
    public string $smtpEncryption = 'tls';
    public string $fromAddress = '';
    public string $fromName = '';

    public string $locale = 'en';
    public string $timezone = 'UTC';
    public string $dateFormat = 'Y-m-d';
    public string $timeFormat = 'g:i A';

    public string $primaryColor = '#3b82f6';
    public bool $darkMode = false;

    public bool $maintenanceEnabled = false;
    public string $maintenanceMessage = '';
    public string $maintenanceAllowedIps = '';

    public function mount()
    {
        $this->companyName = $this->getSetting('company_name', 'My Company');
        $this->companyWebsite = $this->getSetting('company_website', '');
        $this->companyPhone = $this->getSetting('company_phone', '');
        $this->companyEmail = $this->getSetting('company_email', '');
        $this->companyAddress = $this->getSetting('company_address', '');
        $this->companyCity = $this->getSetting('company_city', '');
        $this->companyState = $this->getSetting('company_state', '');
        $this->companyCountry = $this->getSetting('company_country', '');

        $this->smtpHost = $this->getSetting('smtp_host', '');
        $this->smtpPort = (int) $this->getSetting('smtp_port', '587');
        $this->smtpUsername = $this->getSetting('smtp_username', '');
        $this->smtpPassword = $this->getSetting('smtp_password', '');
        $this->smtpEncryption = $this->getSetting('smtp_encryption', 'tls');
        $this->fromAddress = $this->getSetting('from_address', '');
        $this->fromName = $this->getSetting('from_name', '');

        $this->locale = $this->getSetting('locale', 'en');
        $this->timezone = $this->getSetting('timezone', 'UTC');
        $this->dateFormat = $this->getSetting('date_format', 'Y-m-d');
        $this->timeFormat = $this->getSetting('time_format', 'g:i A');

        $this->primaryColor = $this->getSetting('primary_color', '#3b82f6');
        $this->darkMode = $this->getSetting('dark_mode', '0') === '1';

        $this->maintenanceEnabled = $this->getSetting('maintenance_enabled', '0') === '1';
        $this->maintenanceMessage = $this->getSetting('maintenance_message', 'We are performing scheduled maintenance. Please check back soon.');
        $this->maintenanceAllowedIps = $this->getSetting('maintenance_allowed_ips', '');
    }

    protected function getSetting(string $key, string $default = ''): string
    {
        return Setting::where('key', $key)->value('value') ?? $default;
    }

    protected function saveSetting(string $key, string $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public function saveGeneral()
    {
        $this->validate([
            'companyName' => 'required|string|max:255',
            'maintenanceMessage' => 'nullable|string|max:500',
            'maintenanceAllowedIps' => 'nullable|string|max:500',
        ]);

        $this->saveSetting('company_name', $this->companyName);
        $this->saveSetting('maintenance_enabled', $this->maintenanceEnabled ? '1' : '0');
        $this->saveSetting('maintenance_message', $this->maintenanceMessage);
        $this->saveSetting('maintenance_allowed_ips', $this->maintenanceAllowedIps);

        session()->flash('success', $this->maintenanceEnabled ? 'General settings saved. Maintenance mode is now ON.' : 'General settings saved.');
    }

    public function saveCompany()
    {
        $this->saveSetting('company_name', $this->companyName);
        $this->saveSetting('company_website', $this->companyWebsite);
        $this->saveSetting('company_phone', $this->companyPhone);
        $this->saveSetting('company_email', $this->companyEmail);
        $this->saveSetting('company_address', $this->companyAddress);
        $this->saveSetting('company_city', $this->companyCity);
        $this->saveSetting('company_state', $this->companyState);
        $this->saveSetting('company_country', $this->companyCountry);
        session()->flash('success', 'Company settings saved.');
    }

    public function saveEmail()
    {
        $this->saveSetting('smtp_host', $this->smtpHost);
        $this->saveSetting('smtp_port', (string) $this->smtpPort);
        $this->saveSetting('smtp_username', $this->smtpUsername);
        $this->saveSetting('smtp_password', $this->smtpPassword);
        $this->saveSetting('smtp_encryption', $this->smtpEncryption);
        $this->saveSetting('from_address', $this->fromAddress);
        $this->saveSetting('from_name', $this->fromName);
        session()->flash('success', 'Email settings saved.');
    }

    public function saveLocalization()
    {
        $this->validate([
            'timezone' => 'required|string|timezone',
            'dateFormat' => 'required|string|max:32',
            'timeFormat' => 'required|string|max:32',
        ]);

        $this->saveSetting('locale', $this->locale);
        $this->saveSetting('timezone', $this->timezone);
        $this->saveSetting('date_format', $this->dateFormat);
        $this->saveSetting('time_format', $this->timeFormat);
        session()->flash('success', 'Localization settings saved.');
    }

    public function saveTheme()
    {
        $this->saveSetting('primary_color', $this->primaryColor);
        $this->saveSetting('dark_mode', $this->darkMode ? '1' : '0');
        session()->flash('success', 'Theme settings saved.');
    }

    public function exportData(string $type)
    {
        session()->flash('info', 'Exporting ' . $type . ' data...');
    }

    public function previewDateTime(): string
    {
        return Carbon::now($this->timezone)->format($this->dateFormat . ' ' . $this->timeFormat);
    }

    public function timezoneGroups(): array
    {
        $groups = [];

        foreach (\DateTimeZone::listIdentifiers() as $identifier) {
            $parts = explode('/', $identifier, 2);
            $group = $parts[0] ?? 'Other';
            $groups[$group][] = $identifier;
        }

        ksort($groups);

        return $groups;
    }

    public function render()
    {
        return view('livewire.settings.index', [
            'timezoneGroups' => $this->timezoneGroups(),
            'clientIp' => request()->ip(),
        ]);
    }
}
