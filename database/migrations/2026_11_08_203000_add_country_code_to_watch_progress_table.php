<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watch_progress', function (Blueprint $table) {
            if (!Schema::hasColumn('watch_progress', 'country_code')) {
                $table->string('country_code', 8)->nullable()->index()->after('poster');
            }
        });
    }

    public function down(): void
    {
        Schema::table('watch_progress', function (Blueprint $table) {
            if (Schema::hasColumn('watch_progress', 'country_code')) {
                $table->dropColumn('country_code');
            }
        });
    }
};
