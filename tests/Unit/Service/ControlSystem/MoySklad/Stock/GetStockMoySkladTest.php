<?php
declare(strict_types=1);

namespace App\Test\Unit\Service\ControlSystem\MoySklad\Stock;

use App\Domain\ControlSystem\ControlSystemRepositoryInterface;
use App\Domain\Product\ProductRepositoryInterface;
use App\Service\ControlSystem\MoySklad\ApiMoySklad;
use App\Service\ControlSystem\MoySklad\Product\ProductDtoMoySklad;
use App\Service\ControlSystem\MoySklad\Stock\GetStockMoySklad;
use App\Service\Dto\DtoService;
use App\Test\Unit\ProductBuilder;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class GetStockMoySkladTest extends TestCase
{
    protected ProductRepositoryInterface $productRepository;
    protected ProductDtoMoySklad $productDtoMoySklad;
    protected ApiMoySklad $api;
    protected ControlSystemRepositoryInterface $controlSystemRepository;
    protected LoggerInterface $logger;
    protected GetStockMoySklad $getStockMoySklad;
    protected array $taskData = [];

    public function setUp(): void
    {
        $this->productRepository = $this->createStub(ProductRepositoryInterface::class);
        $this->productDtoMoySklad = $this->createStub(ProductDtoMoySklad::class);
        $this->api = $this->createStub(ApiMoySklad::class);
        $this->controlSystemRepository = $this->createStub(ControlSystemRepositoryInterface::class);
        $this->logger = $this->createStub(LoggerInterface::class);

        $this->getStockMoySklad = new GetStockMoySklad(
            $this->productRepository,
            $this->productDtoMoySklad
        );

        $this->getStockMoySklad->boot($this->controlSystemRepository, $this->logger);
        $this->getStockMoySklad->bootMoySklad($this->api);

        $this->taskData = [
            "identifier" => "StockMainMoySkladToWildberries",
            "type" => "Stock",
            "id" => 1,
            "from" => [
                "identifier" => "MoySklad",
                "control_system_inner_id" => "testFromStoreId"
            ],
            "to" => [
                "identifier" => "Wildberries",
                "control_system_inner_id" => "testToStoreId"
            ]
        ];
    }

    public function testWrongInTaskData(): void
    {
        $this->expectException(AssertionFailedException::class);

        $this->taskData["from"]["identifier"] = "Error";

        $this->getStockMoySklad->execute($this->taskData);
    }


    public function testSuccess(): void
    {
        $productsFromMoySklad = $this->productsFromMoySklad();
        $productsFromRepository = $this->productsFromRepository();

        $repositoryStock = $this->execute($productsFromMoySklad, $productsFromRepository);

        $this->assertSame(3, count($repositoryStock));
        $this->assertSame("id100", $repositoryStock[0]["id"]);
        $this->assertSame("id200", $repositoryStock[1]["id"]);
        $this->assertSame("id300", $repositoryStock[2]["id"]);
        $this->assertSame(4, $repositoryStock[1]["quantity"]);
        $this->assertSame("bar200", $repositoryStock[1]["barcode"]);
    }

    public function testNoBarcode(): void
    {
        $productsFromMoySklad = $this->productsFromMoySklad();
        $productsFromRepository = $this->productsFromRepositoryNoBarcode();

        $repositoryStock = $this->execute($productsFromMoySklad, $productsFromRepository);

        $this->assertSame(2, count($repositoryStock));
    }

    public function testNoProductInRepository(): void
    {
        $productsFromMoySklad = $this->productsFromMoySklad();
        $productsFromRepository = $this->productsFromRepository();
        $productsFromRepository[] = null;

        $repositoryStock = $this->execute($productsFromMoySklad, $productsFromRepository);

        $this->assertSame(3, count($repositoryStock));
    }

    public function execute(array $productsFromMoySklad, array $productsFromRepository): array
    {
        $repositoryStock = array();

        $this->api->method("getStockByStore")->willReturn($productsFromMoySklad);

        $this->productRepository->method("get")->willReturn(...$productsFromRepository);

        $this->productDtoMoySklad
            ->method("createFromStock")
            ->will(
                $this->returnCallback(
                    function ($product) use (&$repositoryStock) {
                        $repositoryStock[] = $product;
                        return (new DtoService())->createProductDto($product);
                    }
                )
            );

        $this->getStockMoySklad->execute($this->taskData);

        return $repositoryStock;
    }

    public function productsFromMoySklad(): array
    {
        return [
            [
                "assortmentId" => "id100",
                "storeId" => "testFromStoreId",
                "quantity" => 2
            ],
            [
                "assortmentId" => "id200",
                "storeId" => "testFromStoreId",
                "quantity" => 4
            ],
            [
                "assortmentId" => "id300",
                "storeId" => "testFromStoreId",
                "quantity" => 7
            ],
        ];
    }

    /**
     * @throws AssertionFailedException
     */
    public function productsFromRepository(): array
    {
        return  [
            (new ProductBuilder())->withId("id100")->withBarcode("bar100")->build(),
            (new ProductBuilder())->withId("id200")->withBarcode("bar200")->build(),
            (new ProductBuilder())->withId("id300")->withBarcode("bar300")->build(),
        ];
    }

    /**
     * @throws AssertionFailedException
     */
    public function productsFromRepositoryNoBarcode(): array
    {
        return  [
            (new ProductBuilder())->withId("id100")->withBarcode("bar100")->build(),
            (new ProductBuilder())->withId("id200")->withBarcode("")->build(),
            (new ProductBuilder())->withId("id300")->withBarcode("bar300")->build(),
        ];
    }
}
