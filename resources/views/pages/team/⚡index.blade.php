<?php

use App\Livewire\Forms\InviteForm;
use App\Models\Invitation;
use App\Models\User;
use App\Services\InvitationService;
use App\Services\UserService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Equipe')] class extends Component {
    public InviteForm $form;

    public bool $showInviteModal = false;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    #[Computed]
    public function teamMembers()
    {
        return User::withoutGlobalScopes()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with(['role', 'userStatus'])
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function pendingInvitations()
    {
        return Invitation::whereHas('invitationStatus', fn ($q) => $q->where('name', 'Pending'))
            ->with('invitedBy')
            ->orderByDesc('created_at')
            ->get();
    }

    public function invite(InvitationService $invitationService): void
    {
        $this->authorize('create', Invitation::class);
        $this->form->validate();

        try {
            $invitationService->invite(auth()->user(), $this->form->email);
            $this->form->reset();
            $this->showInviteModal = false;
            session()->flash('success', 'Convite enviado com sucesso!');
        } catch (\InvalidArgumentException $e) {
            $this->addError('form.email', $e->getMessage());
        }
    }

    public function revoke(Invitation $invitation, InvitationService $invitationService): void
    {
        $this->authorize('revoke', $invitation);
        $invitationService->revoke($invitation);
        session()->flash('success', 'Convite revogado com sucesso.');
    }

    public function deactivate(User $target, UserService $userService): void
    {
        $this->authorize('deactivate', $target);
        $userService->deactivate($target);
        session()->flash('success', 'Membro desativado com sucesso.');
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-primary-dark">Membros da equipe</h2>
        <x-button wire:click="$set('showInviteModal', true)">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Convidar
        </x-button>
    </div>

    {{-- Team members table --}}
    <div class="overflow-hidden rounded-xl bg-bg-white shadow-sm">
        <table class="w-full">
            <thead>
                <tr class="border-b border-outline text-left text-xs font-medium text-primary-grey">
                    <th class="px-6 py-3">Nome</th>
                    <th class="px-6 py-3">E-mail</th>
                    <th class="px-6 py-3">Função</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->teamMembers as $member)
                    <tr wire:key="member-{{ $member->id }}" class="border-b border-outline last:border-0">
                        <td class="px-6 py-4 text-sm font-medium text-primary-dark">{{ $member->name }}</td>
                        <td class="px-6 py-4 text-sm text-primary-grey">{{ $member->email }}</td>
                        <td class="px-6 py-4">
                            <x-tag variant="{{ $member->isBusinessOwner() ? 'purple' : 'primary' }}">
                                {{ $member->role->name === 'Business Owner' ? 'Proprietário' : 'Vendedor' }}
                            </x-tag>
                        </td>
                        <td class="px-6 py-4">
                            <x-tag variant="{{ $member->userStatus->name === 'Active' ? 'green' : 'red' }}">
                                {{ $member->userStatus->name === 'Active' ? 'Ativo' : 'Inativo' }}
                            </x-tag>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($member->id !== auth()->id() && $member->userStatus->name === 'Active')
                                <x-button
                                    variant="danger"
                                    size="sm"
                                    x-on:click="if (confirm('Tem certeza que deseja desativar este membro?')) $wire.deactivate({{ $member->id }})"
                                >
                                    Desativar
                                </x-button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pending invitations --}}
    @if($this->pendingInvitations->isNotEmpty())
        <h2 class="mt-8 mb-4 text-lg font-semibold text-primary-dark">Convites pendentes</h2>
        <div class="overflow-hidden rounded-xl bg-bg-white shadow-sm">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-outline text-left text-xs font-medium text-primary-grey">
                        <th class="px-6 py-3">E-mail</th>
                        <th class="px-6 py-3">Enviado em</th>
                        <th class="px-6 py-3">Expira em</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->pendingInvitations as $invitation)
                        <tr wire:key="invitation-{{ $invitation->id }}" class="border-b border-outline last:border-0">
                            <td class="px-6 py-4 text-sm text-primary-dark">{{ $invitation->email }}</td>
                            <td class="px-6 py-4 text-sm text-primary-grey">{{ $invitation->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-primary-grey">{{ $invitation->expires_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <x-button
                                    variant="outline"
                                    size="sm"
                                    x-on:click="if (confirm('Tem certeza que deseja revogar este convite?')) $wire.revoke({{ $invitation->id }})"
                                >
                                    Revogar
                                </x-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Invite modal --}}
    <x-modal name="showInviteModal" maxWidth="md">
        <x-slot:title>Convidar membro</x-slot:title>

        <form wire:submit="invite" class="space-y-5">
            <p class="text-sm text-primary-grey">
                Envie um convite por e-mail para adicionar um novo vendedor à sua equipe.
            </p>

            <x-input
                label="E-mail"
                type="email"
                wire:model="form.email"
                placeholder="email@exemplo.com"
            />

            <div class="flex items-center justify-end gap-3">
                <x-button variant="outline" type="button" x-on:click="open = false">
                    Cancelar
                </x-button>
                <x-button type="submit">
                    <span wire:loading.remove wire:target="invite">Enviar convite</span>
                    <span wire:loading wire:target="invite">Enviando...</span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
