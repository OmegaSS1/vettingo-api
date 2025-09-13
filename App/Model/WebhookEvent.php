<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class WebhookEvent implements JsonSerializable
{
	private int $id;
	private string $stripe_event_id;
	private string $type;
	private ?string $api_version;
	private string $data;
	private bool $processed;
	private ?string $processed_at;
	private ?string $error_message;
	private string $created_at;

	public function __construct(int $id, string $stripe_event_id, string $type, ?string $api_version, string $data, bool $processed, ?string $processed_at, ?string $error_message, string $created_at){$this->id = $id;$this->stripe_event_id = $stripe_event_id;$this->type = $type;$this->api_version = $api_version;$this->data = $data;$this->processed = $processed;$this->processed_at = $processed_at;$this->error_message = $error_message;$this->created_at = $created_at;}
	
	public function getId(): int {return $this->id;}

	public function getStripeEventId(): string {return $this->stripe_event_id;}

	public function getType(): string {return $this->type;}

	public function getApiVersion(): ?string {return $this->api_version;}

	public function getData(): string {return $this->data;}

	public function getProcessed(): bool {return $this->processed;}

	public function getProcessedAt(): ?string {return $this->processed_at;}

	public function getErrorMessage(): ?string {return $this->error_message;}

	public function getCreatedAt(): string {return $this->created_at;}

	public function jsonSerialize(): array
    {
        return [
			"id" => $this->id,
			"stripe_event_id" => $this->stripe_event_id,
			"type" => $this->type,
			"api_version" => $this->api_version,
			"data" => $this->data,
			"processed" => $this->processed,
			"processed_at" => $this->processed_at,
			"error_message" => $this->error_message,
			"created_at" => $this->created_at
        ];
    }
}