<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class VetSpecialization implements JsonSerializable
{
	private int $id;
	private int $veterinarian_id;
	private int $specialization_id;
	private bool $isActive;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, int $veterinarian_id, int $specialization_id, bool $isActive, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->veterinarian_id = $veterinarian_id;$this->specialization_id = $specialization_id;$this->isActive = $isActive;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getVeterinarianId(): int {return $this->veterinarian_id;}

	public function getSpecializationId(): int {return $this->specialization_id;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

	public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"veterinarian_id" => $this->veterinarian_id,
			"specialization_id" => $this->specialization_id,
			"isActive" => $this->isActive,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"deleted_at" => $this->deleted_at,
        ];
    }
}