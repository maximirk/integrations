<?php

declare(strict_types=1);

namespace App\Domain\Synchronization;

use JsonSerializable;

class Synchronization implements JsonSerializable
{
    private int $id;
    private string $identifier;
    private string $type;
    private array $from;
    private array $to;

    public function __construct(int $id, string $identifier, string $type, array $from, array $to)
    {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->type = $type;
        $this->from = $from;
        $this->to = $to;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFrom(): array
    {
        return $this->from;
    }

    public function getTo(): array
    {
        return $this->to;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'type' => $this->type,
            'from' => $this->from,
            'to' => $this->to,
        ];
    }
}
