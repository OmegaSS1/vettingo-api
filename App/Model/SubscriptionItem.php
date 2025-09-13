<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class SubscriptionItem implements JsonSerializable
{
	private int $id;
	private int $subscription_id;
	private string $stripe_subscription_item_id;
	private string $stripe_price_id;
	private int $quantity;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, int $subscription_id, string $stripe_subscription_item_id, string $stripe_price_id, int $quantity, string $created_at, string $updated_at){$this->id = $id;$this->subscription_id = $subscription_id;$this->stripe_subscription_item_id = $stripe_subscription_item_id;$this->stripe_price_id = $stripe_price_id;$this->quantity = $quantity;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getSubscriptionId(): int {return $this->subscription_id;}

	public function getStripeSubscriptionItemId(): string {return $this->stripe_subscription_item_id;}

	public function getStripePriceId(): string {return $this->stripe_price_id;}

	public function getQuantity(): int {return $this->quantity;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"subscription_id" => $this->subscription_id,
			"stripe_subscription_item_id" => $this->stripe_subscription_item_id,
			"stripe_price_id" => $this->stripe_price_id,
			"quantity" => $this->quantity,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at
        ];
    }
}