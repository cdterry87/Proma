<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::prefix('api')->group(function () {
        Route::resource('/clients', 'ClientController');
        Route::resource('/teams', 'TeamController');
        Route::resource('/projects', 'ProjectController');
    });

    // Route::get('/', 'HomeController@index')->name('home');
    Route::get('/{any}', 'HomeController@index')->where('any', '.*');
});
