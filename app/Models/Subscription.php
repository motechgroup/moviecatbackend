<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Subscription extends Model
{
    protected $fillable = [
        "device_id",
        "username",
        "package_id",
        "gateway",
        "amount",
        "currency",
        "reference",
        "checkout_request_id",
        "phone",
        "status",
        "starts_at",
        "expires_at",
        "metadata",
    ];

    protected function casts(): array
    {
        return [
            "amount" => "decimal:2",
            "starts_at" => "datetime",
            "expires_at" => "datetime",
            "metadata" => "json",
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, "device_id", "device_id");
    }

    public function isActive(): bool
    {
        return $this->status === "active" && $this->expires_at && $this->expires_at->isFuture();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where("status", "active")->where("expires_at", ">", now());
    }

    public static function hasActiveFor(?string $deviceId, ?string $username): bool
    {
        if (blank($deviceId) && blank($username)) {
            return false;
        }

        return static::active()
            ->where(function (Builder $query) use ($deviceId, $username) {
                if (!empty($deviceId)) {
                    $query->orWhere("device_id", $deviceId);
                }
                if (!empty($username)) {
                    $query->orWhere("username", $username);
                }
            })
            ->exists();
    }

    public static function getActiveFor(?string $deviceId, ?string $username): ?static
    {
        if (blank($deviceId) && blank($username)) {
            return null;
        }

        return static::active()
            ->where(function (Builder $query) use ($deviceId, $username) {
                if (!empty($deviceId)) {
                    $query->orWhere("device_id", $deviceId);
                }
                if (!empty($username)) {
                    $query->orWhere("username", $username);
                }
            })
            ->latest("expires_at")
            ->first();
    }
}
