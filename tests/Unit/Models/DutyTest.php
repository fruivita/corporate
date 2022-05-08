<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Duty;
use FruiVita\Corporate\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

// Exception
test('throws exception when trying to create duties in duplicate, that is, with equal ids', function () {
    expect(
        fn () => Duty::factory()
                    ->count(2)
                    ->create(['id' => 10])
    )->toThrow(QueryException::class, 'Duplicate entry');
});

test('throws exception when trying to create duty with invalid field', function ($field, $value, $message) {
    expect(
        fn () => Duty::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['id',   'foo',            'Incorrect integer value'],  // non-convertible integer value
    ['id',   null,             'cannot be null'],           // required field
    ['name', Str::random(256), 'Data too long for column'], // maximum 255 characters
    ['name', null,             'cannot be null'],           // required field
]);

// Happy path
test('create multiple duties', function () {
    $amount = 30;

    Duty::factory()
        ->count($amount)
        ->create();

    expect(Duty::count())->toBe($amount);
});

test('duty field in its maximum size is accepted', function () {
    Duty::factory()->create(['name' => Str::random(255)]);

    expect(Duty::count())->toBe(1);
});

test('one duty has many users', function () {
    $amount = 3;

    Duty::factory()
        ->has(User::factory()->count($amount), 'users')
        ->create();

    $duty = Duty::with('users')->first();

    expect($duty->users->random())->toBeInstanceOf(User::class)
    ->and($duty->users)->toHaveCount($amount);
});
