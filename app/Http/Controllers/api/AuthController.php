<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{

	public function login(Request $request)
	{
	    $request->validate([
	        'email' => ['required', 'string', 'email'],
	        'password' => ['required', 'string', 'min:8'],
	        'device_name' => ['required', 'string', 'max:256'],
	    ]);

	    $user = \App\User::where('email', $request->email)->first();

	    if (! $user || ! Hash::check($request->password, $user->password)) {
	        throw ValidationException::withMessages([
	            'email' => ['The provided credentials are incorrect.'],
	        ]);
	    }

	    return $user->createToken($request->device_name)->plainTextToken;
	}
}
