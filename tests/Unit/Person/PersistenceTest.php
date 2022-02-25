<?php

use FruiVita\Corporate\Models\Person;
use Illuminate\Support\Str;

test('cadastra múltiplos pessoas', function () {
    $amount = 30;

    Person::factory()
        ->count($amount)
        ->create();

    expect(Person::count())->toBe($amount);
});

test('campo do pessoa em seu tamanho máximo é aceito', function ($field, $length) {
    Person::factory()->create([$field => Str::random($length)]);

    expect(Person::count())->toBe(1);
})->with([
    ['name', 255],
    ['username', 20],
]);

test('nome é opcional', function () {
    Person::factory()->create(['name' => null]);

    expect(Person::count())->toBe(1);
});
