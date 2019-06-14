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
        Route::resource('/projects', 'ProjectController');
        Route::resource('/issues', 'IssueController');

        // Projects
        Route::post('/projects/{project}/complete', 'ProjectController@complete');
        Route::post('/projects/{project}/incomplete', 'ProjectController@incomplete');

        // Projects - Tasks
        Route::get('/tasks/{project}', 'ProjectTaskController@index');
        Route::post('/tasks/{project}/complete/{task}', 'ProjectTaskController@complete');
        Route::post('/tasks/{project}/incomplete/{task}', 'ProjectTaskController@incomplete');
        Route::resource('/tasks', 'ProjectTaskController');

        // Clients - Contacts
        Route::get('/contacts/{client}', 'ClientContactController@index');
        Route::resource('/contacts', 'ClientContactController');

        // Issues - Activities
        Route::get('/activities/{activity}', 'IssueActivityController@index');
        Route::resource('/activities', 'IssueActivityController');

        // User
        Route::get('/user/', 'UserController@index');
        Route::put('/user/', 'UserController@update');

        // Users - Associated
        Route::get('/users/clients', 'UserController@clients');
        Route::get('/users/projects', 'UserController@projects');
        Route::get('/users/issues', 'UserController@issues');

        // Search
        Route::post('/search', 'HomeController@search')->name('search');

        // Notifications
        Route::get('/notifications', 'HomeController@notifications')->name('notifications');

        // Logout
        Route::get('/logout', function () {
            Auth::logout();
            return Redirect::to('login');
        });
    });

    Route::get('/{any}', 'HomeController@index')->where('any', '.*');
});
