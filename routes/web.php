<?php

use App\Livewire\Home;
use App\Livewire\Login;
use App\Livewire\Notifications;
use App\Livewire\PrivacyPolicy;
use App\Livewire\Settings;
use App\Livewire\Issues\Issues;
use App\Livewire\Issues\IssuesView;
use App\Livewire\Clients\Clients;
use App\Livewire\Clients\ClientsView;
use App\Livewire\Projects\Projects;
use App\Livewire\Projects\ProjectsView;
use App\Livewire\Users\Users;
use App\Livewire\Users\UsersView;
use App\Livewire\Teams\Teams;
use App\Livewire\Teams\TeamsView;
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

        Route::get('/users/{user}', UsersView::class)
            ->name('users.view');

        Route::get('/teams', Teams::class)
            ->name('teams');

        Route::get('/teams/{team}', TeamsView::class)
            ->name('teams.view');
    });

    // Standard Routes
    Route::get('/', Home::class)
        ->name('home');

    Route::get('/projects', Projects::class)
        ->name('projects');

    Route::get('/projects/{project}', ProjectsView::class)
        ->name('projects.view');

    Route::get('/issues', Issues::class)
        ->name('issues');

    Route::get('/issues/{issue}', IssuesView::class)
        ->name('issues.view');

    Route::get('/clients', Clients::class)
        ->name('clients');

    Route::get('/clients/{client}', ClientsView::class)
        ->name('clients.view');

    Route::get('/notifications', Notifications::class)
        ->name('notifications');

    Route::get('/settings', Settings::class)
        ->name('settings');
});
