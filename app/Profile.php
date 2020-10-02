<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    // accessor for the profile picture
    public function getProfilePictureAttribute($value) {
        return $value ? $value : config('consts.default_profile_picture_path');
    }
}
