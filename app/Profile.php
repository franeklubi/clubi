<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $guarded = [];

    public function user() {
        return $this->belongsTo('App\User');
    }

    // accessor for the profile picture
    public function getProfilePictureAttribute($value) {
        return $value ? $value : '/const_assets/default_profile_picture.png';
    }
}
