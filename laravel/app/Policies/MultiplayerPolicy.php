<?php

namespace App\Policies;

use App\Models\Multiplayer;
use App\Models\User;

class MultiplayerPolicy
{
    public function update(User $user, Multiplayer $multiplayer): bool
    {
        return $user->id === $multiplayer->user_id;
    }
}
