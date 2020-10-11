<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// invitation logic goes as follows:
// user is added IFF (user_accepted && admin_accepted)
// cases in which an invitation could be created:
//     - if group is private:
//         - if a member invites:
//             1. admin has to accept first
//             2. target user has to accept/join group
//             3. invitation is destroyed
//         - if admin invites:
//             1. admin_accepted is set to true
//             2. target user has to accept/join group
//             3. invitation is destroyed
//     - if group is public:
//         - if a member invites:
//             1. admin_accepted is set to true
//             2. target user has to accept/join group
//             3. invitation is destroyed
class Invitation extends Model
{
    protected $fillable = ['from_id', 'group_id', 'user_id'];

    protected $appends = ['named_state'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'from_id' => 'integer',
        'group_id' => 'integer',
        'admin_accepted' => 'boolean',
        'user_accepted' => 'boolean',
    ];

    public function getNamedStateAttribute() {
        if ( !$this->admin_accepted && !$this->user_accepted ) {
            return 'Pending for admin approval.';
        }

        if ( $this->admin_accepted ) {
            return 'Waiting for user to accept.';
        }

        // there shouldn't exist a state when an invitation is accepted by the
        // user, but not the admin
        return null;
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function group() {
        return $this->belongsTo('App\Group');
    }
}
