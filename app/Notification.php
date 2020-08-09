<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['from_id', 'user_id', 'message', 'link'];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function from() {
        return $this->belongsTo('App\User', 'from_id');
    }
}
