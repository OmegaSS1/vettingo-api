<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class Refund implements JsonSerializable
{
	private int $id;
	private int $payment_id;
	private string $stripe_refund_id;
	private int $amount;
	private string $currency;
	private ?string $reason;
	private string $status;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, int $payment_id, string $stripe_refund_id, int $amount, string $currency, ?string $reason, string $status, string $created_at, string $updated_at){$this->id = $id;$this->payment_id = $payment_id;$this->stripe_refund_id = $stripe_refund_id;$this->amount = $amount;$this->currency = $currency;$this->reason = $reason;$this->status = $status;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getPaymentId(): int {return $this->payment_id;}

	public function getStripeRefundId(): string {return $this->stripe_refund_id;}

	public function getAmount(): int {return $this->amount;}

	public function getCurrency(): string {return $this->currency;}

	public function getReason(): ?string {return $this->reason;}

	public function getStatus(): string {return $this->status;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"payment_id" => $this->payment_id,
			"stripe_refund_id" => $this->stripe_refund_id,
			"amount" => $this->amount,
			"currency" => $this->currency,
			"reason" => $this->reason,
			"status" => $this->status,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at
        ];
    }
}