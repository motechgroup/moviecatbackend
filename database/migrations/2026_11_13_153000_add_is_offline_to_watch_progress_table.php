<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('watch_progress')) {
            Schema::table('watch_progress', function (Blueprint $table) {
                if (!Schema::hasColumn('watch_progress', 'is_offline')) {
                    $table->boolean('is_offline')->default(false)->index();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('watch_progress')) {
            Schema::table('watch_progress', function (Blueprint $table) {
                if (Schema::hasColumn('watch_progress', 'is_offline')) {
                    $table->dropColumn('is_offline');
                }
            });
        }
    }
};
