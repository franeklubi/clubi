<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Group;
use App\Post;

use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    // returns one page of random posts
    public function randomPosts() {
        $random_public_posts = Cache::remember(
            'random_public_posts.dashboard',
            now()->addMinutes(5),
            function () {
                $public_groups_ids = Group::where('private', false)
                    ->pluck('id');

                $public_posts = Post::whereIn('group_id', $public_groups_ids)
                    ->get();

                $public_posts_count = $public_posts->count();

                return $public_posts->random(
                    $public_posts_count>config('consts.posts_per_page')?
                        config('consts.posts_per_page')
                        : $public_posts_count
                )->load(['user.profile', 'group.owner', 'likes']);
            }
        );

        return $random_public_posts;
    }


    // returns paginated posts for user
    public function posts() {
        $groups = auth()->user()->memberOfGroups->pluck('id');

        $from_date_request = request('from_date');
        $from_date = \Carbon\Carbon::parse($from_date_request)
            ->toDateTimeString();

        $paginated_posts = \App\Post::whereIn('group_id', $groups)
            ->with(['user.profile', 'group.owner', 'likes'])
            ->when($from_date_request?true:false,
                function ($query) use ($from_date) {
                    return $query->where('created_at', '<=', $from_date);
                }
            )->latest()
            ->simplePaginate(config('consts.posts_per_page'));

        return $paginated_posts;
    }


    public function feed(Request $request) {
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
        } else {
            $posts = $this->randomPosts();
        }

        $response_data = [
            'posts' => $posts,
            'next_page_url' => $next_page_url,
        ];

        if ( $request->expectsJson() ) {
            return response()->json($response_data);
        }

        $response_data['user'] = $user;

        return view('dashboard.feed', $response_data);
    }


    public function invitations() {
        return response()->json([
            'invitations' => auth()->user()->invitations->load('group')
        ]);
    }


    public function yourGroups(Request $request) {
        $groups = $request->user()->ownsGroups()->get();

        $zero_warning = "You haven't created any groups yet! Create one and it'll show up here.";

        $response_data = [
            'groups' => $groups,
        ];

        if ( $groups->count() == 0 ) {
            $response_data['zero_warning'] = $zero_warning;
        }

        if ( $request->expectsJson() ) {
            return response()->json($response_data);
        }

        $response_data['user'] = $request->user();

        return view('groups.index', $response_data);
    }


    public function joinedGroups(Request $request) {
        $groups = $request->user()->memberOfGroups()->get();

        $zero_warning = "You haven't joined any groups yet! Join one and it'll show up here.";

        $response_data = [
            'groups' => $groups,
        ];

        if ( $groups->count() == 0 ) {
            $response_data['zero_warning'] = $zero_warning;
        }

        if ( $request->expectsJson() ) {
            return response()->json($response_data);
        }

        $response_data['user'] = $request->user();

        return view('groups.index', $response_data);
    }


    public function popular(Request $request) {
        $most_popular = Cache::remember(
            'most_popular.dashboard',
            now()->addMinutes(5),
            function () {
                return Group::where('private', false)->get()
                    ->sortByDesc(function ($group, $key) {
                        return $group->members()->count()
                            +$group->posts()->count()
                            +$group->comments()->count();
                    })->splice(0,10);
            }
        );

        $response_data = [
            'groups' => $most_popular,
        ];

        if ( $most_popular->count() == 0 ) {
            $response_data['zero_warning'] = 'Nothing there yet...';
        }

        if ( $request->expectsJson() ) {
            return response()->json($response_data);
        }

        $response_data['user'] = $request->user();

        return view('groups.index', $response_data);
    }
}
