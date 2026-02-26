<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class RegisterForm extends Form
{
    #[Validate('required|min:2', as: 'nome da empresa')]
    public string $company_name = '';

    #[Validate('required|min:2', as: 'nome')]
    public string $name = '';

    #[Validate('required|email|unique:users,email', as: 'e-mail')]
    public string $email = '';

    #[Validate('required|min:8|confirmed', as: 'senha')]
    public string $password = '';

    public string $password_confirmation = '';
}
