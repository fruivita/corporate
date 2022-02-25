<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Department;
use FruiVita\Corporate\Models\Person;
use Illuminate\Database\QueryException;

test('lança exceção ao tentar definir relacionamento inválido, isto é, com lotação pai inexistente', function () {
    expect(
        fn () => Department::factory()->create(['parent_department' => 10])
    )->toThrow(QueryException::class, 'Cannot add or update a child row');
});

test('lotação pai é opcional', function () {
    Department::factory()->create(['parent_department' => null]);

    expect(Department::count())->toBe(1);
});

test('lotação pai tem várias filhas e a filha tem apenas um pai', function () {
    $amount_child = 3;
    $id_parent = 1000000;

    Department::factory()->create(['id' => $id_parent]);

    Department::factory()
        ->count($amount_child)
        ->create(['parent_department' => $id_parent]);

    $parent = Department::with(['childDepartments', 'parentDepartment'])
            ->find($id_parent);
    $child = Department::with(['childDepartments', 'parentDepartment'])
                ->where('parent_department', '=', $id_parent)
                ->get()
                ->random();

    expect($parent->childDepartments)->toHaveCount($amount_child)
    ->and($parent->parentDepartment)->toBeNull()
    ->and($child->parentDepartment->id)->toBe($parent->id)
    ->and($child->childDepartments)->toHaveCount(0);
});

test('um lotação possui várias pessoas', function () {
    $amount = 3;

    Department::factory()
        ->has(Person::factory()->count($amount), 'persons')
        ->create();

    $department = Department::with(['persons'])->first();

    expect($department->persons->random())->toBeInstanceOf(Person::class)
    ->and($department->persons)->toHaveCount($amount);
});
