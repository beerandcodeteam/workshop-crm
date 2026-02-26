<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserStatus;

class UserService
{
    public function deactivate(User $target): void
    {
        $target->update([
            'user_status_id' => UserStatus::where('name', 'Inactive')->first()->id,
        ]);
    }
}
