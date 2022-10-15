<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => $this->faker->text(30),
        ];
    }
}
