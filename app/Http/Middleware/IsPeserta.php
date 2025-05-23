<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsPeserta
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            logger('Role user: ' . Auth::user()->role);
            if (Auth::user()->role === 'peserta') {
                return $next($request);
            }
        }

        logger('Redirecting to login from IsPeserta middleware');
        return redirect()->route('login');
    }
}
