<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name', 'banner_picture', 'private'
    ];

    public static function search(string $search) {
        $columns = ['name', 'id_string'];

        if (empty(trim($search))) {
            return null;
        }

        $term = '%'.implode("%", str_split(str_replace(" ", "", $search))).'%';

        return static::select($columns)
            ->where($columns[0], 'like', $term)
            ->orWhere($columns[1], 'like', $term)
            ->get();
    }

    // accessor for the banner picture
    public function getBannerPictureAttribute($value) {
        return $value ? $value : config('consts.default_banner_picture_path');
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
        return $this->hasMany('App\Post');
    }

    public function comments() {
        return $this->hasManyThrough('App\Comment', 'App\Post');
    }

    public function invitations() {
        return $this->hasMany('App\Invitation');
    }
}
