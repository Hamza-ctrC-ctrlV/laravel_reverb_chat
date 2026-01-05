<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Livewire\Chat;

// Only guests see the welcome page. 
// If logged in, Laravel's 'guest' middleware automatically redirects to 'home' (which we will set to /chat)
Route::view('/', 'welcome')->middleware('guest')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // The main app
    Route::get("chat", Chat::class)->name("chat");
    
    // Settings Group
    Route::prefix('settings')->group(function () {
        Route::redirect('/', 'settings/profile');
        Volt::route('profile', 'settings.profile')->name('profile.edit');
        Volt::route('password', 'settings.password')->name('user-password.edit');
        Volt::route('appearance', 'settings.appearance')->name('appearance.edit');
        
        if (Features::canManageTwoFactorAuthentication()) {
            Volt::route('two-factor', 'settings.two-factor')
                ->middleware(Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword') ? ['password.confirm'] : [])
                ->name('two-factor.show');
        }
    });
});