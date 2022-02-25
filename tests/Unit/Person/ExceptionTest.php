<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Person;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

test('lança exceção ao tentar cadastrar pessoas em duplicidade, isto é, com siglas iguais', function () {
    expect(
        fn () => Person::factory()
                    ->count(2)
                    ->create(['username' => 'aduser'])
    )->toThrow(QueryException::class, 'Duplicate entry');
});

test('lança exceção ao tentar cadastrar pessoa com campo inválido', function ($field, $value, $message) {
    expect(
        fn () => Person::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['name',     Str::random(256), 'Data too long for column'], //campo aceita no máximo 255 caracteres
    ['username', Str::random(21),  'Data too long for column'], //campo aceita no máximo 20 caracteres
    ['username', null,             'cannot be null'],           //campo obrigatório
]);
