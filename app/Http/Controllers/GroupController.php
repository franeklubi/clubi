<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;

use Intervention\Image\Facades\Image;

use Illuminate\Support\Str;

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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $group = new \App\Group;
        // setting banner to default accessor value so that vue doesn't have
        // to resolve it by itself
        $group->banner_picture = $group->banner_picture;

        return view('groups.create', ['group' => $group]);
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

        $auth_user = auth()->user();

        $validated_data = $request->validate([
            'name' => [
                'required',
                'max:30',
            ],
            'banner_picture' => [
                'image',
                'dimensions:min_width=250,min_height=250,'.
                    'max_width=5000,max_height=5000',
            ],
            'private' => 'boolean',
            'remove_banner_picture' => 'boolean',
        ]);

        if ( $request->has('banner_picture') ) {
            $request_file = $request->file('banner_picture');

            $image_path = '/storage/'.$request_file->store(
                'banner_pictures/'.$auth_user->id,
                'public',
            );

            $image = Image::make(public_path($image_path))
                ->fit(1500);
            $image->save();

            $validated_data = array_merge(
                $validated_data, ['banner_picture' => $image_path]
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

        return view('groups.show', ['group' => $group]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        //
    }
}
