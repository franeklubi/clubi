<?php

namespace App\Http\Controllers\api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    // profile returns user's profile
    public function profile($user_id = null)
    {
        $user = User::find($user_id);

        $response_data = [
            'user' => $user ? $user->load('profile') : null,
        ];

        return response()->json($response_data);
    }
}
