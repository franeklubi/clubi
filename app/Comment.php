<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'comment_id', 'content', 'picture'];

    protected $appends = ['like_count'];



    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'post_id' => 'integer',
    ];

    protected $hidden = [
        'updated_at',
    ];


    public function getLikeCountAttribute()
    {
        return $this->likes()->count();
    }



    public function post() {
        return $this->belongsTo('App\Post');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function likes() {
        return $this->morphMany('App\Like', 'likeable');
    }



    // link for the Liked event
    public function link() {
        return route('posts.show', [
            'group' => $this->post->group->id_string,
            'post' => $this->post->id,
        ]);
    }
}
