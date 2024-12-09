<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $newUser = new User();
        $validated = $request->validated();

        $newUser->name = $validated['name'];
        $newUser->email = $validated['email'];
        $newUser->password = Hash::make($validated['password']);
        $newUser->blocked = false;
        $newUser->type = $validated['type'] ?? 'P';
        $newUser->nickname = $validated['nickname'];
        $newUser->brain_coins_balance = $newUser->type == 'P' ? 10 : 0;
        $newUser->remember_token = Str::random(10);

        $lastUserId = User::latest()->first()->id;
        if ($request->hasFile('photo_filename')) {
            $customFileName = $lastUserId + 1 . '_' . uniqid() . '.' . $request->file('photo_filename')->getClientOriginalExtension();
            $request->file('photo_filename')->storeAs('photos', $customFileName, 'public');
            $newUser->photo_filename = $customFileName;
        }

        if ($newUser->save()) {
            return new UserResource($newUser);
        } else {
            return response()->json(['error' => 'Unable to create user'], 422);
        }
    }


    /**
     * Display the specified resource.
     */
    public
    function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public
    function update(UserUpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->name = $validated['name'] ?? $user->name;
        $user->email = $validated['email'] ?? $user->email;
        $user->password = isset($validated['password']) ? Hash::make($validated['password']) : $user->password;
        $user->nickname = $validated['nickname'] ?? $user->nickname;

        if ($user->save()) {
            return new UserResource($user);
        } else {
            return response()->json(['error' => 'Unable to update user'], 422);
        }
    }

    public function photo_update(UserUpdateRequest $request, User $user)
    {
        if ($request->hasFile('photo_filename')) {
            $customFileName = $user->id . '_' . uniqid() . '.' . $request->file('photo_filename')->getClientOriginalExtension();
            $request->file('photo_filename')->storeAs('photos', $customFileName, 'public');
            $user->photo_filename = $customFileName;
        }

        if ($user->save()) {
            return new UserResource($user);
        } else {
            return response()->json(['error' => 'Unable to update user'], 422);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public
    function destroy(User $user)
    {

    }

    /**
     * Display the authenticated user.
     */
    public
    function show_me(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Block the specified user.
     */
    public
    function lock_toggle(User $user)
    {
        $user->blocked = !$user->blocked;
        $user->save();
        return new UserResource($user);
    }

}
