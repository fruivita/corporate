<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Occupation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

test('lança exceção ao tentar cadastrar occupations (cargos) em duplicidade, isto é, com ids iguais', function () {
    expect(
        fn () => Occupation::factory()
            ->count(2)
            ->create(['id' => 10])
    )->toThrow(QueryException::class, 'Duplicate entry');
});

test('lança exceção ao tentar cadastrar occupation (cargo) com campo inválido', function ($field, $value, $message) {
    expect(
        fn () => Occupation::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['id',   'texto',          'Incorrect integer value'],  //valor não conversível em inteiro
    ['id',   null,             'cannot be null'],           //campo obrigatório
    ['name', Str::random(256), 'Data too long for column'], //campo aceita no máximo 255 caracteres
    ['name', null,             'cannot be null'],           //campo obrigatório
]);
