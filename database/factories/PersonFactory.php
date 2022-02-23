<?php

namespace FruiVita\Corporate\Database\Factories;

use FruiVita\Corporate\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @see https://laravel.com/docs/8.x/database-testing
 * @see https://fakerphp.github.io/
 */
class PersonFactory extends Factory
{
    protected $model = Person::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'department_id' => null,
            'occupation_id' => null,
            'duty_id' => null,

            'name' => random_int(0, 1)
                        ? $this->faker->name()
                        : null,

            'username' => $this->faker->unique()->word(),
        ];
    }
}
