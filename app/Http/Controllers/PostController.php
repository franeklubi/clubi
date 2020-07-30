<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

use Validator;
use Intervention\Image\Facades\Image;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Group $group
     * @return \Illuminate\Http\Response
     */
    public function index(\App\Group $group)
    {
        return $group->posts()
            ->with(['user.profile', 'group.owner', 'likes'])
            ->latest()
            ->simplePaginate(config('consts.posts_per_page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, \App\Group $group)
    {
        $this->authorize('create', [Post::class, $group]);

        $auth_user = $request->user();

        $validator = Validator::make($request->all(), [
            'content' => 'max:'.config('consts.max_post_length'),
            'picture' => [
                'image',
                'dimensions:'
                    .'min_width='.config('consts.post_picture.min_width')
                    .',min_height='.config('consts.post_picture.min_height')
                    .',max_width='.config('consts.post_picture.max_width')
                    .',max_height='.config('consts.post_picture.max_height')
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
                'post_pictures/'.$auth_user->id,
                'public',
            );

            $picture = Image::make(public_path($picture_path));
            $picture->resize(
                config('consts.post_picture.fit_width'),
                config('consts.post_picture.fit_height'),
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
            $validated_data, ['user_id' => $auth_user->id]
        );

        $post = $group->posts()->create($validated_data);

        return response($post->load(['user.profile', 'group.owner', 'likes']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group $group
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(\App\Group $group, Post $post)
    {
        $this->authorize('view', [$post, $group]);

        // check if owner or admin
        $editable = false;
        $is_member = false;
        $user = null;
        if ( auth()->check() ) {
            $user = auth()->user();
            $editable = $user->can('update', $group);
            $is_member = $user->memberOfGroups->contains($group);
        }

        return view('posts.show', [
            'post' => $post->load(['user.profile', 'group.owner', 'likes']),
            'user_id' => $user?$user->id:-1,
            'editable' => $editable,
            'is_member' => $is_member,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group $group
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(\App\Group $group, Post $post)
    {
        $this->authorize('delete', [$post, $group]);
        $post->delete();
        return response()->json(['post' => $post]);
    }
}
