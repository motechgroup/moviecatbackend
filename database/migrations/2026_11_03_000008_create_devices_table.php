<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique()->index();
            $table->string('username', 64)->nullable()->index();
            $table->string('platform', 32)->default('android'); // 'android', 'ios'
            $table->string('device_name')->nullable(); // e.g. 'Pixel 7', 'iPhone 15'
            $table->string('os_version', 64)->nullable();
            $table->string('app_version', 32)->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->string('country', 64)->nullable();
            $table->string('city', 64)->nullable();
            $table->timestamp('last_active_at')->useCurrent()->index();
            $table->unsignedBigInteger('total_watch_seconds')->default(0);
            $table->unsignedInteger('sessions_count')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};