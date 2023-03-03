<?php
declare(strict_types=1);

namespace App\Test\Unit\Service\ControlSystem\MoySklad\Product;

use App\Domain\ControlSystem\ControlSystemRepositoryInterface;
use App\Domain\Product\ProductRepositoryInterface;
use App\Service\ControlSystem\MoySklad\ApiMoySklad;
use App\Service\ControlSystem\MoySklad\Product\GetProductMoySklad;
use App\Service\ControlSystem\MoySklad\Product\ProductDtoMoySklad;
use App\Service\Dto\DtoService;
use App\Service\Product\ProductService;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class GetProductMoySkladTest extends TestCase
{
    protected ProductRepositoryInterface $productRepository;
    protected ProductService $productService;
    protected ProductDtoMoySklad $productDtoMoySklad;
    protected ApiMoySklad $api;
    protected ControlSystemRepositoryInterface $controlSystemRepository;
    protected LoggerInterface $logger;
    protected GetProductMoySklad $getProductMoySklad;
    protected array $taskData = [];

    public function setUp(): void
    {
        $this->productRepository = $this->createStub(ProductRepositoryInterface::class);
        $this->productService = $this->createStub(ProductService::class);
        $this->productDtoMoySklad = $this->createStub(ProductDtoMoySklad::class);
        $this->api = $this->createStub(ApiMoySklad::class);
        $this->controlSystemRepository = $this->createStub(ControlSystemRepositoryInterface::class);
        $this->logger = $this->createStub(LoggerInterface::class);

        $this->getProductMoySklad = new GetProductMoySklad(
            $this->productRepository,
            $this->productService,
            $this->productDtoMoySklad
        );

        $this->getProductMoySklad->boot($this->controlSystemRepository, $this->logger);
        $this->getProductMoySklad->bootMoySklad($this->api);

        $this->taskData = [
            "identifier" => "ProductsMoySklad",
            "type" => "Products",
            "id" => 3,
            "from" => [
                "identifier" => "MoySklad",
                "control_system_inner_id" => ""
            ],
            "to" => [
                "identifier" => "none",
                "control_system_inner_id" => ""
            ]
        ];
    }

    /**
     * @dataProvider additionalData
     */
    public function testSuccess(array $modifications, array $products): void
    {
        $availableProductIds = $this->execute($products, $modifications);

        $this->assertSame(9, count($availableProductIds));
        $this->assertContains("id400", $availableProductIds);
        $this->assertContains("id500", $availableProductIds);
        foreach ($modifications as $modification) {
            $this->assertContains($modification["id"], $availableProductIds);
        }
    }

    public function testWrongInTaskData(): void
    {
        $this->expectException(AssertionFailedException::class);

        $this->taskData["from"]["identifier"] = "Error";

        $this->getProductMoySklad->execute($this->taskData);
    }

    /**
     * @dataProvider additionalData
     */
    public function testNoFullModifications(array $modifications, array $products): void
    {
        unset($modifications[0]);
        unset($modifications[1]);
        unset($modifications[2]);

        $this->logger->expects($this->once())->method("info");

        $availableProductIds = $this->execute($products, $modifications);

        $this->assertSame(6, count($availableProductIds));
        $this->assertNotContains("mod-id101", $availableProductIds);
        $this->assertNotContains("mod-id102", $availableProductIds);
        $this->assertNotContains("mod-id103", $availableProductIds);
    }

    /**
     * @dataProvider productsWithoutModifications
     */
    public function testNoModifications(array $products): void
    {
        $modifications = array();

        $availableProductIds = $this->execute($products, $modifications);

        $this->assertSame(3, count($availableProductIds));
        $this->assertContains("id100", $availableProductIds);
        $this->assertContains("id200", $availableProductIds);
        $this->assertContains("id300", $availableProductIds);
    }

    public function execute(array $products, array $modifications): array
    {
        $availableProductIds = [];

        $this->api->method("getModifications")->willReturn($modifications);
        $this->api->method("getProducts")->willReturn($products);

        $this->productDtoMoySklad
            ->method("createFromProduct")
            ->will(
                $this->returnCallback(
                    function ($product, $modification = null) {
                        is_null($modification) ?: $product["id"] = $modification["id"];
                        return (new DtoService())->createProductDto($product);
                    }
                )
            );

        $this->productService
            ->method("getMissingProducts")
            ->will(
                $this->returnCallback(
                    function ($products) use (&$availableProductIds) {
                        return $availableProductIds = $products;
                    }
                )
            );

        $this->getProductMoySklad->execute($this->taskData);

        return $availableProductIds;
    }

    public function additionalData(): array
    {
        return [
            [
                "modifications" => [
                    [
                        "id" => "mod-id101",
                        "product" => [
                            "meta" => [
                                "href" => "https://online.moysklad.ru/product/id100"
                            ]
                        ]
                    ],
                    [
                        "id" => "mod-id102",
                        "product" => [
                            "meta" => [
                                "href" => "https://online.moysklad.ru/product/id100"
                            ]
                        ]
                    ],
                    [
                        "id" => "mod-id103",
                        "product" => [
                            "meta" => [
                                "href" => "https://online.moysklad.ru/product/id100"
                            ]
                        ]
                    ],
                    [
                        "id" => "mod-id201",
                        "product" => [
                            "meta" => [
                                "href" => "https://online.moysklad.ru/product/id200"
                            ]
                        ]
                    ],
                    4 => [
                        "id" => "mod-id202",
                        "product" => [
                            "meta" => [
                                "href" => "https://online.moysklad.ru/product/id200"
                            ]
                        ]
                    ],
                    5 => [
                        "id" => "mod-id301",
                        "product" => [
                            "meta" => [
                                "href" => "https://online.moysklad.ru/product/id300"
                            ]
                        ]
                    ],
                    6 => [
                        "id" => "mod-id302",
                        "product" => [
                            "meta" => [
                                "href" => "https://online.moysklad.ru/product/id300"
                            ]
                        ]
                    ]
                ],
                "products" => [
                    [
                        "id" => "id100",
                        "variantsCount" => 3
                    ],
                    [
                        "id" => "id200",
                        "variantsCount" => 2
                    ],
                    [
                        "id" => "id300",
                        "variantsCount" => 2
                    ],
                    [
                        "id" => "id400",
                        "variantsCount" => 0
                    ],
                    [
                        "id" => "id500",
                        "variantsCount" => 0
                    ],
                ]
            ]
        ];
    }

    public function productsWithoutModifications(): array
    {
        return [
            [
                "products" => [
                    [
                        "id" => "id100",
                        "variantsCount" => 0
                    ],
                    [
                        "id" => "id200",
                        "variantsCount" => 0
                    ],
                    [
                        "id" => "id300",
                        "variantsCount" => 0
                    ]
                ]
            ]
        ];
    }
}
