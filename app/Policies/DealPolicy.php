<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\User;

class DealPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Deal $deal): bool
    {
        return $user->isBusinessOwner() || $deal->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Deal $deal): bool
    {
        return $user->isBusinessOwner() || $deal->user_id === $user->id;
    }

    public function move(User $user, Deal $deal): bool
    {
        return $user->isBusinessOwner() || $deal->user_id === $user->id;
    }

    public function assign(User $user): bool
    {
        return $user->isBusinessOwner();
    }
}
