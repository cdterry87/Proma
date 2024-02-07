<?php

use App\Livewire\Home;
use App\Livewire\Login;
use App\Livewire\Notifications;
use App\Livewire\PrivacyPolicy;
use App\Livewire\Settings;
use App\Livewire\Issues\Index as Issues;
use App\Livewire\Clients\Index as Clients;
use App\Livewire\Projects\Index as Projects;
use App\Livewire\Users\Index as Users;
use App\Livewire\Users\View as UsersView;
use App\Livewire\Teams\Index as Teams;
use App\Livewire\Teams\View as TeamsView;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)
    ->name('login');

Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->flash('success', 'You have been successfully logged out.');
    return redirect()->route('login');
})->name('logout');

Route::get('/privacy-policy', PrivacyPolicy::class)
    ->name('privacy-policy');

Route::middleware([
    'auth:sanctum',
    'verified',
])->group(function () {
    // Administrator Routes
    Route::middleware('administrator')->group(function () {
        Route::get('/users', Users::class)
            ->name('users');

        Route::get('/users/view/{user}', UsersView::class)
            ->name('users.view');

        Route::get('/teams', Teams::class)
            ->name('teams');

        Route::get('/teams/view/{team}', TeamsView::class)
            ->name('teams.view');
    });

    // Standard Routes
    Route::get('/', Home::class)
        ->name('home');

    Route::get('/projects', Projects::class)
        ->name('projects');

    Route::get('/issues', Issues::class)
        ->name('issues');

    Route::get('/clients', Clients::class)
        ->name('clients');

    Route::get('/notifications', Notifications::class)
        ->name('notifications');

    Route::get('/settings', Settings::class)
        ->name('settings');
});
