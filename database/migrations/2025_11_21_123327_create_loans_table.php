<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');          // tanpa FK (SQLite friendly)
            $table->string('book_id');
            $table->unsignedTinyInteger('days')->default(7);
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();

            $table->index('student_id');
            $table->index('book_id');
            $table->index('returned_at');
        });
    }
    public function down(): void {
        Schema::dropIfExists('loans');
    }
};
