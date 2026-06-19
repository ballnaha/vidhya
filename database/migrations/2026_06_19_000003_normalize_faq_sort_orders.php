<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('faqs')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->each(function (int $id, int $index) {
                DB::table('faqs')
                    ->where('id', $id)
                    ->update(['sort_order' => ($index + 1) * 10]);
            });
    }

    public function down(): void
    {
        // Normalized ordering is intentionally retained on rollback.
    }
};
