<?php

declare(strict_types=1);

use App\Livewire\Styleguide;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::livewire('/styleguide', Styleguide::class)->name('styleguide');
