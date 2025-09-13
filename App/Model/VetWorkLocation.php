<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class VetWorkLocation implements JsonSerializable
{
	private int $id;
	private int $veterinarian_id;
	private int $city_id;
	private string $address;
	private string $number;
	private ?string $complement;
	private string $neighborhood;
	private string $zip_code;
	private ?float $latitude;
	private ?float $longitude;
	private bool $isActive;
	private string $created_at;
	private string $updated_at;
	private int $state_id;
	private string $name;
	private ?string $deleted_at;

	public function __construct(int $id, int $veterinarian_id, int $city_id, string $address, string $number, ?string $complement, string $neighborhood, string $zip_code, ?float $latitude, ?float $longitude, bool $isActive, string $created_at, string $updated_at, int $state_id, string $name, ?string $deleted_at){$this->id = $id;$this->veterinarian_id = $veterinarian_id;$this->city_id = $city_id;$this->address = $address;$this->number = $number;$this->complement = $complement;$this->neighborhood = $neighborhood;$this->zip_code = $zip_code;$this->latitude = $latitude;$this->longitude = $longitude;$this->isActive = $isActive;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->state_id = $state_id;$this->name = $name;$this->deleted_at = $deleted_at;}

	public function getId(): int {return $this->id;}

	public function getVeterinarianId(): int {return $this->veterinarian_id;}

	public function getCityId(): int {return $this->city_id;}

	public function getAddress(): string {return $this->address;}

	public function getNumber(): string {return $this->number;}

	public function getComplement(): ?string {return $this->complement;}

	public function getNeighborhood(): string {return $this->neighborhood;}

	public function getZipCode(): string {return $this->zip_code;}

	public function getLatitude(): ?float {return $this->latitude;}

	public function getLongitude(): ?float {return $this->longitude;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getStateId(): int {return $this->state_id;}

	public function getName(): string {return $this->name;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}
	

	public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"veterinarianId" => $this->veterinarian_id,
			"cityId" => $this->city_id,
			"address" => $this->address,
			"number" => $this->number,
			"complement" => $this->complement,
			"neighborhood" => $this->neighborhood,
			"zipCode" => $this->zip_code,
			"latitude" => $this->latitude,
			"longitude" => $this->longitude,
			"isActive" => $this->isActive,
			"createdAt" => $this->created_at,
			"updatedAt" => $this->updated_at,
			"stateId" => $this->state_id,
			"name" => $this->name,
			"deletedAt" => $this->deleted_at,
        ];
    }
}