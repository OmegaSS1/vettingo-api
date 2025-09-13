<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class UserEmail implements JsonSerializable
{
	private int $id;
	private string $email;
	private int $user_id;
	private bool $isPublic;
	private bool $isActive;
	private bool $isPrimary;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, string $email, int $user_id, bool $isPublic, bool $isActive, bool $isPrimary, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->email = $email;$this->user_id = $user_id;$this->isPublic = $isPublic;$this->isActive = $isActive;$this->isPrimary = $isPrimary;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getEmail(): string {return $this->email;}

	public function getUserId(): int {return $this->user_id;}

	public function getIsPublic(): bool {return $this->isPublic;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getIsPrimary(): bool {return $this->isPrimary;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id"=> $this->id,
			"userId"=> $this->user_id,
			"email"=> $this->email,
			"isActive"=> $this->isActive,
			"isPublic"=> $this->isPublic,
			"isPrimary"=> $this->isPrimary,
			"createdAt"=> $this->created_at,
			"updatedAt"=> $this->updated_at,
			"deletedAt"=> $this->deleted_at
        ];
    }
}