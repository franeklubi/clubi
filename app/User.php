<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email',
        'email_verified_at',
        'created_at',
        'updated_at',
        'locale',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'id' => 'integer',
        'is_admin' => 'boolean',
    ];

    protected static function boot() {
        parent::boot();

        static::created(function ($user) {
            $user->profile()->create();
        });
    }

    public static function search(string $search) {
        if (empty(trim($search))) {
            return null;
        }

        $term = '%'.implode("%", str_split(str_replace(" ", "", $search))).'%';

        return static::select('username')
            ->where('username', 'like', $term)
            ->get();
    }

    public function profile() {
        return $this->hasOne('App\Profile');
    }

    public function ownsGroups() {
        return $this->hasMany('App\Group', 'owner_id');
    }

    public function memberOfGroups() {
        return $this->belongsToMany('App\Group');
    }

    public function posts() {
        return $this->hasMany('App\Post');
    }

    public function comments() {
        return $this->hasMany('App\Comment');
    }

    public function invitations() {
        return $this->hasMany('App\Invitation')->where('admin_accepted', true);
    }

    public function liked() {
        return $this->hasMany('App\Like');
    }

    public function notifications() {
        return $this->hasMany('App\Notification');
    }
}
