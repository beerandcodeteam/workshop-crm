<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function register(string $companyName, string $userName, string $email, string $password): User
    {
        return DB::transaction(function () use ($companyName, $userName, $email, $password) {
            $tenant = Tenant::create(['name' => $companyName]);

            return User::create([
                'name' => $userName,
                'email' => $email,
                'password' => $password,
                'tenant_id' => $tenant->id,
                'role_id' => Role::where('name', 'Business Owner')->first()->id,
                'user_status_id' => UserStatus::where('name', 'Active')->first()->id,
            ]);
        });
    }
}
