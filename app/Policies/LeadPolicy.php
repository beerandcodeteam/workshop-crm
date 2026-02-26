<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->isBusinessOwner() || $lead->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->isBusinessOwner() || $lead->user_id === $user->id;
    }

    public function assign(User $user): bool
    {
        return $user->isBusinessOwner();
    }
}
