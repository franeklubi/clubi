<?php

namespace App\Http\Controllers\api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    // profile returns user's profile
    public function profile($username = null)
    {
        $user = User::where('username', $username)->get();

        $response_data = [
            'user' => !$user->isEmpty() ? $user->load('profile') : null,
        ];

        return response()->json($response_data);
    }
}
