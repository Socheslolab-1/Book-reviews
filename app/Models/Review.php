<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Вказуємо захисне поле $fillable, яке дозволяє вказати, поля з якими можна працювати
    protected $fillable = ['review', 'rating'];

    public function book() {
        // Визначаємо метод belongsTo() - (Це належить), належить до однієї книги
        return $this->belongsTo(Book::class);
    }

    // Виконуємо реагування на певні події моделі шляхом додавання обробників у методі booted.
    protected static function booted() {
        // Додаємо обробник події "updated" (оновлення моделі Review).
        // Коли модель Review оновлюється, виконується функція, яка видаляє кеш, пов'язаний із відповідною книгою.
        // Метод cache()->forget() видаляє кешовані дані для книги, ідентифіковані через її ID.
        // Це забезпечує, що дані про книгу будуть оновлені при наступному запиті до кешу, гарантуючи актуальність інформації.
        static::updated(fn (Review $review) => cache()->forget('book:' . $review->book->id));

        // Додаємо обробник події "deleted" (видалення моделі Review).
        // Коли модель Review видаляється, виконується функція, яка видаляє кеш, пов'язаний із відповідною книгою.
        // Використовуємо cache()->forget() для очищення кешу за ключем 'book:{id книги}', щоб видалити старі або застарілі дані.
        static::deleted(fn (Review $review) => cache()->forget('book:' . $review->book->id));

        // Додаємо обробник події "created" (створення моделі Review).
        // Коли модель Review створюється, виконується функція, яка видаляє кеш, пов'язаний із відповідною книгою.
        // Метод cache()->forget() видаляє кешовані дані для книги, ідентифіковані через її ID.
        // Це забезпечує, що дані про книгу будуть створені при наступному запиті до кешу, гарантуючи актуальність інформації.
        static::created(fn (Review $review) => cache()->forget('book:' . $review->book->id));
    }


}
