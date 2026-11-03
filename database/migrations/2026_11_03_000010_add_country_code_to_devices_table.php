<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('devices') && !Schema::hasColumn('devices', 'country_code')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->string('country_code', 8)->nullable()->after('country')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('devices') && Schema::hasColumn('devices', 'country_code')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->dropColumn('country_code');
            });
        }
    }
};
