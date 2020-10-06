<?php

use Illuminate\Support\Facades\Route;


Route::post('/login', 'AuthController@login')->name('api.login');

Route::post('/register', 'AuthController@register')->name('api.register');

Route::get('/profile/{username?}', 'UserController@profile')
	->name('api.profile');
