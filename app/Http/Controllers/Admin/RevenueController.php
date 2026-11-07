<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\AppSetting;
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
        $todayRevenue = (float)Subscription::where("status", "active")
            ->where("starts_at", ">=", now()->startOfDay())
            ->sum("amount");

        $activeSubscribers = Subscription::active()->count();
        $totalSubscriptions = Subscription::count();
        $pendingCount = Subscription::where("status", "pending")->count();
        $expiredCount = Subscription::where("status", "expired")->orWhere(function ($q) {
            $q->where("status", "active")->where("expires_at", "<=", now());
        })->count();

        // Gateway Breakdown
        $mpesaRevenue = (float)Subscription::where("status", "active")->where("gateway", "mpesa")->sum("amount");
        $payheroRevenue = (float)Subscription::where("status", "active")->where("gateway", "payhero")->sum("amount");
        $flutterwaveRevenue = (float)Subscription::where("status", "active")->where("gateway", "flutterwave")->sum("amount");

        $metrics = [
            "totalRevenue" => $totalRevenue,
            "thisMonthRevenue" => $thisMonthRevenue,
            "todayRevenue" => $todayRevenue,
            "activeSubscribers" => $activeSubscribers,
            "totalSubscriptions" => $totalSubscriptions,
            "pendingCount" => $pendingCount,
            "expiredCount" => $expiredCount,
            "currency" => $currency,
            "mpesaRevenue" => $mpesaRevenue,
            "payheroRevenue" => $payheroRevenue,
            "flutterwaveRevenue" => $flutterwaveRevenue,
        ];

        // Filter and Search Query
        $query = Subscription::query()->latest();

        $statusFilter = $request->input("status", "all");
        if ($statusFilter === "active") {
            $query->where("status", "active")->where("expires_at", ">", now());
        } elseif ($statusFilter === "pending") {
            $query->where("status", "pending");
        } elseif ($statusFilter === "expired") {
            $query->where(function ($q) {
                $q->where("status", "expired")->orWhere(function ($sq) {
                    $sq->where("status", "active")->where("expires_at", "<=", now());
                });
            });
        } elseif ($statusFilter === "failed") {
            $query->where("status", "failed");
        }

        $gatewayFilter = $request->input("gateway", "all");
        if ($gatewayFilter !== "all") {
            $query->where("gateway", $gatewayFilter);
        }

        $planFilter = $request->input("plan", "all");
        if ($planFilter !== "all") {
            $query->where("package_id", "like", "%" . $planFilter . "%");
        }

        $search = trim((string)$request->input("q", ""));
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where("username", "like", "%{$search}%")
                  ->orWhere("phone", "like", "%{$search}%")
                  ->orWhere("reference", "like", "%{$search}%")
                  ->orWhere("checkout_request_id", "like", "%{$search}%")
                  ->orWhere("device_id", "like", "%{$search}%");
            });
        }

        $subscriptions = $query->paginate(20)->withQueryString();

        $filters = [
            "status" => $statusFilter,
            "gateway" => $gatewayFilter,
            "plan" => $planFilter,
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
     * Extend subscription by +7 or +30 days
     */
    public function extend(int $id, Request $request): RedirectResponse
    {
        $sub = Subscription::findOrFail($id);
        $daysToAdd = (int)$request->input("days", 30);

        $baseDate = ($sub->expires_at && $sub->expires_at->isFuture()) ? $sub->expires_at : now();
        $newExpiry = $baseDate->copy()->addDays($daysToAdd);

        $sub->update([
            "status" => "active",
            "expires_at" => $newExpiry,
            "metadata" => array_merge($sub->metadata ?? [], [
                "extended_by_admin_days" => $daysToAdd,
                "extended_at" => now()->toIso8601String(),
            ]),
        ]);

        return redirect()->back()->with("success", "Subscription for {$sub->username} extended by {$daysToAdd} days (Expires: {$newExpiry->format('d M Y')}).");
    }

    /**
     * Cancel or revoke a subscription
     */
    public function cancel(int $id): RedirectResponse
    {
        $sub = Subscription::findOrFail($id);
        $sub->update([
            "status" => "expired",
            "expires_at" => now(),
            "metadata" => array_merge($sub->metadata ?? [], [
                "revoked_by_admin" => true,
                "revoked_at" => now()->toIso8601String(),
            ]),
        ]);

        return redirect()->back()->with("success", "Subscription for {$sub->username} has been revoked.");
    }
}
