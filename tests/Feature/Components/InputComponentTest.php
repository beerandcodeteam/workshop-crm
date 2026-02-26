<?php

it('renders with label', function () {
    $view = $this->blade('<x-input label="Nome completo" name="name" />');

    $view->assertSee('Nome completo');
    $view->assertSee('name="name"', false);
});

it('displays error message from validation', function () {
    $view = $this->withViewErrors(['email' => 'O campo email é obrigatório.'])
        ->blade('<x-input label="Email" name="email" />');

    $view->assertSee('O campo email é obrigatório.');
    $view->assertSee('border-secondary-red');
});

it('renders with icon slot', function () {
    $view = $this->blade('
        <x-input label="Nome" name="name">
            <svg class="user-icon"></svg>
        </x-input>
    ');

    $view->assertSee('user-icon');
    $view->assertSee('pl-9');
});

it('renders success state', function () {
    $view = $this->blade('<x-input label="Email" name="email" :success="true" />');

    $view->assertSee('border-secondary-green');
});

it('renders default state without errors', function () {
    $view = $this->blade('<x-input label="Campo" name="field" />');

    $view->assertSee('border-outline');
    $view->assertDontSee('border-secondary-red');
});
