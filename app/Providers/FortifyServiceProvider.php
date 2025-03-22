<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse;



class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

       // Custom validation rules for login
       Fortify::authenticateUsing(function (Request $request) {
        $request->validate([
            'ic_number' => ['required', 'numeric', 'digits:12'], // IC Number validation
            'password' => ['required', 'string', 'min:8'], // Password validation
        ]);

        $user = User::where('ic_number', $request->ic_number)->first();

        if ($user) {
            \Log::info('User found: ' . $user->id);
            if (Hash::check($request->password, $user->password)) {
                \Log::info('Password check passed for user: ' . $user->id);
                return $user;
            } else {
                \Log::warning('Password check failed for user: ' . $user->id);
            }
        } else {
            \Log::warning('User not found with IC number: ' . $request->ic_number);
        }

        return null;
    });
    RateLimiter::for('login', function (Request $request) {
        return Limit::perMinute(5)->by($request->ip());
    });


        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
