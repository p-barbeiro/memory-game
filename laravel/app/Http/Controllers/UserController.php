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
    public function index()
    {
        return UserResource::collection(User::paginate(20));
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        try {
            $user->name = $request->validated('name') ?? $user->name;
            $user->email = $request->validated('email') ?? $user->email;
            $user->password = $request->has('password') ? Hash::make($request->validated('password')) : $user->password;
            $user->nickname = $request->validated('nickname') ?? $user->nickname;

            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update user',
                'message' => $e->getMessage()
            ], 422);
        }

        return new UserResource($user);
    }

    public function updatePhoto(UserUpdateRequest $request, User $user)
    {
        try {
            if ($request->hasFile('photo_filename')) {
                $customFileName = $user->id . '_' . uniqid() . '.' . $request->file('photo_filename')->getClientOriginalExtension();
                $request->file('photo_filename')->storeAs('photos', $customFileName, 'public');
                $user->photo_filename = $customFileName;
            }

            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to update user photo',
                'message' => $e->getMessage()
            ], 422);
        }

        return new UserResource($user);
    }

    public function store(UserStoreRequest $request)
    {
        try {
            $newUser = new User();

            $newUser->name = $request->validated('name');
            $newUser->email = $request->validated('email');
            $newUser->password = Hash::make($request->validated('password'));
            $newUser->blocked = false;
            $newUser->type = $request->validated('type') ?? 'P';
            $newUser->nickname = $request->validated('nickname');
            $newUser->brain_coins_balance = $newUser->type == 'P' ? 10 : 0;
            $newUser->remember_token = Str::random(10);

            $lastUserId = User::latest()->first()->id;
            if ($request->hasFile('photo_filename')) {
                $customFileName = $lastUserId + 1 . '_' . uniqid() . '.' . $request->file('photo_filename')->getClientOriginalExtension();
                $request->file('photo_filename')->storeAs('photos', $customFileName, 'public');
                $newUser->photo_filename = $customFileName;
            }

            $newUser->save();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to create user',
                'message' => $e->getMessage()
            ], 422);
        }

        return new UserResource($newUser);
    }

    public function destroy(User $user)
    {
        try {
            if ($user->transactions()->count() > 0) {
                $user->brain_coins_balance = 0;
                $user->save();
                $user->delete();
            } else {
                $user->forceDelete();
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to delete user',
                'message' => $e->getMessage()
            ], 422);
        }

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    public function showMe(Request $request)
    {
        return new UserResource($request->user());
    }

    public function toggleUserLock(User $user)
    {
        try {
            $user->blocked = !$user->blocked;
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to toggle user lock',
                'message' => $e->getMessage()
            ], 422);
        }

        return new UserResource($user);
    }

}
