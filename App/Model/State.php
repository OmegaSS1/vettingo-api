<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class State implements JsonSerializable
{
	private int $id;
	private string $name;
	private string $uf;
	private bool $isActive;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, string $name, string $uf, bool $isActive, string $created_at, string $updated_at){$this->id = $id;$this->name = $name;$this->uf = $uf;$this->isActive = $isActive;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getName(): string {return $this->name;}

	public function getUf(): string {return $this->uf;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"name" => $this->name,
			"uf" => $this->uf,
			"isActive" => $this->isActive,
			"createdAt" => $this->created_at,
			"updatedAt" => $this->updated_at
        ];
    }
}