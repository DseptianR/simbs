<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthNasabah
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('nasabah')->check()) {
            return redirect()->route('nasabah.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}
