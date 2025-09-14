<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class PetDocument implements JsonSerializable
{
	private int $id;
	private int $pet_id;
	private string $title;
	private string $document;
	private string $document_length;
	private string $created_at;
	private ?string $deleted_at;

	public function __construct(int $id, int $pet_id, string $title, string $document, string $document_length, string $created_at, ?string $deleted_at){$this->id = $id;$this->pet_id = $pet_id;$this->title = $title;$this->document = $document;$this->document_length = $document_length;$this->created_at = $created_at;$this->deleted_at = $deleted_at;}
	
	public function getId(): int {return $this->id;}

	public function getPetId(): int {return $this->pet_id;}

	public function getTitle(): string {return $this->title;}

	public function getDocument(): string {return $this->document;}

	public function getDocumentLength(): string {return $this->document_length;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getDeletedAt(): ?string {return $this->deleted_at;}

    public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"petId" => $this->pet_id,
			"title" => $this->title,
			"document" => $this->document,
			"documentLength" => $this->document_length,
			"createdAt" => $this->created_at,
			"deletedAt" => $this->deleted_at,
        ];
    }
}