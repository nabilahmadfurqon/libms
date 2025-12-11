<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans','loaned_at')) {
                $table->timestamp('loaned_at')->nullable()->after('days');
            }
            if (!Schema::hasColumn('loans','due_at')) {
                $table->timestamp('due_at')->nullable()->after('loaned_at');
            }
            // kebanyakan sudah ada returned_at. kalau belum, buka komen di bawah:
            // if (!Schema::hasColumn('loans','returned_at')) {
            //     $table->timestamp('returned_at')->nullable()->after('due_at');
            // }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans','due_at')) {
                $table->dropColumn('due_at');
            }
            if (Schema::hasColumn('loans','loaned_at')) {
                $table->dropColumn('loaned_at');
            }
            // if (Schema::hasColumn('loans','returned_at')) {
            //     $table->dropColumn('returned_at');
            // }
        });
    }
};
