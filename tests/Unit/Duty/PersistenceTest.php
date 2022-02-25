<?php

use FruiVita\Corporate\Models\Duty;
use FruiVita\Corporate\Models\Person;
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

test('uma função possui várias pessoas', function () {
    $amount = 3;

    Duty::factory()
        ->has(Person::factory()->count($amount), 'persons')
        ->create();

    $duty = Duty::with('persons')->first();

    expect($duty->persons->random())->toBeInstanceOf(Person::class)
    ->and($duty->persons)->toHaveCount($amount);
});
