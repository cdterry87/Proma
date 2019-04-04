<?php

Route::post('login', 'UserController@login');
Route::post('register', 'UserController@register');

Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('/clients', 'ClientController');
    Route::resource('/teams', 'TeamController');
    Route::resource('/projects', 'ProjectController');

    Route::get('/tasks/{project}', 'ProjectTaskController@index');
    Route::post('/tasks/{project}/complete/{task}', 'ProjectTaskController@complete');
    Route::resource('/tasks', 'ProjectTaskController');
});
