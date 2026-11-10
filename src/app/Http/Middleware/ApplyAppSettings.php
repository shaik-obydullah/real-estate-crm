<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\AppSettings;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyAppSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        config(['app.timezone' => AppSettings::timezone()]);
        config(['app.name' => AppSettings::appName()]);
        date_default_timezone_set(AppSettings::timezone());

        return $next($request);
    }
}
