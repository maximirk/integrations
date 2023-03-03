<?php

declare(strict_types=1);

namespace App\Service\ControlSystem\MoySklad;

use App\Domain\ControlSystem\ControlSystem;
use App\Service\ControlSystem\BaseControlSystem;
use Assert\AssertionFailedException;
use Evgeek\Moysklad\Exceptions\ConfigException;

abstract class BaseMoySklad extends BaseControlSystem
{
    public const IDENTIFIER = "MoySklad";
    public ControlSystem $controlSystem;
    public ApiMoySklad $apiClient;

    /**
     * @throws ConfigException
     * @throws AssertionFailedException
     */
    public function bootMoySklad(ApiMoySklad $api): void
    {
        $this->controlSystem = $this->controlSystemRepository->findOfIdentifier(self::IDENTIFIER);
        $this->apiClient = $api;
        $this->apiClient->createClient($this->controlSystem->getLogin(), $this->controlSystem->getPassword());
    }
}
