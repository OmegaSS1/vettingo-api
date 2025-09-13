<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class PaymentMethod implements JsonSerializable
{
	private int $id;
	private int $user_id;
	private string $stripe_payment_method_id;
	private string $type;
	private ?string $brand;
	private ?string $last4;
	private ?int $exp_month;
	private ?int $exp_year;
	private bool $is_default;
	private bool $is_active;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, int $user_id, string $stripe_payment_method_id, string $type, ?string $brand, ?string $last4, ?int $exp_month, ?int $exp_year, bool $is_default, bool $is_active, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->user_id = $user_id;$this->stripe_payment_method_id = $stripe_payment_method_id;$this->type = $type;$this->brand = $brand;$this->last4 = $last4;$this->exp_month = $exp_month;$this->exp_year = $exp_year;$this->is_default = $is_default;$this->is_active = $is_active;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getUserId(): int {return $this->user_id;}

	public function getStripePaymentMethodId(): string {return $this->stripe_payment_method_id;}

	public function getType(): string {return $this->type;}

	public function getBrand(): ?string {return $this->brand;}

	public function getLast4(): ?string {return $this->last4;}

	public function getExpMonth(): ?int {return $this->exp_month;}

	public function getExpYear(): ?int {return $this->exp_year;}

	public function getIsDefault(): bool {return $this->is_default;}

	public function getIsActive(): bool {return $this->is_active;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"user_id" => $this->user_id,
			"stripe_payment_method_id" => $this->stripe_payment_method_id,
			"type" => $this->type,
			"brand" => $this->brand,
			"last4" => $this->last4,
			"exp_month" => $this->exp_month,
			"exp_year" => $this->exp_year,
			"is_default" => $this->is_default,
			"is_active" => $this->is_active,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"deleted_at" => $this->deleted_at
        ];
    }
}