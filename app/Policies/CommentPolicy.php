<?php

namespace App\Policies;

use App\Comment;
use App\Group;
use App\Post;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
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
     * @param  \App\Group $group
     * @param  \App\Post  $post
     * @return boolean
     */
    public function viewAny(?User $user, \App\Group $group, \App\Post $post)
    {
        return $group->posts->contains($post);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function view(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @param  \App\Group $group
     * @param  \App\Post  post
     * @return boolean
     */
    public function create(User $user, Group $group, Post $post)
    {
        return $group->members->contains($user)
            && $group->posts->contains($post);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function update(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @param  \App\Group  $group
     * @return mixed
     */
    public function delete(User $user, Comment $comment, Group $group)
    {
        // checking if the comment's in the group
        if ( !$group->comments->contains($comment) ) {
            return false;
        }

        if ($comment->user_id == $user->id || $group->owner_id == $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function restore(User $user, Comment $comment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Comment  $comment
     * @return mixed
     */
    public function forceDelete(User $user, Comment $comment)
    {
        //
    }
}
