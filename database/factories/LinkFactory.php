<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\Post;
use App\Models\Poster;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Link::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'post_id' => Post::factory(['content' => 'ca']),
            'poster_id' => Poster::factory(),
            'site' => 'youtube',
            'resource_id' => 'dQw4w9WgXcQ',
            'title' => 'Rick Astley - Never Gonna Give You Up (Official Music Video)',
            'original' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ];
    }
}
