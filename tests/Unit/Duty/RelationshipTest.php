<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Duty;
use FruiVita\Corporate\Models\Person;

test('uma duty (função) possui vários persons (pessoas)', function () {
    $amount = 3;

    Duty::factory()
        ->has(Person::factory()->count($amount), 'persons')
        ->create();

    $duty = Duty::with('persons')->first();

    expect($duty->persons->random())->toBeInstanceOf(Person::class)
    ->and($duty->persons)->toHaveCount($amount);
});
