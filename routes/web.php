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

        Route::get('/tasks/{project}', 'ProjectTaskController@index');
        Route::post('/tasks/{project}/complete/{task}', 'ProjectTaskController@complete');
        Route::post('/tasks/{project}/incomplete/{task}', 'ProjectTaskController@incomplete');
        Route::resource('/tasks', 'ProjectTaskController');

        Route::get('/contacts/{client}', 'ClientContactController@index');
        Route::resource('/contacts', 'ClientContactController');

        Route::get('/members/{team}', 'TeamMemberController@index');
        Route::resource('/members', 'TeamMemberController');
    });

    Route::get('/{any}', 'HomeController@index')->where('any', '.*');
});
