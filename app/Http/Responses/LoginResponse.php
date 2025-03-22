<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
         // Clear the intended URL for normal users
         if (!Auth::user()->isAdmin()) {
            session()->forget('url.intended');
        }

        // Redirect admin users to the Filament admin dashboard
        if (Auth::user()->isAdmin()) {
            return redirect()->route('filament.admin.pages.dashboard');
        }

        // Redirect regular users to the user dashboard
        return redirect()->route('dashboard');
    }
}
