<?php

namespace App\Policies;

use App\Like;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

use App\Group;
use App\Post;
use App\Comment;

class LikePolicy
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
    public function viewAnyPost(User $user, Group $group, Post $post)
    {
        return $group->posts->contains($post);
    }

    public function viewAnyComment(
        User $user, Group $group, Comment $comment
    ) {
        return $group->comments->contains($comment);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user, Group $group) {
        return $user->memberOfGroups->contains($group);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Like  $like
     * @return mixed
     */
    public function delete(User $user, Like $like)
    {
        return $user->liked->contains($like);
    }
}
