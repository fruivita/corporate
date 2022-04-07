<?php

use FruiVita\Corporate\Models\Duty;
use FruiVita\Corporate\Models\User;
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

test('uma função possui várias pessoas/usuários', function () {
    $amount = 3;

    Duty::factory()
        ->has(User::factory()->count($amount), 'users')
        ->create();

    $duty = Duty::with('users')->first();

    expect($duty->users->random())->toBeInstanceOf(User::class)
    ->and($duty->users)->toHaveCount($amount);
});
