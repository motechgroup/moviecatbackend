<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_events', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->nullable()->index();
            $table->string('ad_unit_id')->nullable();
            $table->string('ad_type', 32); // 'interstitial', 'rewarded', 'banner'
            $table->string('event_type', 32); // 'impression', 'click', 'failed'
            $table->json('extra')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_events');
    }
};