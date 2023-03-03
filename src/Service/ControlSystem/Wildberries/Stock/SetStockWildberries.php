<?php

declare(strict_types=1);

namespace App\Service\ControlSystem\Wildberries\Stock;

use App\Domain\Product\ProductRepositoryInterface;
use App\Service\ControlSystem\Wildberries\BaseWildberries;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Swagger\Client\ApiException;

/**
 * Отправка остатков в Wildberries. Нюансы:
 * Нельзы получить из WB разом массив товаров с остатками, чтобы проверить какие товары можно отправить.
 * Чтобы точно узнать какие товары надо обновить, сначала делается запрос с проверкой, какие товары существуют
 * на нужном складе Wildberries. А потом исходя из проверки обновляются данные.
 */
class SetStockWildberries extends BaseWildberries
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
    ) {
    }

    /**
     * @throws AssertionFailedException
     * @throws ApiException
     */
    public function execute(array $taskData): void
    {
        $this->startValidTo($taskData);
        Assertion::notEmpty($taskData["from"]['control_system_inner_id']);
        Assertion::notEmpty($taskData["to"]["control_system_inner_id"]);

        $controlSystemFrom = $this->controlSystemRepository->findOfIdentifier($taskData["from"]['identifier']);

        $stockRepositoryName = parent::TABLE_STOCK_KEY_BARCODE . ":{$controlSystemFrom->getIdentifier()}:
            store_{$taskData["from"]["control_system_inner_id"]}";
        $stockSource = $this->productRepository->getAll($stockRepositoryName);

        $this->createApi("new", "market");

        //проверка товаров со склада источника, можно ли их обновить на нужном складе в ВБ
        $stockWildberries = $this->checkStock($stockSource, $taskData["to"]["control_system_inner_id"]);

        $requestData = array();

        foreach ($stockWildberries as $barcode) {
            $quantity = (int)$stockSource[$barcode]->getQuantity();
            !($quantity < 0) ?: $quantity = 0;

            $requestData[] = ["sku" => (string)$barcode, "amount" => $quantity];
        }

        $this->setStock($requestData, $taskData);

        $this->logger->info('Загрузились остатки на WB, количество - ' . count($requestData));
    }

    /**
     * @throws ApiException
     */
    protected function checkStock(array $stockSource, string $warehouseId): array
    {
        $products = [];

        $length = 1000; //можно отправить максимум 1000 значений
        for ($offset = 0; $offset < count($stockSource); $offset += $length) {
            $slice = array_slice($stockSource, $offset, $length);
            $productsToSend = array_map(fn ($barcode): string => (string)$barcode, array_keys($slice));

            $result = $this->apiClient->getStocksWarehouse($warehouseId, $productsToSend);

            empty($result["stocks"]) ?: $products = array_merge($products, $result["stocks"]);
        }

        return array_map(fn ($product): string => $product->sku, $products);
    }

    /**
     * @throws AssertionFailedException
     * @throws ApiException
     */
    protected function setStock(array $requestData, array $taskData): void
    {
        $length = 1000; //можно отправить максимум 1000 значений
        for ($offset = 0; $offset < count($requestData); $offset += $length) {
            $currentRequestData = array_slice($requestData, $offset, $length);
            $this->apiClient->updateStocksWarehouse($taskData["to"]['control_system_inner_id'], $currentRequestData);
        }
    }
}
