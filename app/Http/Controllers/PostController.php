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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'content' => 'max:5000',
            'picture' => [
                'image',
                'dimensions:min_width=250,min_height=250,'.
                    'max_width=5000,max_height=5000',
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
            $picture->resize(1920, 1080, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $picture->save();

            $validated_data = array_merge(
                $validated_data, ['picture' => $picture_path]
            );
        }

        $validated_data = array_merge(
            $validated_data, ['user_id' => $auth_user->id]
        );

        $post = $group->posts()->create($validated_data);

        return response($post->load(['user.profile', 'group']));
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
        return view('posts.show', [
            'post' => $post->load(['user.profile', 'group']),
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
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
