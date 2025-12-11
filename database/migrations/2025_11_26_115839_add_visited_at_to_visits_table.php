<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visits', function (Blueprint $table) {
            // Tambahkan kolom visited_at setelah purpose
            $table->timestamp('visited_at')->nullable()->after('purpose');
            $table->index('visited_at');
        });

        // Untuk data lama: visited_at diisi sama dengan created_at
        DB::table('visits')
            ->whereNull('visited_at')
            ->update(['visited_at' => DB::raw('created_at')]);
    }

    public function down()
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex(['visited_at']);
            $table->dropColumn('visited_at');
        });
    }
};
