<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('device_id')->index();
            $table->string('media_id')->index();
            $table->string('media_type', 16)->default('movie');
            $table->string('title');
            $table->string('poster')->nullable();
            $table->string('backdrop')->nullable();
            $table->string('year', 10)->nullable();
            $table->decimal('vote_average', 3, 1)->nullable();
            $table->timestamps();

            $table->index(['device_id', 'media_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};