<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;

use Intervention\Image\Facades\Image;

use Illuminate\Support\Str;

use App\Http\Controllers\PostController;

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

    // return search results
    public function search(Request $request) {
        $validated_data = $request->validate([
            'query' => ['string', 'max:30'],
        ]);

        $groups = Group::search($validated_data['query']);

        return view('groups.index', [
            'user' => $request->user(),
            'groups' => $groups,
            'zero_warning' => 'No groups found :(',
        ]);
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

        if ( $request->has('banner_picture') ) {
            $request_file = $request->file('banner_picture');

            $picture_path = '/storage/'.$request_file->store(
                'banner_pictures/'.$auth_user->id,
                'public',
            );

            $picture = Image::make(public_path($picture_path));
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

        $id_string = Str::random(5);
        while ( Group::where('id_string', $id_string)->count() > 0 ) {
            $id_string = Str::random(5);
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
    public function show(Group $group)
    {
        $this->authorize('view', $group);

        // check if owner or admin
        $editable = false;
        $is_member = false;
        $user = null;
        if ( auth()->check() ) {
            $user = auth()->user();
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

        return view('groups.show', [
            'group' => $group,
            'posts' => $posts,
            'user_id' => $user?$user->id:-1,
            'next_page' => $next_page_url,
            'editable' => $editable,
            'is_member' => $is_member,
        ]);
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

        if ( $request->has('banner_picture') ) {
            $request_file = $request->file('banner_picture');

            $picture_path = '/storage/'.$request_file->store(
                'banner_pictures/'.$auth_user->id,
                'public',
            );

            $picture = Image::make(public_path($picture_path));
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
        } else if (
            $request->has('remove_banner_picture')
            && $request->input('remove_banner_picture')
        ) {
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
        $request->session()->flash('status', $message);

        return response()->json(['group' => $group]);
    }
}
