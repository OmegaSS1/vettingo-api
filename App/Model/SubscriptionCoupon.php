<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class SubscriptionCoupon implements JsonSerializable
{
	private int $id;
	private int $subscription_id;
	private int $coupon_id;
	private ?string $stripe_promotion_code_id;
	private string $applied_at;
	private ?string $expires_at;
	private bool $isActive;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, int $subscription_id, int $coupon_id, ?string $stripe_promotion_code_id, string $applied_at, ?string $expires_at, bool $isActive, string $created_at, string $updated_at){$this->id = $id;$this->subscription_id = $subscription_id;$this->coupon_id = $coupon_id;$this->stripe_promotion_code_id = $stripe_promotion_code_id;$this->applied_at = $applied_at;$this->expires_at = $expires_at;$this->isActive = $isActive;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getSubscriptionId(): int {return $this->subscription_id;}

	public function getCouponId(): int {return $this->coupon_id;}

	public function getStripePromotionCodeId(): ?string {return $this->stripe_promotion_code_id;}

	public function getAppliedAt(): string {return $this->applied_at;}

	public function getExpiresAt(): ?string {return $this->expires_at;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"subscription_id" => $this->subscription_id,
			"coupon_id" => $this->coupon_id,
			"stripe_promotion_code_id" => $this->stripe_promotion_code_id,
			"applied_at" => $this->applied_at,
			"expires_at" => $this->expires_at,
			"isActive" => $this->isActive,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at
        ];
    }
}