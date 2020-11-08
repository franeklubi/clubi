<?php

namespace App\Http\Controllers;

use App\Like;
use Illuminate\Http\Request;

use App\Group;
use App\Post;
use App\Comment;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPost(Group $group, Post $post)
    {
        $this->authorize('viewAnyPost', [Like::class, $group, $post]);

        $likes = $post->like_count;

        return response()->json([
            'likes' => $likes,
        ]);
    }

    /**
     * @param Group $group
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showUsers(Group $group, Post $post)
    {
        $likes = $post->likes;

        return response()->json([
            'likes' => $likes,
        ]);
    }


    public function indexComment(Group $group, Post $post, Comment $comment)
    {
        $this->authorize('viewAnyComment', [Like::class, $group, $comment]);

        $likes = $comment->likes;

        return response()->json([
            'likes' => $likes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function togglePost(Request $request, Group $group, Post $post) {
        $this->authorize('viewAnyPost', [Like::class, $group, $post]);

        $user = $request->user();
        $state = null;

        $like = $post->likes()->where('user_id', $user->id)->first();

        if ( $like == null ) {
            $this->authorize('create', [Like::class, $group]);

            $like = $post->likes()->create(['user_id' => $user->id]);
            $state = 'liked';

            event(new \App\Events\Liked($like));
        } else {
            $this->authorize('delete', $like);

            $like->delete();
            $state = 'unliked';
        }

        return response()->json([
            'state' => $state,
            'like' => $like,
        ]);
    }


    public function toggleComment(
        Request $request, Group $group, Post $post, Comment $comment
    ) {
        $this->authorize('viewAnyComment', [Like::class, $group, $comment]);

        $user = $request->user();
        $state = null;

        $like = $comment->likes()->where('user_id', $user->id)->first();

        if ( $like == null ) {
            $this->authorize('create', [Like::class, $group]);

            $like = $comment->likes()->create(['user_id' => $user->id]);
            $state = 'liked';

            event(new \App\Events\Liked($like));
        } else {
            $this->authorize('delete', $like);

            $like->delete();
            $state = 'unliked';
        }

        return response()->json([
            'state' => $state,
            'like' => $like,
        ]);
    }
}
