<?php

it('renders options', function () {
    $view = $this->blade('
        <x-select label="Status" name="status">
            <option value="active">Ativo</option>
            <option value="inactive">Inativo</option>
        </x-select>
    ');

    $view->assertSee('Ativo');
    $view->assertSee('Inativo');
    $view->assertSee('name="status"', false);
});

it('renders with placeholder', function () {
    $view = $this->blade('
        <x-select label="Cargo" name="role" placeholder="Selecione um cargo">
            <option value="1">Proprietário</option>
        </x-select>
    ');

    $view->assertSee('Selecione um cargo');
});

it('renders with label', function () {
    $view = $this->blade('<x-select label="Etapa" name="stage" />');

    $view->assertSee('Etapa');
});

it('displays error state', function () {
    $view = $this->withViewErrors(['status' => 'Selecione um status.'])
        ->blade('<x-select label="Status" name="status" />');

    $view->assertSee('Selecione um status.');
    $view->assertSee('border-secondary-red');
});
