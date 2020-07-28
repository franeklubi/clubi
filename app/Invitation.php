<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// invitation logic goes as follows:
// user is added IFF (user_accepted && admin_accepted)
// cases in which an invitation could be created:
//     - if group is private:
//         - if a member invites:
//             * admin has to accept first
//             * target user has to accept
//             * invitation is destroyed
//         - if admin invites:
//             * admin_accepted is set to true
//             * target user has to accept
//             * invitation is destroyed
//     - if group is public:
//         - if a member invites:
//             * admin_accepted is set to true
//             * user has to accept
//             * invitation is destroyed
class Invitation extends Model
{
    protected $fillable = ['group_id', 'user_id'];

    protected $appends = ['named_state'];

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
