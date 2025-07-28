<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Cache; // Кешування

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // Передаємо об'єкт запиту Request
    {
        // Додаємо фільтрування за назвою книги за допомогою параметру заголовку, реалізуємо метод input('title') для введення запиту
        $title = $request->input('title');
        // Визанаємо фільтр, та перевіряємо чи він був вказаний за допомогою методу введення
        $filter = $request->input('filter', '');


        // Отримуємо список книг для цього заголовку
        // $books = Book::when($title, function ($query, $title) { // Якщо заголовок не є нульовим чи не порожнім, він запускає цю функція стрілки
        //     return $query->title($title);

        $books = Book::when($title,
        // Якщо заголовок не є нульовим чи не є порожнім, він запускає цю функція стрілки
            fn ($query, $title) => $query->title($title)
    );

    // Виконуємо сортування, використовуємо найновіший оператор доповнення php match() він дозволяє повертати значення, всередині нього ми застосовуємо відповідність того, що хочемо перевіряти, якщо значення фільтра було популярне минулого місяця, то запускаємо функцію стрілки книги, які були популярні минуго місяця
    $books = match ($filter) {
        // Визначаємо значення популярность минулого місяця
        'popular_last_month' => $books->popularLastMonth(),
        // Додаємо ще 4 випадки
        'popular_last_6month' => $books->popular6LastMonths(),
        'highest_rated_last_month' => $books->highestRatedLastMonth(),
        'highest_last_6months' => $books->highestRated6LastMonth(),
        // Якщо фільтр не відповідає жодному із варіантів додаємо фільтр за замовчуванням, завантажуємо середній рейтинг для всіх книг, та кількість відгуків
        default => $books->latest()->withAvgRating()->withReviewsCount()
    };
        // Виконуємо кожен запит окремо
        // $books = $books->get();

        // Генеруємо  ключ кешу, в якому генеруємо  фільтр та назву
        $cacheKey = 'books' . $filter . ':' . $title;
        // Кешуємо результати, використовуємо remember($cacheKey, 3600), задаємо параметр кешування ключ, задаємо 2 аргумент, як довго ми зберігаємо ці дані, 3 аргумент - це функція стрілки, яка поверне фактичні дані книги.
        // метод remember - якщо цей метод працює, він використовує драйвер кешу,  сервер Redis, і він побачить чи він вже містить цей ключ, якщо його немає то він запускає цю функцію fn() => $books->get(), виводимо дані з бази даних
        $books =
        cache()->remember(
            $cacheKey,
            3600,
            fn() =>
            $books->get());

        return view('books.index', ['books' => $books]); // Вказуємо назву так само як назву маршрутів, ['books' => $books] - заміняємо на виклик компактної функції, compact('books') він знаходить змінну з назвою книг та перетворить її на масив
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id) // Передаємо парамтр, який є цілим числом
    {
        // Створюємо змінну кешключ, який дорівнює 'book:' - ми маємо книгу, яку ми хешуємо за її ідентифікатором
        $cacheKey = 'book:' . $id;

        // Отримуємо відгуки, з кешу та виконуємо сортування відгуків
        // Кешуємо результати, використовуємо remember($cacheKey, 3600), задаємо параметр кешування ключ, вказуємо механізм кешування, задаємо 2 аргумент, як довго ми зберігаємо ці дані, 3 аргумент - це функція стрілки, яка поверне фактичні дані книги.
        // метод remember - якщо цей метод працює, він використовує драйвер кешу, це є файл чи сервер Redis, і він побачить чи він вже містить цей ключ, якщо його немає то він запускає цю функцію fn() => $books->get(), виводимо дані з бази даних
        $book = cache()->remember(
            $cacheKey,
            3600,
            fn() =>
            Book::with(
            ['reviews' => fn ($query) => $query->latest()  // Замість  метода load  завантаження моделі, або її зв'язків, використаємо статичний метод класу with - це спосіб завантажити усі відносини разом з моделлю, далі додаємо функцію стрілки з параметром запиту, далі використовуємо запит, який визначаємо останнім, використовуємо вбудовану функцію latest()
        ])->withAvgRating()->withReviewsCount()->findOrFail($id) // Викоористовуємо функцію середнього рейтингу, та підрахунок рейтингів, та далі використовуємо пошук книги, або невдачу ідентифікатора
    );
        // Повертаємо шаблон перегляду книг
        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
