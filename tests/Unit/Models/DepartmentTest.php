<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Department;
use FruiVita\Corporate\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

// Exceptions
test('throws exception when trying to create departments in duplicate, that is, with equal ids', function () {
    expect(
        fn () => Department::factory()
                    ->count(2)
                    ->create(['id' => 10])
    )->toThrow(QueryException::class, 'Duplicate entry');
});

test('throws exception when trying to create department with invalid field', function ($field, $value, $message) {
    expect(
        fn () => Department::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['id',    'foo',             'Incorrect integer value'],  // non-convertible integer value
    ['id',    null,              'cannot be null'],           // required field
    ['name',  Str::random(256),  'Data too long for column'], // maximum 255 characters
    ['name',  null,              'cannot be null'],           // required field
    ['acronym', Str::random(51), 'Data too long for column'], // maximum 50 characters
    ['acronym', null,            'cannot be null'],           // required field
]);

test('throws exception when trying to define invalid relationship, that is, with non-existent parent department', function () {
    expect(
        fn () => Department::factory()->create(['parent_department' => 10])
    )->toThrow(QueryException::class, 'Cannot add or update a child row');
});

// Happy path
test('create multiple departments', function () {
    $amount = 30;

    Department::factory()
        ->count($amount)
        ->create();

    expect(Department::count())->toBe($amount);
});

test('department field in its maximum size is accepted', function ($field, $length) {
    Department::factory()->create([$field => Str::random($length)]);

    expect(Department::count())->toBe(1);
})->with([
    ['name', 255],
    ['acronym', 50],
]);

test('parent department is optional', function () {
    Department::factory()->create(['parent_department' => null]);

    expect(Department::count())->toBe(1);
});

test('one parent department has many child and the child has only one parent', function () {
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

test('one department has many users', function () {
    $amount = 3;

    Department::factory()
        ->has(User::factory()->count($amount), 'users')
        ->create();

    $department = Department::with(['users'])->first();

    expect($department->users->random())->toBeInstanceOf(User::class)
    ->and($department->users)->toHaveCount($amount);
});
