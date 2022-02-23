<?php

namespace FruiVita\Corporate\Database\Factories;

use FruiVita\Corporate\Models\Occupation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @see https://laravel.com/docs/8.x/database-testing
 * @see https://fakerphp.github.io/
 */
class OccupationFactory extends Factory
{
    protected $model = Occupation::class;

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
