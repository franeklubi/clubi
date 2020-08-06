<?php


// function returning a generated string of throttle middleware
function throttleUploads() {
    $uploads_per_minute = config('consts.throttle.uploads_per_minute', 10);
    return "throttle:$uploads_per_minute,1,uploads";
}
