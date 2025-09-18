<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class PetConsult implements JsonSerializable
{
	private int $id;
	private ?int $pet_id;
	private string $tutor_name;
	private string $pet_name;
	private int $vet_id;
	private string $consultation_date;
	private ?string $reason;
	private ?string $diagnosis;
	private ?string $treatment;
	private ?string $prescription;
	private ?string $notes;
	private ?int $weight;
	private ?int $temperature;
	private string $status;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, ?int $pet_id, string $tutor_name, string $pet_name, int $vet_id, string $consultation_date, ?string $reason, ?string $diagnosis, ?string $treatment, ?string $prescription, ?string $notes, ?int $weight, ?int $temperature, string $status, string $created_at, string $updated_at){$this->id = $id;$this->pet_id = $pet_id;$this->tutor_name = $tutor_name;$this->pet_name = $pet_name;$this->vet_id = $vet_id;$this->consultation_date = $consultation_date;$this->reason = $reason;$this->diagnosis = $diagnosis;$this->treatment = $treatment;$this->prescription = $prescription;$this->notes = $notes;$this->weight = $weight;$this->temperature = $temperature;$this->status = $status;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getPetId(): int {return $this->pet_id;}
	
	public function getTutorName(): string {return $this->tutor_name;}

	public function getPetName(): string {return $this->pet_name;}

	public function getVetId(): int {return $this->vet_id;}

	public function getConsultationDate(): string {return $this->consultation_date;}

	public function getReason(): ?string {return $this->reason;}

	public function getDiagnosis(): ?string {return $this->diagnosis;}

	public function getTreatment(): ?string {return $this->treatment;}

	public function getPrescription(): ?string {return $this->prescription;}

	public function getNotes(): ?string {return $this->notes;}

	public function getWeight(): ?int {return $this->weight;}

	public function getTemperature(): ?int {return $this->temperature;}

	public function getStatus(): string {return $this->status;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"petId" => $this->pet_id,
			"tutorName" => $this->tutor_name,
			"petName" => $this->pet_name,
			"vetId" => $this->vet_id,
			"consultationDate" => $this->consultation_date,
			"reason" => $this->reason,
			"diagnosis" => $this->diagnosis,
			"treatment" => $this->treatment,
			"prescription" => $this->prescription,
			"notes" => $this->notes,
			"weight" => $this->weight,
			"temperature" => $this->temperature,
			"status" => $this->status,
			"createdAt" => $this->created_at,
			"updatedAt" => $this->updated_at
        ];
    }
}