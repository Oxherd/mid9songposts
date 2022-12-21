<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Poster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'post_id' => Post::factory(),
            'poster_id' => Poster::factory(),
            'content' => $this->faker->text(),
            'inserted_at' => now(),
        ];
    }
}
