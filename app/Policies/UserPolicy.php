<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isBusinessOwner();
    }

    public function deactivate(User $user, User $target): bool
    {
        return $user->isBusinessOwner() && $target->id !== $user->id;
    }
}
