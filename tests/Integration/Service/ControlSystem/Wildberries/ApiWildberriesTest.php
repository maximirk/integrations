<?php

namespace App\Test\Integration\Service\ControlSystem\Wildberries;

use App\Service\ControlSystem\Wildberries\ApiWildberries;
use App\Test\AppTrait;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Swagger\Client\ApiException;

class ApiWildberriesTest extends TestCase
{
    use AppTrait;

    protected ApiWildberries $api;
    protected LoggerInterface $logger;
    protected array $wildberriesSettings;

    public function setUp(): void
    {
        $this->logger = $this->createStub(LoggerInterface::class);
        $this->wildberriesSettings = $this->getWildberriesSettings();
        $this->api = new ApiWildberries($this->logger);
    }

    public function realProducts(): array
    {
        return [
            ["barcodes" => ["2036740831220", "2036799724382", "2037190819356"]]
        ];
    }

    public function errorProducts(): array
    {
        return [
            ["barcodes" => ["111", "222", "333"]]
        ];
    }

    public function errorPartProducts(): array
    {
        return [
            ["barcodes" => ["2036740831220", "222", "2037190819356"]]
        ];
    }

    public function errorProductsForUpdate(): array
    {
        return [
            [
                "barcodes" => [
                    ["sku" => "111", "amount" => 3],
                    ["sku" => "222", "amount" => 5],
                    ["sku" => "333", "amount" => 7]
                ]
            ]
        ];
    }

    /**
     * @dataProvider realProducts
     */
    public function testGetStocksWarehouseSuccess(array $products): void
    {
        $result = $this->api
            ->createClient($this->wildberriesSettings["newKey"], "market")
            ->getStocksWarehouse($this->wildberriesSettings["storeIdTest"], $products);

        $this->assertContains($result["stocks"][0]->sku, $products);
        $this->assertContains($result["stocks"][1]->sku, $products);
        $this->assertContains($result["stocks"][2]->sku, $products);
        $this->assertSame(3, count($result["stocks"]));
    }

    /**
     * @dataProvider realProducts
     */
    public function testGetStocksWarehouseErrorStoreId(array $products): void
    {
        $this->expectException(ApiException::class);

        $this->api
            ->createClient($this->wildberriesSettings["newKey"], "market")
            ->getStocksWarehouse("errorStoreIdTest", $products);
    }

    /**
     * @dataProvider errorProducts
     */
    public function testGetStocksWarehouseErrorProducts(array $products): void
    {
        $result = $this->api
            ->createClient($this->wildberriesSettings["newKey"], "market")
            ->getStocksWarehouse($this->wildberriesSettings["storeIdTest"], $products);

        $this->assertArrayHasKey("stocks", $result);
        $this->assertEmpty($result["stocks"]);
    }

    /**
     * @dataProvider errorPartProducts
     */
    public function testGetStocksWarehouseErrorPartProducts(array $products): void
    {
        $result = $this->api
            ->createClient($this->wildberriesSettings["newKey"], "market")
            ->getStocksWarehouse($this->wildberriesSettings["storeIdTest"], $products);

        $this->assertContains($result["stocks"][0]->sku, $products);
        $this->assertContains($result["stocks"][1]->sku, $products);
        $this->assertSame(2, count($result["stocks"]));
    }

    /**
     * @dataProvider errorProductsForUpdate
     * 409 error
     */
    public function testUpdateStocksWarehouseErrorProducts(array $products): void
    {
        $resultLogger = false;

        $this->logger
            ->method("info")
            ->will($this->returnCallback(
                function () use (&$resultLogger) {
                    $resultLogger = "ApiException";
                    return true;
                }
            ));

        $this->api
            ->createClient($this->wildberriesSettings["newKey"], "market")
            ->updateStocksWarehouse($this->wildberriesSettings["storeIdTest"], $products);

        $this->assertSame("ApiException", $resultLogger);
    }
}
