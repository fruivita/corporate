<?php

namespace FruiVita\Corporate\Database\Factories;

use FruiVita\Corporate\Models\Duty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @see https://laravel.com/docs/9.x/database-testing
 * @see https://fakerphp.github.io/
 */
class DutyFactory extends Factory
{
    protected $model = Duty::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->randomNumber(),
            'name' => $this->faker->jobTitle(),
        ];
    }
}
