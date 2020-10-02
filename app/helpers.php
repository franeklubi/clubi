<?php

use Illuminate\Support\Facades\Auth;

// function returning a generated string of throttle middleware
function throttleUploads() {
    $uploads_per_minute = config('consts.throttle.uploads_per_minute', 10);
    return "throttle:$uploads_per_minute,1,uploads";
}


function createNotification(
    $user_id,
    $message,
    $from_id = null,
    $link = null
) {
    \App\Notification::firstOrCreate([
        'user_id' => $user_id,
        'message' => $message,
        'from_id' => $from_id,
        'link' => $link,
    ]);
}


function getAuthUser() {
    $user = auth()->user();

    if ( !$user ) {
        $user = sanctumUser();
    }

    return $user;
}


function checkAuthUser() {
    return auth()->check() || sanctumCheck();
}


function sanctumCheck() {
    return Auth::guard('sanctum')->check();
};


function sanctumUser() {
    return Auth::guard('sanctum')->user();
}
