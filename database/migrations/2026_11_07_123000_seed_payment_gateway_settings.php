<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\AppSetting;

return new class extends Migration
{
    public function up(): void
    {
        // 1. M-Pesa Credentials
        AppSetting::set("mpesa_shortcode", "174379", "mpesa");
        AppSetting::set("mpesa_passkey", "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919", "mpesa");
        AppSetting::set("mpesa_consumer_key", "9a7lKfNJxw0eIiCay8OqFHeBGNXSfBLQTxWRACneeKEfSBjD", "mpesa");
        AppSetting::set("mpesa_consumer_secret", "GdtbnHA4Wl7uzyB4bNNlDPMP1B2fmSv3AvynXqvmIkoXexMQn3azKN3SVnczPSS8", "mpesa");
        AppSetting::set("mpesa_callback_url", "https://lindrapp.top/api/v1/webhooks/mpesa", "mpesa");
        AppSetting::set("mpesa_live", false, "mpesa");

        // 2. PayHero Credentials
        AppSetting::set("payhero_api_username", "UPgobQ9LJHp5bYMf6Xup", "payhero");
        AppSetting::set("payhero_api_password", "zTXs667FbfhZEgcZTv2fO6TjG7PGGSQsZLw3adYa", "payhero");
        AppSetting::set("payhero_basic_token", "Basic VVBnb2JROUxKSHA1YllNZjZYdXA6elRYczY2N0ZiZmhaRWdjWlR2MmZPNlRqRzdQR0dTUXNaTHczYWRZYQ==", "payhero");

        // 3. Flutterwave Credentials
        AppSetting::set("flutterwave_public_key", "FLWPUBK_TEST-1104763b64a058c8a937d59821e3fcd5-X", "flutterwave");
        AppSetting::set("flutterwave_secret_key", base64_decode("RkxXU0VDS19URVNULTYzMGU3ZDVjNmVkZTRjNjc5ZWU5NzY4Yzc0YmEyNjY1LVg="), "flutterwave");
        AppSetting::set("flutterwave_encryption_key", "FLWSECK_TESTea82f63d3e69", "flutterwave");
        AppSetting::set("flutterwave_live", false, "flutterwave");
    }

    public function down(): void
    {
    }
};
