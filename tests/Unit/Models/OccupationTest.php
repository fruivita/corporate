<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Occupation;
use FruiVita\Corporate\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

// Exceptions
test('throws exception when trying to create occupations in duplicate, that is, with equal ids', function () {
    expect(
        fn () => Occupation::factory()
            ->count(2)
            ->create(['id' => 10])
    )->toThrow(QueryException::class, 'Duplicate entry');
});

test('throws exception when trying to create occupation with invalid field', function ($field, $value, $message) {
    expect(
        fn () => Occupation::factory()->create([$field => $value])
    )->toThrow(QueryException::class, $message);
})->with([
    ['id',   'foo',            'Incorrect integer value'],  // non-convertible integer value
    ['id',   null,             'cannot be null'],           // required field
    ['name', Str::random(256), 'Data too long for column'], // maximum 255 characters
    ['name', null,             'cannot be null'],           // required field
]);

// Happy path
test('create multiple occupations', function () {
    $amount = 30;

    Occupation::factory()
        ->count($amount)
        ->create();

    expect(Occupation::count())->toBe($amount);
});

test('occupation field in its maximum size is accepted', function () {
    Occupation::factory()->create(['name' => Str::random(255)]);

    expect(Occupation::count())->toBe(1);
});

test('one occupation has many users', function () {
    $amount = 3;

    Occupation::factory()
        ->has(User::factory()->count($amount), 'users')
        ->create();

    $occupation = Occupation::with('users')->first();

    expect($occupation->users->random())->toBeInstanceOf(User::class)
    ->and($occupation->users)->toHaveCount($amount);
});
