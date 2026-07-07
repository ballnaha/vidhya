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
        Schema::table('directors', function (Blueprint $table) {
            $table->integer('sort_order')->default(0);
        });

        // Initialize sort_order for existing directors
        \Illuminate\Support\Facades\DB::table('directors')
            ->orderBy('id')
            ->pluck('id')
            ->each(function (int $id, int $index) {
                \Illuminate\Support\Facades\DB::table('directors')
                    ->where('id', $id)
                    ->update(['sort_order' => ($index + 1) * 10]);
            });

        Schema::table('directors', function (Blueprint $table) {
            $table->unique('sort_order', 'directors_sort_order_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('directors', function (Blueprint $table) {
            $table->dropUnique('directors_sort_order_unique');
            $table->dropColumn('sort_order');
        });
    }
};
