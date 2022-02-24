<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Department;
use FruiVita\Corporate\Models\Duty;
use FruiVita\Corporate\Models\Occupation;
use FruiVita\Corporate\Models\Person;
use Illuminate\Database\QueryException;

test('occupation (cargo), duty (função) e department (lotação) são opcionais', function ($field) {
    Person::factory()->create([$field => null]);

    expect(Person::count())->toBe(1);
})->with([
    'occupation_id',
    'duty_id',
    'department_id',
]);

test('um person (pessoa) possui uma occupation (cargo), uma duty (função) e/ou um department (lotação)', function () {
    $occupation = Occupation::factory()->create();
    $duty = Duty::factory()->create();
    $department = Department::factory()->create();

    $person = Person::factory()
                ->for($occupation, 'occupation')
                ->for($duty, 'duty')
                ->for($department, 'department')
                ->create();

    $person->load(['occupation', 'duty', 'department']);

    expect($person->occupation)->toBeInstanceOf(Occupation::class)
    ->and($person->duty)->toBeInstanceOf(Duty::class)
    ->and($person->department)->toBeInstanceOf(Department::class);
});

test('lança exceção ao tentar definir relacionamento inválido', function ($field, $value, $message) {
    expect(
        fn () => Person::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['occupation_id', 10, 'Cannot add or update a child row'], //inexistente
    ['duty_id',       10, 'Cannot add or update a child row'], //inexistente
    ['department_id', 10, 'Cannot add or update a child row'], //inexistente
]);
