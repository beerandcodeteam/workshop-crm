<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;

new #[Layout('layouts.guest')] #[Title('Entrar')] class extends Component {
    #[Validate('required|email', as: 'e-mail')]
    public string $email = '';

    #[Validate('required', as: 'senha')]
    public string $password = '';

    public function login(): void
    {
        $this->validate();

        $throttleKey = str()->lower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', "Muitas tentativas de login. Tente novamente em {$seconds} segundos.");

            return;
        }

        if (! auth()->attempt(['email' => $this->email, 'password' => $this->password])) {
            RateLimiter::hit($throttleKey);
            $this->addError('email', 'E-mail ou senha incorretos.');

            return;
        }

        if (auth()->user()->userStatus->name === 'Inactive') {
            auth()->logout();
            $this->addError('email', 'Sua conta está inativa. Entre em contato com o administrador.');

            return;
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        $this->redirect(route('kanban.index'));
    }
};
?>

<div>
    <h1 class="text-2xl font-bold text-primary-dark">
        Bem-vindo de volta.
    </h1>
    <p class="mt-1 text-2xl font-bold text-primary-dark">
        Acesse sua conta.
    </p>
    <p class="mt-2 text-sm text-primary-grey">
        Preencha seus dados para continuar
    </p>

    @session('status')
        <div class="mt-4 rounded-lg bg-secondary-green/10 px-4 py-3 text-sm text-secondary-green">
            {{ $value }}
        </div>
    @endsession

    <form wire:submit="login" class="mt-8 space-y-5">
        <x-input
            label="E-mail"
            type="email"
            wire:model="email"
            placeholder="seu@email.com"
        />

        <x-input
            label="Senha"
            type="password"
            wire:model="password"
            placeholder="Sua senha"
        />

        <div class="flex items-center justify-between pt-2">
            <x-button type="submit" size="lg">
                <span wire:loading.remove wire:target="login">Entrar</span>
                <span wire:loading wire:target="login">Entrando...</span>
            </x-button>

            <a href="{{ route('password.request') }}" wire:navigate class="text-sm font-medium text-primary hover:underline">
                Esqueci minha senha
            </a>
        </div>

        <p class="text-sm text-primary-grey">
            Não tem uma conta?
            <a href="{{ route('register') }}" wire:navigate class="font-medium text-primary hover:underline">
                Criar conta
            </a>
        </p>
    </form>
</div>
