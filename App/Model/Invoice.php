<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class Invoice implements JsonSerializable
{
	private int $id;
	private int $user_id;
	private ?int $subscription_id;
	private string $stripe_invoice_id;
	private ?string $stripe_invoice_number;
	private int $amount_due;
	private int $amount_paid;
	private int $amount_remaining;
	private string $currency;
	private string $status;
	private ?string $billing_reason;
	private ?string $due_date;
	private ?string $paid_at;
	private ?string $period_start;
	private ?string $period_end;
	private int $subtotal;
	private ?int $tax;
	private int $total;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, int $user_id, ?int $subscription_id, string $stripe_invoice_id, ?string $stripe_invoice_number, int $amount_due, int $amount_paid, int $amount_remaining, string $currency, string $status, ?string $billing_reason, ?string $due_date, ?string $paid_at, ?string $period_start, ?string $period_end, int $subtotal, ?int $tax, int $total, string $created_at, string $updated_at){$this->id = $id;$this->user_id = $user_id;$this->subscription_id = $subscription_id;$this->stripe_invoice_id = $stripe_invoice_id;$this->stripe_invoice_number = $stripe_invoice_number;$this->amount_due = $amount_due;$this->amount_paid = $amount_paid;$this->amount_remaining = $amount_remaining;$this->currency = $currency;$this->status = $status;$this->billing_reason = $billing_reason;$this->due_date = $due_date;$this->paid_at = $paid_at;$this->period_start = $period_start;$this->period_end = $period_end;$this->subtotal = $subtotal;$this->tax = $tax;$this->total = $total;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getUserId(): int {return $this->user_id;}

	public function getSubscriptionId(): ?int {return $this->subscription_id;}

	public function getStripeInvoiceId(): string {return $this->stripe_invoice_id;}

	public function getStripeInvoiceNumber(): ?string {return $this->stripe_invoice_number;}

	public function getAmountDue(): int {return $this->amount_due;}

	public function getAmountPaid(): int {return $this->amount_paid;}

	public function getAmountRemaining(): int {return $this->amount_remaining;}

	public function getCurrency(): string {return $this->currency;}

	public function getStatus(): string {return $this->status;}

	public function getBillingReason(): ?string {return $this->billing_reason;}

	public function getDueDate(): ?string {return $this->due_date;}

	public function getPaidAt(): ?string {return $this->paid_at;}

	public function getPeriodStart(): ?string {return $this->period_start;}

	public function getPeriodEnd(): ?string {return $this->period_end;}

	public function getSubtotal(): int {return $this->subtotal;}

	public function getTax(): ?int {return $this->tax;}

	public function getTotal(): int {return $this->total;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"user_id" => $this->user_id,
			"subscription_id" => $this->subscription_id,
			"stripe_invoice_id" => $this->stripe_invoice_id,
			"stripe_invoice_number" => $this->stripe_invoice_number,
			"amount_due" => $this->amount_due,
			"amount_paid" => $this->amount_paid,
			"amount_remaining" => $this->amount_remaining,
			"currency" => $this->currency,
			"status" => $this->status,
			"billing_reason" => $this->billing_reason,
			"due_date" => $this->due_date,
			"paid_at" => $this->paid_at,
			"period_start" => $this->period_start,
			"period_end" => $this->period_end,
			"subtotal" => $this->subtotal,
			"tax" => $this->tax,
			"total" => $this->total,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at
        ];
    }
}