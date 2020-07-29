<?php

namespace App\Policies;

use App\Invitation;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

use Illuminate\Auth\Access\Response;
use App\Group;

class InvitationPolicy
{
    use HandlesAuthorization;

    // check if admin
    public function before(User $user, $ability) {
        if ( $user->is_admin ) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return \Illuminate\Auth\Access\Response
     */
    public function viewAny(User $user, Group $group)
    {
        return $group->members->contains($user)?
            Response::allow()
            : Response::deny('Authenticated user not in group.');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return \Illuminate\Auth\Access\Response
     */
    public function create(User $user, Group $group)
    {
        if ( !$group->members->contains($user) ) {
            return Response::deny('Authenticated user not in group');
        }

        $max_i = config('consts.max_invitations_per_group');
        if ( $group->invitations->count() > $max_i ) {
            return Response::deny(
                "Group has reached it's limit of $max_i invitations."
            );
        }

        return Response::allow();
    }

    // determine whether the user can confirm the invitation
    public function confirm(User $user, Invitation $invitation, Group $group) {
        if ( $group->owner_id != $user->id ) {
            return Response::deny("You can't confirm this invitation");
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @param  \App\Invitation  $invitation
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete(User $user, Invitation $invitation, Group $group)
    {
        if ( $group->owner_id == $user->id ) {
            return Response::allow();
        }

        if ( !$group->members->contains($user) ) {
            return Response::deny('Authenticated user not in group');
        }

        if ( $invitation->from_id != $user->id ) {
            return Response::deny("That's not your invitation.");
        }

        return Response::allow();
    }
}
