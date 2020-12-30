<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login']]);
    }

    public function login(Request $request)

    {
        $credentials = $request->only(['email', 'password']);

        try {

            if (!$token = \JWTAuth::attempt($credentials)) {

                return response()->json(['error' => 'Error in user or password'], 401);

            }

            $user = User::whereEmail(request(['email']))->first();
            $user->token = $token;
            $user->save();

        } catch (\JWTException $exception) {

            return response()->json(['error' => 'Could not create token'], 500);

        }

        return new UserResource($user);

    }

}
