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

// Auth routes for login/registration
Auth::routes();

// Logout route
Route::get('/api/logout', function () {
    Auth::logout();
    return Redirect::to('login');
});

// User authenticated routes
Route::group(['middleware' => 'auth'], function () {
    // Application api routes
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

        // Projects - Issues
        Route::get('/projects/{project}/issues', 'ProjectController@issues');

        // Clients - Contacts
        Route::get('/contacts/{client}', 'ClientContactController@index');
        Route::resource('/contacts', 'ClientContactController');

        // Clients - Projects
        Route::get('/clients/{client}/projects', 'ClientController@projects');

        // Issues
        Route::post('/issues/{issue}/resolve', 'IssueController@resolve');
        Route::post('/issues/{issue}/unresolve', 'IssueController@unresolve');

        // Issues - Notes
        Route::get('/notes/{issue}', 'IssueNoteController@index');
        Route::resource('/notes', 'IssueNoteController');

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
    });

    // Catch-all route
    Route::get('/{any}', 'HomeController@index')->where('any', '.*');
});
