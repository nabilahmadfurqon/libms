<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->string('book_id')->unique();     // kode buku / ID katalog
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('category')->nullable();
            $table->string('isbn')->nullable();
            $table->string('barcode')->nullable()->unique();

            $table->unsignedInteger('total_copies')->default(0);
            $table->unsignedInteger('available_copies')->default(0);

            $table->timestamps();

            // indeks bantu pencarian
            $table->index(['title']);
            $table->index(['author']);
            $table->index(['category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
