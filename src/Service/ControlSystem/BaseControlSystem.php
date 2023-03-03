<?php

declare(strict_types=1);

namespace App\Service\ControlSystem;

use App\Domain\ControlSystem\ControlSystemRepositoryInterface;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Psr\Log\LoggerInterface;

abstract class BaseControlSystem
{
    const IDENTIFIER = "";
    //Все товары с ключом по id, используется только в связке с названием ControlSystem
    public const TABLE_PRODUCTS_KEY_PRODUCT_ID = "products:key_product_id";
    //Количество товаров, краткая таблица всех товаров, где указано количество. Только в связке с данными ControlSystem.
    public const TABLE_STOCK_KEY_BARCODE = "stock:key_barcode";

    protected ControlSystemRepositoryInterface $controlSystemRepository;
    protected LoggerInterface $logger;

    public function boot(
        ControlSystemRepositoryInterface $controlSystemRepository,
        LoggerInterface $logger
    ): void {
        $this->controlSystemRepository = $controlSystemRepository;
        $this->logger = $logger;
    }

    /**
     * @throws AssertionFailedException
     */
    public function startValidFrom(array $taskData): void
    {
        Assertion::same($taskData["from"]["identifier"], static::IDENTIFIER);
    }

    /**
     * @throws AssertionFailedException
     */
    public function startValidTo(array $taskData): void
    {
        Assertion::same($taskData["to"]["identifier"], static::IDENTIFIER);
    }

    abstract public function execute(array $taskData): void;
}
