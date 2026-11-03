<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('app_errors')) {
            Schema::create('app_errors', function (Blueprint $table) {
                $table->id();
                $table->string('device_id')->nullable()->index();
                $table->string('error_type', 64)->default('app_error')->index();
                $table->text('error_message');
                $table->string('screen', 64)->nullable()->index();
                $table->string('severity', 32)->default('error')->index();
                $table->longText('stack_trace')->nullable();
                $table->json('metadata')->nullable();
                $table->string('platform', 32)->nullable();
                $table->string('app_version', 32)->nullable();
                $table->boolean('resolved')->default(false)->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('app_errors');
    }
};
