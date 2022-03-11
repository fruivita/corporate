<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Department;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

test('lança exceção ao tentar cadastrar lotações em duplicidade, isto é, com ids iguais', function () {
    expect(
        fn () => Department::factory()
                    ->count(2)
                    ->create(['id' => 10])
    )->toThrow(QueryException::class, 'Duplicate entry');
});

test('lança exceção ao tentar cadastrar lotação com campo inválido', function ($field, $value, $message) {
    expect(
        fn () => Department::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['id',    'texto',           'Incorrect integer value'],  // valor não conversível em inteiro
    ['id',    null,              'cannot be null'],           // campo obrigatório
    ['name',  Str::random(256),  'Data too long for column'], // campo aceita no máximo 255 caracteres
    ['name',  null,              'cannot be null'],           // campo obrigatório
    ['acronym', Str::random(51), 'Data too long for column'], // campo aceita no máximo 50 caracteres
    ['acronym', null,            'cannot be null'],           // campo obrigatório
]);

test('lança exceção ao tentar definir relacionamento inválido, isto é, com lotação pai inexistente', function () {
    expect(
        fn () => Department::factory()->create(['parent_department' => 10])
    )->toThrow(QueryException::class, 'Cannot add or update a child row');
});
