<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\RegisterController;


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

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $validator = app(RegisterController::class)->validator($request->all());

        if($validator->fails()){
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = app(RegisterController::class)->create($request->all());

        $token = $user->createToken('register_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
