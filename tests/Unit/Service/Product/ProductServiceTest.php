<?php
declare(strict_types=1);

namespace App\Test\Unit\Service\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;
use App\Service\Dto\DtoService;
use App\Service\Product\ProductService;
use App\Test\Unit\ProductBuilder;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Generator;

final class ProductServiceTest extends TestCase
{
    protected ProductRepositoryInterface $productRepository;
    protected DtoService $dtoService;
    protected Product $product;

    public function setUp(): void
    {
        $this->productRepository = $this->createStub(ProductRepositoryInterface::class);
        $this->dtoService = $this->createMock(DtoService::class);
        $this->product = $this->createStub(Product::class);
    }

    public function testUpdateProductWithData(): void
    {
        $this->product->method("jsonSerialize")->willReturn(["archived" => false]);
        $this->dtoService
            ->expects($this->once())
            ->method("createProductDto")
            ->with($this->equalTo(["archived" => true]));

        $productService = new ProductService($this->productRepository, $this->dtoService);
        $productService->updateProduct($this->product, ["archived" => true], "", "");
    }

    public function testUpdateProductWithoutData(): void
    {
        $this->product->method("jsonSerialize")->willReturn(["archived" => false]);
        $this->dtoService
            ->expects($this->once())
            ->method("createProductDto")
            ->with($this->equalTo(["archived" => false]));

        $productService = new ProductService($this->productRepository, $this->dtoService);
        $productService->updateProduct($this->product, [], "", "");
    }

    public function testUpdateProductIncorrectData(): void
    {
        $this->product->method("jsonSerialize")->willReturn(["archived" => false]);
        $this->dtoService
            ->expects($this->once())
            ->method("createProductDto")
            ->with($this->equalTo(["archived" => false]));

        $productService = new ProductService($this->productRepository, $this->dtoService);
        $productService->updateProduct($this->product, ["archived2" => true], "", "");
    }

    /**
     * @dataProvider products
     */
    public function testGetMissingProducts(
        array $availableProductIds,
        array $dataFromRepository,
        array $productsMissing
    ): void {
        $this->productRepository->method("getAllGenerator")
            ->willReturn($this->arrayAsGenerator($dataFromRepository));

        $productService = new ProductService($this->productRepository, $this->dtoService);
        $results = $productService->getMissingProducts($availableProductIds, "");

        $this->assertSame(count($productsMissing), count($results));

        foreach ($results as $productId => $product) {
            $this->assertSame($productsMissing[$productId]->getId(), $product->getId());
        }
    }

    /**
     * @throws AssertionFailedException
     */
    public function products(): array
    {
        return [
            [
                "availableProductIds" => [
                    "id200", "id300", "id500"
                ],
                "dataFromRepository" => [
                    "id200" => (new ProductBuilder())->withId("id200")->build(),
                    "id300" => (new ProductBuilder())->withId("id300")->build(),
                    "id400" => (new ProductBuilder())->withId("id400")->build(),
                    "id500" => (new ProductBuilder())->withId("id500")->build(),
                    "id600" => (new ProductBuilder())->withId("id600")->build(),
                ],
                "productsMissing" => [
                    "id400" => (new ProductBuilder())->withId("id400")->build(),
                    "id600" => (new ProductBuilder())->withId("id600")->build(),
                ]
            ]
        ];
    }

    public function arrayAsGenerator(array $array): Generator
    {
        foreach ($array as $key => $value) {
            yield $key => $value;
        }
    }
}
