<?php

declare(strict_types=1);

namespace App\Factory;

use App\Service\ControlSystem\BaseControlSystem;
use App\Service\ControlSystem\MoySklad\Product\GetProductMoySklad;
use App\Service\ControlSystem\MoySklad\Stock\GetStockMoySklad;
use App\Service\ControlSystem\Wildberries\Stock\SetStockWildberries;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use UnexpectedValueException;

/**
 * Создается объект для обработки задачи по синхронизации
 */
class SynchronizationTaskFactory
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createControlSystem(string $syncType, string $action, string $identifier): BaseControlSystem
    {
        $class = match (true) {
            //MoySklad
            $syncType == "Products" && $action == "From" && $identifier == "MoySklad"
                => GetProductMoySklad::class,
            $syncType == "Stock" && $action == "From" && $identifier == "MoySklad"
                => GetStockMoySklad::class,

            //Wildberries
            $syncType == "Stock" && $action == "To" && $identifier == "Wildberries"
                => SetStockWildberries::class,

            default => throw new UnexpectedValueException("Невозможно обработать задачу")
        };

        return $this->container->get($class);
    }
}
