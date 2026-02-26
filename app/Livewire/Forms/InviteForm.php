<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class InviteForm extends Form
{
    #[Validate('required|email', as: 'e-mail')]
    public string $email = '';
}
