<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class City implements JsonSerializable
{
	private int $id;
	private string $name;
	private int $state_id;
	private bool $isActive;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, string $name, int $state_id, bool $isActive, string $created_at, string $updated_at){$this->id = $id;$this->name = $name;$this->state_id = $state_id;$this->isActive = $isActive;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getName(): string {return $this->name;}

	public function getStateId(): int {return $this->state_id;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"name" => $this->name,
			"state_id" => $this->state_id,
			"isActive" => $this->isActive,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at
        ];
    }
}