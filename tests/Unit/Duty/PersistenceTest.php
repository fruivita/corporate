<?php

use FruiVita\Corporate\Models\Duty;
use Illuminate\Support\Str;

test('cadastra múltiplas duties (funções)', function () {
    $amount = 30;

    Duty::factory()
        ->count($amount)
        ->create();

    expect(Duty::count())->toBe($amount);
});

test('duty (função) name em seu tamanho máximo é aceito', function () {
    Duty::factory()->create(['name' => Str::random(255)]);

    expect(Duty::count())->toBe(1);
});
