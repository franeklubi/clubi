<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['from_id', 'user_id', 'message', 'link'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'from_id' => 'integer',
        'seen' => 'boolean',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function from() {
        return $this->belongsTo('App\User', 'from_id');
    }
}
