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
            'has_music' => true,
            'content' => '<iframe data-src="https://www.youtube.com/embed/CpxQPlNP-nk?wmode=transparent"></iframe>',
            'inserted_at' => now()->toDateTimeString(),
        ];
    }
}
