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
        $validatedData = Validator::make($request->all(), [
            'first_name' => ['required', 'max:128'],
            'last_name' => ['required', 'max:128'],
            'email' => ['required', 'email', 'max:65', 'unique:users'],
            'age' => ['required'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validatedData->fails()) {
            return response()->json([$validatedData->errors()], 422);
        }

        try {

            $payload = $request->all();
            $payload['password'] = Hash::make($request->password);
            $user = User::create($payload);

            return new UserResource($user);

        } catch (\Exception $exception) {

            return response()->json(['error' => 'Error at the server'], 500);
        }

    }

    public function list()
    {
        try {

            return UserResource::collection(User::paginate(5));

        } catch (\Exception $exception) {

            return response()->json(['error' => 'Error at the server'], 500);
        }
    }

    public function show($id)
    {
        try {

            return new UserResource(User::findOrFail($id));

        } catch (\Exception $exception) {

            return response()->json(['error' => 'Not found'], 404);
        }
    }

    public function edit(Request $request, $id)
    {

        $validatedData = Validator::make($request->all(), [

            'first_name' => 'required|max:128',
            'last_name' => 'required|max:128',
            'email' => 'required|email|unique:users,email'.$id,
            'age' => 'required',
            'password' => 'required|min:6',
            'description' => 'required',

        ]);

        if ($validatedData->fails()) {
            return response()->json([$validatedData->errors()], 422);
        }

        try {

            $user = User::findOrFail($id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->age = $request->age;
            $user->image = $request->image;
            $user->description = $request->description;
            $user->save();

            return new UserResource($user);

        } catch (\Exception $exception) {

            return response()->json(['error' => 'Not found'], 404);
        }


    }

    public function partialEdit(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [

            'first_name' => 'required|max:128',
            'last_name' => 'required|max:128',
            'email' => 'sometimes|email|unique:users,email,'.$id,
            'age' => 'required',
            'password' => 'min:6',

        ]);

        if ($validatedData->fails()) {
            return response()->json([$validatedData->errors()], 422);
        }

        try {

            $user = User::findOrFail($id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->age = $request->age;
            $user->email = empty($request->email) ? $user->email : $request->email;
            $user->password = empty($request->password) ? $user->password : Hash::make($request->password);
            $user->image = empty($request->image) ? $user->image : $request->image;
            $user->description = empty($request->description) ? $user->description : $request->description;
            $user->save();

            return new UserResource($user);

        } catch (\Exception $exception) {

            return response()->json(['error' => 'Not found'], 404);
        }
    }

    public function delete($id)
    {
        try {

            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'User deleted succesfull'], 200);

        } catch (\Exception $exception) {

            return response()->json(['error' => 'Not found'], 404);
        }
    }


}
