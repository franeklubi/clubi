<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;

use Intervention\Image\Facades\Image;

use Illuminate\Support\Str;

use App\Http\Controllers\PostController;

use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
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

    // redirectGet is a helper function to redirect to
    // dashboard if someone tries to access '/groups' with GET
    // I'm not using a closure-based route because that would disallow
    // route caching
    public function redirectGet() {
        return redirect()->route('dashboard.feed');
    }

    // return search results
    public function search(Request $request) {
        $validated_data = $request->validate([
            'query' => ['string', 'max:30', 'nullable'],
        ]);

        $query = request('query')?request('query'):'';

        $groups = Group::search($query);

        $response_data = [
            'groups' => $groups?$groups:collect([]),
            'zero_warning' => 'No groups found :(',
        ];

        if ( $request->expectsJson() ) {
            return response()->json($response_data);
        }

        $response_data['user'] = $request->user();

        return view('groups.index', $response_data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Group::class);

        $auth_user = $request->user();

        $validated_data = $request->validate(Group::rules());

        // generate a unique id_sting
        $id_string = Str::random(5);
        while ( Group::where('id_string', $id_string)->count() > 0 ) {
            $id_string = Str::random(5);
        }

        if ( $request->has('banner_picture') ) {
            $request_file = $request->file('banner_picture');

            $picture_path = '/storage/'.$request_file->store(
                'banner_pictures/'.$id_string,
                'public',
            );

            $picture = Image::make(public_path($picture_path));
            $picture->orientate();
            $picture->resize(
                config('consts.banner_picture.fit_width'),
                config('consts.banner_picture.fit_height'),
                function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            );
            $picture->save();

            $validated_data = array_merge(
                $validated_data, ['banner_picture' => $picture_path]
            );
        } else if ( $request->has('remove_banner_picture') ) {
            unset($validated_data['remove_banner_picture']);
            $validated_data = array_merge(
                $validated_data, ['banner_picture' => null]
            );
        }

        // create group
        $group = new Group($validated_data);
        $group->id_string = $id_string;
        $group->owner_id = $auth_user->id;
        $group->save();

        // add owner to the group
        $auth_user->memberOfGroups()->attach($group);

        return response()->json(['string_id' => $id_string]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Group $group)
    {
        $this->authorize('view', $group);

        // check if owner or admin
        $editable = false;
        $is_member = false;
        $user = null;
        if ( checkAuthUser() ) {
            $user = getAuthUser();
            $editable = $user->can('update', $group);
            $is_member = $user->memberOfGroups->contains($group);
        }

        // call post controller for the paginated posts
        $paginated_posts = app(PostController::class)->index($group);

        // creating a collection of the first page of posts
        $posts = collect($paginated_posts->items());

        $next_page_url = null;
        if ( $paginated_posts->hasMorePages() ) {
            $next_page_url = route('posts.index', [
                'group' => $group->id_string,
                'page' => 2,
            ]);
        }

        $response_data = [
            'group' => $group,
            'editable' => $editable,
            'is_member' => $is_member,
        ];

        if ( $request->expectsJson() ) {
            return response()->json($response_data);
        }

        $response_data = array_merge($response_data, [
            'posts' => $posts,
            'next_page' => $next_page_url,
            'user_id' => $user?$user->id:-1,
        ]);

        return view('groups.show', $response_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        $this->authorize('update', $group);

        $auth_user = $request->user();

        $validated_data = $request->validate(Group::rules());

        $remove_picture = (
            $request->has('remove_banner_picture')
            && $request->input('remove_banner_picture')
        );


        if ( $request->has('banner_picture') || $remove_picture ) {
            // remove the old picture
            $old_path = $group->banner_picture;
            if ( $old_path != config('consts.default_banner_picture_path') ) {
                $relative_old_path = Str::after($old_path,'storage/');
                Storage::disk('public')->delete($relative_old_path);
            }
        }

        if ( $request->has('banner_picture') ) {
            $request_file = $request->file('banner_picture');

            $picture_path = '/storage/'.$request_file->store(
                'banner_pictures/'.$group->id_string,
                'public',
            );

            $picture = Image::make(public_path($picture_path));
            $picture->orientate();
            $picture->resize(
                config('consts.banner_picture.fit_width'),
                config('consts.banner_picture.fit_height'),
                function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            );
            $picture->save();

            $validated_data = array_merge(
                $validated_data, ['banner_picture' => $picture_path]
            );
        } else if ( $remove_picture ) {
            unset($validated_data['remove_banner_picture']);
            $validated_data = array_merge(
                $validated_data, ['banner_picture' => null]
            );
        }

        // update group
        $group->update($validated_data);

        return response()->json(['group' => $group]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Group $group)
    {
        $this->authorize('delete', $group);
        $group->delete();

        $message = "Group $group->name has been deleted.";
        if ( $request->session ) {
            $request->session()->flash('status', $message);
        }

        return response()->json(['group' => $group]);
    }
}
