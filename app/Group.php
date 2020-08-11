<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Cache;

class Group extends Model
{
    protected $appends = ['user_count', 'post_count'];

    protected $fillable = [
        'name', 'banner_picture', 'private'
    ];

    public static function search(string $search) {
        $columns = ['name', 'id_string'];

        if (empty(trim($search))) {
            return null;
        }

        $term = '%'.implode("%", str_split(str_replace(" ", "", $search))).'%';


        $group_ids = [];

        if ( auth()->check() ) {
            $group_ids = auth()->user()->memberOfGroups
                ->where('private', true)
                ->pluck('id');
        }

        return Group::where('private', false)
            ->where($columns[0], 'like', $term)
            ->orWhere('private', false)
            ->where($columns[1], 'like', $term)
            ->orWhere('private', true)
            ->whereIn('id', $group_ids)
            ->get();
    }

    // accessor for the banner picture
    public function getBannerPictureAttribute($value) {
        return $value ? $value : config('consts.default_banner_picture_path');
    }

    // accessor for the user count
    public function getUserCountAttribute() {
        $user_count = Cache::remember(
            'user_count.group_'.$this->id,
            now()->addSeconds(30),
            function () {
                return $this->members()->count();
            }
        );
        return $user_count;
    }

    // accessor for the post count
    public function getPostCountAttribute() {
        $post_count = Cache::remember(
            'post_count.group_'.$this->id,
            now()->addSeconds(30),
            function () {
                return $this->posts()->count();
            }
        );
        return $post_count;
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

    public static function rules() {
        return [
            'name' => [
                'required',
                'max:30',
            ],
            'banner_picture' => [
                'image',
                'dimensions:'
                    .'min_width='.config('consts.banner_picture.min_width')
                    .',min_height='.config('consts.banner_picture.min_height')
                    .',max_width='.config('consts.banner_picture.max_width')
                    .',max_height='.config('consts.banner_picture.max_height')
            ],
            'private' => 'boolean',
            'remove_banner_picture' => 'boolean',
        ];
    }

    public function link() {
        return route('groups.show', [
            'group' => $this->id_string,
        ]);
    }
}
