<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class RegisterInvitedForm extends Form
{
    #[Validate('required|min:2', as: 'nome')]
    public string $name = '';

    #[Validate('required|min:8|confirmed', as: 'senha')]
    public string $password = '';

    public string $password_confirmation = '';
}
