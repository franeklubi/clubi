<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'likeable_id' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function likeable() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
