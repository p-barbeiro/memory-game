<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use function Psy\debug;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {

    }

    /**
     * Display the authenticated user.
     */
    public function show_me(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Block the specified user.
     */
    public function lock_toggle(User $user)
    {
        $user->blocked = !$user->blocked;
        $user->save();
        return new UserResource($user);
    }

}
