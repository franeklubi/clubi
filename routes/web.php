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


Route::get('/', 'DashboardController@feed')->name('dashboard.feed');

Route::get('/popular', 'DashboardController@popular')
    ->name('dashboard.popular');


Route::middleware('auth')->group(function () {
    // dashboard routes
    Route::get('/dashboard/posts', 'DashboardController@posts')
        ->name('dashboard.posts');

    Route::get('/dashboard/invitations', 'DashboardController@invitations')
        ->name('dashboard.invitations');

    Route::get('/yourGroups', 'DashboardController@yourGroups')
        ->name('dashboard.yourGroups');

    Route::get('/joinedGroups', 'DashboardController@joinedGroups')
        ->name('dashboard.joinedGroups');


    // settings routes
    Route::get('/settings', 'SettingsController@edit')->name('settings.edit');

    Route::patch('/settings', 'SettingsController@update')
        ->name('settings.update');

    // group routes
    Route::get('/groups/create', 'GroupController@create')
        ->name('groups.create');

    Route::post('/groups', 'GroupController@store')->name('groups.store');

    Route::delete('/groups/{group}', 'GroupController@destroy')
        ->middleware('can:view,group')
        ->name('groups.destroy');

    Route::patch('/groups/{group}', 'GroupController@update')
        ->middleware('can:view,group')
        ->name('groups.destroy');


    Route::post('/groups/{group}/join', 'GroupMembershipController@store')
        ->middleware('can:view,group')
        ->name('groups.membership');


    // post routes
    Route::post('/groups/{group}/posts', 'PostController@store')
        ->middleware('can:view,group')
        ->name('posts.store');

    Route::delete('/groups/{group}/posts/{post}', 'PostController@destroy')
        ->middleware('can:view,group')
        ->name('posts.destroy');

    Route::get('/groups/{group}/posts/{post}/likes', 'LikeController@indexPost')
        ->middleware('can:view,group')
        ->name('posts.likes.index');

    Route::post(
        '/groups/{group}/posts/{post}/likes',
        'LikeController@togglePost'
    )->middleware('can:view,group')->name('posts.likes.toggle');


    // comment routes
    Route::post(
        '/groups/{group}/posts/{post}/comments',
        'CommentController@store'
    )->middleware(['can:view,group'])
        ->name('comments.store');

    Route::delete(
        '/groups/{group}/posts/{post}/comments/{comment}',
        'CommentController@destroy'
    )->middleware('can:view,group')->name('comments.destroy');

    Route::get(
        '/groups/{group}/posts/{post}/comments/{comment}/likes',
        'LikeController@indexComment'
    )->middleware('can:view,group')->name('comments.likes.index');

    Route::post(
        '/groups/{group}/posts/{post}/comments/{comment}/likes',
        'LikeController@toggleComment'
    )->middleware('can:view,group')->name('comments.likes.toggle');

    Route::post('/groups/search/', 'GroupController@search')
        ->name('groups.search');


    Route::get(
        '/groups/{group}/invitations',
        'InvitationController@groupIndex'
    )->middleware('can:view,group')->name('invitations.groupIndex');

    Route::post('/groups/{group}/invitations', 'InvitationController@store')
        ->middleware('can:view,group')
        ->name('invitations.store');

    Route::delete(
        '/groups/{group}/invitations/{invitation}',
        'InvitationController@destroy'
    )->middleware('can:view,group')->name('invitations.destroy');

    Route::patch(
        '/groups/{group}/invitations/{invitation}',
        'InvitationController@adminConfirm'
    )->middleware('can:view,group')->name('invitations.adminConfirm');
});


Route::get('/groups/search/{search?}', 'GroupController@search')
    ->name('groups.search');


Route::get('/groups/{group}', 'GroupController@show')
    ->name('groups.show');


Route::get(
    '/groups/{group}/posts',
    'PostController@index'
)->middleware('can:view,group')->name('posts.index');


Route::get('/groups/{group}/posts/{post}', 'PostController@show')
    ->middleware('can:view,group')
    ->name('posts.show');


Route::get(
    '/groups/{group}/posts/{post}/comments',
    'CommentController@index'
)->middleware('can:view,group')->name('comments.index');
