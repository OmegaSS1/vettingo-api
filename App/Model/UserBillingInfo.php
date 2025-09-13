<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class UserBillingInfo implements JsonSerializable
{
	private int $id;
	private int $user_id;
	private ?string $full_name;
	private string $street;
	private string $number;
	private ?string $complement;
	private string $neighborhood;
	private string $zip_code;
	private int $city_id;
	private int $state_id;
	private ?string $strip_customer_id;
	private bool $is_active;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, int $user_id, ?string $full_name, string $street, string $number, ?string $complement, string $neighborhood, string $zip_code, int $city_id, int $state_id, ?string $strip_customer_id, bool $is_active, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->user_id = $user_id;$this->full_name = $full_name;$this->street = $street;$this->number = $number;$this->complement = $complement;$this->neighborhood = $neighborhood;$this->zip_code = $zip_code;$this->city_id = $city_id;$this->state_id = $state_id;$this->strip_customer_id = $strip_customer_id;$this->is_active = $is_active;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getUserId(): int {return $this->user_id;}

	public function getFullName(): ?string {return $this->full_name;}

	public function getStreet(): string {return $this->street;}

	public function getNumber(): string {return $this->number;}

	public function getComplement(): ?string {return $this->complement;}

	public function getNeighborhood(): string {return $this->neighborhood;}

	public function getZipCode(): string {return $this->zip_code;}

	public function getCityId(): int {return $this->city_id;}

	public function getStateId(): int {return $this->state_id;}

	public function getStripCustomerId(): ?string {return $this->strip_customer_id;}

	public function getIsActive(): bool {return $this->is_active;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"user_id" => $this->user_id,
			"full_name" => $this->full_name,
			"street" => $this->street,
			"number" => $this->number,
			"complement" => $this->complement,
			"neighborhood" => $this->neighborhood,
			"zip_code" => $this->zip_code,
			"city_id" => $this->city_id,
			"state_id" => $this->state_id,
			"strip_customer_id" => $this->strip_customer_id,
			"is_active" => $this->is_active,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"deleted_at" => $this->deleted_at
        ];
    }
}