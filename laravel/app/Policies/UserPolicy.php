<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(?User $user): bool
    {
        if ($user->type == 'A') {
            return true;
        }
        return false;
    }

    public function view(User $authUser, User $user): bool
    {
        if ($authUser->id == $user->id || $authUser->type == 'A') {
            return true;
        }
        return false;
    }

    public function update(User $authUser, User $user): bool
    {
        if ($authUser->id == $user->id) {
            return true;
        }
        return false;
    }

    public function delete(User $authUser, User $user): bool
    {
        if ($authUser->type == 'A' && $authUser->id == $user->id) {
            return false;
        }

        if ($authUser->id == $user->id) {
            return true;
        }
        return false;
    }

    public function toggleLock(User $authUser, User $user): bool
    {
        if ($authUser->type == 'A') {
            return true;
        }
        return false;
    }
}
