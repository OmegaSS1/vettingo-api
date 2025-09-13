<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class VetReview implements JsonSerializable
{
	private int $id;
	private bool $anonymous;
	private ?string $authorName;
	private ?string $authorAvatar;
	private int $veterinarian_id;
	private int $rating;
	private ?string $comment;
	private string $created_at;
	private string $updated_at;

	public function __construct(int $id, bool $anonymous, ?string $authorName, ?string $authorAvatar, int $veterinarian_id, int $rating, ?string $comment, string $created_at, string $updated_at){$this->id = $id;$this->anonymous = $anonymous;$this->authorName = $authorName;$this->authorAvatar = $authorAvatar;$this->veterinarian_id = $veterinarian_id;$this->rating = $rating;$this->comment = $comment;$this->created_at = $created_at;$this->updated_at = $updated_at;}
	
	public function getId(): int {return $this->id;}

	public function getAnonymous(): bool {return $this->anonymous;}

	public function getAuthorName(): ?string {return $this->authorName;}

	public function getAuthorAvatar(): ?string {return $this->authorAvatar;}

	public function getVeterinarianId(): int {return $this->veterinarian_id;}

	public function getRating(): int {return $this->rating;}

	public function getComment(): ?string {return $this->comment;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"anonymous" => $this->anonymous,
			"authorName" => $this->authorName,
			"authorAvatar" => $this->authorAvatar,
			"veterinarian_id" => $this->veterinarian_id,
			"rating" => $this->rating,
			"comment" => $this->comment,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
        ];
    }
}