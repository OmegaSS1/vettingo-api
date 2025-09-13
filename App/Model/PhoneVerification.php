<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class PhoneVerification implements JsonSerializable
{
	private int $id;
	private int $phone_id;
	private string $token;
	private string $created_at;
	private string $updated_at;
	private string $expired_at;
	private ?string $completed_at;
	
	public function __construct(int $id, int $phone_id, string $token, string $created_at, string $updated_at, string $expired_at, ?string $completed_at){$this->id = $id;$this->phone_id = $phone_id;$this->token = $token;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->expired_at = $expired_at;$this->completed_at = $completed_at;}
	
	public function getId(): int {return $this->id;}

	public function getPhoneId(): int {return $this->phone_id;}

	public function getToken(): string {return $this->token;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getExpiredAt(): string {return $this->expired_at;}

	public function getCompletedAt(): ?string {return $this->completed_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"phone_id" => $this->phone_id,
			"token" => $this->token,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"expired_at" => $this->expired_at,
			"completed_at" => $this->completed_at
        ];
    }
}