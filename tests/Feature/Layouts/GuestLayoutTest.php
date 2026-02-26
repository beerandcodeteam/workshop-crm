<?php

use Illuminate\Support\HtmlString;

it('guest layout renders without auth', function () {
    $html = view('layouts.guest', [
        'slot' => new HtmlString('<p>Conteúdo de teste</p>'),
    ])->render();

    expect($html)->toContain('Conteúdo de teste');
});

it('guest layout has two-column design', function () {
    $html = view('layouts.guest', [
        'slot' => new HtmlString('Formulário'),
    ])->render();

    expect($html)
        ->toContain('lg:w-1/2')
        ->toContain('Workshop CRM');
});
