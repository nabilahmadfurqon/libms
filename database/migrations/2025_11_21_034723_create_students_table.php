<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->string('student_id')->unique();  // NIS/ID kartu
            $table->string('name');
            $table->string('grade', 20)->nullable(); // mis. "5" / "6"
            $table->string('kelas', 20)->nullable(); // mis. "5A"
            $table->string('status', 20)->default('aktif'); // aktif/nonaktif
            $table->string('barcode')->nullable()->unique();

            $table->timestamps();

            $table->index(['name']);
            $table->index(['grade']);
            $table->index(['kelas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
