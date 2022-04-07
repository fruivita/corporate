<?php

namespace FruiVita\Corporate\Database\Factories;

use FruiVita\Corporate\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @see https://laravel.com/docs/9.x/database-testing
 * @see https://fakerphp.github.io/
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    /**
     * {@inheritdoc}
     */
    public function definition()
    {
        return [
            'parent_department' => null,
            'id' => $this->faker->unique()->randomNumber(),
            'name' => $this->faker->company(),
            'acronym' => $this->faker->word(),
        ];
    }
}
