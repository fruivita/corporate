<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

test('lança exceção ao tentar cadastrar pessoas/usuários em duplicidade, isto é, com siglas iguais', function () {
    expect(
        fn () => User::factory()
                    ->count(2)
                    ->create(['username' => 'aduser'])
    )->toThrow(QueryException::class, 'Duplicate entry');
});

test('lança exceção ao tentar cadastrar pessoa/usuário com campo inválido', function ($field, $value, $message) {
    expect(
        fn () => User::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['name',     Str::random(256), 'Data too long for column'], // campo aceita no máximo 255 caracteres
    ['username', Str::random(21),  'Data too long for column'], // campo aceita no máximo 20 caracteres
    ['username', null,             'cannot be null'],           // campo obrigatório
]);

test('lança exceção ao tentar definir relacionamento inválido', function ($field, $value, $message) {
    expect(
        fn () => User::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['occupation_id', 10, 'Cannot add or update a child row'], // inexistente
    ['duty_id',       10, 'Cannot add or update a child row'], // inexistente
    ['department_id', 10, 'Cannot add or update a child row'], // inexistente
]);
