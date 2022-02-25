<?php

use FruiVita\Corporate\Models\Duty;
use Illuminate\Support\Str;

test('cadastra múltiplas funções', function () {
    $amount = 30;

    Duty::factory()
        ->count($amount)
        ->create();

    expect(Duty::count())->toBe($amount);
});

test('nome da função em seu tamanho máximo é aceito', function () {
    Duty::factory()->create(['name' => Str::random(255)]);

    expect(Duty::count())->toBe(1);
});
