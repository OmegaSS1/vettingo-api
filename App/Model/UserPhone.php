<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class UserPhone implements JsonSerializable
{
	private int $id;
	private int $user_id;
	private string $number;
	private string $areaCode;
	private string $countryCode;
	private bool $isPublic;
	private bool $isWhatsapp;
	private bool $isActive;
	private bool $isPrimary;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;
	
	public function __construct(int $id, int $user_id, string $number, string $areaCode, string $countryCode, bool $isPublic, bool $isWhatsapp, bool $isActive, bool $isPrimary, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->user_id = $user_id;$this->number = $number;$this->areaCode = $areaCode;$this->countryCode = $countryCode;$this->isPublic = $isPublic;$this->isWhatsapp = $isWhatsapp;$this->isActive = $isActive;$this->isPrimary = $isPrimary;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}
	
	public function getUserId(): int {return $this->user_id;}

	public function getNumber(): string {return $this->number;}

	public function getAreaCode(): string {return $this->areaCode;}

	public function getCountryCode(): string {return $this->countryCode;}

	public function getIsPublic(): bool {return $this->isPublic;}

	public function getIsWhatsapp(): bool {return $this->isWhatsapp;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getIsPrimary(): bool {return $this->isPrimary;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"userId" => $this->user_id,
			"number" => $this->number,
			"ddd" => $this->areaCode,
			"ddi" => $this->countryCode,
			"isPublic" => $this->isPublic,
			"isWhatsapp" => $this->isWhatsapp,
			"isActive" => $this->isActive,
			"isPrimary" => $this->isPrimary,
			"createdAt" => $this->created_at,
			"updatedAt" => $this->updated_at,
			"deletedAt" => $this->deleted_at
        ];
    }
}