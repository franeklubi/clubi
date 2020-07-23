<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;

class GroupMembershipController extends Controller
{
    public function store(Request $request, Group $group) {
        $res = $group->members()->toggle($request->user());

        return response()->json([
            'status' => count($res['attached'])==0?'left':'joined',
        ]);
    }
}
