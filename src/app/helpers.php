<?php

use App\Support\AppSettings;

if (! function_exists('app_setting')) {
    function app_setting(string $key, mixed $default = null): mixed
    {
        return AppSettings::get($key, $default);
    }
}

if (! function_exists('format_date')) {
    function format_date(mixed $date): string
    {
        return AppSettings::formatDate($date);
    }
}

if (! function_exists('format_time')) {
    function format_time(mixed $date): string
    {
        return AppSettings::formatTime($date);
    }
}

if (! function_exists('format_datetime')) {
    function format_datetime(mixed $date): string
    {
        return AppSettings::formatDateTime($date);
    }
}
