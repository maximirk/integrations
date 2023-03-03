<?php

declare(strict_types=1);

namespace App\Service\Dto;

use Assert\Assertion;
use Assert\AssertionFailedException;

class DtoService
{
    /**
     * @throws AssertionFailedException
     */
    public function createProductDto(array $product): ProductDto
    {
        Assertion::notEmpty($product["id"]);

        $productDto = new ProductDto();

        $productDto->id = $product["id"];
        $productDto->sku = $product["sku"] ?? null;
        $productDto->quantity = $product["quantity"] ?? null;
        $productDto->type = $product["type"] ?? null;
        $productDto->modificationId = $product["modificationId"] ?? null;
        $productDto->barcode = $product["barcode"] ?? null;
        $productDto->modificationCharacteristics = $product["modificationCharacteristics"] ?? null;
        $productDto->archived = $product["archived"] ?? false;
        $productDto->price = $product["price"] ?? null;

        return $productDto;
    }
}
