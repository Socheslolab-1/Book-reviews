<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use Illuminate\Database\Eloquent\Scope;

Route::get('/', function () {
   return redirect()->route('books.index');
});

Route::resource('books', BookController::class)
    ->only(['index', 'show']);


// Реєстрація ресурсу для маршруту "book-reviews", який використовує ReviewController.
// Маршрут обмежений лише методами "create" і "store".
// За допомогою методу "scoped" змінено поведінку параметра "review",
// тепер він пов'язаний з атрибутом "book" у моделі.

// Route::resource створює стандартні маршрути для ресурсного контролера.
// ->scoped(['review' => 'book']) дозволяє налаштувати параметр "review",
// щоб він підключався до іншого атрибута моделі (в даному випадку "book").
// ->only(['create', 'store']) обмежує маршрути лише двома методами:
// - create: показує форму для створення нового огляду.
// - store: обробляє POST-запит для збереження нового огляду в базі даних.

// Route::resource('book-reviews', ReviewController::class)
//     ->scoped(['review' => 'book'])
//     ->only(['create', 'store']);


Route::get('books/{book}/reviews/create', [ReviewController::class, 'create'])
    ->name('books.reviews.create');

Route::post('books/{book}/reviews', [ReviewController::class, 'store'])
    ->name('books.reviews.store')
    ->middleware('throttle:reviews'); // Додавання проміжного програмного забезпечення
