<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
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
            'book_id' => null,
            'review' => fake()->paragraph,
            'rating' => fake()->numberBetween(1, 5), // Використовуємо метод який генерує він 1 до 5
            'created_at' => fake()->dateTimeBetween('-2 years'), // Вказуємо, що книги та рецензії були додані за останні 2 роки
            'updated_at' => function (array  $attributes) {
                return fake()->dateTimeBetween($attributes['created_at']); // Вказуємо, що оновлене оголошення є датою, яка є зараз
            }
        ];
    }

    // Створюємо метод який генерує хороші відгуки
    public function good() {

        return $this->state(function (array $attributes) {
            return [
                'rating' => fake()->numberBetween(4, 5)
            ];
        }); // Встановлюємо метод стану, який приймає  функцію, яка приймає в собі атрибут масиву типів, атрибутів
    }

    // Створюємо метод який генерує середні відгуки
    public function avarage() {

        return $this->state(function (array $attributes) {
            return [
                'rating' => fake()->numberBetween(2, 5)
            ];
        }); // Встановлюємо метод стану, який приймає  функцію, яка приймає в собі атрибут масиву типів, атрибутів
    }

    // Створюємо метод який генерує погані відгуки
    public function bad() {

        return $this->state(function (array $attributes) {
            return [
                'rating' => fake()->numberBetween(1, 3)
            ];
        }); // Встановлюємо метод стану, який приймає  функцію, яка приймає в собі атрибут масиву типів, атрибутів
    }
}
