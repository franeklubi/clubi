<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Post;
use Illuminate\Http\Request;

use Validator;
use Intervention\Image\Facades\Image;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Group $group
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function index(\App\Group $group, Post $post)
    {
        $this->authorize('viewAny', [Comment::class, $group, $post]);

        return $post->comments()->with(['user.profile', 'likes'])
            ->simplePaginate(config('consts.comments_per_page'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group $group
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, \App\Group $group, Post $post)
    {
        $this->authorize('create', [Comment::class, $group, $post]);

        $validator = Validator::make($request->all(), [
            'content' => 'max:'.config('consts.max_comment_length'),
            'picture' => [
                'image',
                'dimensions:'
                    .'min_width='.config('consts.comment_picture.min_width')
                    .',min_height='.config('consts.comment_picture.min_height')
                    .',max_width='.config('consts.comment_picture.max_width')
                    .',max_height='.config('consts.comment_picture.max_height')
            ],
        ]);

        $validator->sometimes('content', 'required',
            function ($input) {
                return $input->picture == null;
            }
        );

        $validated_data = $validator->validate();

        if ( $request->has('picture') ) {
            $request_file = $request->file('picture');

            $picture_path = '/storage/'.$request_file->store(
                'comment_pictures/'.$post->id,
                'public',
            );

            $picture = Image::make(public_path($picture_path));
            $picture->resize(
                config('consts.comment_picture.fit_width'),
                config('consts.comment_picture.fit_height'),
                function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            );
            $picture->save();

            $validated_data = array_merge(
                $validated_data, ['picture' => $picture_path]
            );
        }

        $validated_data = array_merge(
            $validated_data, ['user_id' => $request->user()->id]
        );

        $comment = $post->comments()->create($validated_data);

        return response($comment->load(['user.profile', 'likes']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(\App\Group $group, Post $post, Comment $comment)
    {
        $this->authorize('delete', [$comment, $group]);
        $comment->delete();
        return response()->json(['comment' => $comment]);
    }
}
