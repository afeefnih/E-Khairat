<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;



class Login extends Component
{
    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }

    public function showLoginForm()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }




    public function login(Request $request)
    {
        // Validate credentials
        $request->validate([
            'ic_number' => 'required|numeric|digits:12',
            'password' => 'required|string|min:8',
        ]);

        // Attempt authentication
        if (Auth::attempt(['ic_number' => $request->ic_number, 'password' => $request->password])) {
            $request->session()->regenerate();

            // Redirect based on role
            $user = Auth::user();
            if ($user instanceof \App\Models\User) {
                return $this->redirectToDashboard($user);
            }

            return back()->withErrors([
                'ic_number' => 'Invalid credentials.',
            ]);
        }


        // If authentication fails
        return back()->withErrors([
            'ic_number' => 'Invalid credentials.',
        ]);
    }
    protected function redirectToDashboard(User $user)
    {
        // Redirect admin users to the Filament admin dashboard
        if ($user->isAdmin()) {
            return redirect()->route('filament.admin.pages.dashboard');
        }

        // Redirect regular users to the user dashboard
        return redirect()->route('dashboard');
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
