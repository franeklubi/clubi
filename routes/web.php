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


// routes that need authentication
Route::middleware('auth')->group(function () {
    // notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', 'NotificationController@index')
            ->name('notifications.index');

        Route::delete('/', 'NotificationController@destroyAll')
            ->name('notifications.destroyAll');

        Route::get('/count', 'NotificationController@unseenCount')
            ->name('notifications.count');

        Route::post('/count', 'NotificationController@markReadNotifications')
            ->name('notifications.markRead');

        Route::delete('/{notification}', 'NotificationController@destroy')
            ->name('notification.destroy');
    });


    // dashboard routes
    Route::get('/yourGroups', 'DashboardController@yourGroups')
        ->name('dashboard.yourGroups');

    Route::get('/joinedGroups', 'DashboardController@joinedGroups')
        ->name('dashboard.joinedGroups');

    Route::prefix('dashboard')->group(function () {
        Route::get('/posts', 'DashboardController@posts')
            ->name('dashboard.posts');

        Route::get('/invitations', 'DashboardController@invitations')
            ->name('dashboard.invitations');
    });


    // settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/', 'SettingsController@edit')->name('settings.edit');

        Route::patch('/', 'SettingsController@update')
            ->middleware(throttleUploads())
            ->name('settings.update');
    });


    // group routes
    Route::prefix('groups')->group(function () {
        // group creation routes
        Route::get('/create', 'GroupController@create')
            ->name('groups.create');

        Route::post('/', 'GroupController@store')
            ->middleware(throttleUploads())
            ->name('groups.store');

        // group-specific modification routes
        Route::group([
            'prefix' => '{group}',
            'middleware' => 'can:view,group',
        ], function () {
            Route::delete('/', 'GroupController@destroy')
                ->name('groups.destroy');

            Route::patch('/', 'GroupController@update')
                ->middleware(throttleUploads())
                ->name('groups.update');

            Route::post('/membership', 'GroupMembershipController@toggle')
                ->name('groups.membership');
        });
    });


    // post routes
    Route::group([
        'prefix' => 'groups/{group}/posts',
        'middleware' => 'can:view,group',
    ], function () {
        Route::post('/', 'PostController@store')
            ->middleware(throttleUploads())
            ->name('posts.store');

        // post-specific routes
        Route::prefix('{post}')->group(function () {
            Route::delete('/', 'PostController@destroy')->name('posts.destroy');

            Route::get('/likes', 'LikeController@indexPost')
                ->name('posts.likes.index');

            Route::post('/likes', 'LikeController@togglePost')
                ->name('posts.likes.toggle');
        });
    });


    // comment routes
    Route::group([
        'prefix' => 'groups/{group}/posts/{post}/comments',
        'middleware' => 'can:view,group',
    ], function () {
        Route::post('/', 'CommentController@store')
            ->middleware(throttleUploads())
            ->name('comments.store');

        // comment-specific routes
        Route::prefix('{comment}')->group(function () {
            Route::delete('/', 'CommentController@destroy')
                ->name('comments.destroy');

            Route::get('/likes', 'LikeController@indexComment')
                ->name('comments.likes.index');

            Route::post('/likes', 'LikeController@toggleComment')
                ->name('comments.likes.toggle');
        });
    });


    // invitation routes
    Route::group([
        'prefix' => 'groups/{group}/invitations',
        'middleware' => 'can:view,group',
    ], function () {
        Route::get('/', 'InvitationController@groupIndex')
            ->name('invitations.groupIndex');

        Route::post('/', 'InvitationController@store')
            ->name('invitations.store');

        // invitation-specific routes
        Route::prefix('{invitation}')->group(function () {
            Route::delete('/', 'InvitationController@destroy')
                ->name('invitations.destroy');

            Route::patch('/', 'InvitationController@adminConfirm')
                ->name('invitations.adminConfirm');
        });
    });
});


// public routes
Route::get('/', 'DashboardController@feed')->name('dashboard.feed');

Route::get('/popular', 'DashboardController@popular')
    ->name('dashboard.popular');

Route::prefix('groups')->group(function () {
    Route::get('/', 'GroupController@redirectGet')->name('groups.redirect');

    Route::any('/search/', 'GroupController@search')
        ->name('groups.search');

    // indexing group's contents
    Route::group([
        'prefix' => '{group}',
        'middleware' => 'can:view,group',
    ], function () {
        Route::get('/', 'GroupController@show')
            ->name('groups.show');

        Route::get('/posts', 'PostController@index')
            ->name('posts.index');

        Route::get('/posts/{post}', 'PostController@show')
            ->name('posts.show');

        Route::get('/posts/{post}/comments', 'CommentController@index')
            ->name('comments.index');
    });
});
