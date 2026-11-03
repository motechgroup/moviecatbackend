<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_events', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->index();
            $table->string('media_id', 64)->index();
            $table->string('media_type', 16)->default('movie')->index(); // 'movie', 'tv'
            $table->string('title');
            $table->text('poster')->nullable();
            $table->unsignedInteger('season')->nullable();
            $table->unsignedInteger('episode')->nullable();
            $table->unsignedBigInteger('file_size_bytes')->default(0);
            $table->string('quality', 32)->nullable();
            $table->string('status', 32)->default('completed')->index(); // 'started', 'completed', 'deleted', 'offline_watched'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_events');
    }
};
