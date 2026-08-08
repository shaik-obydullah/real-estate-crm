<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Company / General
            'company_name' => 'My Company',
            'company_website' => '',
            'company_phone' => '',
            'company_email' => '',
            'company_address' => '',
            'company_city' => '',
            'company_state' => '',
            'company_country' => '',

            // Email
            'smtp_host' => '',
            'smtp_port' => '587',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'from_address' => '',
            'from_name' => '',

            // Localization
            'locale' => 'en',
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'time_format' => 'g:i A',

            // Theme
            'primary_color' => '#3b82f6',
            'dark_mode' => '0',

            // Maintenance
            'maintenance_enabled' => '0',
            'maintenance_message' => 'We are performing scheduled maintenance. Please check back soon.',
            'maintenance_allowed_ips' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
