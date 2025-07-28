<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder; // Долаємо псевдонім as QueryBuilder
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Book extends Model
{
    use HasFactory;

    // // Вказуємо захисне поле $fillable, яке дозволяє вказати, поля з якими можна працювати
    // protected $fillable = ['title', 'author'];

    // Робимо зв'язок з таблицей reviews, цей метод hasMany визначає зв'язок один до багатьох, тобто одна книга може мати багато відгуків
    public function reviews() {

        return $this->hasMany(Review::class);
    }

    // Метод для обробки локального запиту $query, він потребує хоча б одного аргументу, який є конструктором запитів $query та підказку Builder, щоб отримати пропозиції щодо доступних методів ми можемо вводити не лише аргументи підказки,  й тип повернення Builder, який є конструктором запитів
    public function scopeTitle(Builder  $query, string $title): Builder | QueryBuilder{ // Додаємо додаткові параметри string та title

        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    // Додаємо область для підрахунку відгуків
    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null) : Builder | QueryBuilder {
        // Повертаємо книгу в якої є найбільше відгуків, передаємо масив у якому зв'язок буде ключем, додаємо функцію тут fn(Builder $q) - функція стрілки(всередині неї є лише один вираз), яка використовує стенограму синтаксис, та визначаємо коструктор запитів $q та підказку Builder, та викликаємо функцію фільтрації дат dateRangeFillter($q, $from, $to)
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFillter($q, $from, $to)
        ]);
    }


     // Додаємо область для підрахунку найвищого рейтингу
    public function scopeWithAvgRating(Builder $query, $from = null, $to = null) : Builder | QueryBuilder {
        // Повертаємо книгу в якої є найбільше відгуків, передаємо масив у якому зв'язок буде ключем, додаємо функцію тут fn(Builder $q) - функція стрілки(всередині неї є лише один вираз), яка використовує стенограму синтаксис, та визначаємо коструктор запитів $q та підказку Builder, та викликаємо функцію фільтрації дат dateRangeFillter($q, $from, $to)
         // Повертаємо книгу у якої найвищий рейтинг(для цього використовуємо середній рейтинг avg)
        return  $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFillter($q,  $from, $to)
        ], 'rating');
    }

    // Додаємо нову область видимості scope локальних запитів, створюємо метод з найбільшої популярності книг, він потребує хоча б одного аргументу, який є конструктором запитів $query та підказку Builder,також додаємо два додаткові параметри повернення from = null, що означає необов'язковий параметр, та to = null,  тип повернення Builder, який є конструктором запитів або QueryBuilder, який використовується для більш специфічних запитів на рівні таблиць.
    public function scopePopular(Builder $query) : Builder | QueryBuilder {
        // Використовуємо створену область запитів
        return $query->withReviewsCount()
            ->orderBy('reviews_count','desc'); // Створюємо сповпець reviews_count(кількість відгуків) та відсортовуємо в порядку спадання
    }

    // Додаємо нову область видимості scope локальних запитів, створюємо метод з найвищим рейтенгом книг, він потребує хоча б одного аргументу, який є конструктором запитів $query та підказку Builder, тип, також додаємо два додаткові параметри повернення from = null, що означає необов'язковий параметр, та to = null, тип повернення Builder який є конструктором запитів тип повернення Builder, який є конструктором запитів  або псевдонім QueryBuilder
    public  function scopeHighestRated(Builder $query) : Builder | QueryBuilder {
        // Повертаємо книгу у якої найвищий рейтинг(для цього використовуємо середній рейтинг avg)
        return  $query->withAvgRating()
            ->orderBy('reviews_avg_rating', 'desc'); // Створюємо сповпець reviews_avg_rating(середній рейтинг) та відсортовуємо в порядку спадання
    }

    // Додаємо ще один метод, який показує результати, коли книги мають мінімальну суму відгуків, оскільки ми приймаємо певну кількість відгуків, то визначаємо int $minReviews як ціле число, він потребує хоча б одного аргументу, який є конструктором запитів $query та підказку Builder та тип повернення Builder, який є конструктором запитів або псевдонім QueryBuilder
    public function scopeMinReviews(Builder $query, int $minReviews) : Builder | QueryBuilder {
        // Використовуємо having оскільки ми працюємо з результатами агрегатних функцій база даних mysql, where використовується sqlite
        return $query->where('reviews_count', '>=', $minReviews);
    }

    // Створюємо приватний метод фільтрації дат визначаємо метод який отримує конструктор як аргумент $query та підказку Builde, та визначаємо 2 параметри $from, $to
    // query - це об'єкт, тому його не повертаємо, об'єкти передаються посиланням, а не копією
    private function dateRangeFillter(Builder $query, $from = null, $to = null) {
        // Фільтруємо відгуки
        if($from && !$to){
            $query->where('created_at', '>=', $from);
        } elseif(!$from && $to){
            $query->where('created_at', '<=', $from);
        } elseif($from && $to){
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    // Створюємо метод який реалізовує книги, які були популярні минулого місяця передаємо конструктор як аргумент $query та підказку Builder, та тип повернення Builder, який є конструктором запитів або псевдонім QueryBuilder
    public function scopePopularLastMonth(Builder $query) : Builder | QueryBuilder {
        // Викликаємо метод популярності книг, та створюємо нову дату(всередині цього метода) за допомогою метода now() та віднямаємо від неї місяць за допомогою метода subMonth(), далі знову реалізуємо метод now(), тобто отримуємо всі книги з минулого місяця до цього часу
        return $query->popular(now()->subMonth(), now())
            // Далі отримуємо найвищу оцінку
            ->highestRated(now()->subMonth(), now())
            // Далі реалізуємо мінімальні відгуки
            ->minReviews(2);
    }

    // Створюємо метод який реалізовує книги, які були популярні минулих 6 місяців передаємо конструктор як аргумент $query та підказку Builder, та тип повернення Builder, який є конструктором запитів або псевдонім QueryBuilder
    public function scopePopular6LastMonths(Builder $query) : Builder | QueryBuilder {
        // Викликаємо метод популярності книг, та створюємо нову дату(всередині цього метода) за допомогою метода now() та віднямаємо від неї 6 місяців за допомогою метода subMonths(6) та встановлюємо параметр 6 тобто останні 6 місяців, далі знову реалізуємо метод now(), тобто отримуємо всі книги з минулого місяця до цього часу
        return $query->popular(now()->subMonths(6), now())
            // Далі отримуємо найвищу оцінку за останні 6
            ->highestRated(now()->subMonths(6), now())
            // Далі реалізуємо мінімальні відгуки
            ->minReviews(5);
    }

    // Створюємо метод який реалізовує книги, які мали найвищий рейтинг  передаємо конструктор як аргумент $query та підказку Builder, та тип повернення Builder, який є конструктором запитів або псевдонім QueryBuilder
    public function scopeHighestRatedLastMonth(Builder $query) : Builder | QueryBuilder {
        // Викликаємо метод популярності книг, визначаємо метод найвищого рейтинга та створюємо нову дату(всередині цього метода) за допомогою метода now() та віднямаємо від неї місяць за допомогою метода subMonth(), далі знову реалізуємо метод now(), тобто отримуємо всі книги з минулого місяця до цього часу
        return $query->highestRated(now()->subMonth(), now())
            // Реалізуємо книги, які були популярні минулого місяця і зараз
            ->popular(now()->subMonth(), now())
            // Далі реалізуємо мінімальні відгуки
            ->minReviews(2);
    }

    // Створюємо метод який реалізовує книги, які мали найвищий рейтинг за останні 6 місяців,  передаємо конструктор як аргумент $query та підказку Builder, та тип повернення Builder, який є конструктором запитів або псевдонім QueryBuilder
    public function scopeHighestRated6LastMonth(Builder $query) : Builder | QueryBuilder {
        // Викликаємо метод популярності книг, визначаємо метод найвищого рейтинга та створюємо нову дату(всередині цього метода) за допомогою метода now() та віднямаємо від неї місяць за допомогою метода subMonths(6) та встановлюємо параметр 6 тобто останні 6 місяців, далі знову реалізуємо метод now(), тобто отримуємо всі книги з минулого місяця до цього часу
        return $query->highestRated(now()->subMonths(6), now())
            // Реалізуємо книги, які були популярні минулих 6 місяців і зараз
            ->popular(now()->subMonths(6), now())
            // Далі реалізуємо мінімальні відгуки
            ->minReviews(5);
    }

    // Виконуємо реагування на певні події моделі шляхом додавання обробників у методі booted.
    protected static function booted() {
        // Додаємо обробник події "updated" (оновлення моделі Book).
        // Коли модель Book оновлюється, виконується функція, яка видаляє кеш, пов'язаний із відповідною книгою.
        // Метод cache()->forget() видаляє кешовані дані для книги, ідентифіковані через її ID.
        // Це забезпечує, що книгуа буде оновлена при наступному запиті до кешу, гарантуючи актуальність інформації.
        static::updated(
            fn (Book $book) => cache()->forget('book:' . $book->id));

        // Додаємо обробник події "deleted" (видалення моделі Book).
        // Коли модель Book видаляється, виконується функція, яка видаляє кеш, пов'язаний із відповідною книгою.
        // Використовуємо cache()->forget() для очищення кешу за ключем 'book:{id книги}', щоб видалити старі або застарілі дані.
        static::deleted(
            fn (Book $book) => cache()->forget('book:' . $book->id));
    }
}
