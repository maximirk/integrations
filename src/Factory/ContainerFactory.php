<?php

namespace App\Factory;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    /**
     * @throws \Exception
     */
    public function createInstance(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions(__DIR__ . '/../../config/container.php');
        $containerBuilder->useAttributes(true);

        return $containerBuilder->build();
    }
}
