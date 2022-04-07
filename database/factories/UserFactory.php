<?php

namespace FruiVita\Corporate\Database\Factories;

use FruiVita\Corporate\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @see https://laravel.com/docs/9.x/database-testing
 * @see https://fakerphp.github.io/
 */
class UserFactory extends Factory
{
    protected $model = User::class;

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
