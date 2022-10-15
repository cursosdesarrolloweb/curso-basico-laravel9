<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'title' => $this->faker->text(30),
            'content' => $this->faker->text,
            'user_id' => User::all()->random(1)->first()->id,
            'category_id' => Category::all()->random(1)->first()->id,
            'created_at' => now(),
        ];
    }
}
