<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(?User $user): bool
    {
        return $user->type == 'A';
    }

    public function view(User $authUser, User $user): bool
    {
        return $authUser->id == $user->id || $authUser->type == 'A';
    }

    public function update(User $authUser, User $user): bool
    {
        return $authUser->id == $user->id;
    }

    public function delete(User $authUser, User $user): bool
    {
        return $authUser->id == $user->id || $authUser->type == 'A';
    }

    public function toggleLock(User $authUser, User $user): bool
    {
        return $authUser->type == 'A';
    }

    public function createAdmin(User $user): bool
    {
        return $user->type === 'A'; // Only administrators can create other administrators
    }
}
