<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Events\Login;
use Laravel\Jetstream\Jetstream;


class AuthenticatedSessionController extends Controller
{
    /**
     * Handle user login request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Redirect logged in users to dashboard
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        // Validate credentials
        $request->validate([
            'ic_number' => 'required|string|digits:12',
            'password' => 'required|string|min:8',
        ], [
            'ic_number.required' => 'Nombor IC diperlukan.',
            'ic_number.string' => 'Nombor IC mesti berupa angka.',
            'ic_number.digits' => 'Nombor IC mesti 12 digit.',
            'password.required' => 'Kata laluan diperlukan.',
            'password.string' => 'Kata laluan mesti berupa teks.',
            'password.min' => 'Kata laluan mesti sekurang-kurangnya 8 aksara.',
        ]);

        // Attempt authentication - the remember me checkbox is optional
        if (Auth::attempt(['ic_number' => $request->ic_number, 'password' => $request->password])) {
            $request->session()->regenerate();

            // Dispatch login event for Jetstream
            event(new Login(Auth::guard('web'), Auth::user()));

            // Here's the important part - redirect based on user role regardless of remember status
            return $this->redirectToDashboard(Auth::user());
        }

        // If authentication fails
        return redirect()->route('home')->withErrors([
            'login' => 'Invalid credentials.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect users based on their role.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToDashboard(User $user)
    {
        // Make sure your User model has this method correctly implemented
        if ($user->Role('admin') || $user->isAdmin()) {
            return redirect()->intended(config('filament.path', '/admin'));
        }

        // Redirect to Jetstream dashboard
        return redirect()->intended(Jetstream::redirects()->home ?? '/dashboard');
    }
}
