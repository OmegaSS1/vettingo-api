<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class User implements JsonSerializable
{
	private int $id;
	private bool $isActive;
	private string $role;
	private string $cpf;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;
	private string $first_name;
	private string $last_name;
	private string $gender;
	private string $birth_date;
	private bool $wants_newsletter;
	private ?string $avatar;
	private ?string $stripe_customer_id;

	public function __construct(int $id, bool $isActive, string $role, string $cpf, string $created_at, string $updated_at, ?string $deleted_at, string $first_name, string $last_name, string $gender, string $birth_date, bool $wants_newsletter, ?string $avatar, ?string $stripe_customer_id){$this->id = $id;$this->isActive = $isActive;$this->role = $role;$this->cpf = $cpf;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;$this->first_name = $first_name;$this->last_name = $last_name;$this->gender = $gender;$this->birth_date = $birth_date;$this->wants_newsletter = $wants_newsletter;$this->avatar = $avatar;$this->stripe_customer_id = $stripe_customer_id;}
	
	public function getId(): int {return $this->id;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getRole(): string {return $this->role;}

	public function getCpf(): string {return $this->cpf;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

	public function getFirstName(): string {return $this->first_name;}

	public function getLastName(): string {return $this->last_name;}

	public function getGender(): string {return $this->gender;}

	public function getBirthDate(): string {return $this->birth_date;}

	public function getWantsNewsletter(): bool {return $this->wants_newsletter;}

	public function getAvatar(): ?string {return $this->avatar;}

	public function getStripeCustomerId(): ?string {return $this->stripe_customer_id;}

	#[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"isActive" => $this->isActive,
			"role" => $this->role,
			"cpf" => $this->cpf,
			"createdAt" => $this->created_at,
			"updatedAt" => $this->updated_at,
			"deletedAt" => $this->deleted_at,
			"firstName" => $this->first_name,
			"lastName" => $this->last_name,
			"gender" => $this->gender,
			"birthDate" => $this->birth_date,
			"wantsNewsletter" => $this->wants_newsletter,
			"avatar" => $this->avatar,
			"stripeCustomerId" => $this->stripe_customer_id
        ];
    }
}