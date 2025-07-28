<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Створюємо 100 книг,за допомогою класу Book, використовуючи метод factory
        Book::factory(33)->create()->each(function ($book){ //Визначаємо функцію each()зворотній виклик, який викликається для кожної моделі книги
            //Визначаємо випадкову кількість відгуків для книги
            $numberReviews = random_int(5, 30);

            Review::factory()->count($numberReviews) // Використовуємо метод count, для визначення моделей
                ->good() // Далі визначаємо  метод превизначення  стану good
                ->for($book) // Далі викликаємо метод for для зв'язки відгуків із книгою
                ->create(); // Далі викликаємо метод для create для створення нової моделі та забереження її в базі даних
        });

        // Створюємо 100 книг,за допомогою класу Book, використовуючи метод factory
        Book::factory(33)->create()->each(function ($book){ //Визначаємо функцію each()зворотній виклик, який викликається для кожної моделі книги
            //Визначаємо випадкову кількість відгуків для книги
            $numberReviews = random_int(5, 30);

            Review::factory()->count($numberReviews) // Використовуємо метод count, для визначення моделей
                ->average() // Далі визначаємо  метод превизначення  стану average
                ->for($book) // Далі викликаємо метод for для зв'язки відгуків із книгою
                ->create(); // Далі викликаємо метод для create для створення нової моделі та забереження її в базі даних
        });

        // Створюємо 100 книг,за допомогою класу Book, використовуючи метод factory
        Book::factory(34)->create()->each(function ($book){ //Визначаємо функцію each()зворотній виклик, який викликається для кожної моделі книги
            //Визначаємо випадкову кількість відгуків для книги
            $numberReviews = random_int(5, 30);

            Review::factory()->count($numberReviews) // Використовуємо метод count, для визначення моделей
                ->bad() // Далі визначаємо  метод превизначення  стану bad
                ->for($book) // Далі викликаємо метод for для зв'язки відгуків із книгою
                ->create(); // Далі викликаємо метод для create для створення нової моделі та забереження її в базі даних
        });
    }
}
