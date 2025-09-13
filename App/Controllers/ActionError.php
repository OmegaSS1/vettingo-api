<?php

declare(strict_types=1);

namespace App\Controllers;
use JsonSerializable;

class ActionError implements JsonSerializable
{
    public const BAD_REQUEST = 'Bad Request';
    public const INSUFFICIENT_PRIVILEGES = 'Insufficient Privileges';
    public const NOT_ALLOWED = 'Not Allowed';
    public const NOT_IMPLEMENTED = 'Not Implemented';
    public const RESOURCE_NOT_FOUND = 'Not Found';
    public const SERVER_ERROR = 'Server Error';
    public const UNAUTHENTICATED = 'Unauthenticated';
    public const VALIDATION_ERROR = 'Validation Error';
    public const VERIFICATION_ERROR = 'Verification Error';
    private string $type;
    private ?string $description;

    public function __construct(string $type, ?string $description = null)
    {
        $this->type = $type;
        $this->description = $description;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description = null): self
    {
        $this->description = $description;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}
