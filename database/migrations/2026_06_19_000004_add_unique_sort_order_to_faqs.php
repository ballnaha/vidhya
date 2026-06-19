<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('faqs')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('id')
                ->each(function (int $id, int $index) {
                    DB::table('faqs')
                        ->where('id', $id)
                        ->update(['sort_order' => ($index + 1) * 10]);
                });
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->unique('sort_order', 'faqs_sort_order_unique');
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropUnique('faqs_sort_order_unique');
        });
    }
};
