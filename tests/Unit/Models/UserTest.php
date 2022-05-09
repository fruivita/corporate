<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Department;
use FruiVita\Corporate\Models\Duty;
use FruiVita\Corporate\Models\Occupation;
use FruiVita\Corporate\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

// Exceptions
test('throws exception when trying to create users in duplicate, that is, with equal username', function () {
    expect(
        fn () => User::factory()
                    ->count(2)
                    ->create(['username' => 'aduser'])
    )->toThrow(QueryException::class, 'Duplicate entry');
});

test('throws exception when trying to create user with invalid field', function ($field, $value, $message) {
    expect(
        fn () => User::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['name',     Str::random(256), 'Data too long for column'], // maximum 255 characters
    ['username', Str::random(21),  'Data too long for column'], // maximum 20 characters
    ['username', null,             'cannot be null'],           // required field
]);

test('throws exception when trying to define invalid relationship, that is, with non-existent models', function ($field, $value, $message) {
    expect(
        fn () => User::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['occupation_id', 10, 'Cannot add or update a child row'],
    ['duty_id',       10, 'Cannot add or update a child row'],
    ['department_id', 10, 'Cannot add or update a child row'],
]);

// Happy path
test('create multiple users', function () {
    $amount = 30;

    User::factory()
        ->count($amount)
        ->create();

    expect(User::count())->toBe($amount);
});

test('user field in its maximum size is accepted', function ($field, $length) {
    User::factory()->create([$field => Str::random($length)]);

    expect(User::count())->toBe(1);
})->with([
    ['name', 255],
    ['username', 20],
]);

test('name is optional', function () {
    User::factory()->create(['name' => null]);

    expect(User::count())->toBe(1);
});

test('occupation, duty and e department are optional', function ($field) {
    User::factory()->create([$field => null]);

    expect(User::count())->toBe(1);
})->with([
    'occupation_id',
    'duty_id',
    'department_id',
]);

test('one user has one occupation, one duty and/or one  e/ou department', function () {
    $occupation = Occupation::factory()->create();
    $duty = Duty::factory()->create();
    $department = Department::factory()->create();

    $user = User::factory()
                ->for($occupation, 'occupation')
                ->for($duty, 'duty')
                ->for($department, 'department')
                ->create();

    $user->load(['occupation', 'duty', 'department']);

    expect($user->occupation)->toBeInstanceOf(Occupation::class)
    ->and($user->duty)->toBeInstanceOf(Duty::class)
    ->and($user->department)->toBeInstanceOf(Department::class);
});
