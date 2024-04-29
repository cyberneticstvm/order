<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class MobileAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Str::contains($request->userAgent(), ['iPhone', 'Android']) && !$request->user()->mobile_access) :
            Auth::logout();
            return redirect()->route('login')->with("error", "Mobile access has been restricted for this login");
        endif;
        return $next($request);
    }
}
