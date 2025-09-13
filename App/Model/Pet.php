<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class Pet implements JsonSerializable
{
	private int $id;
	private string $name;
	private int $owner_id;
	private int $pet_type_id;
	private ?string $breed;
	private ?string $birth_date;
	private ?string $gender;
	private ?string $weight;
	private bool $has_pedigree;
	private ?string $pedigree_number;
	private ?string $avatar;
	private ?string $description;
	private bool $isActive;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, string $name, int $owner_id, int $pet_type_id, ?string $breed, ?string $birth_date, ?string $gender, ?string $weight, bool $has_pedigree, ?string $pedigree_number, ?string $avatar, ?string $description, bool $isActive, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->name = $name;$this->owner_id = $owner_id;$this->pet_type_id = $pet_type_id;$this->breed = $breed;$this->birth_date = $birth_date;$this->gender = $gender;$this->weight = $weight;$this->has_pedigree = $has_pedigree;$this->pedigree_number = $pedigree_number;$this->avatar = $avatar;$this->description = $description;$this->isActive = $isActive;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getName(): string {return $this->name;}

	public function getOwnerId(): int {return $this->owner_id;}

	public function getPetTypeId(): int {return $this->pet_type_id;}

	public function getBreed(): ?string {return $this->breed;}

	public function getBirthDate(): ?string {return $this->birth_date;}

	public function getGender(): ?string {return $this->gender;}

	public function getWeight(): ?string {return $this->weight;}

	public function getHasPedigree(): bool {return $this->has_pedigree;}

	public function getPedigreeNumber(): ?string {return $this->pedigree_number;}

	public function getAvatar(): ?string {return $this->avatar;}

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
			"ownerId" => $this->owner_id,
			"petTypeId" => $this->pet_type_id,
			"breed" => $this->breed,
			"birthDate" => $this->birth_date,
			"gender" => $this->gender,
			"weight" => $this->weight,
			"hasPedigree" => $this->has_pedigree,
			"pedigreeNumber" => $this->pedigree_number,
			"avatar" => $this->avatar,
			"description" => $this->description,
			"isActive" => $this->isActive,
			"createdAt" => $this->created_at,
			"updatedAt" => $this->updated_at,
			"deletedAt" => $this->deleted_at
        ];
    }
}