<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        $price = (float)AppSetting::get("vault_subscription_price", 100);
        $currency = (string)AppSetting::get("vault_subscription_currency", "KES");
        $periodDays = (int)AppSetting::get("vault_subscription_period_days", 30);
        $reference = "MC_" . strtoupper(Str::random(10));

        // 1. M-Pesa Direct STK Push
        if ($gateway === "mpesa") {
            if (empty($rawPhone)) {
                return response()->json(["success" => false, "message" => "Phone number is required for M-Pesa payment."], 422);
            }
            $phone = $this->normalizePhone($rawPhone);

            $subscription = Subscription::create([
                "device_id" => $deviceId,
                "username" => $username,
                "package_id" => "vault_vip",
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
                "package_id" => "vault_vip",
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

        $sub = Subscription::where(function ($q) use ($reference, $checkoutRequestId) {
            if (!empty($reference)) {
                $q->orWhere("reference", $reference);
            }
            if (!empty($checkoutRequestId)) {
                $q->orWhere("checkout_request_id", $checkoutRequestId);
            }
        })->first();

        // Also check if any active subscription already exists for this device/username
        if (!$sub || $sub->status !== "active") {
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

        if (!$sub) {
            return response()->json(["success" => false, "paid" => false, "message" => "Transaction not found."], 404);
        }

        return response()->json([
            "success" => true,
            "paid" => $sub->status === "active",
            "status" => $sub->status,
            "expires_at" => $sub->expires_at ? $sub->expires_at->toIso8601String() : null,
        ]);
    }

    /**
     * Webhook callbacks for payment gateways
     */
    public function webhook(Request $request, string $gateway): JsonResponse
    {
        $payload = $request->all();
        Log::info("Payment webhook received [{$gateway}]:", $payload);

        $periodDays = (int)AppSetting::get("vault_subscription_period_days", 30);

        if ($gateway === "mpesa") {
            $callback = $payload["Body"]["stkCallback"] ?? null;
            if ($callback) {
                $checkoutRequestId = $callback["CheckoutRequestID"] ?? null;
                $resultCode = $callback["ResultCode"] ?? -1;

                $sub = Subscription::where("checkout_request_id", $checkoutRequestId)->first();
                if ($sub) {
                    if ($resultCode === 0) {
                        $sub->update([
                            "status" => "active",
                            "starts_at" => now(),
                            "expires_at" => now()->addDays($periodDays),
                            "metadata" => $callback,
                        ]);
                    } else {
                        $sub->update([
                            "status" => "failed",
                            "metadata" => $callback,
                        ]);
                    }
                }
            }
            return response()->json(["ResultCode" => 0, "ResultDesc" => "Accepted"]);
        }

        if ($gateway === "payhero") {
            $status = strtolower($payload["status"] ?? "");
            $externalRef = $payload["external_reference"] ?? $payload["reference"] ?? null;
            if ($externalRef) {
                $sub = Subscription::where("reference", $externalRef)
                    ->orWhere("checkout_request_id", $externalRef)
                    ->first();
                if ($sub) {
                    if (in_array($status, ["success", "successful", "completed"])) {
                        $sub->update([
                            "status" => "active",
                            "starts_at" => now(),
                            "expires_at" => now()->addDays($periodDays),
                            "metadata" => $payload,
                        ]);
                    }
                }
            }
            return response()->json(["status" => "received"]);
        }

        if ($gateway === "flutterwave") {
            $status = strtolower($payload["data"]["status"] ?? $payload["status"] ?? "");
            $txRef = $payload["data"]["tx_ref"] ?? $payload["tx_ref"] ?? null;
            if ($txRef) {
                $sub = Subscription::where("reference", $txRef)->first();
                if ($sub && in_array($status, ["successful", "completed"])) {
                    $sub->update([
                        "status" => "active",
                        "starts_at" => now(),
                        "expires_at" => now()->addDays($periodDays),
                        "metadata" => $payload,
                    ]);
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
        $callbackUrl = (string)AppSetting::get("mpesa_callback_url", "https://lindrapp.top/api/v1/webhooks/mpesa");

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
     * PayHero STK Push implementation
     */
    private function triggerPayheroStkPush(Subscription $sub, string $phone, int $amount, string $username): array
    {
        // PayHero Credentials
        $basicToken = (string)AppSetting::get("payhero_basic_token", env("PAYHERO_BASIC_AUTH_TOKEN", ""));
        $channelId = (string)AppSetting::get("payhero_channel_id", env("PAYHERO_CHANNEL_ID", ""));
        $callbackUrl = url("/api/v1/subscription/webhook/payhero");

        if (!str_starts_with($basicToken, "Basic ")) {
            $basicToken = "Basic " . $basicToken;
        }

        $payload = [
            "amount" => max(1, $amount),
            "phone_number" => $phone,
            "provider" => "m-pesa",
            "external_reference" => $sub->reference,
            "customer_name" => $username,
            "callback_url" => $callbackUrl,
        ];
        if (!empty($channelId)) {
            $payload["channel_id"] = (int)$channelId;
        }

        $res = Http::withHeaders([
            "Authorization" => $basicToken,
            "Content-Type" => "application/json",
            "Accept" => "application/json",
        ])->timeout(25)->post("https://backend.payhero.co.ke/api/v2/payments", $payload);

        if ($res->failed()) {
            $err = $res->json("message") ?? $res->json("error") ?? $res->body();
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
