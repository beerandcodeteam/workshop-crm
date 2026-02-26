<?php

it('renders with title and body slots', function () {
    $view = $this->blade('
        <x-modal :show="true">
            <x-slot:title>Confirmação</x-slot:title>
            Tem certeza que deseja continuar?
        </x-modal>
    ');

    $view->assertSee('Confirmação');
    $view->assertSee('Tem certeza que deseja continuar?');
});

it('renders close button', function () {
    $view = $this->blade('<x-modal :show="true">Conteúdo</x-modal>');

    $view->assertSee('M6 18L18 6M6 6l12 12');
});

it('renders footer slot', function () {
    $view = $this->blade('
        <x-modal :show="true">
            Conteúdo do modal
            <x-slot:footer>
                <x-button variant="outline">Cancelar</x-button>
                <x-button>Confirmar</x-button>
            </x-slot:footer>
        </x-modal>
    ');

    $view->assertSee('Cancelar');
    $view->assertSee('Confirmar');
});
