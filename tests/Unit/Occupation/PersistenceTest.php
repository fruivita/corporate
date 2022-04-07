<?php

use FruiVita\Corporate\Models\Occupation;
use FruiVita\Corporate\Models\User;
use Illuminate\Support\Str;

test('cadastra múltiplos cargos', function () {
    $amount = 30;

    Occupation::factory()
        ->count($amount)
        ->create();

    expect(Occupation::count())->toBe($amount);
});

test('nome do cargo em seu tamanho máximo é aceito', function () {
    Occupation::factory()->create(['name' => Str::random(255)]);

    expect(Occupation::count())->toBe(1);
});

test('um cargo possui vários pessoas/usuários', function () {
    $amount = 3;

    Occupation::factory()
        ->has(User::factory()->count($amount), 'users')
        ->create();

    $occupation = Occupation::with('users')->first();

    expect($occupation->users->random())->toBeInstanceOf(User::class)
    ->and($occupation->users)->toHaveCount($amount);
});
