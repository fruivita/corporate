<?php

/**
 * @see https://pestphp.com/docs/
 */

use FruiVita\Corporate\Models\Occupation;
use FruiVita\Corporate\Models\Person;

test('uma cargo possui vários pessoas', function () {
    $amount = 3;

    Occupation::factory()
        ->has(Person::factory()->count($amount), 'persons')
        ->create();

    $occupation = Occupation::with('persons')->first();

    expect($occupation->persons->random())->toBeInstanceOf(Person::class)
    ->and($occupation->persons)->toHaveCount($amount);
});
