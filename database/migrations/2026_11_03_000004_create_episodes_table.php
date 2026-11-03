<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_item_id')->constrained('media_items')->cascadeOnDelete();
            $table->foreignId('season_id')->nullable()->constrained('seasons')->cascadeOnDelete();
            $table->integer('season_number')->default(1);
            $table->integer('episode_number')->default(1);
            $table->string('title')->nullable();
            $table->text('overview')->nullable();
            $table->string('still_path')->nullable();
            $table->integer('duration')->default(0); // in minutes or seconds
            $table->text('stream_url')->nullable();
            $table->text('direct_url')->nullable();
            $table->json('quality_options')->nullable();
            $table->timestamps();

            $table->index(['media_item_id', 'season_number', 'episode_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};