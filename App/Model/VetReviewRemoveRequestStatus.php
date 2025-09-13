<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class VetReviewRemoveRequestStatus implements JsonSerializable
{
	private int $id;
	private int $vet_review_remove_request_id;
	private string $status;
	private string $created_at;
	private string $updated_at;
	private ?string $deleted_at;

	public function __construct(int $id, int $vet_review_remove_request_id, string $status, string $created_at, string $updated_at, ?string $deleted_at){$this->id = $id;$this->vet_review_remove_request_id = $vet_review_remove_request_id;$this->status = $status;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getVetReviewRemoveRequestId(): int {return $this->vet_review_remove_request_id;}

	public function getStatus(): string {return $this->status;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

	
	public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"vet_review_remove_request_id" => $this->vet_review_remove_request_id,
			"status" => $this->status,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			"deleted_at" => $this->deleted_at,
        ];
    }
}