<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\DealNote;
use App\Models\User;

class DealNotePolicy
{
    public function view(User $user, DealNote $dealNote): bool
    {
        return $user->isBusinessOwner() || $dealNote->deal->user_id === $user->id;
    }

    public function create(User $user, Deal $deal): bool
    {
        return $user->isBusinessOwner() || $deal->user_id === $user->id;
    }
}
