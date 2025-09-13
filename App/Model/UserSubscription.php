<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class UserSubscription implements JsonSerializable
{
	private int $id;
	private int $user_id;
	private int $plan_id;
	private string $status;
	private ?string $stripe_subscription_id;
	private ?string $current_period_start;
	private ?string $current_period_end;
	private bool $cancel_at_period_end;
	private ?string $canceled_at;
	private ?string $trial_start;
	private ?string $trial_end;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, int $user_id, int $plan_id, string $status, ?string $stripe_subscription_id, ?string $current_period_start, ?string $current_period_end, bool $cancel_at_period_end, ?string $canceled_at, ?string $trial_start, ?string $trial_end, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->user_id = $user_id;$this->plan_id = $plan_id;$this->status = $status;$this->stripe_subscription_id = $stripe_subscription_id;$this->current_period_start = $current_period_start;$this->current_period_end = $current_period_end;$this->cancel_at_period_end = $cancel_at_period_end;$this->canceled_at = $canceled_at;$this->trial_start = $trial_start;$this->trial_end = $trial_end;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getUserId(): int {return $this->user_id;}

	public function getPlanId(): int {return $this->plan_id;}

	public function getStatus(): string {return $this->status;}

	public function getStripeSubscriptionId(): ?string {return $this->stripe_subscription_id;}

	public function getCurrentPeriodStart(): ?string {return $this->current_period_start;}

	public function getCurrentPeriodEnd(): ?string {return $this->current_period_end;}

	public function getCancelAtPeriodEnd(): bool {return $this->cancel_at_period_end;}

	public function getCanceledAt(): ?string {return $this->canceled_at;}

	public function getTrialStart(): ?string {return $this->trial_start;}

	public function getTrialEnd(): ?string {return $this->trial_end;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"user_id" => $this->user_id,
			"plan_id" => $this->plan_id,
			"status" => $this->status,
			"stripe_subscription_id" => $this->stripe_subscription_id,
			"current_period_start" => $this->current_period_start,
			"current_period_end" => $this->current_period_end,
			"cancel_at_period_end" => $this->cancel_at_period_end,
			"canceled_at" => $this->canceled_at,
			"trial_start" => $this->trial_start,
			"trial_end" => $this->trial_end,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"deleted_at" => $this->deleted_at
        ];
    }
}