<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

new #[Layout('layouts.guest')] #[Title('Redefinir senha')] class extends Component {
    public string $token = '';

    #[Validate('required|email', as: 'e-mail')]
    public string $email = '';

    #[Validate('required|min:8|confirmed', as: 'senha')]
    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', 'Sua senha foi redefinida com sucesso.');
            $this->redirect(route('login'));

            return;
        }

        $this->addError('email', match ($status) {
            Password::INVALID_USER => 'Não encontramos um usuário com esse e-mail.',
            Password::INVALID_TOKEN => 'O link de redefinição é inválido ou expirou.',
            Password::RESET_THROTTLED => 'Aguarde antes de tentar novamente.',
            default => 'Não foi possível redefinir a senha. Tente novamente.',
        });
    }
};
?>

<div>
    <h1 class="text-2xl font-bold text-primary-dark">
        Redefinir senha
    </h1>
    <p class="mt-2 text-sm text-primary-grey">
        Escolha uma nova senha para sua conta.
    </p>

    <form wire:submit="resetPassword" class="mt-8 space-y-5">
        <x-input
            label="E-mail"
            type="email"
            wire:model="email"
            placeholder="seu@email.com"
        />

        <x-input
            label="Nova senha"
            type="password"
            wire:model="password"
            placeholder="Mínimo 8 caracteres"
        />

        <x-input
            label="Confirmar nova senha"
            type="password"
            wire:model="password_confirmation"
            placeholder="Repita a nova senha"
        />

        <div class="pt-2">
            <x-button type="submit" size="lg">
                <span wire:loading.remove wire:target="resetPassword">Redefinir senha</span>
                <span wire:loading wire:target="resetPassword">Redefinindo...</span>
            </x-button>
        </div>
    </form>
</div>
