<?php

use App\Livewire\Forms\RegisterInvitedForm;
use App\Models\Invitation;
use App\Services\InvitationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.guest')] #[Title('Aceitar convite')] class extends Component {
    public RegisterInvitedForm $form;

    public ?Invitation $invitation = null;

    public string $email = '';

    public string $tenantName = '';

    public bool $invalidToken = false;

    public string $errorMessage = '';

    public function mount(string $token): void
    {
        $this->invitation = Invitation::withoutGlobalScopes()
            ->where('token', $token)
            ->with(['invitationStatus', 'tenant'])
            ->first();

        if (! $this->invitation) {
            $this->invalidToken = true;
            $this->errorMessage = 'Convite inválido.';

            return;
        }

        if ($this->invitation->invitationStatus->name !== 'Pending') {
            $this->invalidToken = true;
            $this->errorMessage = 'Este convite já foi utilizado ou revogado.';

            return;
        }

        if ($this->invitation->expires_at->isPast()) {
            $this->invalidToken = true;
            $this->errorMessage = 'Este convite expirou.';

            return;
        }

        $this->email = $this->invitation->email;
        $this->tenantName = $this->invitation->tenant->name;
    }

    public function register(InvitationService $invitationService): void
    {
        $this->form->validate();

        try {
            $user = $invitationService->acceptInvitation(
                token: $this->invitation->token,
                name: $this->form->name,
                password: $this->form->password,
            );

            auth()->login($user);

            $this->redirect(route('kanban.index'));
        } catch (\InvalidArgumentException $e) {
            $this->addError('form.name', $e->getMessage());
        }
    }
};
?>

<div>
    @if($invalidToken)
        <h1 class="text-2xl font-bold text-primary-dark">
            Convite indisponível
        </h1>
        <p class="mt-2 text-sm text-secondary-red">
            {{ $errorMessage }}
        </p>
        <div class="mt-6">
            <x-button href="{{ route('login') }}" wire:navigate>
                Ir para o login
            </x-button>
        </div>
    @else
        <h1 class="text-2xl font-bold text-primary-dark">
            Aceitar convite
        </h1>
        <p class="mt-1 text-2xl font-bold text-primary-dark">
            Junte-se à {{ $tenantName }}.
        </p>
        <p class="mt-2 text-sm text-primary-grey">
            Preencha seus dados para criar sua conta
        </p>

        <form wire:submit="register" class="mt-8 space-y-5">
            <x-input
                label="E-mail"
                type="email"
                :value="$email"
                disabled
            />

            <x-input
                label="Nome completo"
                wire:model="form.name"
                placeholder="Seu nome"
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

            <div class="pt-2">
                <x-button type="submit" size="lg">
                    <span wire:loading.remove wire:target="register">Criar conta</span>
                    <span wire:loading wire:target="register">Criando...</span>
                </x-button>
            </div>
        </form>
    @endif
</div>
