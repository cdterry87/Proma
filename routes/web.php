<?php

use App\Livewire\Issues;
use App\Livewire\Clients;
use App\Livewire\Projects;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect(config('fortify.home'));
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', Dashboard::class)
        ->name('dashboard');

    Route::get('/projects', Projects::class)
        ->name('projects');

    Route::get('/issues', Issues::class)
        ->name('issues');

    Route::get('/clients', Clients::class)
        ->name('clients');
});
