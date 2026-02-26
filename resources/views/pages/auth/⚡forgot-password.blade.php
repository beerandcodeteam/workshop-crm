<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Password;

new #[Layout('layouts.guest')] #[Title('Esqueci minha senha')] class extends Component {
    #[Validate('required|email', as: 'e-mail')]
    public string $email = '';

    public ?string $successMessage = null;

    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->successMessage = 'Enviamos um link de redefinição de senha para o seu e-mail.';
            $this->reset('email');

            return;
        }

        $this->addError('email', match ($status) {
            Password::INVALID_USER => 'Não encontramos um usuário com esse e-mail.',
            Password::RESET_THROTTLED => 'Aguarde antes de tentar novamente.',
            default => 'Não foi possível enviar o link. Tente novamente.',
        });
    }
};
?>

<div>
    <h1 class="text-2xl font-bold text-primary-dark">
        Esqueceu sua senha?
    </h1>
    <p class="mt-2 text-sm text-primary-grey">
        Informe seu e-mail e enviaremos um link para redefinir sua senha.
    </p>

    @if($successMessage)
        <div class="mt-4 rounded-lg bg-secondary-green/10 px-4 py-3 text-sm text-secondary-green">
            {{ $successMessage }}
        </div>
    @endif

    <form wire:submit="sendResetLink" class="mt-8 space-y-5">
        <x-input
            label="E-mail"
            type="email"
            wire:model="email"
            placeholder="seu@email.com"
        />

        <div class="flex items-center gap-3 pt-2">
            <x-button type="submit" size="lg">
                <span wire:loading.remove wire:target="sendResetLink">Enviar link</span>
                <span wire:loading wire:target="sendResetLink">Enviando...</span>
            </x-button>

            <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium text-primary hover:underline">
                Voltar ao login
            </a>
        </div>
    </form>
</div>
