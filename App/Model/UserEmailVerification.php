<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class UserEmailVerification implements JsonSerializable
{
	private int $id;
	private int $user_id;
	private string $token;
	private string $created_at;
	private string $updated_at;
	private string $expired_at;
	private ?string $completed_at;
	private int $user_email_id;

	public function __construct(int $id, int $user_id, string $token, string $created_at, string $updated_at, string $expired_at, ?string $completed_at, int $user_email_id){$this->id = $id;$this->user_id = $user_id;$this->token = $token;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->expired_at = $expired_at;$this->completed_at = $completed_at;$this->user_email_id = $user_email_id;}
	
	public function getId(): int {return $this->id;}

	public function getUserId(): int {return $this->user_id;}

	public function getToken(): string {return $this->token;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getExpiredAt(): string {return $this->expired_at;}

	public function getCompletedAt(): ?string {return $this->completed_at;}

	public function getUserEmailId(): int {return $this->user_email_id;}


    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"user_id" => $this->user_id,
			"token" => $this->token,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"expired_at" => $this->expired_at,
			"completed_at" => $this->completed_at,
			"user_email_id" => $this->user_email_id
        ];
    }
}