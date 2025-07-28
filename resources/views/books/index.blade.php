@extends('layouts.app')

@section('content')
    <h1 class="mb-10 text-2x1">Books</h1>

    <form method="GET" action="{{ route('books.index') }}" class="mb-4 flex items-center space-x-2">
        <input type="text" name="title" placeholder="Search by title" {{--Додаємо заповнювач--}}
        value="{{ request('title') }}" class="input h-10"> {{--Додаємо значення, яке було відправлене раніше, використовуємо функцію назви запиту--}}
        {{--Додаємо прихований тип запиту--}}
        <input type="hidden" name="filter" value="{{ request('filter') }}"/> {{--Надаємо фільтр запиту--}}
        <button type="submit" class="btn h-10">Search</button>
        <a href="{{ route('books.index') }}" class="btn h-10">Clear</a>
    </form>

    {{--Додаємо вкладки вибору популярності книг--}}
    <div class="filter-container mb-4 flex">
        @php
        // Визначаємо фільтри за допомогою директиви @php
            $filters = [
                '' => 'Latest',
                'popular_last_month' => 'Popular Last Month',
                'popular_last_6month' => 'Popular Last 6 Months',
                'highest_rated_last_month' => 'Highest Rated Last Month',
                'highest_last_6months' => 'Highest Rated Last 6 Months',
            ];
        @endphp

        {{-- Генеруємо список фільтрів --}}
        @foreach ($filters as $key => $label)
            <a href="{{ route('books.index', [...request()->query(), 'filter' => $key]) }}", class="{{ request('filter') === $key || ( request('filter') === null && $key == '') ? 'filter-item-active' : 'filter-item'}}"> {{--Використовуємо помічник запиту, який надає доступ до запиту, це суворо дорівнює ключу, який буде частиною форми--}}
                {{ $label }}
            </a>
        @endforeach
    </div>


    <ul>
        @forelse ($books as $book)
        <li class="mb-4">
            <div class="book-item">
              <div
                class="flex flex-wrap items-center justify-between">
                <div class="w-full flex-grow sm:w-auto">
                  <a href="{{ route('books.show', $book) }}" class="book-title">{{ $book->title }}</a>
                  <span class="book-author">by {{ $book->author }}</span>
                </div>
                <div>
                  <div class="book-rating">
                    {{ number_format($book->reviews_avg_rating, 1) }}
                    {{--Використання компоненту рейтингу передаємо рейтинг з класу та значення $book->reviews_avg_ratingг--}}
                    <x-star-rating :rating="$book->reviews_avg_rating"/>
                  </div>
                  <div class="book-review-count">
                    out of {{ number_format($book->reviews_count)}} {{ Str::plural('review', $book->reviews_count) }} {{--Використовуємо plural метод до нього передаємо відгуки(якщо кількість відгуків буде одиничною, він відоразить відгук, а якщо буде множинне цей метод перетворить його на форму множини)--}}
                  </div>
                </div>
              </div>
            </div>
          </li>
        @empty
            <li class="mb-4">
                <div class="empty-book-item">
                    <p class="empty-text">No books found</p>
                    <a href="{{ route('books.index') }}" class="reset-link">Reset criteria</a>
                </div>
            </li>
        @endforelse
    </ul>
@endsection
