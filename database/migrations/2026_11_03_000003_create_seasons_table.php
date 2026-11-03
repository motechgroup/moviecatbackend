<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_item_id')->constrained('media_items')->cascadeOnDelete();
            $table->integer('season_number')->default(1);
            $table->string('name')->nullable();
            $table->text('overview')->nullable();
            $table->string('poster')->nullable();
            $table->integer('episode_count')->default(0);
            $table->timestamps();

            $table->unique(['media_item_id', 'season_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};