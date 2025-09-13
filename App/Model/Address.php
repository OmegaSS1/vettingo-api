<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class Address implements JsonSerializable
{
	private int $id;
	private int $user_id;
	private string $type;
	private ?string $label;
	private string $street;
	private string $number;
	private ?string $complement;
	private string $neighborhood;
	private string $zip_code;
	private int $city_id;
	private int $state_id;
	private bool $is_primary;
	private bool $is_active;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, int $user_id, string $type, ?string $label, string $street, string $number, ?string $complement, string $neighborhood, string $zip_code, int $city_id, int $state_id, bool $is_primary, bool $is_active, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->user_id = $user_id;$this->type = $type;$this->label = $label;$this->street = $street;$this->number = $number;$this->complement = $complement;$this->neighborhood = $neighborhood;$this->zip_code = $zip_code;$this->city_id = $city_id;$this->state_id = $state_id;$this->is_primary = $is_primary;$this->is_active = $is_active;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getUserId(): int {return $this->user_id;}

	public function getType(): string {return $this->type;}

	public function getLabel(): ?string {return $this->label;}

	public function getStreet(): string {return $this->street;}

	public function getNumber(): string {return $this->number;}

	public function getComplement(): ?string {return $this->complement;}

	public function getNeighborhood(): string {return $this->neighborhood;}

	public function getZipCode(): string {return $this->zip_code;}

	public function getCityId(): int {return $this->city_id;}

	public function getStateId(): int {return $this->state_id;}

	public function getIsPrimary(): bool {return $this->is_primary;}

	public function getIsActive(): bool {return $this->is_active;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"user_id" => $this->user_id,
			"type" => $this->type,
			"label" => $this->label,
			"street" => $this->street,
			"number" => $this->number,
			"complement" => $this->complement,
			"neighborhood" => $this->neighborhood,
			"zip_code" => $this->zip_code,
			"city_id" => $this->city_id,
			"state_id" => $this->state_id,
			"is_primary" => $this->is_primary,
			"is_active" => $this->is_active,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"deleted_at" => $this->deleted_at,
        ];
    }
}