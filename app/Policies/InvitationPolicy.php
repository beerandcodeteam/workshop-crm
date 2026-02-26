<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isBusinessOwner();
    }

    public function create(User $user): bool
    {
        return $user->isBusinessOwner();
    }

    public function revoke(User $user, Invitation $invitation): bool
    {
        return $user->isBusinessOwner();
    }
}
