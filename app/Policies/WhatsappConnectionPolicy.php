<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WhatsappConnection;

class WhatsappConnectionPolicy
{
    public function manage(User $user, WhatsappConnection $whatsappConnection): bool
    {
        return $user->isBusinessOwner();
    }
}
