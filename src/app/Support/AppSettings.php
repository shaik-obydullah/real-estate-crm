<?php

namespace App\Support;

use App\Models\Setting;
use Carbon\Carbon;

class AppSettings
{
    protected static ?array $cache = null;

    public static function flush(): void
    {
        static::$cache = null;
    }

    public static function all(): array
    {
        if (static::$cache !== null) {
            return static::$cache;
        }

        try {
            return static::$cache = Setting::pluck('value', 'key')->all();
        } catch (\Throwable) {
            return static::$cache = [];
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all()[$key] ?? $default;
    }

    public static function appName(): string
    {
        return (string) static::get('company_name', 'CRM');
    }

    public static function timezone(): string
    {
        return (string) static::get('timezone', 'UTC');
    }

    public static function dateFormat(): string
    {
        return (string) static::get('date_format', 'Y-m-d');
    }

    public static function timeFormat(): string
    {
        return (string) static::get('time_format', 'g:i A');
    }

    public static function maintenanceEnabled(): bool
    {
        return static::get('maintenance_enabled', '0') === '1';
    }

    public static function maintenanceMessage(): string
    {
        return (string) static::get('maintenance_message', 'We are performing scheduled maintenance. Please check back soon.');
    }

    public static function maintenanceAllowedIps(): array
    {
        $raw = (string) static::get('maintenance_allowed_ips', '');

        return array_values(array_filter(array_map('trim', explode(',', $raw)), fn ($ip) => $ip !== ''));
    }

    public static function formatDate(mixed $date): string
    {
        return static::toCarbon($date)?->format(static::dateFormat()) ?? '';
    }

    public static function formatTime(mixed $date): string
    {
        return static::toCarbon($date)?->format(static::timeFormat()) ?? '';
    }

    public static function formatDateTime(mixed $date): string
    {
        return static::toCarbon($date)?->format(static::dateFormat() . ' ' . static::timeFormat()) ?? '';
    }

    protected static function toCarbon(mixed $date): ?Carbon
    {
        if ($date === null || $date === '') {
            return null;
        }

        return $date instanceof Carbon ? $date : Carbon::parse($date);
    }
}
