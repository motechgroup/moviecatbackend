<?php

use App\Models\AppError;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('app_errors')) {
            if (!Schema::hasColumn('app_errors', 'module')) {
                Schema::table('app_errors', function (Blueprint $table) {
                    $table->string('module', 32)->nullable()->default('core')->index()->after('screen');
                });
            }

            // Backfill module classification for existing historical errors
            try {
                AppError::whereNull('module')->orWhere('module', 'core')->chunkById(100, function ($errors) {
                    foreach ($errors as $error) {
                        $module = AppError::determineModule(
                            $error->screen,
                            $error->error_type,
                            $error->error_message,
                            $error->metadata
                        );
                        if ($module !== $error->module) {
                            $error->update(['module' => $module]);
                        }
                    }
                });
            } catch (\Throwable) {
                // Non-blocking in migration
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('app_errors') && Schema::hasColumn('app_errors', 'module')) {
            Schema::table('app_errors', function (Blueprint $table) {
                $table->dropColumn('module');
            });
        }
    }
};
