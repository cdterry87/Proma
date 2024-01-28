<?php

use App\Livewire\Home;
use App\Livewire\Login;
use App\Livewire\Teams;
use App\Livewire\Users;
use App\Livewire\Issues;
use App\Livewire\Clients;
use App\Livewire\Notifications;
use App\Livewire\Projects;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)
    ->name('login');

Route::middleware([
    'auth:sanctum',
    'verified',
])->group(function () {
    Route::get('/', Home::class)
        ->name('home');

    Route::get('/projects', Projects::class)
        ->name('projects');

    Route::get('/issues', Issues::class)
        ->name('issues');

    Route::get('/clients', Clients::class)
        ->name('clients');

    Route::get('/users', Users::class)
        ->name('users');

    Route::get('/teams', Teams::class)
        ->name('teams');

    Route::get('/notifications', Notifications::class)
        ->name('notifications');

    Route::get('/settings', Settings::class)
        ->name('settings');
});
