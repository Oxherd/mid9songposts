<?php

namespace Database\Factories;

use App\Models\Poster;
use Illuminate\Database\Eloquent\Factories\Factory;

class PosterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Poster::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account' => $this->faker->userName,
            'name' => $this->faker->name(),
        ];
    }
}
