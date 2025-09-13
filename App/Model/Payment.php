<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class Payment implements JsonSerializable
{
	private int $id;
	private int $user_id;
	private ?int $subscription_id;
	private ?string $stripe_payment_intent_id;
	private ?string $stripe_invoice_id;
	private int $amount;
	private string $currency;
	private string $status;
	private ?string $description;
	private ?string $metadata;
	private ?string $paid_at;
	private ?string $failed_at;
	private ?string $refunded_at;
	private ?int $refund_amount;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, int $user_id, ?int $subscription_id, ?string $stripe_payment_intent_id, ?string $stripe_invoice_id, int $amount, string $currency, string $status, ?string $description, ?string $metadata, ?string $paid_at, ?string $failed_at, ?string $refunded_at, ?int $refund_amount, string $created_at, string $updated_at){$this->id = $id;$this->user_id = $user_id;$this->subscription_id = $subscription_id;$this->stripe_payment_intent_id = $stripe_payment_intent_id;$this->stripe_invoice_id = $stripe_invoice_id;$this->amount = $amount;$this->currency = $currency;$this->status = $status;$this->description = $description;$this->metadata = $metadata;$this->paid_at = $paid_at;$this->failed_at = $failed_at;$this->refunded_at = $refunded_at;$this->refund_amount = $refund_amount;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getUserId(): int {return $this->user_id;}

	public function getSubscriptionId(): ?int {return $this->subscription_id;}

	public function getStripePaymentIntentId(): ?string {return $this->stripe_payment_intent_id;}

	public function getStripeInvoiceId(): ?string {return $this->stripe_invoice_id;}

	public function getAmount(): int {return $this->amount;}

	public function getCurrency(): string {return $this->currency;}

	public function getStatus(): string {return $this->status;}

	public function getDescription(): ?string {return $this->description;}

	public function getMetadata(): ?string {return $this->metadata;}

	public function getPaidAt(): ?string {return $this->paid_at;}

	public function getFailedAt(): ?string {return $this->failed_at;}

	public function getRefundedAt(): ?string {return $this->refunded_at;}

	public function getRefundAmount(): ?int {return $this->refund_amount;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"user_id" => $this->user_id,
			"subscription_id" => $this->subscription_id,
			"stripe_payment_intent_id" => $this->stripe_payment_intent_id,
			"stripe_invoice_id" => $this->stripe_invoice_id,
			"amount" => $this->amount,
			"currency" => $this->currency,
			"status" => $this->status,
			"description" => $this->description,
			"metadata" => $this->metadata,
			"paid_at" => $this->paid_at,
			"failed_at" => $this->failed_at,
			"refunded_at" => $this->refunded_at,
			"refund_amount" => $this->refund_amount,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at
        ];
    }
}