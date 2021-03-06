<?php

namespace App\Policies;

use App\Post;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
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
     * @param  \App\Post  $post
     * @param  \App\Group $group
     * @return mixed
     */
    public function view(?User $user, Post $post, \App\Group $group)
    {
        return $group->posts->contains($post);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @param  \App\Group $group
     * @return mixed
     */
    public function create(User $user, \App\Group $group)
    {
        return $group->members->contains($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Group $group
     * @param  \App\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post, \App\Group $group)
    {
        // checking if the group contains the post
        if ( !$group->posts->contains($post) ) {
            return false;
        }

        if ( $user->id == $post->user->id || $user->id == $group->owner_id ) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function restore(User $user, Post $post)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function forceDelete(User $user, Post $post)
    {
        //
    }
}
