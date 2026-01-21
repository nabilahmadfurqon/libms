<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {

            // HAPUS index() supaya tidak duplikat
            $table->foreign('student_id')
                ->references('student_id')->on('students')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('book_id')
                ->references('book_id')->on('books')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('visits', function (Blueprint $table) {

            // HAPUS index() juga
            $table->foreign('student_id')
                ->references('student_id')->on('students')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['book_id']);
        });

        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
        });
    }
};
