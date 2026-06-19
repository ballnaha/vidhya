<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalize('services');
        $this->normalize('portfolios');

        Schema::table('services', function (Blueprint $table) {
            $table->unique('sort_order', 'services_sort_order_unique');
        });

        Schema::table('portfolios', function (Blueprint $table) {
            $table->unique('sort_order', 'portfolios_sort_order_unique');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique('services_sort_order_unique');
        });

        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropUnique('portfolios_sort_order_unique');
        });
    }

    private function normalize(string $table): void
    {
        DB::table($table)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->each(function (int $id, int $index) use ($table) {
                DB::table($table)
                    ->where('id', $id)
                    ->update(['sort_order' => ($index + 1) * 10]);
            });
    }
};
