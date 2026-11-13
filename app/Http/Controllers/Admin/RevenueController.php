<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\AppSetting;
use App\Models\Device;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevenueController extends Controller
{
    public function index(Request $request): View
    {
        $currency = (string)AppSetting::get("vault_subscription_currency", "KES");

        // High-level Revenue & Subscriber KPI Metrics
        $totalRevenue = (float)Subscription::where("status", "active")->sum("amount");
        $thisMonthRevenue = (float)Subscription::where("status", "active")
            ->where("starts_at", ">=", now()->startOfMonth())
            ->sum("amount");
        $thisWeekRevenue = (float)Subscription::where("status", "active")
            ->where("starts_at", ">=", now()->subDays(7))
            ->sum("amount");
        $todayRevenue = (float)Subscription::where("status", "active")
            ->where("starts_at", ">=", now()->startOfDay())
            ->sum("amount");

        $activeSubscribers = Subscription::active()->count();
        $totalSubscriptions = Subscription::count();
        $successfulCount = Subscription::where("status", "active")->count();
        $pendingCount = Subscription::where("status", "pending")->count();
        $failedCount = Subscription::where("status", "failed")->count();
        $revokedCount = Subscription::where("status", "revoked")->count();
        $expiredCount = Subscription::where("status", "expired")->orWhere(function ($q) {
            $q->where("status", "active")->where("expires_at", "<=", now());
        })->count();

        $expiringSoonCount = Subscription::active()
            ->where("expires_at", "<=", now()->addDays(2))
            ->count();

        // Transaction Success Rate
        $successRate = $totalSubscriptions > 0 ? round(($successfulCount / $totalSubscriptions) * 100, 1) : 0;

        // ARPU (Average Revenue per Active Subscriber)
        $arpu = $activeSubscribers > 0 ? round($totalRevenue / $activeSubscribers, 1) : 0;

        // Fleet Conversion Rate
        $totalDevices = Device::count();
        $conversionRate = $totalDevices > 0 ? round(($activeSubscribers / $totalDevices) * 100, 1) : 0;

        // Gateway Revenue Breakdown
        $mpesaTotal = Subscription::where("gateway", "mpesa")->count();
        $mpesaRevenue = (float)Subscription::where("status", "active")->where("gateway", "mpesa")->sum("amount");
        $payheroTotal = Subscription::where("gateway", "payhero")->count();
        $payheroRevenue = (float)Subscription::where("status", "active")->where("gateway", "payhero")->sum("amount");
        $flutterwaveTotal = Subscription::where("gateway", "flutterwave")->count();
        $flutterwaveRevenue = (float)Subscription::where("status", "active")->where("gateway", "flutterwave")->sum("amount");

        // Plan Duration Breakdown (Week vs Month)
        $weekCount = Subscription::where("package_id", "like", "%week%")->where("status", "active")->count();
        $weekRevenue = (float)Subscription::where("package_id", "like", "%week%")->where("status", "active")->sum("amount");
        $monthCount = Subscription::where("package_id", "like", "%month%")->where("status", "active")->count();
        $monthRevenue = (float)Subscription::where("package_id", "like", "%month%")->where("status", "active")->sum("amount");
        $totalActivePlans = $weekCount + $monthCount;
        $weekShare = $totalActivePlans > 0 ? round(($weekCount / $totalActivePlans) * 100, 1) : 0;
        $monthShare = $totalActivePlans > 0 ? round(($monthCount / $totalActivePlans) * 100, 1) : 0;

        $metrics = [
            "totalRevenue" => $totalRevenue,
            "thisMonthRevenue" => $thisMonthRevenue,
            "thisWeekRevenue" => $thisWeekRevenue,
            "todayRevenue" => $todayRevenue,
            "activeSubscribers" => $activeSubscribers,
            "expiringSoonCount" => $expiringSoonCount,
            "totalSubscriptions" => $totalSubscriptions,
            "successfulCount" => $successfulCount,
            "pendingCount" => $pendingCount,
            "expiredCount" => $expiredCount,
            "failedCount" => $failedCount,
            "revokedCount" => $revokedCount,
            "successRate" => $successRate,
            "arpu" => $arpu,
            "conversionRate" => $conversionRate,
            "currency" => $currency,
            "gateways" => [
                "mpesa" => ["revenue" => $mpesaRevenue, "count" => $mpesaTotal, "share" => $totalRevenue > 0 ? round(($mpesaRevenue / $totalRevenue) * 100, 1) : 0],
                "payhero" => ["revenue" => $payheroRevenue, "count" => $payheroTotal, "share" => $totalRevenue > 0 ? round(($payheroRevenue / $totalRevenue) * 100, 1) : 0],
                "flutterwave" => ["revenue" => $flutterwaveRevenue, "count" => $flutterwaveTotal, "share" => $totalRevenue > 0 ? round(($flutterwaveRevenue / $totalRevenue) * 100, 1) : 0],
            ],
            "plans" => [
                "week" => ["count" => $weekCount, "revenue" => $weekRevenue, "share" => $weekShare],
                "month" => ["count" => $monthCount, "revenue" => $monthRevenue, "share" => $monthShare],
            ],
        ];

        // Filter and Search Query
        $query = Subscription::query()->with("device");

        $statusFilter = $request->input("status", "all");
        if ($statusFilter === "active") {
            $query->where("status", "active")->where("expires_at", ">", now());
        } elseif ($statusFilter === "expiring_soon") {
            $query->where("status", "active")
                  ->where("expires_at", ">", now())
                  ->where("expires_at", "<=", now()->addDays(2));
        } elseif ($statusFilter === "pending") {
            $query->where("status", "pending");
        } elseif ($statusFilter === "expired") {
            $query->where(function ($q) {
                $q->whereIn("status", ["expired"])->orWhere(function ($sq) {
                    $sq->where("status", "active")->where("expires_at", "<=", now());
                });
            });
        } elseif ($statusFilter === "failed") {
            $query->where("status", "failed");
        } elseif ($statusFilter === "revoked") {
            $query->where("status", "revoked");
        }

        $gatewayFilter = $request->input("gateway", "all");
        if ($gatewayFilter !== "all") {
            $query->where("gateway", $gatewayFilter);
        }

        $planFilter = $request->input("plan", "all");
        if ($planFilter !== "all") {
            $query->where("package_id", "like", "%" . $planFilter . "%");
        }

        $periodFilter = $request->input("period", "all");
        if ($periodFilter === "today") {
            $query->where("created_at", ">=", now()->startOfDay());
        } elseif ($periodFilter === "week") {
            $query->where("created_at", ">=", now()->subDays(7));
        } elseif ($periodFilter === "month") {
            $query->where("created_at", ">=", now()->startOfMonth());
        } elseif ($periodFilter === "last30") {
            $query->where("created_at", ">=", now()->subDays(30));
        }

        $search = trim((string)$request->input("q", ""));
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where("username", "like", "%{$search}%")
                  ->orWhere("phone", "like", "%{$search}%")
                  ->orWhere("reference", "like", "%{$search}%")
                  ->orWhere("checkout_request_id", "like", "%{$search}%")
                  ->orWhere("device_id", "like", "%{$search}%")
                  ->orWhereHas("device", function ($dq) use ($search) {
                      $dq->where("device_name", "like", "%{$search}%")
                        ->orWhere("country", "like", "%{$search}%");
                  });
            });
        }

        // Sorting Options
        $sort = $request->input("sort", "newest");
        if ($sort === "highest") {
            $query->orderByDesc("amount");
        } elseif ($sort === "lowest") {
            $query->orderBy("amount");
        } elseif ($sort === "expiring_soon") {
            $query->orderBy("expires_at");
        } elseif ($sort === "oldest") {
            $query->orderBy("created_at");
        } else {
            $query->latest();
        }

        $subscriptions = $query->paginate(20)->withQueryString();

        $filters = [
            "status" => $statusFilter,
            "gateway" => $gatewayFilter,
            "plan" => $planFilter,
            "period" => $periodFilter,
            "sort" => $sort,
            "q" => $search,
        ];

        return view("admin.revenue", compact("metrics", "subscriptions", "filters"));
    }

    /**
     * Manually activate a subscription
     */
    public function activate(int $id): RedirectResponse
    {
        $sub = Subscription::findOrFail($id);
        $periodDays = $sub->period_days ?: ($sub->package_id === "vault_vip_week" ? 7 : 30);

        $sub->update([
            "status" => "active",
            "starts_at" => now(),
            "expires_at" => now()->addDays($periodDays),
            "metadata" => array_merge($sub->metadata ?? [], [
                "manual_activated_by_admin" => true,
                "activated_at" => now()->toIso8601String(),
            ]),
        ]);

        return redirect()->back()->with("success", "Subscription for {$sub->username} successfully activated for {$periodDays} days.");
    }

    /**
     * Cancel or revoke a subscription
     */
    public function cancel(int $id): RedirectResponse
    {
        $sub = Subscription::findOrFail($id);
        $sub->update([
            "status" => "revoked",
            "expires_at" => now()->subDay(),
            "metadata" => array_merge($sub->metadata ?? [], [
                "revoked_by_admin" => true,
                "revoked_at" => now()->toIso8601String(),
            ]),
        ]);

        // Revoke any other active subscriptions for this device or username
        Subscription::where(function ($q) use ($sub) {
            if (!empty($sub->device_id)) {
                $q->orWhere("device_id", $sub->device_id);
            }
            if (!empty($sub->username)) {
                $q->orWhere("username", $sub->username);
            }
        })
        ->where("id", "!=", $sub->id)
        ->update([
            "status" => "revoked",
            "expires_at" => now()->subDay(),
        ]);

        return redirect()->back()->with("success", "VIP subscription access revoked for {$sub->username}.");
    }
}
