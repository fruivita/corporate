<?php

use FruiVita\Corporate\Models\Occupation;
use Illuminate\Support\Str;

test('cadastra múltiplos occupations (cargos)', function () {
    $amount = 30;

    Occupation::factory()
        ->count($amount)
        ->create();

    expect(Occupation::count())->toBe($amount);
});

test('occupation (cargo) name em seu tamanho máximo é aceito', function () {
    Occupation::factory()->create(['name' => Str::random(255)]);

    expect(Occupation::count())->toBe(1);
});
