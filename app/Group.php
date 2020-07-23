<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name', 'banner_picture', 'private'
    ];

    // accessor for the banner picture
    public function getBannerPictureAttribute($value) {
        return $value ? $value : '/const_assets/default_banner_picture.png';
    }

    public function getRouteKeyName() {
        return 'id_string';
    }

    public function owner() {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function members() {
        return $this->belongsToMany('App\User');
    }

    public function posts() {
        return $this->hasMany('App\Post')
            ->with(['user.profile', 'group'])
            ->orderBy('created_at', 'DESC');
    }
}
