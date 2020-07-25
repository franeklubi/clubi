<?php

// key-value pairs defining all the constants
return [
    'name' => 'club',

    'profile_picture' => [
        'min_width' => 250,
        'min_height' => 250,
        'max_width' => 5000,
        'max_height' => 5000,
        'fit_width' => 1000,
        'fit_height' => 1000,
    ],

    'banner_picture' => [
        'min_width' => 250,
        'min_height' => 250,
        'max_width' => 5000,
        'max_height' => 5000,
        'fit_width' => 1920,
        'fit_height' => 1080,
    ],

    'post_picture' => [
        'min_width' => 250,
        'min_height' => 250,
        'max_width' => 5000,
        'max_height' => 5000,
        'fit_width' => 1920,
        'fit_height' => 1080,
    ],

    'comment_picture' => [
        'min_width' => 250,
        'min_height' => 250,
        'max_width' => 5000,
        'max_height' => 5000,
        'fit_width' => 1920,
        'fit_height' => 1080,
    ],

    'comments_per_page' => env('MIX_COMMENTS_PER_PAGE'),
    'posts_per_page' => env('MIX_POSTS_PER_PAGE'),
    'default_profile_picture_path' => env('MIX_DEFAULT_PROFILE_PICTURE_PATH'),
    'default_banner_picture_path' => env('MIX_DEFAULT_BANNER_PICTURE_PATH'),

    'max_post_length' => 5000,
    'max_comment_length' => 1000,
];
