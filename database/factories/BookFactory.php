<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // заповнюємо підробленими даними таблицю книг
            'title' => fake()->sentence(3),
            'author' => fake()->name,
            'created_at' => fake()->dateTimeBetween('-2 years'), // Вказуємо, що книги та рецензії були додані за останні 2 роки
            'updated_at' => function (array  $attributes) {
                return fake()->dateTimeBetween($attributes['created_at']); // Вказуємо, що оновлене оголошення є датою, яка є зараз
            }
        ];
    }
}
