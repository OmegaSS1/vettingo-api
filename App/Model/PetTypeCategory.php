<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class PetTypeCategory implements JsonSerializable
{
	private int $id;
	private string $name;
	private ?string $description;
	private bool $isActive;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, string $name, ?string $description, bool $isActive, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->name = $name;$this->description = $description;$this->isActive = $isActive;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getName(): string {return $this->name;}

	public function getDescription(): ?string {return $this->description;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"name" => $this->name,
			"description" => $this->description,
			"isActive" => $this->isActive,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"deleted_at" => $this->deleted_at
        ];
    }
}