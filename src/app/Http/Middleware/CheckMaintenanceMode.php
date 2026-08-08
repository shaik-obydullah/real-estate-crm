<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\AppSettings;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! AppSettings::maintenanceEnabled()) {
            return $next($request);
        }

        if (in_array($request->ip(), AppSettings::maintenanceAllowedIps(), true)) {
            return $next($request);
        }

        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        if ($request->is('login', 'logout')) {
            return $next($request);
        }

        return response()->view('errors.maintenance', [
            'message' => AppSettings::maintenanceMessage(),
        ], 503);
    }
}
