<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class DashboardController extends Controller
{
    // returns paginated posts for user
    public function posts() {
        $groups = auth()->user()->memberOfGroups->pluck('id');

        $paginated_posts = \App\Post::whereIn('group_id', $groups)
            ->with(['user.profile', 'group.owner'])
            ->latest()
            ->simplePaginate(config('consts.posts_per_page'));

        return $paginated_posts;
    }


    public function feed() {
        $posts = [];
        $next_page_url = '';
        $user = null;

        if ( auth()->check() ) {
            $user = auth()->user();
            $paginated_posts = $this->posts();

            // creating a collection of the first page of posts
            $posts = collect($paginated_posts->items());

            if ( $paginated_posts->hasMorePages() ) {
                $next_page_url = route('dashboard.posts', [
                    'page' => 2,
                ]);
            }
        }

        return view('dashboard.feed', [
            'posts' => $posts,
            'user_id' => $user?$user->id:-1,
            'next_page_url' => $next_page_url,
        ]);
    }


    public function popular() {
        return view('dashboard.popular');
    }
}
