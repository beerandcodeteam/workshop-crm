<?php

use App\Livewire\Forms\RegisterForm;
use App\Services\RegistrationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.guest')] #[Title('Criar conta')] class extends Component {
    public RegisterForm $form;

    public function register(RegistrationService $registrationService): void
    {
        $this->form->validate();

        $user = $registrationService->register(
            companyName: $this->form->company_name,
            userName: $this->form->name,
            email: $this->form->email,
            password: $this->form->password,
        );

        auth()->login($user);

        $this->redirect(route('kanban.index'));
    }
};
?>

<div>
    <h1 class="text-2xl font-bold text-primary-dark">
        Bem-vindo ao nosso CRM.
    </h1>
    <p class="mt-1 text-2xl font-bold text-primary-dark">
        Crie sua conta para começar.
    </p>
    <p class="mt-2 text-sm text-primary-grey">
        Preencha seus dados para continuar
    </p>

    <form wire:submit="register" class="mt-8 space-y-5">
        <x-input
            label="Nome da empresa"
            wire:model="form.company_name"
            placeholder="Sua empresa"
        />

        <x-input
            label="Nome completo"
            wire:model="form.name"
            placeholder="Seu nome"
        />

        <x-input
            label="E-mail"
            type="email"
            wire:model="form.email"
            placeholder="seu@email.com"
        />

        <x-input
            label="Senha"
            type="password"
            wire:model="form.password"
            placeholder="Mínimo 8 caracteres"
        />

        <x-input
            label="Confirmar senha"
            type="password"
            wire:model="form.password_confirmation"
            placeholder="Repita a senha"
        />

        <div class="flex items-center gap-3 pt-2">
            <x-button type="submit" size="lg">
                <span wire:loading.remove wire:target="register">Criar conta</span>
                <span wire:loading wire:target="register">Criando...</span>
            </x-button>

            <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium text-primary hover:underline">
                Já tenho conta
            </a>
        </div>
    </form>
</div>
