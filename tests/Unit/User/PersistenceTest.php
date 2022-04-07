<?php

use FruiVita\Corporate\Models\Department;
use FruiVita\Corporate\Models\Duty;
use FruiVita\Corporate\Models\Occupation;
use FruiVita\Corporate\Models\User;
use Illuminate\Support\Str;

test('cadastra múltiplos pessoas/usuários', function () {
    $amount = 30;

    User::factory()
        ->count($amount)
        ->create();

    expect(User::count())->toBe($amount);
});

test('campo do pessoa/usuário em seu tamanho máximo é aceito', function ($field, $length) {
    User::factory()->create([$field => Str::random($length)]);

    expect(User::count())->toBe(1);
})->with([
    ['name', 255],
    ['username', 20],
]);

test('nome é opcional', function () {
    User::factory()->create(['name' => null]);

    expect(User::count())->toBe(1);
});

test('cargo, função e lotação são opcionais', function ($field) {
    User::factory()->create([$field => null]);

    expect(User::count())->toBe(1);
})->with([
    'occupation_id',
    'duty_id',
    'department_id',
]);

test('uma pessoa/usuário possui um cargo, uma função e/ou uma lotação', function () {
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
