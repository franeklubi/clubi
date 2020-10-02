<?php

namespace App\Policies;

use App\Group;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

use Illuminate\Auth\Access\Response;

class GroupPolicy
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
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return boolean
     */
    public function view(?User $user, Group $group)
    {
        // fetch optional sanctum user
        if ( $user == null ) {
            $user = sanctumUser();
        }

        // if group is not private
        if ( $group->private != '1' && $group->private != 'true' ) {
            return true;
        }

        // all cases below are checked only if the group is private

        // if guest
        if ( $user == null ) {
            return false;
        }

        // if user's the group owner
        if ( $group->owner_id == $user->id ) {
            return true;
        }

        // if user belongs to the group
        if ( $group->members->contains($user) ) {
            return true;
        }

        // if there's an admin accepted invitation for the user
        $invitation = $group->invitations
            ->where('user_id', $user->id)
            ->where('admin_accepted', true)
            ->first();

        if ( $invitation != null ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function create(User $user)
    {
        return ( $user->ownsGroups->count() >= 5 )?
            Response::deny("You can't own more than 5 groups.")
            : Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return boolean
     */
    public function update(User $user, Group $group)
    {
        return ($group->owner_id != $user->id)?
            Response::deny("You can't update this group.")
            : Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function delete(User $user, Group $group)
    {
        return ($user->id == $group->owner_id);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function restore(User $user, Group $group)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Group  $group
     * @return mixed
     */
    public function forceDelete(User $user, Group $group)
    {
        //
    }
}
