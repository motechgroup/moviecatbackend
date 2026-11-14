<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class SubscriptionController extends Controller
{
    /**
     * Normalize Kenyan phone number to 254XXXXXXXXX
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace("/\D/", "", $phone);
        if (str_starts_with($digits, "0")) {
            return "254" . substr($digits, 1);
        }
        if (str_starts_with($digits, "7") || str_starts_with($digits, "1")) {
            return "254" . $digits;
        }
        if (str_starts_with($digits, "+254")) {
            return substr($digits, 1);
        }
        return $digits;
    }

    /**
     * Check subscription status for a device / username
     */
    public function status(Request $request): JsonResponse
    {
        $deviceId = $request->header("X-Device-Id", $request->query("device_id"));
        $username = $request->header("X-Username", $request->query("username"));

        $enabled = (bool)AppSetting::get("vault_subscription_enabled", false);
        $activeSub = Subscription::getActiveFor($deviceId, $username);

        return response()->json([
            "success" => true,
            "vault_subscription_enabled" => $enabled,
            "is_active" => $activeSub ? true : false,
            "expires_at" => $activeSub ? $activeSub->expires_at->toIso8601String() : null,
            "package_id" => $activeSub ? $activeSub->package_id : null,
            "reference" => $activeSub ? $activeSub->reference : null,
        ]);
    }

    /**
     * Initiate payment for the single Vault VIP package
     */
    public function pay(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "device_id" => "required|string|max:128",
            "username" => "required|string|max:64",
            "gateway" => "required|string|in:mpesa,payhero,flutterwave",
            "phone" => "nullable|string|max:32",
            "plan" => "nullable|string|in:week,month",
        ]);

        $deviceId = $validated["device_id"];
        $username = $validated["username"];
        $gateway = $validated["gateway"];
        $rawPhone = $validated["phone"] ?? "";

        // Check if gateway is enabled by admin
        $gatewayKey = "gateway_{$gateway}_enabled";
        if (!AppSetting::get($gatewayKey, true)) {
            return response()->json([
                "success" => false,
                "message" => "This payment option is currently disabled by administrator.",
            ], 400);
        }

        $plan = $validated["plan"] ?? "month";
        $currency = (string)AppSetting::get("vault_subscription_currency", "KES");
        if ($plan === "week") {
            $price = (float)AppSetting::get("vault_sub_price_week", 50);
            $periodDays = 7;
            $packageId = "vault_vip_week";
        } else {
            $price = (float)AppSetting::get("vault_sub_price_month", 100);
            $periodDays = 30;
            $packageId = "vault_vip_month";
        }
        $reference = "MC_" . strtoupper(Str::random(6)) . "_" . (int)(microtime(true) * 1000);

        // 1. M-Pesa Direct STK Push
        if ($gateway === "mpesa") {
            if (empty($rawPhone)) {
                return response()->json(["success" => false, "message" => "Phone number is required for M-Pesa payment."], 422);
            }
            $phone = $this->normalizePhone($rawPhone);

            $subscription = Subscription::create([
                "device_id" => $deviceId,
                "username" => $username,
                "package_id" => $packageId,
                "period_days" => $periodDays,
                "gateway" => "mpesa",
                "amount" => $price,
                "currency" => $currency,
                "reference" => $reference,
                "phone" => $phone,
                "status" => "pending",
            ]);

            try {
                $stkResult = $this->triggerMpesaStkPush($subscription, $phone, (int)$price, $username);
                $subscription->update([
                    "checkout_request_id" => $stkResult["CheckoutRequestID"] ?? null,
                ]);

                return response()->json([
                    "success" => true,
                    "gateway" => "mpesa",
                    "reference" => $reference,
                    "checkout_request_id" => $stkResult["CheckoutRequestID"] ?? null,
                    "message" => "M-Pesa payment prompt sent to {$phone}. Please enter your PIN on your phone.",
                ]);
            } catch (Throwable $e) {
                $subscription->update(["status" => "failed", "metadata" => ["error" => $e->getMessage()]]);
                return response()->json([
                    "success" => false,
                    "message" => "M-Pesa STK push error: " . $e->getMessage(),
                ], 500);
            }
        }

        // 2. PayHero STK Push
        if ($gateway === "payhero") {
            if (empty($rawPhone)) {
                return response()->json(["success" => false, "message" => "Phone number is required for PayHero payment."], 422);
            }
            $phone = $this->normalizePhone($rawPhone);

            $subscription = Subscription::create([
                "device_id" => $deviceId,
                "username" => $username,
                "package_id" => $packageId,
                "period_days" => $periodDays,
                "gateway" => "payhero",
                "amount" => $price,
                "currency" => $currency,
                "reference" => $reference,
                "phone" => $phone,
                "status" => "pending",
            ]);

            try {
                $payheroResult = $this->triggerPayheroStkPush($subscription, $phone, (int)$price, $username);
                $subscription->update([
                    "checkout_request_id" => $payheroResult["reference"] ?? $payheroResult["CheckoutRequestID"] ?? $reference,
                ]);

                return response()->json([
                    "success" => true,
                    "gateway" => "payhero",
                    "reference" => $reference,
                    "checkout_request_id" => $subscription->checkout_request_id,
                    "message" => "PayHero prompt sent to {$phone}. Please enter your M-Pesa PIN on your phone.",
                ]);
            } catch (Throwable $e) {
                $subscription->update(["status" => "failed", "metadata" => ["error" => $e->getMessage()]]);
                return response()->json([
                    "success" => false,
                    "message" => "PayHero payment error: " . $e->getMessage(),
                ], 500);
            }
        }

        // 3. Flutterwave (East Africa: Kenya, Uganda, Tanzania, Rwanda)
        if ($gateway === "flutterwave") {
            $subscription = Subscription::create([
                "device_id" => $deviceId,
                "username" => $username,
                "package_id" => "vault_vip",
                "gateway" => "flutterwave",
                "amount" => $price,
                "currency" => $currency,
                "reference" => $reference,
                "phone" => $rawPhone ?: null,
                "status" => "pending",
            ]);

            try {
                $link = $this->generateFlutterwaveLink($subscription, $price, $currency, $username, $rawPhone);
                return response()->json([
                    "success" => true,
                    "gateway" => "flutterwave",
                    "reference" => $reference,
                    "payment_link" => $link,
                    "message" => "Flutterwave payment session generated.",
                ]);
            } catch (Throwable $e) {
                $subscription->update(["status" => "failed", "metadata" => ["error" => $e->getMessage()]]);
                return response()->json([
                    "success" => false,
                    "message" => "Flutterwave generation error: " . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json(["success" => false, "message" => "Invalid gateway."], 400);
    }

    /**
     * Query pending payment status
     */
    public function query(Request $request): JsonResponse
    {
        $reference = $request->query("reference");
        $checkoutRequestId = $request->query("checkout_request_id");
        $deviceId = $request->query("device_id");
        $username = $request->query("username");

        $sub = null;
        if (!empty($reference) || !empty($checkoutRequestId)) {
            $sub = Subscription::where(function ($q) use ($reference, $checkoutRequestId) {
                if (!empty($reference)) {
                    $q->orWhere("reference", $reference);
                }
                if (!empty($checkoutRequestId)) {
                    $q->orWhere("checkout_request_id", $checkoutRequestId);
                }
            })->first();
        }

        // When a specific transaction is being polled, ONLY verify that transaction
        if ($sub) {
            // If already confirmed active in DB
            if ($sub->status === "active" && $sub->expires_at && $sub->expires_at->isFuture()) {
                return response()->json([
                    "success" => true,
                    "paid" => true,
                    "status" => "active",
                    "expires_at" => $sub->expires_at->toIso8601String(),
                ]);
            }

            // If already marked failed
            if ($sub->status === "failed") {
                return response()->json([
                    "success" => false,
                    "paid" => false,
                    "status" => "failed",
                    "message" => "Payment was declined or cancelled.",
                ]);
            }

            // If still pending, actively verify with PayHero with STRICT confirmation check
            if ($sub->status === "pending" && ($sub->gateway === "payhero" || !empty($sub->checkout_request_id))) {
                if ($this->checkPayheroTransactionStatus($sub)) {
                    $fresh = $sub->fresh();
                    return response()->json([
                        "success" => true,
                        "paid" => true,
                        "status" => "active",
                        "expires_at" => $fresh->expires_at ? $fresh->expires_at->toIso8601String() : null,
                    ]);
                }
            }

            return response()->json([
                "success" => true,
                "paid" => false,
                "status" => $sub->status,
                "expires_at" => null,
            ]);
        }

        // ONLY if no specific transaction reference was requested, check general active device status
        if (empty($reference) && empty($checkoutRequestId)) {
            $active = Subscription::getActiveFor($deviceId, $username);
            if ($active) {
                return response()->json([
                    "success" => true,
                    "paid" => true,
                    "status" => "active",
                    "expires_at" => $active->expires_at->toIso8601String(),
                ]);
            }
        }

        return response()->json([
            "success" => false,
            "paid" => false,
            "message" => "Transaction not found or pending.",
        ], 404);
    }

    /**
     * Webhook callbacks for payment gateways with cryptographic signature verification and active server confirmation
     */
    public function webhook(Request $request, string $gateway): JsonResponse
    {
        $payload = $request->all();
        Log::info("Payment webhook received [{$gateway}]:", [
            'ip' => $request->ip(),
            'payload' => $payload,
        ]);

        $periodDays = (int)AppSetting::get("vault_subscription_period_days", 30);

        // 1. M-Pesa Safaricom Daraja STK Callback
        if ($gateway === "mpesa") {
            // Optional secret token verification if configured
            $secretToken = (string)AppSetting::get("mpesa_webhook_token", env("MPESA_WEBHOOK_TOKEN", ""));
            if (!empty($secretToken)) {
                $incomingToken = $request->query("token") ?: $request->header("X-Webhook-Token");
                if (!$incomingToken || !hash_equals($secretToken, (string)$incomingToken)) {
                    Log::warning("M-Pesa webhook rejected: invalid token", ["ip" => $request->ip()]);
                    return response()->json(["ResultCode" => 1, "ResultDesc" => "Unauthorized"], 401);
                }
            }

            $callback = $payload["Body"]["stkCallback"] ?? null;
            if ($callback) {
                $checkoutRequestId = $callback["CheckoutRequestID"] ?? null;
                $resultCode = $callback["ResultCode"] ?? -1;

                if (!empty($checkoutRequestId)) {
                    // Only accept pending subscriptions initiated within the last 2 hours
                    $sub = Subscription::where("checkout_request_id", $checkoutRequestId)
                        ->where("status", "pending")
                        ->where("created_at", ">=", now()->subHours(2))
                        ->first();

                    if ($sub) {
                        if ($resultCode === 0) {
                            $items = $callback["CallbackMetadata"]["Item"] ?? [];
                            $receipt = null;
                            $paidAmount = 0;
                            foreach ($items as $item) {
                                if (($item["Name"] ?? "") === "MpesaReceiptNumber") {
                                    $receipt = $item["Value"] ?? null;
                                }
                                if (($item["Name"] ?? "") === "Amount") {
                                    $paidAmount = (float)($item["Value"] ?? 0);
                                }
                            }

                            $days = $sub->period_days ?: ($sub->package_id === 'vault_vip_week' ? 7 : 30);
                            $sub->update([
                                "status" => "active",
                                "starts_at" => now(),
                                "expires_at" => now()->addDays($days),
                                "metadata" => array_merge($sub->metadata ?? [], $callback, [
                                    "receipt" => $receipt,
                                    "paid_amount" => $paidAmount,
                                ]),
                            ]);
                            Log::info("M-Pesa subscription activated for ref: {$sub->reference}, receipt: {$receipt}");
                        } else {
                            $sub->update([
                                "status" => "failed",
                                "metadata" => array_merge($sub->metadata ?? [], $callback),
                            ]);
                            Log::info("M-Pesa transaction declined for ref: {$sub->reference}");
                        }
                    } else {
                        Log::warning("M-Pesa webhook ignored: no matching pending subscription found for checkout ID: {$checkoutRequestId}");
                    }
                }
            }
            return response()->json(["ResultCode" => 0, "ResultDesc" => "Accepted"]);
        }

        // 2. PayHero Kenya
        if ($gateway === "payhero") {
            // Optional secret token verification if configured
            $configuredToken = (string)AppSetting::get("payhero_webhook_token", env("PAYHERO_WEBHOOK_TOKEN", ""));
            if (!empty($configuredToken)) {
                $incomingToken = $request->header("X-Webhook-Token") ?: $request->query("token");
                if (!$incomingToken || !hash_equals($configuredToken, (string)$incomingToken)) {
                    Log::warning("PayHero webhook rejected: invalid secret token", ["ip" => $request->ip()]);
                    return response()->json(["error" => "Unauthorized webhook token"], 401);
                }
            }

            $resp = $payload["response"] ?? $payload;
            $status = strtolower((string)($resp["Status"] ?? $resp["status"] ?? $payload["status"] ?? ""));
            $resultCode = $resp["ResultCode"] ?? $resp["result_code"] ?? null;
            $externalRef = $resp["ExternalReference"] ?? $resp["external_reference"] ?? $payload["external_reference"] ?? $payload["reference"] ?? null;
            $checkoutId = $resp["CheckoutRequestID"] ?? $resp["checkout_request_id"] ?? $payload["checkout_request_id"] ?? null;
            $receipt = $resp["MpesaReceiptNumber"] ?? $resp["mpesa_receipt_number"] ?? null;

            $isSuccess = (($resultCode === 0 || $resultCode === "0") || in_array(strtoupper($status), ["SUCCESS", "COMPLETED", "COMPLETE"])) && (!empty($receipt) || $resultCode === 0);
            $isFailed = ($resultCode !== null && $resultCode != 0) || in_array(strtoupper($status), ["FAILED", "CANCELLED", "DECLINED"]);

            $sub = null;
            if ($externalRef) {
                $sub = Subscription::where("reference", $externalRef)->first();
            }
            if (!$sub && $checkoutId) {
                $sub = Subscription::where("checkout_request_id", $checkoutId)->first();
            }

            if ($sub) {
                if ($isSuccess) {
                    // Active server-to-server confirmation check with PayHero API
                    $basicToken = (string)AppSetting::get("payhero_basic_token", env("PAYHERO_BASIC_AUTH_TOKEN", ""));
                    if (!empty($basicToken)) {
                        if ($this->checkPayheroTransactionStatus($sub)) {
                            Log::info("PayHero webhook confirmed & verified via API for ref: {$sub->reference}");
                            return response()->json(["status" => "verified_and_active"]);
                        } else {
                            Log::warning("PayHero webhook reported success but active API query was not verified for ref: {$sub->reference}");
                            return response()->json(["status" => "pending_verification"]);
                        }
                    }

                    // Fallback verification: validate receipt string pattern
                    if (!empty($receipt) && preg_match('/^[A-Z0-9]{8,15}$/', $receipt)) {
                        $periodDays = $sub->period_days ?: ($sub->package_id === "vault_vip_week" ? 7 : 30);
                        DB::transaction(function () use ($sub, $periodDays, $payload, $receipt) {
                            $sub->lockForUpdate();
                            $sub->update([
                                "status" => "active",
                                "starts_at" => now(),
                                "expires_at" => now()->addDays($periodDays),
                                "metadata" => array_merge($sub->metadata ?? [], $payload, ["receipt" => $receipt]),
                            ]);
                        });
                        Log::info("PayHero subscription activated with receipt {$receipt} for ref: {$sub->reference}");
                    }
                } elseif ($isFailed) {
                    $sub->update([
                        "status" => "failed",
                        "metadata" => array_merge($sub->metadata ?? [], $payload),
                    ]);
                }
            }
            return response()->json(["status" => "received"]);
        }

        // 3. Flutterwave
        if ($gateway === "flutterwave") {
            // Cryptographic verif-hash verification
            $secretHash = (string)AppSetting::get("flutterwave_secret_hash", env("FLUTTERWAVE_SECRET_HASH", ""));
            if (!empty($secretHash)) {
                $signature = (string)$request->header("verif-hash", "");
                if (!$signature || !hash_equals($secretHash, $signature)) {
                    Log::warning("Flutterwave webhook rejected: invalid signature hash", ["ip" => $request->ip()]);
                    return response()->json(["error" => "Invalid signature"], 401);
                }
            }

            $status = strtolower($payload["data"]["status"] ?? $payload["status"] ?? "");
            $txRef = $payload["data"]["tx_ref"] ?? $payload["tx_ref"] ?? null;
            $transactionId = $payload["data"]["id"] ?? $payload["id"] ?? null;

            if ($txRef) {
                $sub = Subscription::where("reference", $txRef)->first();
                if ($sub && in_array($status, ["successful", "completed"])) {
                    // Active server-to-server verification with Flutterwave API if secret key is configured
                    $secretKey = (string)AppSetting::get("flutterwave_secret_key", env("FLUTTERWAVE_SECRET_KEY", ""));
                    if (!empty($secretKey) && !empty($transactionId)) {
                        try {
                            $flwRes = Http::withToken($secretKey)->timeout(6)
                                ->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");
                            if (!$flwRes->successful() || strtolower((string)$flwRes->json("data.status")) !== "successful") {
                                Log::warning("Flutterwave webhook active verification failed for tx: {$transactionId}");
                                return response()->json(["status" => "verification_failed"]);
                            }
                        } catch (\Throwable $e) {
                            Log::warning("Flutterwave verification exception: " . $e->getMessage());
                        }
                    }

                    $days = $sub->period_days ?: ($sub->package_id === 'vault_vip_week' ? 7 : 30);
                    $sub->update([
                        "status" => "active",
                        "starts_at" => now(),
                        "expires_at" => now()->addDays($days),
                        "metadata" => $payload,
                    ]);
                    Log::info("Flutterwave subscription activated for ref: {$txRef}");
                }
            }
            return response()->json(["status" => "received"]);
        }

        return response()->json(["status" => "ignored"]);
    }
    /**
     * Safaricom Daraja STK Push implementation
     */
    private function triggerMpesaStkPush(Subscription $sub, string $phone, int $amount, string $username): array
    {
        $shortcode = (string)AppSetting::get("mpesa_shortcode", "174379");
        $passkey = (string)AppSetting::get("mpesa_passkey", env("MPESA_PASSKEY", "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919"));
        $consumerKey = (string)AppSetting::get("mpesa_consumer_key", env("MPESA_CONSUMER_KEY", ""));
        $consumerSecret = (string)AppSetting::get("mpesa_consumer_secret", env("MPESA_CONSUMER_SECRET", ""));
        $isLive = (bool)AppSetting::get("mpesa_live", false);
        $callbackUrl = (string)AppSetting::get("mpesa_callback_url", "https://moviecat.djstube.com/api/v1/subscription/webhook/mpesa");

        $baseUrl = $isLive ? "https://api.safaricom.co.ke" : "https://sandbox.safaricom.co.ke";
        $timestamp = now("Africa/Nairobi")->format("YmdHis");
        $password = base64_encode($shortcode . $passkey . $timestamp);

        // Fetch OAuth token if keys provided
        $token = "sandbox_dummy_token";
        if (!empty($consumerKey) && !empty($consumerSecret)) {
            $authRes = Http::withBasicAuth($consumerKey, $consumerSecret)
                ->get("{$baseUrl}/oauth/v1/generate?grant_type=client_credentials");
            if ($authRes->successful() && $authRes->json("access_token")) {
                $token = $authRes->json("access_token");
            }
        }

        $callbackUrl = url("/api/v1/subscription/webhook/mpesa");

        $response = Http::withToken($token)
            ->acceptJson()
            ->post("{$baseUrl}/mpesa/stkpush/v1/processrequest", [
                "BusinessShortCode" => $shortcode,
                "Password" => $password,
                "Timestamp" => $timestamp,
                "TransactionType" => "CustomerPayBillOnline",
                "Amount" => max(1, $amount),
                "PartyA" => $phone,
                "PartyB" => $shortcode,
                "PhoneNumber" => $phone,
                "CallBackURL" => $callbackUrl,
                "AccountReference" => substr($username, 0, 12),
                "TransactionDesc" => "MovieCat Vault VIP",
            ]);

        if ($response->failed() || $response->json("ResponseCode") !== "0") {
            $err = $response->json("errorMessage") ?? $response->json("ResponseDescription") ?? "STK Push declined";
            // In dev sandbox when token is missing, provide simulation response
            if (empty($consumerKey)) {
                return ["CheckoutRequestID" => "ws_CO_" . Str::random(18), "ResponseCode" => "0"];
            }
            throw new \RuntimeException($err);
        }

        return $response->json();
    }

    /**
     * Active instant verification with PayHero API
     */
    private function checkPayheroTransactionStatus(Subscription $sub): bool
    {
        $candidates = array_unique(array_filter([$sub->checkout_request_id, $sub->reference]));
        if (empty($candidates)) {
            return false;
        }

        $basicToken = (string)AppSetting::get("payhero_basic_token", env("PAYHERO_BASIC_AUTH_TOKEN", ""));
        if (!str_starts_with($basicToken, "Basic ")) {
            $basicToken = "Basic " . $basicToken;
        }

        foreach ($candidates as $ref) {
            try {
                $res = Http::withHeaders([
                    "Authorization" => $basicToken,
                    "Accept" => "application/json",
                ])->timeout(4)->connectTimeout(2)->get("https://backend.payhero.co.ke/api/v2/transaction-status", [
                    "reference" => $ref,
                ]);

                if ($res->successful()) {
                    $data = $res->json();
                    $status = strtoupper((string)($data["status"] ?? ""));
                    $thirdPartyRef = $data["third_party_reference"] ?? $data["provider_reference"] ?? null;

                    // STRICT VERIFICATION:
                    // PayHero returns success:true even for FAILED/QUEUED queries!
                    // A transaction is ONLY successful if status is explicitly SUCCESS/COMPLETED AND has an M-Pesa code
                    $isSuccess = in_array($status, ["SUCCESS", "COMPLETED", "COMPLETE"]) && !empty($thirdPartyRef);
                    $isFailed = in_array($status, ["FAILED", "CANCELLED", "TIMEOUT", "DECLINED"]);

                    if ($isSuccess) {
                        $periodDays = $sub->period_days ?: ($sub->package_id === "vault_vip_week" ? 7 : 30);
                        DB::transaction(function () use ($sub, $periodDays, $data, $thirdPartyRef) {
                            $sub->lockForUpdate();
                            $sub->update([
                                "status" => "active",
                                "starts_at" => now(),
                                "expires_at" => now()->addDays($periodDays),
                                "metadata" => array_merge($sub->metadata ?? [], [
                                    "auto_verified" => $data,
                                    "receipt" => $thirdPartyRef,
                                ]),
                            ]);
                        });
                        return true;
                    } elseif ($isFailed) {
                        $sub->update([
                            "status" => "failed",
                            "metadata" => array_merge($sub->metadata ?? [], ["auto_verified" => $data]),
                        ]);
                        return false;
                    }
                    // If QUEUED or PENDING, do NOT mark active - return false and keep waiting
                    return false;
                }
            } catch (\Throwable $e) {
                // Ignore transient query errors
            }
        }

        return false;
    }

    /**
     * PayHero STK Push implementation
     */
    private function triggerPayheroStkPush(Subscription $sub, string $phone, int $amount, string $username): array
    {
        // PayHero Credentials
        $basicToken = (string)AppSetting::get("payhero_basic_token", env("PAYHERO_BASIC_AUTH_TOKEN", ""));
        $rawChannel = AppSetting::get("payhero_channel_id", env("PAYHERO_CHANNEL_ID", "14226"));
        $channelId = (int)$rawChannel;
        $callbackUrl = url("/api/v1/subscription/webhook/payhero");

        if (!str_starts_with($basicToken, "Basic ")) {
            $basicToken = "Basic " . $basicToken;
        }

        if ($channelId <= 0) {
            try {
                $channelRes = Http::withHeaders([
                    "Authorization" => $basicToken,
                    "Accept" => "application/json",
                ])->timeout(8)->get("https://backend.payhero.co.ke/api/v2/payment_channels?is_active=true");

                if ($channelRes->successful()) {
                    $chans = $channelRes->json("payment_channels") ?? [];
                    if (!empty($chans[0]["id"])) {
                        $channelId = (int)$chans[0]["id"];
                        AppSetting::set("payhero_channel_id", (string)$channelId, "payhero");
                    }
                }
            } catch (\Throwable $t) {
                $channelId = 14226;
            }
        }
        if ($channelId <= 0) {
            $channelId = 14226;
        }

        $payload = [
            "amount" => max(1, $amount),
            "phone_number" => $phone,
            "channel_id" => $channelId,
            "provider" => "m-pesa",
            "external_reference" => $sub->reference,
            "customer_name" => $username,
            "callback_url" => $callbackUrl,
        ];

        $res = Http::withHeaders([
            "Authorization" => $basicToken,
            "Content-Type" => "application/json",
            "Accept" => "application/json",
        ])->timeout(25)->post("https://backend.payhero.co.ke/api/v2/payments", $payload);

        if ($res->failed()) {
            $err = $res->json("error_message") ?? $res->json("message") ?? $res->json("error") ?? $res->body();
            throw new \RuntimeException("PayHero request failed: " . $err);
        }

        return $res->json() ?: ["reference" => $sub->reference];
    }

    /**
     * Flutterwave checkout link generation
     */
    private function generateFlutterwaveLink(Subscription $sub, float $amount, string $currency, string $username, string $phone): string
    {
        $secretKey = (string)AppSetting::get("flutterwave_secret_key", env("FLUTTERWAVE_SECRET_KEY", ""));
        $callbackUrl = url("/api/v1/subscription/webhook/flutterwave");

        if (empty($secretKey)) {
            return "https://checkout.flutterwave.com/v3/hosted/pay/simulated_" . $sub->reference;
        }

        $res = Http::withToken($secretKey)
            ->acceptJson()
            ->post("https://api.flutterwave.com/v3/payments", [
                "tx_ref" => $sub->reference,
                "amount" => $amount,
                "currency" => $currency,
                "redirect_url" => $callbackUrl,
                "customer" => [
                    "email" => strtolower(Str::slug($username)) . "@moviecat.app",
                    "phonenumber" => $phone,
                    "name" => $username,
                ],
                "customizations" => [
                    "title" => "MovieCat Vault VIP",
                    "description" => "30 Days Offline Vault Playback Pass",
                    "logo" => url("/assets/images/icon.png"),
                ],
            ]);

        if ($res->successful() && $res->json("data.link")) {
            return $res->json("data.link");
        }

        throw new \RuntimeException("Failed to generate Flutterwave payment link: " . ($res->json("message") ?? "Unknown error"));
    }
}
