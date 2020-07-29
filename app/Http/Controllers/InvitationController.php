<?php

namespace App\Http\Controllers;

use App\User;
use App\Group;
use App\Invitation;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function groupIndex(Group $group)
    {
        $this->authorize('viewAnyGroup', [Invitation::class, $group]);

        return response()->json([
            'invitations' => $group->invitations()->with('user.profile')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Group $group)
    {
        $this->authorize('create', [Invitation::class, $group]);

        $validated_data = $request->validate([
            'username' => [
                'required',
                'string',
                'min:4',
                'max:20',
            ],
        ]);

        $user = User::where('username', $validated_data['username'])->first();

        if ( $user == null ) {
            return abort(404, $validated_data['username'].' not found.');
        }

        // checking if user's already in the group
        if ( $group->members->contains($user) ) {
            return abort(403, $user->username.' is already in the group.');
        }

        // checking if an invitation exists
        $invitation = $group->invitations->where('user_id', $user->id)->first();
        if ( $invitation != null ) {
            return abort(403, $user->username.' has already been invited.');
        }

        $invitation = Invitation::create([
            'from_id' => $request->user()->id,
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);

        // cases where admin_accepted is set to true at creation time
        if (
            !$group->private
            || $group->owner_id == $request->user()->id
            || $request->user()->is_admin
        ) {
            $invitation->admin_accepted = true;
            $invitation->save();
        }

        return response()->json([
            'invitation' => $invitation->load('user.profile')
        ]);
    }

    public function adminConfirm(
        Request $request,
        Group $group,
        Invitation $invitation
    ) {
        $this->authorize('adminConfirm', [$invitation, $group]);

        $invitation->admin_accepted = true;
        $invitation->save();

        return response()->json([
            'invitation' => $invitation->load('user.profile'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Group  $group
     * @param  \App\Invitation  $invitation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group, Invitation $invitation)
    {
        $this->authorize('delete', [$invitation, $group]);

        $invitation->delete();

        return response()->json(['invitation' => $invitation]);
    }
}
