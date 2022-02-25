<?php

use FruiVita\Corporate\Models\Occupation;
use FruiVita\Corporate\Models\Person;
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

test('um cargo possui vários pessoas', function () {
    $amount = 3;

    Occupation::factory()
        ->has(Person::factory()->count($amount), 'persons')
        ->create();

    $occupation = Occupation::with('persons')->first();

    expect($occupation->persons->random())->toBeInstanceOf(Person::class)
    ->and($occupation->persons)->toHaveCount($amount);
});
