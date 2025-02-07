<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HomePage;
use App\Livewire\InfaqPage;
use App\Livewire\TermsPage;

Route::get('/', HomePage::class)->name('home');  // Correct way to use Livewire components in routes

Route::get('/infaq', InfaqPage::class)->name('infaq');

Route::get('/terms', TermsPage::class)->name('terms');



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
