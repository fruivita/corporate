<?php

use FruiVita\Corporate\Models\Department;
use FruiVita\Corporate\Models\Person;
use Illuminate\Support\Str;

test('cadastra múltiplas lotações', function () {
    $amount = 30;

    Department::factory()
        ->count($amount)
        ->create();

    expect(Department::count())->toBe($amount);
});

test('campo da lotação em seu tamanho máximo é aceito', function ($field, $length) {
    Department::factory()->create([$field => Str::random($length)]);

    expect(Department::count())->toBe(1);
})->with([
    ['name', 255],
    ['acronym', 50],
]);

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

test('uma lotação possui várias pessoas', function () {
    $amount = 3;

    Department::factory()
        ->has(Person::factory()->count($amount), 'persons')
        ->create();

    $department = Department::with(['persons'])->first();

    expect($department->persons->random())->toBeInstanceOf(Person::class)
    ->and($department->persons)->toHaveCount($amount);
});
