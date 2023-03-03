<?php

namespace App\Test;

use App\Factory\ContainerFactory;
use App\Repository\JsonControlSystemRepository;
use App\Service\ControlSystem\MoySklad\BaseMoySklad;
use App\Service\ControlSystem\Wildberries\BaseWildberries;
use Assert\AssertionFailedException;
use Exception;
use Psr\Container\ContainerInterface;

trait AppTrait
{
    public function getSettings(): array
    {
        return require __DIR__ . "/../config/settings.php";
    }

    /**
     * @throws Exception
     */
    public function getContainer(): ContainerInterface
    {
        return (new ContainerFactory())->createInstance();
    }

    public function getRedisSettings(): array
    {
        $settings = $this->getSettings();

        return [
            "host" => $settings['redis']['host'],
            "port" => $settings['redis']['port']
        ];
    }

    /**
     * @throws AssertionFailedException
     */
    public function getMoySkladSettings(): array
    {
        $settings = $this->getSettings();
        $moySklad = (new JsonControlSystemRepository($settings))->findOfIdentifier(BaseMoysklad::IDENTIFIER);

        return [
            "login" => $moySklad->getLogin(),
            "password" => $moySklad->getPassword(),
            "storeIdTest" => "storeIdMoySkladTest"
        ];
    }

    /**
     * @throws AssertionFailedException
     */
    public function getWildberriesSettings(): array
    {
        $settings = $this->getSettings();
        $wildberries = (new JsonControlSystemRepository($settings))->findOfIdentifier(BaseWildberries::IDENTIFIER);

        return [
            "key" => $wildberries->getApiKey()["key"],
            "newKey" => $wildberries->getApiKey()["newKey"],
            "storeIdTest" => "storeIdWbTest"
        ];
    }
}
