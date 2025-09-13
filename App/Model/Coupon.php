<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class Coupon implements JsonSerializable
{
	private int $id;
	private string $stripe_coupon_id;
	private string $name;
	private ?string $description;
	private ?int $percent_off;
	private ?int $amount_off;
	private ?string $currency;
	private string $duration;
	private ?int $duration_in_months;
	private ?int $max_redemptions;
	private int $times_redeemed;
	private bool $valid;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, string $stripe_coupon_id, string $name, ?string $description, ?int $percent_off, ?int $amount_off, ?string $currency, string $duration, ?int $duration_in_months, ?int $max_redemptions, int $times_redeemed, bool $valid, string $created_at, string $updated_at){$this->id = $id;$this->stripe_coupon_id = $stripe_coupon_id;$this->name = $name;$this->description = $description;$this->percent_off = $percent_off;$this->amount_off = $amount_off;$this->currency = $currency;$this->duration = $duration;$this->duration_in_months = $duration_in_months;$this->max_redemptions = $max_redemptions;$this->times_redeemed = $times_redeemed;$this->valid = $valid;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getStripeCouponId(): string {return $this->stripe_coupon_id;}

	public function getName(): string {return $this->name;}

	public function getDescription(): ?string {return $this->description;}

	public function getPercentOff(): ?int {return $this->percent_off;}

	public function getAmountOff(): ?int {return $this->amount_off;}

	public function getCurrency(): ?string {return $this->currency;}

	public function getDuration(): string {return $this->duration;}

	public function getDurationInMonths(): ?int {return $this->duration_in_months;}

	public function getMaxRedemptions(): ?int {return $this->max_redemptions;}

	public function getTimesRedeemed(): int {return $this->times_redeemed;}

	public function getValid(): bool {return $this->valid;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"stripe_coupon_id" => $this->stripe_coupon_id,
			"name" => $this->name,
			"description" => $this->description,
			"percent_off" => $this->percent_off,
			"amount_off" => $this->amount_off,
			"currency" => $this->currency,
			"duration" => $this->duration,
			"duration_in_months" => $this->duration_in_months,
			"max_redemptions" => $this->max_redemptions,
			"times_redeemed" => $this->times_redeemed,
			"valid" => $this->valid,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at
        ];
    }
}