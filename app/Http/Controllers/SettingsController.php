<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Intervention\Image\Facades\Image;

class SettingsController extends Controller
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('settings.edit', ['user' => auth()->user()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validated_data = $request->validate([
            'username' => [
                'required',
                'unique:users,username,'.$request->user()->id,
                'alpha_dash',
            ],
            'description' => '',
            'profile_picture' => [
                'image',
                'dimensions:'
                    .'min_width='.config('consts.profile_picture.min_width')
                    .',min_height='.config('consts.profile_picture.min_height')
                    .',max_width='.config('consts.profile_picture.max_width')
                    .',max_height='.config('consts.profile_picture.max_height')
            ],
            'remove_profile_picture' => '',
        ]);

        if ( $request->has('profile_picture') ) {
            $request_file = $request->file('profile_picture');

            $image_path = '/storage/'.$request_file->storeAs(
                'profile_pictures',
                "{$request->user()->id}.{$request_file->extension()}",
                'public',
            );

            $image = Image::make(public_path($image_path))->fit(
                config('consts.profile_picture.fit_width'),
                config('consts.profile_picture.fit_height')
            );
            $image->save();

            $validated_data = array_merge(
                $validated_data, ['profile_picture' => $image_path]
            );
        } else if ( $request->has('remove_profile_picture') ) {
            unset($validated_data['remove_profile_picture']);
            $validated_data = array_merge(
                $validated_data, ['profile_picture' => null]
            );
        }

        $user_data = ['username' => $validated_data['username']];

        unset($validated_data['username']);

        $request->user()->profile->update($validated_data);
        $request->user()->update($user_data);

        return redirect('settings')->with('status', 'Update successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
