<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable()->index();
            $table->string('moviebox_id')->nullable()->index();
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->text('overview')->nullable();
            $table->string('media_type', 16)->default('movie'); // 'movie' or 'tv'
            $table->string('poster')->nullable();
            $table->string('backdrop')->nullable();
            $table->string('year', 10)->nullable();
            $table->string('runtime', 32)->nullable();
            $table->decimal('vote_average', 3, 1)->nullable();
            $table->json('genres')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->string('status', 32)->default('published');
            $table->text('direct_url')->nullable();
            $table->json('quality_options')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_items');
    }
};