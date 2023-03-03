<?php

declare(strict_types=1);

namespace App\Service\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;
use App\Service\Dto\DtoService;
use Assert\AssertionFailedException;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected DtoService $dtoService
    ) {
    }

    /**
     * @throws AssertionFailedException
     */
    public function updateProduct(Product $product, array $newData, string $productId, string $repositoryName): void
    {
        $product = $product->jsonSerialize();
        foreach ($newData as $key => $value) {
            !isset($product[$key]) ?: $product[$key] = $value;
        }

        $productDto = $this->dtoService->createProductDto($product);
        $this->productRepository->update($productDto, $productId, $repositoryName);
    }

    public function getMissingProducts(array $productIds, string $repositoryName): array
    {
        $products = array();

        foreach ($this->productRepository->getAllGenerator($repositoryName) as $productId => $product) {
            if (!in_array($productId, $productIds)) {
                $products[$productId] = $product;
            }
        }

        return $products;
    }

    /**
     * @throws AssertionFailedException
     */
    public function archivedProducts(array $products, string $productRepositoryName): void
    {
        foreach ($products as $key => $product) {
            $this->updateProduct($product, ["archived" => true], $key, $productRepositoryName);
        }
    }
}
