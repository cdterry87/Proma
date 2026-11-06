<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Clients\Show as ClientsShow;
use App\Livewire\Dashboard;
use App\Livewire\Issues\Index as IssuesIndex;
use App\Livewire\Issues\Show as IssuesShow;
use App\Livewire\PrivacyPolicy;
use App\Livewire\Projects\Index as ProjectsIndex;
use App\Livewire\Projects\Show as ProjectsShow;
use App\Livewire\Settings;
use App\Livewire\Styleguide;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::livewire('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::livewire('/', Dashboard::class)->name('dashboard');
    Route::livewire('/clients', ClientsIndex::class)->name('clients.index');
    Route::livewire('/clients/{client}', ClientsShow::class)->name('clients.show');
    Route::livewire('/projects', ProjectsIndex::class)->name('projects.index');
    Route::livewire('/projects/{project}', ProjectsShow::class)->name('projects.show');
    Route::livewire('/issues', IssuesIndex::class)->name('issues.index');
    Route::livewire('/issues/{issue}', IssuesShow::class)->name('issues.show');
    Route::livewire('/settings', Settings::class)->name('settings');
    Route::post('/logout', LogoutController::class)->name('logout');
});

Route::livewire('/privacy-policy', PrivacyPolicy::class)->name('privacy');
Route::livewire('/styleguide', Styleguide::class)->name('styleguide');
