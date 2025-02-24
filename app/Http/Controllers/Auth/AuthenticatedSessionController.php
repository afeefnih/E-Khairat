<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Laravel\Jetstream\InteractsWithBanner;


class AuthenticatedSessionController extends Controller
{public function login(Request $request)
    {
        use InteractsWithBanner;
        // If the user is already logged in, skip the login attempt and redirect to the dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Validate the ic_number and password
        $request->validate([
            'ic_number' => 'required|string|digits:12',  // Assuming ic_number is 12 digits
            'password' => 'required|string|min:8', // Password validation
        ],
        [
            'ic_number.required' => 'Nombor IC diperlukan.',
            'ic_number.string' => 'Nombor IC mesti berupa angka.',
            'ic_number.digits' => 'Nombor IC mesti 12 digit.',
            'password.required' => 'Kata laluan diperlukan.',
            'password.string' => 'Kata laluan mesti berupa teks.',
            'password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
        ]);

        // Authenticate the user using the provided credentials
        if (Auth::attempt($request->only('ic_number', 'password'))) {
            $this->banner('You are successfully logged in!');
            return redirect()->route('dashboard');
        } else {
            // If authentication fails
            return redirect()->route('login')->withErrors('Invalid credentials.');
        }
    }


    // Log out method
    public function destroy(Request $request)
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
