<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\InvitationStatus;
use App\Models\Role;
use App\Models\User;
use App\Models\UserStatus;
use App\Notifications\InvitationSentNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use InvalidArgumentException;

class InvitationService
{
    public function invite(User $inviter, string $email): Invitation
    {
        $existingUser = User::withoutGlobalScopes()
            ->where('email', $email)
            ->where('tenant_id', $inviter->tenant_id)
            ->exists();

        if ($existingUser) {
            throw new InvalidArgumentException('Este e-mail já está cadastrado na sua empresa.');
        }

        $invitation = Invitation::create([
            'tenant_id' => $inviter->tenant_id,
            'invited_by_user_id' => $inviter->id,
            'invitation_status_id' => InvitationStatus::where('name', 'Pending')->first()->id,
            'email' => $email,
            'token' => Str::random(64),
            'expires_at' => now()->addHours(72),
        ]);

        Notification::route('mail', $email)
            ->notify(new InvitationSentNotification($invitation));

        return $invitation;
    }

    public function acceptInvitation(string $token, string $name, string $password): User
    {
        return DB::transaction(function () use ($token, $name, $password) {
            $invitation = Invitation::withoutGlobalScopes()
                ->where('token', $token)
                ->first();

            if (! $invitation) {
                throw new InvalidArgumentException('Convite inválido.');
            }

            if ($invitation->invitationStatus->name !== 'Pending') {
                throw new InvalidArgumentException('Este convite já foi utilizado ou revogado.');
            }

            if ($invitation->expires_at->isPast()) {
                throw new InvalidArgumentException('Este convite expirou.');
            }

            $user = User::create([
                'name' => $name,
                'email' => $invitation->email,
                'password' => $password,
                'tenant_id' => $invitation->tenant_id,
                'role_id' => Role::where('name', 'Salesperson')->first()->id,
                'user_status_id' => UserStatus::where('name', 'Active')->first()->id,
            ]);

            $invitation->update([
                'invitation_status_id' => InvitationStatus::where('name', 'Accepted')->first()->id,
            ]);

            return $user;
        });
    }

    public function revoke(Invitation $invitation): void
    {
        $invitation->update([
            'invitation_status_id' => InvitationStatus::where('name', 'Revoked')->first()->id,
        ]);
    }
}
