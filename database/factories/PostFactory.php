<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Poster;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'poster_id' => Poster::factory(),
            'no' => (string) $this->faker->randomNumber(),
            'content' => '<a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">Link</a>',
            'inserted_at' => now()->toDateTimeString(),
        ];
    }
}
