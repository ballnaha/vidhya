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
        Schema::create('directors', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('eyebrow');
            $table->string('role');
            $table->json('stats'); // List of: value, suffix, label
            $table->string('bio_title_white');
            $table->string('bio_title_gradient');
            $table->string('bio_image');
            $table->string('bio_alt');
            $table->json('bio'); // Paragraphs array
            $table->string('works_eyebrow');
            $table->string('works_title_white');
            $table->string('works_title_muted');
            $table->json('works'); // List of: image, title, span, video_url
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directors');
    }
};
