<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class VetReviewRemoveRequest implements JsonSerializable
{
	private int $id;
	private int $veterinarian_id;
	private int $review_id;
	private string $reason;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, int $veterinarian_id, int $review_id, string $reason, string $created_at, string $updated_at){$this->id = $id;$this->veterinarian_id = $veterinarian_id;$this->review_id = $review_id;$this->reason = $reason;$this->created_at = $created_at;$this->updated_at = $updated_at;}

	public function getId(): int {return $this->id;}

	public function getVeterinarianId(): int {return $this->veterinarian_id;}

	public function getReviewId(): int {return $this->review_id;}

	public function getReason(): string {return $this->reason;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	
	public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"veterinarian_id" => $this->veterinarian_id,
			"review_id" => $this->review_id,
			"reason" => $this->reason,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
        ];
    }
}