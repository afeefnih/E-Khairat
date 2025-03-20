<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in and is an admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        // If not admin, redirect to home or show error
        return redirect()->route('home')->with('error', 'You do not have permission to access the admin panel.');
    }
}
