<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('device_id')->index();
            $table->string('media_id')->index();
            $table->string('media_type', 16)->default('movie');
            $table->string('title');
            $table->string('poster')->nullable();
            $table->integer('season')->nullable();
            $table->integer('episode')->nullable();
            $table->double('current_time')->default(0);
            $table->double('duration')->default(0);
            $table->boolean('completed')->default(false);
            $table->timestamp('last_watched_at')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'media_id', 'media_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_progress');
    }
};