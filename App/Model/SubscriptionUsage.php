<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class SubscriptionUsage implements JsonSerializable
{
	private int $id;
	private int $subscription_id;
	private string $feature;
	private int $quantity;
	private string $period_start;
	private string $period_end;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, int $subscription_id, string $feature, int $quantity, string $period_start, string $period_end, string $created_at, string $updated_at){$this->id = $id;$this->subscription_id = $subscription_id;$this->feature = $feature;$this->quantity = $quantity;$this->period_start = $period_start;$this->period_end = $period_end;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getSubscriptionId(): int {return $this->subscription_id;}

	public function getFeature(): string {return $this->feature;}

	public function getQuantity(): int {return $this->quantity;}

	public function getPeriodStart(): string {return $this->period_start;}

	public function getPeriodEnd(): string {return $this->period_end;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"subscription_id" => $this->subscription_id,
			"feature" => $this->feature,
			"quantity" => $this->quantity,
			"period_start" => $this->period_start,
			"period_end" => $this->period_end,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at
        ];
    }
}