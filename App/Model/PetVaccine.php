<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class PetVaccine implements JsonSerializable
{
	private int $id;
	private int $pet_id;
	private string $vaccine_name;
	private string $vaccination_date;
	private ?string $next_due_date;
	private ?int $vet_id;
	private ?string $batch_number;
	private string $status;
	private ?string $notes;
	private string $created_at;
	private ?string $deleted_at;

	public function __construct(int $id, int $pet_id, string $vaccine_name, string $vaccination_date, ?string $next_due_date, ?int $vet_id, ?string $batch_number, string $status, ?string $notes, string $created_at, ?string $deleted_at){$this->id = $id;$this->pet_id = $pet_id;$this->vaccine_name = $vaccine_name;$this->vaccination_date = $vaccination_date;$this->next_due_date = $next_due_date;$this->vet_id = $vet_id;$this->batch_number = $batch_number;$this->status = $status;$this->notes = $notes;$this->created_at = $created_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getPetId(): int {return $this->pet_id;}

	public function getVaccineName(): string {return $this->vaccine_name;}

	public function getVaccinationDate(): string {return $this->vaccination_date;}

	public function getNextDueDate(): ?string {return $this->next_due_date;}

	public function getVetId(): ?int {return $this->vet_id;}

	public function getBatchNumber(): ?string {return $this->batch_number;}

	public function getStatus(): string {return $this->status;}

	public function getNotes(): ?string {return $this->notes;}

	public function getCreatedAt(): string {return $this->created_at;}
	
	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"petId" => $this->pet_id,
			"vaccineName" => $this->vaccine_name,
			"vaccinationDate" => $this->vaccination_date,
			"nextDueDate" => $this->next_due_date,
			"vetId" => $this->vet_id,
			"batchNumber" => $this->batch_number,
			"status" => $this->status,
			"notes" => $this->notes,
			"createdAt" => $this->created_at,
			"deletedAt" => $this->deleted_at,
        ];
    }
}