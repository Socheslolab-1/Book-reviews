<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Створили зовнішній ключ book_id
            // $table->unsignedBigInteger('book_id');

            $table->text('review');
            $table->unsignedBigInteger('rating');

            $table->timestamps();

            // Робимо посилання зовнішнього ключа до таблиці книги(додаємо обробник видалення onDelete(), вказавши що він буде каскадним, вказує видалення, коли книгу буде видалено)
            // $table->foreign('book_id')->references('id')->on('books')
            //      ->onDelete('cascade');

            // Використовуємо коротший синтаксис, додаємо зовнішній ідентифікатор foreignId та його назву book_id, далі викликаємо метод обмеження constrained, який автоматично налаштує обмеження зовнішнього ключа, далі просто викликаємо метод cascadeDelete() при видаленя
            $table->foreignId('book_id')->constrained()
                ->cascadeDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
