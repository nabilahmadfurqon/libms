<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // hanya tambah kalau kolom belum ada
        if (!Schema::hasColumn('visits', 'class_name') || !Schema::hasColumn('visits', 'student_count')) {
            Schema::table('visits', function (Blueprint $table) {
                if (!Schema::hasColumn('visits', 'class_name')) {
                    $table->string('class_name')->nullable()->after('student_id');
                }

                if (!Schema::hasColumn('visits', 'student_count')) {
                    $table->unsignedSmallInteger('student_count')->nullable()->after('class_name');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            if (Schema::hasColumn('visits', 'student_count')) {
                $table->dropColumn('student_count');
            }

            if (Schema::hasColumn('visits', 'class_name')) {
                $table->dropColumn('class_name');
            }
        });
    }
};
