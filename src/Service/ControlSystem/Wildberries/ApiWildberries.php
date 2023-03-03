<?php

declare(strict_types=1);

namespace App\Service\ControlSystem\Wildberries;

use DomainException;
use Exception;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Swagger\Client\Api\DefaultApi;
use Swagger\Client\Api\Marketplace_Api;
use Swagger\Client\ApiException;
use Swagger\Client\Configuration;
use Swagger\Client\Api\_Api;
use Swagger\Client\Model\StocksWarehouseBody;
use Swagger\Client\Model\StocksWarehouseBody1;

class ApiWildberries
{
    protected LoggerInterface $logger;
    protected DefaultApi|_Api|Marketplace_Api $apiInstance;
    protected Configuration $config;
    protected string $apiKeyIdentifier;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @throws AssertionFailedException
     * @throws Exception
     * $apiType = api, лимит в 100 запросов в минуту
     */
    public function createClient(string $apiKey, string $apiType): self
    {
        Assertion::notEmpty($apiKey);

        $this->apiKeyIdentifier = "Authorization";
        $host = "https://suppliers-api.wildberries.ru";

        if ($apiType == "default") {
            $this->apiKeyIdentifier = "key";
            $host = "https://suppliers-stats.wildberries.ru";
            $class = DefaultApi::class;
        } elseif ($apiType == "api") {
            $class = _Api::class;
        } elseif ($apiType == "market") {
            $class = Marketplace_Api::class;
        } else {
            throw new DomainException('Неверный вид api wildberries');
        }

        $this->config = Configuration::getDefaultConfiguration()->setApiKey(
            $this->apiKeyIdentifier,
            $apiKey
        )->setHost($host);

        $this->apiInstance = new $class(new Client(), $this->config);

        return $this;
    }

    /**
     * Нужен client с $apiType = market
     *
     * @throws ApiException
     */
    public function getStocksWarehouse(string $warehouseId, array $products): array
    {
        $data["skus"] = $products;
        $body = new StocksWarehouseBody1($data);

        /** @phpstan-ignore-next-line */
        return $this->apiInstance->apiV3StocksWarehousePost($body, $warehouseId);
    }

    /**
     *
     * Нужен client с $apiType = market
     *
     * @throws AssertionFailedException
     * @throws ApiException
     */
    public function updateStocksWarehouse(string $warehouseId, array $products): void
    {
        Assertion::notEmpty($products);
        Assertion::notEmpty($warehouseId);

        $requestData["stocks"] = $products;
        $body = new StocksWarehouseBody($requestData);

        try {
            /** @phpstan-ignore-next-line */
            $this->apiInstance->apiV3StocksWarehousePut($warehouseId, $body);
        } catch (ApiException $e) {
            if ($e->getCode() == 409) {
                //при этой ошибке обновление товаров все равно происходит
                $this->logger->info("Обновление остатков на WB, ошибка 409, не все товары были найдены");
            } else {
                throw $e;
            }
        }
    }
}
