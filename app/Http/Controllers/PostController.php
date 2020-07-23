<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

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

        $validated_data = $request->validate([
            'content' => [
                'required',
                'min:4',
                'max:5000',
            ],
            'image' => '',
        ]);

        $post = $group->posts()->create([
            'user_id' => $auth_user->id,
            'content' => $validated_data['content'],
        ]);

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
