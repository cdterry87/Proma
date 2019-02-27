<?php

Route::post('login', 'UserController@login');
Route::post('register', 'UserController@register');

Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('/clients', 'ClientController');
    Route::resource('/teams', 'TeamController');
    Route::resource('/projects', 'ProjectController');
});
