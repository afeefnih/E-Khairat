<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Dependent;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Set up morphMap to help with morph relations
        Relation::morphMap([
            'App\\Models\\User' => User::class,
            'App\\Models\\Dependent' => Dependent::class,
            // These are for malformed entries
            'AppModelsUser' => User::class,
            'AppModelsDependent' => Dependent::class,
        ]);
    }
}
