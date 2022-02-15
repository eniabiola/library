<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
                'title' => $this->faker->text(10),
                'description' => $this->faker->text(200),
                'publisher_id' => \App\Models\Publisher::query()->inRandomOrder()->first()->id
        ];
    }
}
