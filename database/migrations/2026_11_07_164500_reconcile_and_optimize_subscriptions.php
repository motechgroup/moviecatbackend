<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Subscription;

return new class extends Migration
{
    public function up(): void
    {
        // Reconcile any existing pending PayHero subscription that completed
        $pending = Subscription::where("reference", "MC_XXYPBDDEFG")
            ->orWhere("checkout_request_id", "bbf3-4892-babd-607a84d933122560555")
            ->first();

        if ($pending) {
            $days = $pending->period_days ?: 30;
            $pending->update([
                "status" => "active",
                "starts_at" => now(),
                "expires_at" => now()->addDays($days),
                "metadata" => array_merge($pending->metadata ?? [], [
                    "reconciled" => true,
                    "mpesa_code" => "UK7I912QA6",
                    "payhero_ref" => "bbf3-4892-babd-607a84d933122560555",
                ]),
            ]);
        }
    }

    public function down(): void
    {
    }
};
