<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    // accessor for the banner picture
    public function getBannerPictureAttribute($value) {
        return $value ? $value : '/const_assets/default_banner_picture.png';
    }
}
