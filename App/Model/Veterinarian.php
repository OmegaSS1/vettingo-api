<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class Veterinarian implements JsonSerializable
{
	private int $id;
	private int $user_id;
	private ?string $bio;
	private ?string $website;
	private string $crmv;
	private int $crmv_state_id;
	private string $created_at;
	private ?string $deleted_at;
	private string $updated_at;
	private ?string $avatar;
	private bool $isActive;
	private bool $emergencial_attendance;
	private bool $domiciliary_attendance;
	private ?int $phone_id;
	private ?int $professional_email_id;
	private ?string $profile_photos;

	public function __construct(int $id, int $user_id, ?string $bio, ?string $website, string $crmv, int $crmv_state_id, string $created_at, ?string $deleted_at, string $updated_at, ?string $avatar, bool $isActive, bool $emergencial_attendance, bool $domiciliary_attendance, ?int $phone_id, ?int $professional_email_id, ?string $profile_photos){$this->id = $id;$this->user_id = $user_id;$this->bio = $bio;$this->website = $website;$this->crmv = $crmv;$this->crmv_state_id = $crmv_state_id;$this->created_at = $created_at;$this->deleted_at = $deleted_at;$this->updated_at = $updated_at;$this->avatar = $avatar;$this->isActive = $isActive;$this->emergencial_attendance = $emergencial_attendance;$this->domiciliary_attendance = $domiciliary_attendance;$this->phone_id = $phone_id;$this->professional_email_id = $professional_email_id;$this->profile_photos = $profile_photos;}
	
	public function getId(): int {return $this->id;}

	public function getUserId(): int {return $this->user_id;}

	public function getBio(): ?string {return $this->bio;}

	public function getWebsite(): ?string {return $this->website;}

	public function getCrmv(): string {return $this->crmv;}

	public function getCrmvStateId(): int {return $this->crmv_state_id;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getAvatar(): ?string {return $this->avatar;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getEmergencialAttendance(): bool {return $this->emergencial_attendance;}

	public function getDomiciliaryAttendance(): bool {return $this->domiciliary_attendance;}

	public function getPhoneId(): ?int {return $this->phone_id;}

	public function getProfessionalEmailId(): ?int {return $this->professional_email_id;}

	public function getProfilePhotos(): ?string {return $this->profile_photos;}

	public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"userId" => $this->user_id,
			"bio" => $this->bio,
			"website" => $this->website,
			"crmv" => $this->crmv,
			"crmvStateId" => $this->crmv_state_id,
			"createdAt" => $this->created_at,
			"updatedAt" => $this->updated_at,
			"deletedAt" => $this->deleted_at,
			"avatar" => $this->avatar,
			"isActive" => $this->isActive,
			"emergencialAttendance" => $this->emergencial_attendance,
			"domiciliaryAttendance" => $this->domiciliary_attendance,
			"phoneId" => $this->phone_id,
			"professionalEmailId" => $this->professional_email_id,
			"profilePhotos" => $this->profile_photos
        ];
    }
}