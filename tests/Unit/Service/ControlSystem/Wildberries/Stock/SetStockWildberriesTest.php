<?php
declare(strict_types=1);

namespace App\Test\Unit\Service\ControlSystem\Wildberries\Stock;

use App\Domain\ControlSystem\ControlSystem;
use App\Domain\ControlSystem\ControlSystemRepositoryInterface;
use App\Domain\Product\ProductRepositoryInterface;
use App\Service\ControlSystem\Wildberries\ApiWildberries;
use App\Service\ControlSystem\Wildberries\Stock\SetStockWildberries;
use App\Test\Unit\ProductBuilder;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class SetStockWildberriesTest extends TestCase
{
    protected ProductRepositoryInterface $productRepository;
    protected ApiWildberries $api;
    protected ControlSystemRepositoryInterface $controlSystemRepository;
    protected LoggerInterface $logger;
    protected SetStockWildberries $setStockWildberries;
    protected array $taskData = [];
    protected string $warehouseId = "testWarehouseId";

    public function setUp(): void
    {
        $this->productRepository = $this->createStub(ProductRepositoryInterface::class);
        $this->api = $this->createStub(ApiWildberries::class);
        $this->controlSystemRepository = $this->createStub(ControlSystemRepositoryInterface::class);
        $this->logger = $this->createStub(LoggerInterface::class);

        $controlSystem = $this->createStub(ControlSystem::class);
        $this->controlSystemRepository->method("findOfIdentifier")->willReturn($controlSystem);

        $this->setStockWildberries = new SetStockWildberries($this->productRepository);
        $this->setStockWildberries->boot($this->controlSystemRepository, $this->logger);
        $this->setStockWildberries->bootWildberries($this->api);

        $controlSystem
            ->method("getApiKey")
            ->will(
                $this->returnCallback(
                    function () {
                        return ["newKey" => "", "key" => ""];
                    }
                )
            );

        $this->api->method("createClient")->willReturn($this->api);

        $this->taskData = [
            "identifier" => "StockMainMoySkladToWildberries",
            "type" => "Stock",
            "id" => 1,
            "from" => [
                "identifier" => "MoySklad",
                "control_system_inner_id" => "testStockId"
            ],
            "to" => [
                "identifier" => "Wildberries",
                "control_system_inner_id" => $this->warehouseId
            ]
        ];
    }

    public function testWrongInTaskData(): void
    {
        $this->expectException(AssertionFailedException::class);

        $this->taskData["to"]["identifier"] = "Error";

        $this->setStockWildberries->execute($this->taskData);
    }

    public function testWithMoreThan1000Products(): void
    {
        $requestData = [];

        $productsInStockRepository = $this->stockRepositoryMoreThan1000();
        $productsInWildberries = $this->wildberriesRemoteRepository();

        $this->productRepository->method("getAll")->willReturn($productsInStockRepository);

        $this->createGetStocksWarehouse($productsInWildberries, [3,1000,1000,700]);
        $this->createUpdateStocksWarehouse($requestData, [3,1000,1000,700]);

        $this->setStockWildberries->execute($this->taskData);

        $this->assertSame(2700, count($requestData));
        $this->assertSame(2700, count(array_unique(array_column($requestData, "sku"))));

        $productsForTestQuantity = array_map(
            fn ($product): int => (int)$product->getQuantity(),
            $productsInStockRepository
        );
        $this->assertSame(
            array_column($requestData, "amount"),
            array_values($productsForTestQuantity)
        );
    }

    public function testWithLessThan1000Products(): void
    {
        $requestData = [];

        $productsInStockRepository = $this->stockRepositoryLessThan1000();
        $productsInWildberries = $this->wildberriesRemoteRepository();

        $this->productRepository->method("getAll")->willReturn($productsInStockRepository);
        $this->createGetStocksWarehouse($productsInWildberries, [1, 100]);
        $this->createUpdateStocksWarehouse($requestData, [1,100]);

        $this->setStockWildberries->execute($this->taskData);

        $this->assertSame(100, count($requestData));
        $this->assertSame(100, count(array_unique(array_column($requestData, "sku"))));

        $productsForTestQuantity = array_map(
            fn ($product): int => (int)$product->getQuantity(),
            $productsInStockRepository
        );
        $this->assertSame(
            array_column($requestData, "amount"),
            array_values($productsForTestQuantity)
        );
    }

    public function testSuccessWithEmptyProducts(): void
    {
        $productsInStockRepository = array();

        $this->productRepository->method("getAll")->willReturn($productsInStockRepository);
        $this->api->expects($this->exactly(0))->method("getStocksWarehouse");
        $this->api->expects($this->exactly(0))->method("updateStocksWarehouse");

        $this->setStockWildberries->execute($this->taskData);
    }

    public function testWithNoWildberriesProducts(): void
    {
        $requestData = [];

        $productsInStockRepository = $this->stockRepositoryMoreThan1000();
        $productsInStockRepository[] = (new ProductBuilder())->withId("idd1")->withBarcode("barr1")->build();
        $productsInStockRepository[] = (new ProductBuilder())->withId("idd2")->withBarcode("barr2")->build();
        $productsInStockRepository[] = (new ProductBuilder())->withId("idd3")->withBarcode("barr3")->build();
        $productsInWildberries = $this->wildberriesRemoteRepository();

        $this->productRepository->method("getAll")->willReturn($productsInStockRepository);
        $this->createGetStocksWarehouse($productsInWildberries, [3,1000,1000,703]);
        $this->createUpdateStocksWarehouse($requestData, [3,1000,1000,700]);

        $this->setStockWildberries->execute($this->taskData);

        $this->assertSame(2700, count($requestData));
        $this->assertSame(2700, count(array_unique(array_column($requestData, "sku"))));

        $productsForTestQuantity = array_map(
            fn ($product): int => (int)$product->getQuantity(),
            array_slice($productsInStockRepository, 0, 2700)
        );
        $this->assertSame(
            array_column($requestData, "amount"),
            array_values($productsForTestQuantity)
        );
    }

    public function createGetStocksWarehouse(array $productsInWildberries, array $counts): void
    {
        isset($counts[2]) ?: $counts[2] = 0;
        isset($counts[3]) ?: $counts[3] = 0;

        $this->api
            ->expects($this->exactly($counts[0]))
            ->method("getStocksWarehouse")
            ->will(
                $this->returnCallback(
                    function ($warehouseId, $productsToSend) use ($productsInWildberries) {
                        $issetProducts["stocks"] = array();
                        foreach ($productsInWildberries as $barcode => $product) {
                            !(in_array($barcode, $productsToSend) && $warehouseId === $product->warehouseId)
                                ?: $issetProducts["stocks"][] = $product;
                        }

                        return $issetProducts;
                    }
                )
            )
            ->withConsecutive(
                [$this->equalTo($this->warehouseId), $this->countOf($counts[1])],
                [$this->equalTo($this->warehouseId), $this->countOf($counts[2])],
                [$this->equalTo($this->warehouseId), $this->countOf($counts[3])]
            );
    }

    public function createUpdateStocksWarehouse(array &$requestData, array $counts): void
    {
        isset($counts[2]) ?: $counts[2] = 0;
        isset($counts[3]) ?: $counts[3] = 0;

        $this->api
            ->expects($this->exactly($counts[0]))
            ->method("updateStocksWarehouse")
            ->will(
                $this->returnCallback(
                    function ($warehouseId, $currentRequestData) use (&$requestData) {
                        $requestData = array_merge($requestData, $currentRequestData);
                    }
                )
            )
            ->withConsecutive(
                [$this->equalTo($this->warehouseId), $this->countOf($counts[1])],
                [$this->equalTo($this->warehouseId), $this->countOf($counts[2])],
                [$this->equalTo($this->warehouseId), $this->countOf($counts[3])],
            );
    }

    /**
     * @throws AssertionFailedException
     */
    public function stockRepositoryMoreThan1000(): array
    {
        $products = array();

        for ($i = 10; $i < 2710; $i++) {
            $products["bar$i"] = (new ProductBuilder())
                ->withId("id$i")
                ->withBarcode("bar$i")
                ->withQuantity((float)rand(0, 9))
                ->build();
        }

        return $products;
    }

    /**
     * @throws AssertionFailedException
     */
    public function stockRepositoryLessThan1000(): array
    {
        $products = array();

        for ($i = 10; $i < 110; $i++) {
            $products["bar$i"] = (new ProductBuilder())
                ->withId("id$i")
                ->withBarcode("bar$i")
                ->withQuantity((float)rand(0, 9))
                ->build();
        }

        return $products;
    }

    public function wildberriesRemoteRepository(): array
    {
        $products = array();

        for ($i = 0; $i < 3500; $i++) {
            $products["bar$i"] = (object)[
                "sku" => "bar$i",
                "amount" => 0,
                "warehouseId" => $this->warehouseId
            ];
        }

        return $products;
    }
}
