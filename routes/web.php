<?php

use Illuminate\Support\Facades\Route;

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


Route::get('/', 'HomeController@index')->name('home');


Route::middleware('auth')->group(function () {
    // settings routes
    Route::get('/settings', 'SettingsController@edit')->name('settings.edit');

    Route::patch('/settings', 'SettingsController@update')
        ->name('settings.update');

    // group routes
    Route::get('/groups/create', 'GroupController@create')
        ->name('groups.create');

    Route::post('/groups', 'GroupController@store')->name('groups.store');
});


Route::get('/groups/{group}', 'GroupController@show')
    ->name('groups.show');

Route::get('/groups/{group}/posts/{post}', 'PostController@show')
    ->middleware('can:view,group')
    ->name('posts.show');
