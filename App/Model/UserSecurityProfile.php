<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class UserSecurityProfile implements JsonSerializable
{
	private int $id;
	private int $user_id;
	private string $password;	
	private string $created_at;	
	private string $updated_at;	
	private ?string $deleted_at;
	
	public function __construct(int $id, int $user_id, string $password, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->user_id = $user_id;$this->password = $password;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getUserId(): int {return $this->user_id;}

	public function getPassword(): string {return $this->password;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"user_id"=> $this->user_id,
			"password"=> $this->password,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"deleted_at" => $this->deleted_at,

        ];
    }
}