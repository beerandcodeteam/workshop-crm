<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invitation $invitation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tenantName = $this->invitation->tenant->name;
        $url = route('register.invited', $this->invitation->token);

        return (new MailMessage)
            ->subject("Você foi convidado para {$tenantName}")
            ->greeting('Olá!')
            ->line("Você foi convidado para fazer parte da equipe **{$tenantName}** no Workshop CRM.")
            ->action('Aceitar convite', $url)
            ->line('Este convite expira em 72 horas.')
            ->salutation('Atenciosamente, equipe Workshop CRM');
    }
}
