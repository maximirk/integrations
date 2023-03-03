<?php

declare(strict_types=1);

namespace App\Service\ControlSystem\Wildberries;

use App\Domain\ControlSystem\ControlSystem;
use App\Service\ControlSystem\BaseControlSystem;
use Assert\AssertionFailedException;

abstract class BaseWildberries extends BaseControlSystem
{
    public const IDENTIFIER = "Wildberries";
    public ControlSystem $controlSystem;
    public ApiWildberries $apiClient;
    public ApiWildberries $api;

    public function bootWildberries(
        ApiWildberries $api
    ): void {
        $this->controlSystem = $this->controlSystemRepository->findOfIdentifier(self::IDENTIFIER);
        $this->api = $api;
    }

    /**
     * @throws AssertionFailedException
     */
    public function createApi(string $apiKeyType, string $apiType): ApiWildberries
    {
        if ($apiKeyType == "new") {
            $apiKey = $this->controlSystem->getApiKey()["newKey"];
        } else {
            $apiKey = $this->controlSystem->getApiKey()["key"];
        }

        return $this->apiClient = $this->api->createClient($apiKey, $apiType);
    }
}
