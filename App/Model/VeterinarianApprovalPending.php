<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class VeterinarianApprovalPending implements JsonSerializable
{
	private int $id;
	private int $veterinarian_id;
	private ?string $rg_front_image_url;
	private ?string $rg_back_image_url;
	private ?string $crmv_document_image_url;
	private string $status;
	private string $created_at;
	private string $updated_at;
	private ?string $approved_at;
	private ?int $approved_by;
	private ?string $rejected_at;
	private ?int $rejected_by;
	private ?string $rejection_reason;

	public function __construct(int $id, int $veterinarian_id, ?string $rg_front_image_url, ?string $rg_back_image_url, ?string $crmv_document_image_url, string $status, string $created_at, string $updated_at, ?string $approved_at, ?int $approved_by, ?string $rejected_at, ?int $rejected_by, ?string $rejection_reason){$this->id = $id;$this->veterinarian_id = $veterinarian_id;$this->rg_front_image_url = $rg_front_image_url;$this->rg_back_image_url = $rg_back_image_url;$this->crmv_document_image_url = $crmv_document_image_url;$this->status = $status;$this->created_at = $created_at;$this->updated_at = $updated_at;$this->approved_at = $approved_at;$this->approved_by = $approved_by;$this->rejected_at = $rejected_at;$this->rejected_by = $rejected_by;$this->rejection_reason = $rejection_reason;}
	
	public function getId(): int {return $this->id;}

	public function getVeterinarianId(): int {return $this->veterinarian_id;}

	public function getRgFrontImageUrl(): ?string {return $this->rg_front_image_url;}

	public function getRgBackImageUrl(): ?string {return $this->rg_back_image_url;}

	public function getCrmvDocumentImageUrl(): ?string {return $this->crmv_document_image_url;}

	public function getStatus(): string {return $this->status;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function getUpdatedAt(): string {return $this->updated_at;}

	public function getApprovedAt(): ?string {return $this->approved_at;}

	public function getApprovedBy(): ?int {return $this->approved_by;}

	public function getRejectedAt(): ?string {return $this->rejected_at;}

	public function getRejectedBy(): ?int {return $this->rejected_by;}

	public function getRejectionReason(): ?string {return $this->rejection_reason;}

	public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"veterinarianId" => $this->veterinarian_id,
			"rgFrontImageUrl" => $this->rg_front_image_url,
			"rgBackImageUrl" => $this->rg_back_image_url,
			"crmvDocumentImageUrl" => $this->crmv_document_image_url,
			"status" => $this->status,
			"createdAt" => $this->created_at,
			"updatedAt" => $this->updated_at,
			"approvedAt" => $this->approved_at,
			"approvedBy" => $this->approved_by,
			"rejectedAt" => $this->rejected_at,
			"rejectedBy" => $this->rejected_by,
			"rejectionReason" => $this->rejection_reason
        ];
    }
}