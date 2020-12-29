<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.verify');
    }

    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(),[
            'first_name' => ['required','max:128'],
            'last_name' => ['required','max:128'],
            'email' => ['required','email', 'max:65', 'unique:users'],
            'age' => ['required'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validatedData->fails()) {
            return response()->json([$validatedData->errors()],400);
        }

        try {

            $payload = $request->all();
            $payload['password'] = Hash::make($request->password);
            $user = User::create($payload);

            return new UserResource($user);

        } catch (\Exception $error) {

            return response()->json(['error'=>'Error at the server'],500);
        }

    }

    public function list()
    {
        return UserResource::collection(User::paginate(5));
    }
}
