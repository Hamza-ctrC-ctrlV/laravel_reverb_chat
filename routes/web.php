<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Livewire\Chat;

// 1. Home logic: If logged in, go to chat. If not, show welcome.
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('chat');
    }
    return view('welcome');
})->name('home');

// 2. Main App Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // THE CHAT (Your new "Dashboard")
    Route::get("chat", Chat::class)->name("chat");

    // SETTINGS / PROFILE
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// 3. REMOVED: Dashboard route has been deleted.