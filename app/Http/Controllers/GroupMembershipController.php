<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;

class GroupMembershipController extends Controller
{
    public function toggle(Request $request, Group $group) {
        $user = $request->user();

        $res = $group->members()->toggle($user);

        // if there's an invitation for the user
        $invitation = $group->invitations->where('user_id', $user->id)->first();
        if ( $invitation != null ) {
            $invitation->delete();
        }

        return response()->json([
            'status' => count($res['attached'])==0?'left':'joined',
        ]);
    }
}
