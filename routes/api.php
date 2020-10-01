<?php

use Illuminate\Support\Facades\Route;


Route::post('/login', 'AuthController@login')->name('api.login');

Route::post('/register', 'AuthController@register')->name('api.register');

Route::prefix('profile')->group(function () {
    Route::get('/{user?}', 'UserController@profile')->name('api.profile');

    Route::post('/', '\App\Http\Controllers\SettingsController@update')
        ->middleware(['auth:sanctum', throttleUploads()])
        ->name('api.profile.update');
});
