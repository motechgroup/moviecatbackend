<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("subscriptions", function (Blueprint $table) {
            $table->id();
            $table->string("device_id")->index();
            $table->string("username")->index();
            $table->string("package_id")->default("vault_vip");
            $table->string("gateway")->default("mpesa"); // mpesa, payhero, flutterwave, manual
            $table->decimal("amount", 10, 2)->default(100);
            $table->string("currency", 8)->default("KES");
            $table->string("reference")->unique();
            $table->string("checkout_request_id")->nullable()->index();
            $table->string("phone")->nullable();
            $table->string("status")->default("pending"); // pending, active, failed, expired
            $table->timestamp("starts_at")->nullable();
            $table->timestamp("expires_at")->nullable()->index();
            $table->json("metadata")->nullable();
            $table->timestamps();

            $table->index(["device_id", "status", "expires_at"]);
            $table->index(["username", "status", "expires_at"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("subscriptions");
    }
};
