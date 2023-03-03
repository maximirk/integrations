<?php

declare(strict_types=1);

namespace App\Domain\ControlSystem;

use JsonSerializable;

class ControlSystem implements JsonSerializable
{
    private string $identifier;
    private int $id;
    private string $login;
    private string $password;
    private array $apiKey;
    private array $customFields;

    public function __construct(
        string $identifier,
        int $id,
        string $login,
        string $password,
        array $apiKey,
        array $customFields
    ) {
        $this->identifier = $identifier;
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
        $this->apiKey = $apiKey;
        $this->customFields = $customFields;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getApiKey(): array
    {
        return $this->apiKey;
    }

    public function getCustomField(): array
    {
        return $this->customFields;
    }

    public function jsonSerialize(): array
    {
        return [
            'identifier' => $this->identifier,
            'id' => $this->id,
            'login' => $this->login,
            'password' => $this->password,
            'apiKey' => $this->apiKey,
            'customFields' => $this->customFields
        ];
    }
}
