<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('search_logs')) {
            Schema::create('search_logs', function (Blueprint $table) {
                $table->id();
                $table->string('device_id')->nullable()->index();
                $table->string('username')->nullable()->index();
                $table->string('query')->index();
                $table->unsignedInteger('results_count')->default(0);
                $table->string('media_id', 64)->nullable()->index();
                $table->string('media_type', 16)->nullable()->index(); // 'movie', 'tv'
                $table->string('title')->nullable();
                $table->text('poster')->nullable();
                $table->string('year', 16)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
