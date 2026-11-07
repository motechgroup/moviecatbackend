<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\AppSetting;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable("subscriptions") && !Schema::hasColumn("subscriptions", "period_days")) {
            Schema::table("subscriptions", function (Blueprint $table) {
                $table->integer("period_days")->default(30)->after("amount");
            });
        }

        // Default settings for 1 Week and 1 Month plans
        AppSetting::set("vault_sub_price_week", 50, "subscription");
        AppSetting::set("vault_sub_price_month", 100, "subscription");
        AppSetting::set("vault_sub_week_enabled", true, "subscription");
        AppSetting::set("vault_sub_month_enabled", true, "subscription");
        AppSetting::set("payhero_channel_id", "14226", "payhero");
        AppSetting::set("mpesa_callback_url", "https://moviecat.djstube.com/api/v1/subscription/webhook/mpesa", "mpesa");
    }

    public function down(): void
    {
        if (Schema::hasTable("subscriptions") && Schema::hasColumn("subscriptions", "period_days")) {
            Schema::table("subscriptions", function (Blueprint $table) {
                $table->dropColumn("period_days");
            });
        }
    }
};
