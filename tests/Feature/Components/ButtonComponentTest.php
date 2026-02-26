<?php

it('renders primary variant by default', function () {
    $view = $this->blade('<x-button>Clique aqui</x-button>');

    $view->assertSee('Clique aqui');
    $view->assertSee('bg-primary');
});

it('renders outline variant', function () {
    $view = $this->blade('<x-button variant="outline">Outline</x-button>');

    $view->assertSee('Outline');
    $view->assertSee('border-primary');
});

it('renders danger variant', function () {
    $view = $this->blade('<x-button variant="danger">Excluir</x-button>');

    $view->assertSee('Excluir');
    $view->assertSee('bg-secondary-red');
});

it('renders success variant', function () {
    $view = $this->blade('<x-button variant="success">Salvar</x-button>');

    $view->assertSee('Salvar');
    $view->assertSee('bg-secondary-green');
});

it('renders disabled state', function () {
    $view = $this->blade('<x-button :disabled="true">Desabilitado</x-button>');

    $view->assertSee('disabled');
});

it('renders with icon slot', function () {
    $view = $this->blade('<x-button><svg class="icon"></svg> Com Icone</x-button>');

    $view->assertSee('Com Icone');
    $view->assertSee('icon');
});

it('renders as link when href is provided', function () {
    $view = $this->blade('<x-button href="/dashboard">Dashboard</x-button>');

    $view->assertSee('href="/dashboard"', false);
    $view->assertSee('Dashboard');
});

it('renders different sizes', function (string $size, string $expectedClass) {
    $view = $this->blade('<x-button :size="$size">Botão</x-button>', ['size' => $size]);

    $view->assertSee($expectedClass);
})->with([
    'small' => ['sm', 'px-3'],
    'medium' => ['md', 'px-5'],
    'large' => ['lg', 'px-7'],
]);
