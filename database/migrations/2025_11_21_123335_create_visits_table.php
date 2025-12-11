<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('student_id');          // tanpa FK (SQLite friendly)
            $table->string('purpose')->nullable(); // baca/pinjam/kembali/tugas/dll
            $table->timestamps();

            $table->index('student_id');
            $table->index('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('visits');
    }
};
