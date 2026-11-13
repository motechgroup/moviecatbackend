<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('search_logs')) {
            // Delete legacy keystroke fragments without a retrieved title
            DB::table('search_logs')
                ->whereNull('title')
                ->orWhere('title', '')
                ->delete();

            try {
                Schema::table('search_logs', function (Blueprint $table) {
                    $table->index('title');
                });
            } catch (\Throwable $e) {
                // Index already exists or not supported
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('search_logs')) {
            try {
                Schema::table('search_logs', function (Blueprint $table) {
                    $table->dropIndex(['title']);
                });
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }
};
