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
        // Primary Resources
        Route::resource('/clients', 'ClientController');
        Route::resource('/teams', 'TeamController');
        Route::resource('/projects', 'ProjectController');

        // Projects - Tasks
        Route::get('/tasks/{project}', 'ProjectTaskController@index');
        Route::post('/tasks/{project}/complete/{task}', 'ProjectTaskController@complete');
        Route::post('/tasks/{project}/incomplete/{task}', 'ProjectTaskController@incomplete');
        Route::resource('/tasks', 'ProjectTaskController');

        // Clients - Contacts
        Route::get('/contacts/{client}', 'ClientContactController@index');
        Route::resource('/contacts', 'ClientContactController');

        // Teams - Users
        Route::get('/teams/{team}/users', 'TeamController@users');

        // Teams - Members
        Route::get('/members/{team}', 'TeamMemberController@index');
        Route::resource('/members', 'TeamMemberController');

        // User
        Route::get('/user/', 'UserController@index');
        Route::put('/user/', 'UserController@update');

        // Users - Associated
        Route::get('/users/teams', 'UserController@teams');
        Route::get('/users/clients', 'UserController@clients');
        Route::get('/users/projects', 'UserController@projects');

        // Logout
        Route::get('/logout', function () {
            Auth::logout();
            return Redirect::to('login');
        });
    });

    Route::get('/{any}', 'HomeController@index')->where('any', '.*');
});
