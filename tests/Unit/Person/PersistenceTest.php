<?php

use FruiVita\Corporate\Models\Department;
use FruiVita\Corporate\Models\Duty;
use FruiVita\Corporate\Models\Occupation;
use FruiVita\Corporate\Models\Person;
use Illuminate\Support\Str;

test('cadastra múltiplos pessoas', function () {
    $amount = 30;

    Person::factory()
        ->count($amount)
        ->create();

    expect(Person::count())->toBe($amount);
});

test('campo do pessoa em seu tamanho máximo é aceito', function ($field, $length) {
    Person::factory()->create([$field => Str::random($length)]);

    expect(Person::count())->toBe(1);
})->with([
    ['name', 255],
    ['username', 20],
]);

test('nome é opcional', function () {
    Person::factory()->create(['name' => null]);

    expect(Person::count())->toBe(1);
});

test('cargo, função e lotação são opcionais', function ($field) {
    Person::factory()->create([$field => null]);

    expect(Person::count())->toBe(1);
})->with([
    'occupation_id',
    'duty_id',
    'department_id',
]);

test('uma pessoa possui um cargo, uma função e/ou uma lotação', function () {
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
