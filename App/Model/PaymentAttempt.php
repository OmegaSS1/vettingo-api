<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class PaymentAttempt implements JsonSerializable
{
	private int $id;
	private int $payment_id;
	private string $stripe_payment_intent_id;
	private int $amount;
	private string $currency;
	private string $status;
	private ?int $payment_method_id;
	private ?string $failure_reason;
	private ?string $failure_code;
	private ?string $next_action;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, int $payment_id, string $stripe_payment_intent_id, int $amount, string $currency, string $status, ?int $payment_method_id, ?string $failure_reason, ?string $failure_code, ?string $next_action, string $created_at, string $updated_at){$this->id = $id;$this->payment_id = $payment_id;$this->stripe_payment_intent_id = $stripe_payment_intent_id;$this->amount = $amount;$this->currency = $currency;$this->status = $status;$this->payment_method_id = $payment_method_id;$this->failure_reason = $failure_reason;$this->failure_code = $failure_code;$this->next_action = $next_action;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getPaymentId(): int {return $this->payment_id;}

	public function getStripePaymentIntentId(): string {return $this->stripe_payment_intent_id;}

	public function getAmount(): int {return $this->amount;}

	public function getCurrency(): string {return $this->currency;}

	public function getStatus(): string {return $this->status;}

	public function getPaymentMethodId(): ?int {return $this->payment_method_id;}

	public function getFailureReason(): ?string {return $this->failure_reason;}

	public function getFailureCode(): ?string {return $this->failure_code;}

	public function getNextAction(): ?string {return $this->next_action;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"payment_id" => $this->payment_id,
			"stripe_payment_intent_id" => $this->stripe_payment_intent_id,
			"amount" => $this->amount,
			"currency" => $this->currency,
			"status" => $this->status,
			"payment_method_id" => $this->payment_method_id,
			"failure_reason" => $this->failure_reason,
			"failure_code" => $this->failure_code,
			"next_action" => $this->next_action,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at
        ];
    }
}