<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class SubscriptionHistory implements JsonSerializable
{
	private int $id;
	private int $subscription_id;
	private ?int $old_plan_id;
	private int $new_plan_id;
	private ?string $old_status;
	private string $new_status;
	private ?string $reason;
	private ?string $stripe_event_id;
	private string $created_at;

	public function __construct(int $id, int $subscription_id, ?int $old_plan_id, int $new_plan_id, ?string $old_status, string $new_status, ?string $reason, ?string $stripe_event_id, string $created_at){$this->id = $id;$this->subscription_id = $subscription_id;$this->old_plan_id = $old_plan_id;$this->new_plan_id = $new_plan_id;$this->old_status = $old_status;$this->new_status = $new_status;$this->reason = $reason;$this->stripe_event_id = $stripe_event_id;$this->created_at = $created_at;}
	
	public function getId(): int {return $this->id;}

	public function getSubscriptionId(): int {return $this->subscription_id;}

	public function getOldPlanId(): ?int {return $this->old_plan_id;}

	public function getNewPlanId(): int {return $this->new_plan_id;}

	public function getOldStatus(): ?string {return $this->old_status;}

	public function getNewStatus(): string {return $this->new_status;}

	public function getReason(): ?string {return $this->reason;}

	public function getStripeEventId(): ?string {return $this->stripe_event_id;}

	public function getCreatedAt(): string {return $this->created_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"subscription_id" => $this->subscription_id,
			"old_plan_id" => $this->old_plan_id,
			"new_plan_id" => $this->new_plan_id,
			"old_status" => $this->old_status,
			"new_status" => $this->new_status,
			"reason" => $this->reason,
			"stripe_event_id" => $this->stripe_event_id,
			"created_at" => $this->created_at,
        ];
    }
}