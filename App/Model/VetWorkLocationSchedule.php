<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class VetWorkLocationSchedule implements JsonSerializable
{
	private int $id;
	private int $vet_work_location_id;
	private int $day_of_week;
	private string $start_time;
	private string $end_time;
	private bool $isActive;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, int $vet_work_location_id, int $day_of_week, string $start_time, string $end_time, bool $isActive, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->vet_work_location_id = $vet_work_location_id;$this->day_of_week = $day_of_week;$this->start_time = $start_time;$this->end_time = $end_time;$this->isActive = $isActive;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getVetWorkLocationId(): int {return $this->vet_work_location_id;}

	public function getDayOfWeek(): int {return $this->day_of_week;}

	public function getStartTime(): string {return $this->start_time;}

	public function getEndTime(): string {return $this->end_time;}

	public function getIsActive(): bool {return $this->isActive;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

	public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"vetWorkLocationId" => $this->vet_work_location_id,
			"dayOfWeek" => $this->day_of_week,
			"startTime" => $this->start_time,
			"endTime" => $this->end_time,
			"isActive" => $this->isActive,
			"createdAt" => $this->created_at,
			"updatedAt" => $this->updated_at,
			"deletedAt" => $this->deleted_at
        ];
    }
}